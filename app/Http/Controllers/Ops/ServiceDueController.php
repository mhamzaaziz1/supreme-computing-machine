<?php

namespace App\Http\Controllers\Ops;

use App\Business;
use App\Services\Ops\ServiceDue;
use App\Services\Ops\Whatsapp;
use App\Utils\Util;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The oil-change bay's due list, vehicle history, and owner reminders.
 */
class ServiceDueController extends OpsController
{
    public function __construct(private ServiceDue $due) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorizeView();

        $within = in_array((int) $request->query('within'), [7, 14, 30], true) ? (int) $request->query('within') : 7;

        return response()->json([
            'within' => $within,
            'items' => $this->due->dueList($this->businessId(), $within),
            'sms_available' => $this->smsSettings() !== null,
        ]);
    }

    public function vehicle(int $id): JsonResponse
    {
        $this->authorizeView();

        $v = DB::table('customer_vehicles AS v')->join('contacts AS c', 'c.id', '=', 'v.contact_id')
            ->where('v.business_id', $this->businessId())->where('v.id', $id)
            ->first(['v.*', 'c.name AS owner_name', 'c.supplier_business_name', 'c.mobile']);

        abort_unless($v, 404, 'That vehicle does not exist.');
        $this->outlet((int) $v->contact_id);

        $records = DB::table('vehicle_mileage_records AS r')
            ->leftJoin('transactions AS t', 't.id', '=', 'r.invoice_id')
            ->where('r.vehicle_id', $id)
            ->orderByDesc('r.created_at')
            ->get(['r.id', 'r.previous_mileage', 'r.oil_change_mileage', 'r.next_mileage', 'r.created_at', 'r.invoice_id',
                't.invoice_no', 't.final_total', 't.transaction_date']);

        $lines = DB::table('transaction_sell_lines AS l')->join('products AS p', 'p.id', '=', 'l.product_id')
            ->leftJoin('variations AS va', 'va.id', '=', 'l.variation_id')
            ->whereIn('l.transaction_id', $records->pluck('invoice_id')->filter())
            ->whereNull('l.parent_sell_line_id')
            ->get(['l.transaction_id', 'p.name', 'va.name AS variation', 'l.quantity'])
            ->groupBy('transaction_id');

        $prediction = $this->due->forVehicles($this->businessId(), [$id])[$id] ?? null;
        $lastReminder = DB::table('service_reminders')->where('vehicle_id', $id)->orderByDesc('sent_at')->first(['sent_at', 'channel']);

        return response()->json([
            'vehicle' => [
                'id' => (int) $v->id,
                'plate' => $v->license_plate,
                'name' => trim($v->make.' '.$v->model.($v->year ? ' '.$v->year : '')) ?: null,
                'color' => $v->color,
                'notes' => $v->notes,
            ],
            'owner' => [
                'id' => (int) $v->contact_id,
                'name' => trim($v->supplier_business_name ?: $v->owner_name) ?: 'Unnamed',
                'mobile' => $v->mobile,
            ],
            'service' => $prediction,
            'last_reminder' => $lastReminder,
            'history' => $records->map(fn ($r) => [
                'id' => (int) $r->id,
                'date' => substr((string) ($r->transaction_date ?? $r->created_at), 0, 10),
                'reading' => $r->oil_change_mileage !== null ? (int) $r->oil_change_mileage : null,
                'next' => $r->next_mileage !== null ? (int) $r->next_mileage : null,
                'invoice_id' => $r->invoice_id ? (int) $r->invoice_id : null,
                'invoice_no' => $r->invoice_no,
                'total' => $r->final_total !== null ? (float) $r->final_total : null,
                'items' => ($lines[$r->invoice_id] ?? collect())->map(fn ($l) => trim($l->name.(($l->variation && $l->variation !== 'DUMMY') ? ' '.$l->variation : '')).' × '.(float) $l->quantity)->values()->all(),
            ])->all(),
            'links' => [
                'repeat' => $prediction && $prediction['last_invoice_id']
                    ? route('sales.create', ['contact_id' => $v->contact_id, 'repeat' => $prediction['last_invoice_id']])
                    : null,
                'whatsapp' => Whatsapp::link($v->mobile, $this->message($v, $prediction)),
            ],
        ]);
    }

    /**
     * Record reminders. WhatsApp is sent by the person at the screen (the
     * response carries the links); SMS goes through the business's own SMS
     * gateway settings, the same ones invoice notifications use.
     */
    public function remind(Request $request, Util $util): JsonResponse
    {
        $this->authorizeView();

        $data = $request->validate([
            'vehicle_ids' => ['required', 'array', 'min:1', 'max:200'],
            'vehicle_ids.*' => ['integer'],
            'channel' => ['required', 'in:whatsapp,sms'],
        ]);

        $sms = $data['channel'] === 'sms' ? $this->smsSettings() : null;
        if ($data['channel'] === 'sms' && ! $sms) {
            return response()->json(['message' => 'No SMS gateway is set up in business settings.'], 422);
        }

        $vehicles = DB::table('customer_vehicles AS v')->join('contacts AS c', 'c.id', '=', 'v.contact_id')
            ->where('v.business_id', $this->businessId())->whereIn('v.id', $data['vehicle_ids'])
            ->get(['v.id', 'v.contact_id', 'v.license_plate', 'v.make', 'v.model', 'c.name AS owner_name', 'c.supplier_business_name', 'c.mobile']);

        $predictions = $this->due->forVehicles($this->businessId(), $vehicles->pluck('id')->all());

        $sent = 0;
        $failed = [];
        $links = [];

        foreach ($vehicles as $v) {
            $p = $predictions[$v->id] ?? null;
            $text = $this->message($v, $p);

            if ($data['channel'] === 'sms') {
                if (empty($v->mobile)) {
                    $failed[] = $v->license_plate ?: '#'.$v->id;

                    continue;
                }
                try {
                    $util->sendSms(['sms_settings' => $sms, 'mobile_number' => $v->mobile, 'sms_body' => $text]);
                } catch (\Throwable $e) {
                    \Log::warning('Service reminder SMS failed: '.$e->getMessage());
                    $failed[] = $v->license_plate ?: '#'.$v->id;

                    continue;
                }
            } else {
                $link = Whatsapp::link($v->mobile, $text);
                if (! $link) {
                    $failed[] = $v->license_plate ?: '#'.$v->id;

                    continue;
                }
                $links[] = ['vehicle_id' => (int) $v->id, 'url' => $link];
            }

            DB::table('service_reminders')->insert([
                'business_id' => $this->businessId(),
                'vehicle_id' => $v->id,
                'contact_id' => $v->contact_id,
                'due_on' => $p['due_on'] ?? null,
                'channel' => $data['channel'],
                'message' => $text,
                'sent_at' => now(),
                'sent_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $sent++;
        }

        return response()->json([
            'message' => $data['channel'] === 'sms'
                ? sprintf('%d %s sent by SMS.', $sent, $sent === 1 ? 'reminder' : 'reminders')
                : sprintf('%d WhatsApp %s ready.', $sent, $sent === 1 ? 'message' : 'messages'),
            'sent' => $sent,
            'failed' => $failed,
            'links' => $links,
        ]);
    }

    private function message(object $v, ?array $p): string
    {
        $owner = trim($v->supplier_business_name ?: $v->owner_name) ?: 'there';
        $vehicle = trim(($v->make ?? '').' '.($v->model ?? '')) ?: 'vehicle';
        $plate = $v->license_plate ? ' ('.$v->license_plate.')' : '';

        $when = $p
            ? ($p['days_until'] < 0
                ? sprintf('was due for an oil change around %s', Carbon::parse($p['due_on'])->format('d M'))
                : sprintf('is due for an oil change around %s, at about %s km', Carbon::parse($p['due_on'])->format('d M'), number_format($p['next_mileage'])))
            : 'is due for an oil change';

        return sprintf("Assalam o Alaikum %s, your %s%s %s. Visit %s — reply to this message to book a time.",
            $owner, $vehicle, $plate, $when, session('business.name'));
    }

    /**
     * @return array<string, mixed>|null
     */
    private function smsSettings(): ?array
    {
        $raw = Business::where('id', $this->businessId())->value('sms_settings');
        $s = is_array($raw) ? $raw : json_decode((string) $raw, true);

        if (! is_array($s)) {
            return null;
        }

        $service = $s['sms_service'] ?? 'other';
        $ready = match ($service) {
            'nexmo' => ! empty($s['nexmo_key']) && ! empty($s['nexmo_secret']),
            'twilio' => ! empty($s['twilio_sid']) && ! empty($s['twilio_token']),
            default => ! empty($s['url']) && ! empty($s['send_to_param_name']) && ! empty($s['msg_param_name']),
        };

        return $ready ? $s : null;
    }

    private function authorizeView(): void
    {
        if (! $this->canViewCustomers()) {
            abort(403, 'You do not have permission to view customers.');
        }
    }
}
