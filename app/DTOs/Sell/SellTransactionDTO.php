<?php

namespace App\DTOs\Sell;

class SellTransactionDTO
{
    public function __construct(
        public array $products,
        public int $tax_rate_id,
        public string $discount_type,
        public float $discount_amount,
        public int $location_id,
        public int $contact_id,
        public string $status,
        public string $sub_types,
        public ?string $transaction_date = null,
        public ?int $commission_agent = null,
        // Add other fields as necessary
    ) {}

    public static function fromRequest(array $input)
    {
        return new self(
            products: $input['products'],
            tax_rate_id: $input['tax_rate_id'],
            discount_type: $input['discount_type'],
            discount_amount: $input['discount_amount'],
            location_id: $input['location_id'],
            contact_id: $input['contact_id'],
            status: $input['status'],
            sub_types: $input['sub_type'] ?? null,
            transaction_date: $input['transaction_date'] ?? null,
            commission_agent: $input['commission_agent'] ?? null,
        );
    }
}
