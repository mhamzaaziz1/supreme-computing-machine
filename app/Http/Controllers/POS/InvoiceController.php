<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\BusinessLocation;
use App\Transaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Razorpay\Api\Api;
use Stripe\Charge;
use Stripe\Stripe;

class InvoiceController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param BusinessUtil $businessUtil
     * @param TransactionUtil $transactionUtil
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(
        BusinessUtil $businessUtil,
        TransactionUtil $transactionUtil,
        ModuleUtil $moduleUtil
    ) {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Returns the content for the receipt
     *
     * @param  int  $business_id
     * @param  int  $location_id
     * @param  int  $transaction_id
     * @param  string  $printer_type = null
     * @return array
     */
    public function receiptContent(
        $business_id,
        $location_id,
        $transaction_id,
        $printer_type = null,
        $is_package_slip = false,
        $from_pos_screen = true,
        $invoice_layout_id = null,
        $is_delivery_note = false
    ) {
        $output = ['is_enabled' => false,
            'print_type' => 'browser',
            'html_content' => null,
            'printer_config' => [],
            'data' => [],
        ];

        $business_details = $this->businessUtil->getDetails($business_id);
        $location_details = BusinessLocation::find($location_id);

        if ($from_pos_screen && $location_details->print_receipt_on_invoice != 1) {
            return $output;
        }
        //Check if printing of invoice is enabled or not.
        //If enabled, get print type.
        $output['is_enabled'] = true;

        $invoice_layout_id = !empty($invoice_layout_id) ? $invoice_layout_id : $location_details->invoice_layout_id;
        $invoice_layout = $this->businessUtil->invoiceLayout($business_id, $invoice_layout_id);

        //Check if printer setting is provided.
        $receipt_printer_type = is_null($printer_type) ? $location_details->receipt_printer_type : $printer_type;

        $receipt_details = $this->transactionUtil->getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type);

        $currency_details = [
            'symbol' => $business_details->currency_symbol,
            'thousand_separator' => $business_details->thousand_separator,
            'decimal_separator' => $business_details->decimal_separator,
        ];
        $receipt_details->currency = $currency_details;

        if ($is_package_slip) {
            $output['html_content'] = view('sale_pos.receipts.packing_slip', compact('receipt_details'))->render();

            return $output;
        }

        if ($is_delivery_note) {
            $output['html_content'] = view('sale_pos.receipts.delivery_note', compact('receipt_details'))->render();

            return $output;
        }

        $output['print_title'] = $receipt_details->invoice_no;
        //If print type browser - return the content, printer - return printer config data, and invoice format config
        if ($receipt_printer_type == 'printer') {
            $output['print_type'] = 'printer';
            $output['printer_config'] = $this->businessUtil->printerConfig($business_id, $location_details->printer_id);
            $output['data'] = $receipt_details;
        } else {
            $layout = !empty($receipt_details->design) ? 'sale_pos.receipts.' . $receipt_details->design : 'sale_pos.receipts.classic';

            $output['html_content'] = view($layout, compact('receipt_details'))->render();
        }

        return $output;
    }

    /**
     * Prints invoice for sell
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice(Request $request, $transaction_id)
    {
        if (request()->ajax()) {
            try {
                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];

                $business_id = $request->session()->get('user.business_id');

                $transaction = Transaction::where('business_id', $business_id)
                    ->where('id', $transaction_id)
                    ->with(['location'])
                    ->first();

                if (empty($transaction)) {
                    return $output;
                }

                $printer_type = 'browser';
                if (!empty(request()->input('check_location')) && request()->input('check_location') == true) {
                    $printer_type = $transaction->location->receipt_printer_type;
                }

                $is_package_slip = !empty($request->input('package_slip')) ? true : false;
                $is_delivery_note = !empty($request->input('delivery_note')) ? true : false;

                $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;
                $receipt = $this->receiptContent($business_id, $transaction->location_id, $transaction_id, $printer_type, $is_package_slip, false, $invoice_layout_id, $is_delivery_note);

                if (!empty($receipt)) {
                    $output = ['success' => 1, 'receipt' => $receipt];
                }
            } catch (\Exception $e) {
                \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

                $output = ['success' => 0,
                    'msg' => trans('messages.something_went_wrong'),
                ];
            }

            return $output;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showInvoiceUrl($id)
    {
        if (!auth()->user()->can('sell.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        
        $transaction = Transaction::where('business_id', $business_id)
                                ->findorfail($id);
        $transaction_url = $this->transactionUtil->getInvoiceUrl($id, $business_id);
        $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;

        return view('sale_pos.partials.invoice_url_modal')
            ->with(compact('transaction', 'transaction_url', 'invoice_layout_id'));
    }

    /**
     * Shows invoice url.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showInvoice($token)
    {
        $transaction = Transaction::where('invoice_token', $token)->with(['business', 'location'])->first();
        
        if (!empty($transaction)) {
            $invoice_layout_id = $transaction->is_direct_sale ? $transaction->location->sale_invoice_layout_id : null;
            
            $receipt = $this->receiptContent($transaction->business_id, $transaction->location_id, $transaction->id, null, false, false, $invoice_layout_id);
            
            $payment_link = '';
            if ($transaction->payment_status != 'paid' && $transaction->status == 'final') {
                $payment_link = $this->transactionUtil->getInvoicePaymentLink($transaction->id, $transaction->business_id);
            }
            
            return view('sale_pos.partials.show_invoice')
                ->with(compact('receipt', 'payment_link'));
        } else {
            die(__("messages.something_went_wrong"));
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function invoicePayment($token)
    {
        $transaction = Transaction::where('invoice_token', $token)
                                    ->with(['business', 'contact', 'location'])
                                    ->first();
        
        if (!empty($transaction)) {
            $business = $transaction->business;
            $business_details = $this->businessUtil->getDetails($business->id);
            $pos_settings = empty($business->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business->pos_settings, true);
            
            return view('sale_pos.partials.invoice_payment')
                ->with(compact('transaction', 'business_details', 'pos_settings'));
        } else {
            die(__("messages.something_went_wrong"));
        }
    }

    /**
     * Handles payment for invoice by razorpay.
     *
     * @param  int  $transaction
     * @param  int  $total_payable
     * @param  array  $request
     * @return \Illuminate\Http\Response
     */
    public function pay_razorpay($transaction, $total_payable, $request)
    {
        $razorpay_payment_id = $request->razorpay_payment_id;
        $razorpay_api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));
        
        $payment = $razorpay_api->payment->fetch($razorpay_payment_id)->capture(['amount' => $total_payable * 100]); // Razorpay amount is in paisa
        
        if ($payment['status'] == 'captured') {
            return $payment['id'];
        }
        
        return null;
    }

    /**
     * Handles payment for invoice by stripe.
     *
     * @param  int  $transaction
     * @param  int  $total_payable
     * @param  array  $request
     * @return \Illuminate\Http\Response
     */
    public function pay_stripe($transaction, $total_payable, $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));
        
        $metadata = ['transaction_id' => $transaction->id];
        
        $charge = Charge::create([
            'amount' => $total_payable * 100, // Amount in cents
            'currency' => 'usd',
            'source' => $request->stripeToken,
            'metadata' => $metadata,
        ]);
        
        return $charge['id'];
    }

    /**
     * Handles payment for invoice.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment($id, Request $request)
    {
        try {
            DB::beginTransaction();
            
            $transaction = Transaction::findOrFail($id);
            
            $transaction_before = $transaction->replicate();
            
            $payment_type = $request->gateway;
            $total_payable = $this->transactionUtil->num_uf($request->total_payable);
            
            $payment_id = null;
            if ($payment_type == 'razorpay') {
                $payment_id = $this->pay_razorpay($transaction, $total_payable, $request);
            } elseif ($payment_type == 'stripe') {
                $payment_id = $this->pay_stripe($transaction, $total_payable, $request);
            }
            
            if (!empty($payment_id)) {
                $inputs['method'] = 'cash';
                $inputs['amount'] = $total_payable;
                $inputs['paid_on'] = \Carbon::now()->toDateTimeString();
                $inputs['transaction_id'] = $transaction->id;
                $inputs['gateway'] = $payment_type;
                $inputs['payment_id'] = $payment_id;
                
                $prefix_type = 'sell_payment';
                if ($transaction->type == 'purchase') {
                    $prefix_type = 'purchase_payment';
                }
                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                //Generate reference number
                $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
                
                $payment_status = $this->transactionUtil->createOrUpdatePaymentLines($transaction, $inputs);
                $transaction->payment_status = $payment_status;
                $transaction->save();
                
                $payment_link = $this->transactionUtil->getInvoicePaymentLink($transaction->id, $transaction->business_id);
                
                $output = ['success' => 1, 'msg' => __('purchase.payment_added_success'), 'payment_link' => $payment_link];
            } else {
                $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0, 'msg' => __('messages.something_went_wrong')];
        }
        
        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * download PDF for invoice
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf($id)
    {
        if (!(auth()->user()->can('sell.view') || 
            auth()->user()->can('direct_sell.view') || 
            auth()->user()->can('view_own_sell_only'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        
        $receipt_contents = $this->transactionUtil->getPdfContentsForGivenTransaction($business_id, $id);
        $receipt_details = $receipt_contents['receipt_details'];
        $location_details = $receipt_contents['location_details'];
        $business_details = $receipt_contents['business_details'];
        
        $output_file_name = 'Invoice_' . $receipt_details->invoice_no . '.pdf';
        
        $layout = 'sale_pos.receipts.download_pdf';
        if (!empty($receipt_details->is_export)) {
            $layout = 'sale_pos.receipts.download_export_pdf';
        }
        
        $html = view($layout, compact('receipt_details', 'location_details', 'business_details'))->render();
        
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
            'mode' => 'utf-8', 
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,
            'margin_top' => 8,
            'margin_bottom' => 8,
            'format' => 'A4'
        ]);
        
        $mpdf->useSubstitutions = true;
        $mpdf->SetWatermarkText($receipt_details->business_name, 0.1);
        $mpdf->showWatermarkText = true;
        $mpdf->SetTitle('INVOICE-' . $receipt_details->invoice_no);
        $mpdf->WriteHTML($html);
        $mpdf->Output($output_file_name, 'I');
    }

    /**
     * download PDF for quotation
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadQuotationPdf($id)
    {
        if (!(auth()->user()->can('sell.view') || 
            auth()->user()->can('direct_sell.view') || 
            auth()->user()->can('view_own_sell_only'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        
        $receipt_contents = $this->transactionUtil->getPdfContentsForGivenTransaction($business_id, $id);
        $receipt_details = $receipt_contents['receipt_details'];
        $location_details = $receipt_contents['location_details'];
        $business_details = $receipt_contents['business_details'];
        
        $output_file_name = 'Quotation_' . $receipt_details->invoice_no . '.pdf';
        
        $html = view('sale_pos.receipts.download_quotation_pdf', compact('receipt_details', 'location_details', 'business_details'))->render();
        
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
            'mode' => 'utf-8', 
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,
            'margin_top' => 8,
            'margin_bottom' => 8,
            'format' => 'A4'
        ]);
        
        $mpdf->useSubstitutions = true;
        $mpdf->SetWatermarkText($receipt_details->business_name, 0.1);
        $mpdf->showWatermarkText = true;
        $mpdf->SetTitle('QUOTATION-' . $receipt_details->invoice_no);
        $mpdf->WriteHTML($html);
        $mpdf->Output($output_file_name, 'I');
    }

    /**
     * download PDF for packing list
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPackingListPdf($id)
    {
        if (!(auth()->user()->can('sell.view') || 
            auth()->user()->can('direct_sell.view') || 
            auth()->user()->can('view_own_sell_only'))) {
            abort(403, 'Unauthorized action.');
        }
        
        $business_id = request()->session()->get('user.business_id');
        
        $receipt_contents = $this->transactionUtil->getPdfContentsForGivenTransaction($business_id, $id);
        $receipt_details = $receipt_contents['receipt_details'];
        $location_details = $receipt_contents['location_details'];
        $business_details = $receipt_contents['business_details'];
        
        $output_file_name = 'PackingList_' . $receipt_details->invoice_no . '.pdf';
        
        $html = view('sale_pos.receipts.download_packing_list_pdf', compact('receipt_details', 'location_details', 'business_details'))->render();
        
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
            'mode' => 'utf-8', 
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'autoVietnamese' => true,
            'autoArabic' => true,
            'margin_top' => 8,
            'margin_bottom' => 8,
            'format' => 'A4'
        ]);
        
        $mpdf->useSubstitutions = true;
        $mpdf->SetWatermarkText($receipt_details->business_name, 0.1);
        $mpdf->showWatermarkText = true;
        $mpdf->SetTitle('PACKING-LIST-' . $receipt_details->invoice_no);
        $mpdf->WriteHTML($html);
        $mpdf->Output($output_file_name, 'I');
    }
}