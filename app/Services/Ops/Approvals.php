<?php

namespace App\Services\Ops;

use App\User;
use App\Utils\Util;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * The single queue for exceptions a manager has to decide.
 *
 * A decision is either taken on the spot (a manager types their staff PIN on
 * the seller's screen) or asynchronously (the request lands in the inbox and
 * the seller's screen polls for the answer). Both end in the same approved
 * row, and an approved credit override is redeemed with a short-lived token
 * so it cannot be reused for a second, larger sale.
 */
class Approvals
{
    /** Minutes an approved override stays redeemable. */
    public const TOKEN_TTL = 30;

    public function __construct(private Util $util) {}

    public static function canApprove(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return app(Util::class)->is_admin($user, $user->business_id) || $user->can('ops.approve');
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    public function request(int $businessId, string $type, string $summary, array $attrs = []): int
    {
        return DB::table('ops_approvals')->insertGetId([
            'business_id' => $businessId,
            'type' => $type,
            'status' => 'pending',
            'contact_id' => $attrs['contact_id'] ?? null,
            'subject_type' => $attrs['subject_type'] ?? null,
            'subject_id' => $attrs['subject_id'] ?? null,
            'amount' => $attrs['amount'] ?? null,
            'summary' => mb_substr($summary, 0, 250),
            'payload' => isset($attrs['payload']) ? json_encode($attrs['payload']) : null,
            'requested_by' => $attrs['requested_by'] ?? auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Find a manager by the staff PIN they typed on someone else's screen.
     *
     * Throttled per requesting user: a four-digit PIN is guessable otherwise.
     */
    public function approverByPin(int $businessId, string $pin): ?User
    {
        $key = 'ops-pin:'.auth()->id();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            abort(429, 'Too many PIN attempts. Wait a minute and try again.');
        }

        $pin = trim($pin);
        $candidates = $pin === '' ? collect() : User::where('business_id', $businessId)
            ->whereNotNull('service_staff_pin')
            ->where('service_staff_pin', $pin)
            ->get();

        $approver = $candidates->first(fn ($u) => self::canApprove($u));

        if (! $approver) {
            RateLimiter::hit($key, 60);

            return null;
        }

        RateLimiter::clear($key);

        return $approver;
    }

    public function decide(int $businessId, int $id, bool $approve, int $userId, ?string $note = null): object
    {
        $row = $this->find($businessId, $id);

        if ($row->status !== 'pending') {
            abort(409, 'This request has already been decided.');
        }

        DB::table('ops_approvals')->where('id', $id)->update([
            'status' => $approve ? 'approved' : 'rejected',
            'decided_by' => $userId,
            'decided_at' => now(),
            'decision_note' => $note ? mb_substr($note, 0, 250) : null,
            'updated_at' => now(),
        ]);

        return $this->find($businessId, $id);
    }

    public function find(int $businessId, int $id): object
    {
        $row = DB::table('ops_approvals')->where('business_id', $businessId)->where('id', $id)->first();

        if (! $row) {
            abort(404);
        }

        return $row;
    }

    /**
     * An opaque, expiring handle on an approved override.
     */
    public function token(object $approval): string
    {
        return Crypt::encryptString(json_encode([
            'id' => (int) $approval->id,
            'b' => (int) $approval->business_id,
            'c' => $approval->contact_id ? (int) $approval->contact_id : null,
            'exp' => now()->addMinutes(self::TOKEN_TTL)->timestamp,
        ]));
    }

    /**
     * The approval behind a token, if it is valid for this business and
     * contact, approved, unexpired and not yet redeemed.
     */
    public function redeemable(?string $token, int $businessId, ?int $contactId, string $type): ?object
    {
        if (empty($token)) {
            return null;
        }

        try {
            $data = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException) {
            return null;
        }

        if (! is_array($data) || ($data['b'] ?? null) !== $businessId || ($data['exp'] ?? 0) < now()->timestamp) {
            return null;
        }

        if ($contactId !== null && ($data['c'] ?? null) !== $contactId) {
            return null;
        }

        $row = DB::table('ops_approvals')->where('id', $data['id'] ?? 0)->first();

        if (! $row || $row->type !== $type || $row->status !== 'approved' || $row->consumed_at !== null) {
            return null;
        }

        return $row;
    }

    public function consume(int $id, string $ref): void
    {
        DB::table('ops_approvals')->where('id', $id)->whereNull('consumed_at')->update([
            'consumed_at' => now(),
            'consumed_ref' => mb_substr($ref, 0, 190),
            'updated_at' => now(),
        ]);
    }

    public function pendingCount(int $businessId): int
    {
        return DB::table('ops_approvals')->where('business_id', $businessId)->where('status', 'pending')->count();
    }
}
