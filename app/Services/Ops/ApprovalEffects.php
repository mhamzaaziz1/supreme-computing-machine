<?php

namespace App\Services\Ops;

/**
 * What happens when a manager decides a request, by request type.
 *
 * A credit override needs nothing here: its token is redeemed by the sale.
 * Van variances and field returns post their stock and ledger entries only
 * once approved, so the seller's claim never moves stock on its own.
 */
class ApprovalEffects
{
    public function apply(object $approval): void
    {
        if ($approval->status === 'rejected') {
            match ($approval->type) {
                'van_variance' => app(VanStock::class)->rejectVariance($approval),
                'field_return' => app(FieldReturns::class)->rejected($approval),
                default => null,
            };

            return;
        }

        if ($approval->status !== 'approved') {
            return;
        }

        match ($approval->type) {
            'van_variance' => app(VanStock::class)->postApprovedVariance($approval),
            'field_return' => app(FieldReturns::class)->postApproved($approval),
            default => null,
        };
    }
}
