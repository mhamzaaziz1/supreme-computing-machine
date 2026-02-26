<?php

namespace App\Actions\Sell;

use App\Transaction;
use App\Utils\TransactionUtil;
use Illuminate\Support\Facades\DB;
use App\Utils\ProductUtil;

class CreateSellTransaction
{
    protected $transactionUtil;
    protected $productUtil;

    protected $businessUtil;
    protected $moduleUtil;
    protected $cashRegisterUtil;
    protected $notificationUtil;

    public function __construct(
        TransactionUtil $transactionUtil,
        ProductUtil $productUtil,
        \App\Utils\BusinessUtil $businessUtil,
        \App\Utils\ModuleUtil $moduleUtil,
        \App\Utils\CashRegisterUtil $cashRegisterUtil,
        \App\Utils\NotificationUtil $notificationUtil
    ) {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->notificationUtil = $notificationUtil;
    }

    public function execute(array $input, $request)
    {
        $business_id = $input['business_id'];
        $user_id = $input['created_by'];

        $discount = [
            'discount_type' => $input['discount_type'],
            'discount_amount' => $input['discount_amount'],
        ];

        $invoice_total = $this->productUtil->calculateInvoiceTotal($input['products'], $input['tax_rate_id'], $discount);

        DB::beginTransaction();

        $transaction = $this->transactionUtil->createSellTransaction($business_id, $input, $invoice_total, $user_id);

        //Upload Shipping documents
        \App\Media::uploadMedia($business_id, $transaction, $request, 'shipping_documents', false, 'shipping_document');

        $this->transactionUtil->createOrUpdateSellLines($transaction, $input['products'], $input['location_id']);

        // Handle Change Return
        if (isset($input['payment']['change_return'])) {
            $change_return = $input['payment']['change_return'];
            unset($input['payment']['change_return']);
        } else {
            $change_return = ['amount' => 0, 'is_return' => 0, 'method' => 'cash', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '', 'transaction_no' => '', 'note' => ''];
        }
        
        $change_return['amount'] = $input['change_return'] ?? 0;
        $change_return['is_return'] = 1;

        $input['payment'][] = $change_return;

        $is_credit_sale = isset($input['is_credit_sale']) && $input['is_credit_sale'] == 1 ? true : false;

        if (!$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $input['payment']);
        }

        //Check for final and do some processing.
        $whatsapp_link = null;
        if ($input['status'] == 'final') {
             // ... processing logic (omitted for brevity in this initial step, to be added iteratively)
             // For now, assume simplified flow or add TODO
             
            //update product stock
            foreach ($input['products'] as $product) {
                $decrease_qty = $this->productUtil->num_uf($product['quantity']);
                if (!empty($product['base_unit_multiplier'])) {
                    $decrease_qty = $decrease_qty * $product['base_unit_multiplier'];
                }

                if ($product['enable_stock']) {
                    $this->productUtil->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $decrease_qty
                    );
                }

                if ($product['product_type'] == 'combo') {
                    $this->productUtil->decreaseProductQuantityCombo($product['combo'], $input['location_id']);
                }
            }

            $is_direct_sale = !empty($input['is_direct_sale']) ? true : false;

            //Add payments to Cash Register
            if (!$is_direct_sale && !$transaction->is_suspend && !empty($input['payment']) && !$is_credit_sale) {
                $this->cashRegisterUtil->addSellPayments($transaction, $input['payment']);
            }

            //Update payment status
            $payment_status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
            $transaction->payment_status = $payment_status;

            //Auto send notification
            $whatsapp_link = $this->notificationUtil->autoSendNotification($business_id, 'new_sale', $transaction, $transaction->contact);
        }
        
        // ... (Other status updates and media uploads)
         if (!empty($transaction->sales_order_ids)) {
            $this->transactionUtil->updateSalesOrderStatus($transaction->sales_order_ids);
        }

        \App\Media::uploadMedia($business_id, $transaction, $request, 'documents');
        $this->transactionUtil->activityLog($transaction, 'added');
        
        DB::commit();

        return [
            'transaction' => $transaction,
            'whatsapp_link' => $whatsapp_link
        ];
    }
}
