<?php

namespace App\Services\Ops;

use App\Utils\GeofenceUtil;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

/**
 * Proof that a seller is at the outlet they are billing.
 *
 * GeofenceUtil already had the checks (route assignment, radius/polygon,
 * GPS accuracy, mock location, allowed hours) but nothing called it. This
 * is the caller: it applies the route's mode (off / log / enforce), records
 * the visit when the check passes, and hands back a short-lived token the
 * sale must carry when the route is enforced.
 *
 * Only users with an active route assignment are subject to it — office
 * and counter staff are never asked where they are.
 *
 * Outlets without coordinates are pinned by the first accurate check-in
 * instead of failing every visit while the coordinates are being collected.
 */
class FieldCheckIn
{
    public const MODES = ['off', 'log', 'enforce'];

    /** Minutes a check-in covers the sales and collections that follow it. */
    public const TOKEN_TTL = 20;

    /** Best accuracy (metres) we will trust to pin an outlet. */
    private const PIN_ACCURACY = 50;

    private const VISIT_TYPE = [
        'place_order' => 'order',
        'record_payment' => 'collection',
        'check_in' => 'check_in',
        'mark_visit_done' => 'visit_end',
        'return' => 'return',
    ];

    public function __construct(private GeofenceUtil $geo) {}

    public function isFieldUser(int $businessId, int $userId): bool
    {
        return DB::table('route_seller_assignments')
            ->where('business_id', $businessId)
            ->where('user_id', $userId)
            ->where('is_active', 1)
            ->exists();
    }

    public function modeFor(?int $routeId): string
    {
        if (! $routeId) {
            return 'off';
        }

        $mode = DB::table('route_zone_restrictions')->where('customer_route_id', $routeId)->value('geofence_mode');

        return in_array($mode, self::MODES, true) ? $mode : 'off';
    }

    public function requiresToken(int $businessId, int $userId, int $contactId): bool
    {
        if (! $this->isFieldUser($businessId, $userId)) {
            return false;
        }

        $routeId = DB::table('contacts')->where('id', $contactId)->value('customer_route_id');

        return $this->modeFor($routeId ? (int) $routeId : null) === 'enforce';
    }

    /**
     * @return array<string, mixed>
     */
    public function check(
        int $businessId,
        int $userId,
        int $contactId,
        string $action,
        ?float $lat,
        ?float $lng,
        ?float $accuracy = null,
        bool $mock = false,
        ?string $reason = null,
        ?string $notes = null,
    ): array {
        $contact = DB::table('contacts')->where('business_id', $businessId)->where('id', $contactId)
            ->first(['id', 'customer_route_id', 'latitude', 'longitude', 'geofence_type', 'geofence_radius']);

        if (! $contact) {
            abort(404, 'That outlet does not exist.');
        }

        $routeId = $contact->customer_route_id ? (int) $contact->customer_route_id : null;
        $mode = $this->modeFor($routeId);
        $out = ['mode' => $mode, 'pinned' => false, 'distance' => null, 'violation' => null, 'logged_visit' => false, 'visit_id' => null];

        if (! $this->isFieldUser($businessId, $userId)) {
            return $out + ['allowed' => true, 'exempt' => true, 'message' => null, 'token' => $this->token($businessId, $userId, $contactId)];
        }

        $hasFix = $lat !== null && $lng !== null;
        $radius = (float) ($contact->geofence_radius ?: config('constants.default_geofence_radius', 100));

        // First accurate visit pins an outlet that has no coordinates yet.
        if ($hasFix && $contact->latitude === null && ! $mock && ($accuracy === null || $accuracy <= self::PIN_ACCURACY)) {
            DB::table('contacts')->where('id', $contactId)->update([
                'latitude' => $lat, 'longitude' => $lng, 'geofence_type' => 'radius', 'geofence_radius' => $radius,
            ]);
            $out['pinned'] = true;
        } elseif ($contact->latitude !== null && empty($contact->geofence_type)) {
            // Coordinates but no fence: give it the default radius so the
            // util's check has something to test against.
            DB::table('contacts')->where('id', $contactId)->update(['geofence_type' => 'radius', 'geofence_radius' => $radius]);
        }

        if (! $hasFix) {
            $passed = false;
            $message = 'This device did not report a location.';
            $out['violation'] = 'no_location';
            if ($mode !== 'off') {
                $this->geo->logViolation($businessId, $userId, $routeId, $contactId, 'no_location', $action, 0, 0, null, null, null, false,
                    'No GPS fix'.($reason ? ' — reason: '.$reason : ''));
            }
        } elseif ($mode === 'off' || $out['pinned'] || $contact->latitude === null) {
            $passed = true;
            $message = $out['pinned'] ? 'Outlet location saved from this visit.' : null;
        } else {
            $res = $this->geo->isActionAllowed($businessId, $userId, $contactId, $action, $lat, $lng, $accuracy, null, $mock);
            $passed = (bool) $res['is_allowed'];
            $message = $passed ? null : $res['message'];
            $out['violation'] = $res['violation_type'];
            $out['distance'] = (int) round($this->geo->calculateDistance($lat, $lng, (float) $contact->latitude, (float) $contact->longitude));
        }

        $allowed = $passed || $mode === 'log' || ($mode !== 'enforce') || ! empty($reason);

        if (! $passed && ! empty($reason)) {
            // The util logged the violation a moment ago; attach the reason.
            $latest = DB::table('geofence_violation_logs')->where('user_id', $userId)->where('contact_id', $contactId)
                ->where('created_at', '>=', now()->subMinutes(2))->orderByDesc('id')->first(['id', 'details']);
            if ($latest) {
                DB::table('geofence_violation_logs')->where('id', $latest->id)->update([
                    'details' => trim(($latest->details ?? '').' — seller reason: '.$reason),
                ]);
            }
        }

        if ($allowed && $hasFix && $routeId) {
            $visit = $this->geo->logVisit($businessId, $userId, $routeId, $contactId, self::VISIT_TYPE[$action] ?? $action,
                $lat, $lng, $accuracy, trim(implode(' — ', array_filter([$notes, $passed ? null : 'Outside fence: '.($reason ?: 'no reason')]))) ?: null,
                null, null, null, $mock);
            $out['logged_visit'] = true;
            $out['visit_id'] = (int) $visit->id;
        }

        return $out + [
            'allowed' => $allowed,
            'exempt' => false,
            'passed' => $passed,
            'message' => $message,
            'token' => $allowed ? $this->token($businessId, $userId, $contactId) : null,
        ];
    }

    public function token(int $businessId, int $userId, int $contactId): string
    {
        return Crypt::encryptString(json_encode([
            'b' => $businessId, 'u' => $userId, 'c' => $contactId, 'exp' => now()->addMinutes(self::TOKEN_TTL)->timestamp,
        ]));
    }

    public function verify(?string $token, int $businessId, int $userId, int $contactId): bool
    {
        if (empty($token)) {
            return false;
        }

        try {
            $d = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException) {
            return false;
        }

        return is_array($d)
            && ($d['b'] ?? null) === $businessId
            && ($d['u'] ?? null) === $userId
            && ($d['c'] ?? null) === $contactId
            && ($d['exp'] ?? 0) >= now()->timestamp;
    }
}
