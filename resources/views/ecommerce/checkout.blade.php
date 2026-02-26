@extends('layouts.ecommerce')

@section('title', 'Checkout')

@section('content')
    <div class="container mt-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.cart') }}">Shopping Cart</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout</li>
            </ol>
        </nav>
        
        <h1 class="mb-4">Checkout</h1>
        
        @if(count($cart_items) > 0)
            <form action="{{ route('ecommerce.place_order') }}" method="POST" id="checkout-form" class="checkout-form">
                @csrf
                
                <div class="row">
                    <!-- Checkout Steps -->
                    <div class="col-lg-8">
                        <!-- Checkout Progress -->
                        <div class="card mb-4">
                            <div class="card-body p-0">
                                <div class="d-flex">
                                    <div class="flex-grow-1 text-center py-3 border-end checkout-step active" id="step-shipping">
                                        <span class="step-number">1</span>
                                        <span class="step-title">Shipping</span>
                                    </div>
                                    <div class="flex-grow-1 text-center py-3 border-end checkout-step" id="step-payment">
                                        <span class="step-number">2</span>
                                        <span class="step-title">Payment</span>
                                    </div>
                                    <div class="flex-grow-1 text-center py-3 checkout-step" id="step-review">
                                        <span class="step-number">3</span>
                                        <span class="step-title">Review</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Shipping Information -->
                        <div class="card mb-4 checkout-section" id="shipping-section">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Shipping Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="city" class="form-label">City</label>
                                        <input type="text" class="form-control" id="city" name="city" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="state" class="form-label">State/Province</label>
                                        <input type="text" class="form-control" id="state" name="state" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="zip" class="form-label">Zip/Postal Code</label>
                                        <input type="text" class="form-control" id="zip" name="zip" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <select class="form-select" id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        <option value="US">United States</option>
                                        <option value="CA">Canada</option>
                                        <option value="UK">United Kingdom</option>
                                        <option value="AU">Australia</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Shipping Method</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="shipping_standard" value="standard" checked>
                                        <label class="form-check-label" for="shipping_standard">
                                            Standard Shipping (3-5 business days) - {{ $total >= 50 ? 'Free' : '$5.00' }}
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="shipping_method" id="shipping_express" value="express">
                                        <label class="form-check-label" for="shipping_express">
                                            Express Shipping (1-2 business days) - $15.00
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('ecommerce.cart') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Cart
                                    </a>
                                    <button type="button" class="btn btn-primary" id="shipping-next-btn">
                                        Continue to Payment <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="card mb-4 checkout-section d-none" id="payment-section">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_credit_card" value="credit_card" checked>
                                        <label class="form-check-label" for="payment_credit_card">
                                            Credit Card
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_paypal" value="paypal">
                                        <label class="form-check-label" for="payment_paypal">
                                            PayPal
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_bank_transfer" value="bank_transfer">
                                        <label class="form-check-label" for="payment_bank_transfer">
                                            Bank Transfer
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Credit Card Details (shown only when credit card is selected) -->
                                <div id="credit-card-details">
                                    <div class="mb-3">
                                        <label for="card_name" class="form-label">Name on Card</label>
                                        <input type="text" class="form-control" id="card_name" name="card_name">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="XXXX XXXX XXXX XXXX">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="card_expiry" class="form-label">Expiration Date</label>
                                            <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="card_cvv" class="form-label">CVV</label>
                                            <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="XXX">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- PayPal Instructions (hidden by default) -->
                                <div id="paypal-instructions" class="d-none">
                                    <div class="alert alert-info">
                                        <p>You will be redirected to PayPal to complete your payment after reviewing your order.</p>
                                    </div>
                                </div>
                                
                                <!-- Bank Transfer Instructions (hidden by default) -->
                                <div id="bank-transfer-instructions" class="d-none">
                                    <div class="alert alert-info">
                                        <p>Please use the following information to complete your bank transfer:</p>
                                        <p><strong>Bank Name:</strong> Example Bank</p>
                                        <p><strong>Account Name:</strong> {{ $business->name }}</p>
                                        <p><strong>Account Number:</strong> XXXXXXXXXXXX</p>
                                        <p><strong>Routing Number:</strong> XXXXXXXXX</p>
                                        <p>Your order will be processed once payment is received.</p>
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="billing_same_as_shipping" name="billing_same_as_shipping" checked>
                                    <label class="form-check-label" for="billing_same_as_shipping">Billing address same as shipping address</label>
                                </div>
                                
                                <!-- Billing Address (hidden by default) -->
                                <div id="billing-address-section" class="d-none">
                                    <h5 class="mb-3">Billing Address</h5>
                                    
                                    <div class="mb-3">
                                        <label for="billing_address" class="form-label">Address</label>
                                        <input type="text" class="form-control" id="billing_address" name="billing_address">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="billing_city" name="billing_city">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_state" class="form-label">State/Province</label>
                                            <input type="text" class="form-control" id="billing_state" name="billing_state">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="billing_zip" class="form-label">Zip/Postal Code</label>
                                            <input type="text" class="form-control" id="billing_zip" name="billing_zip">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="billing_country" class="form-label">Country</label>
                                        <select class="form-select" id="billing_country" name="billing_country">
                                            <option value="">Select Country</option>
                                            <option value="US">United States</option>
                                            <option value="CA">Canada</option>
                                            <option value="UK">United Kingdom</option>
                                            <option value="AU">Australia</option>
                                            <!-- Add more countries as needed -->
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" id="payment-back-btn">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Shipping
                                    </button>
                                    <button type="button" class="btn btn-primary" id="payment-next-btn">
                                        Review Order <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Order Review -->
                        <div class="card mb-4 checkout-section d-none" id="review-section">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Review Your Order</h5>
                            </div>
                            <div class="card-body">
                                <h5 class="mb-3">Shipping Information</h5>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Name:</strong> <span id="review-name"></span></p>
                                        <p class="mb-1"><strong>Email:</strong> <span id="review-email"></span></p>
                                        <p class="mb-1"><strong>Phone:</strong> <span id="review-phone"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Address:</strong> <span id="review-address"></span></p>
                                        <p class="mb-1"><strong>City:</strong> <span id="review-city"></span>, <strong>State:</strong> <span id="review-state"></span>, <strong>Zip:</strong> <span id="review-zip"></span></p>
                                        <p class="mb-1"><strong>Country:</strong> <span id="review-country"></span></p>
                                    </div>
                                </div>
                                
                                <h5 class="mb-3">Payment Information</h5>
                                <p class="mb-1"><strong>Payment Method:</strong> <span id="review-payment-method"></span></p>
                                <div id="review-credit-card-info" class="mb-4">
                                    <p class="mb-1"><strong>Card Number:</strong> **** **** **** <span id="review-card-last4"></span></p>
                                </div>
                                
                                <h5 class="mb-3">Order Items</h5>
                                <div class="table-responsive mb-4">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Quantity</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cart_items as $variation_id => $item)
                                                <tr>
                                                    <td>
                                                        {{ $item['variation']->product->name }}
                                                        @if($item['variation']->name != 'DUMMY')
                                                            <small class="text-muted d-block">{{ $item['variation']->name }}</small>
                                                        @endif
                                                    </td>
                                                    <td>${{ number_format($item['variation']->sell_price_inc_tax, 2) }}</td>
                                                    <td>{{ $item['quantity'] }}</td>
                                                    <td class="text-end">${{ number_format($item['subtotal'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms_accepted" name="terms_accepted" required>
                                    <label class="form-check-label" for="terms_accepted">I agree to the <a href="{{ route('ecommerce.terms') }}" target="_blank">Terms and Conditions</a> and <a href="{{ route('ecommerce.privacy') }}" target="_blank">Privacy Policy</a></label>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" id="review-back-btn">
                                        <i class="fas fa-arrow-left me-2"></i> Back to Payment
                                    </button>
                                    <button type="submit" class="btn btn-success" id="place-order-btn">
                                        <i class="fas fa-check me-2"></i> Place Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card cart-summary">
                            <div class="card-body">
                                <h5 class="cart-summary-title">Order Summary</h5>
                                
                                <div class="cart-summary-item">
                                    <span>Subtotal</span>
                                    <span>${{ number_format($total, 2) }}</span>
                                </div>
                                
                                <div class="cart-summary-item">
                                    <span>Shipping</span>
                                    <span id="shipping-cost">{{ $total >= 50 ? 'Free' : '$5.00' }}</span>
                                </div>
                                
                                @php
                                    $shipping_cost = $total >= 50 ? 0 : 5;
                                    $tax_rate = 0.07; // 7% tax rate
                                    $tax_amount = $total * $tax_rate;
                                    $grand_total = $total + $shipping_cost + $tax_amount;
                                @endphp
                                
                                <div class="cart-summary-item">
                                    <span>Tax (7%)</span>
                                    <span>${{ number_format($tax_amount, 2) }}</span>
                                </div>
                                
                                <div class="cart-summary-total">
                                    <span>Total</span>
                                    <span id="grand-total">${{ number_format($grand_total, 2) }}</span>
                                </div>
                                
                                <!-- Order Items -->
                                <div class="mt-4">
                                    <h6 class="mb-3">{{ count($cart_items) }} item(s) in cart</h6>
                                    
                                    @foreach($cart_items as $variation_id => $item)
                                        <div class="d-flex mb-2">
                                            @php
                                                $image_url = 'https://via.placeholder.com/50x50?text=Product+Image';
                                                if($item['variation']->media->isNotEmpty()) {
                                                    $image_url = $item['variation']->media->first()->display_url;
                                                }
                                            @endphp
                                            
                                            <img src="{{ $image_url }}" class="me-2" width="50" height="50" alt="{{ $item['variation']->product->name }}">
                                            <div>
                                                <p class="mb-0">{{ $item['variation']->product->name }}</p>
                                                <p class="text-muted small mb-0">
                                                    @if($item['variation']->name != 'DUMMY')
                                                        {{ $item['variation']->name }} |
                                                    @endif
                                                    Qty: {{ $item['quantity'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Payment Methods -->
                                <div class="mt-4 text-center">
                                    <p class="text-muted small mb-2">We Accept</p>
                                    <div>
                                        <i class="fab fa-cc-visa fa-2x me-2"></i>
                                        <i class="fab fa-cc-mastercard fa-2x me-2"></i>
                                        <i class="fab fa-cc-amex fa-2x me-2"></i>
                                        <i class="fab fa-cc-paypal fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <!-- Empty Cart -->
            <div class="card text-center py-5">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
                    <h3>Your cart is empty</h3>
                    <p class="mb-4">You need to add items to your cart before proceeding to checkout.</p>
                    <a href="{{ route('ecommerce.products') }}" class="btn btn-primary">Start Shopping</a>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Step navigation
        const shippingSection = document.getElementById('shipping-section');
        const paymentSection = document.getElementById('payment-section');
        const reviewSection = document.getElementById('review-section');
        
        const stepShipping = document.getElementById('step-shipping');
        const stepPayment = document.getElementById('step-payment');
        const stepReview = document.getElementById('step-review');
        
        const shippingNextBtn = document.getElementById('shipping-next-btn');
        const paymentBackBtn = document.getElementById('payment-back-btn');
        const paymentNextBtn = document.getElementById('payment-next-btn');
        const reviewBackBtn = document.getElementById('review-back-btn');
        
        // Payment method selection
        const paymentCreditCard = document.getElementById('payment_credit_card');
        const paymentPaypal = document.getElementById('payment_paypal');
        const paymentBankTransfer = document.getElementById('payment_bank_transfer');
        
        const creditCardDetails = document.getElementById('credit-card-details');
        const paypalInstructions = document.getElementById('paypal-instructions');
        const bankTransferInstructions = document.getElementById('bank-transfer-instructions');
        
        // Billing address toggle
        const billingSameAsShipping = document.getElementById('billing_same_as_shipping');
        const billingAddressSection = document.getElementById('billing-address-section');
        
        // Shipping method selection
        const shippingStandard = document.getElementById('shipping_standard');
        const shippingExpress = document.getElementById('shipping_express');
        const shippingCost = document.getElementById('shipping-cost');
        const grandTotal = document.getElementById('grand-total');
        
        // Initial subtotal and tax
        const subtotal = {{ $total }};
        const taxRate = 0.07;
        const taxAmount = subtotal * taxRate;
        
        // Update shipping cost and grand total based on shipping method
        function updateTotals() {
            let shipping = 0;
            
            if (shippingStandard.checked) {
                shipping = subtotal >= 50 ? 0 : 5;
                shippingCost.textContent = subtotal >= 50 ? 'Free' : '$5.00';
            } else if (shippingExpress.checked) {
                shipping = 15;
                shippingCost.textContent = '$15.00';
            }
            
            const total = subtotal + shipping + taxAmount;
            grandTotal.textContent = '$' + total.toFixed(2);
        }
        
        // Navigate to payment step
        shippingNextBtn.addEventListener('click', function() {
            // Validate shipping form
            const shippingForm = document.getElementById('checkout-form');
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const address = document.getElementById('address');
            const city = document.getElementById('city');
            const state = document.getElementById('state');
            const zip = document.getElementById('zip');
            const country = document.getElementById('country');
            
            if (!name.value || !email.value || !phone.value || !address.value || !city.value || !state.value || !zip.value || !country.value) {
                alert('Please fill in all required shipping information fields.');
                return;
            }
            
            // Hide shipping section, show payment section
            shippingSection.classList.add('d-none');
            paymentSection.classList.remove('d-none');
            
            // Update step indicators
            stepShipping.classList.remove('active');
            stepPayment.classList.add('active');
        });
        
        // Navigate back to shipping step
        paymentBackBtn.addEventListener('click', function() {
            // Hide payment section, show shipping section
            paymentSection.classList.add('d-none');
            shippingSection.classList.remove('d-none');
            
            // Update step indicators
            stepPayment.classList.remove('active');
            stepShipping.classList.add('active');
        });
        
        // Navigate to review step
        paymentNextBtn.addEventListener('click', function() {
            // Validate payment form if credit card is selected
            if (paymentCreditCard.checked) {
                const cardName = document.getElementById('card_name');
                const cardNumber = document.getElementById('card_number');
                const cardExpiry = document.getElementById('card_expiry');
                const cardCvv = document.getElementById('card_cvv');
                
                if (!cardName.value || !cardNumber.value || !cardExpiry.value || !cardCvv.value) {
                    alert('Please fill in all credit card information fields.');
                    return;
                }
            }
            
            // Validate billing address if different from shipping
            if (!billingSameAsShipping.checked) {
                const billingAddress = document.getElementById('billing_address');
                const billingCity = document.getElementById('billing_city');
                const billingState = document.getElementById('billing_state');
                const billingZip = document.getElementById('billing_zip');
                const billingCountry = document.getElementById('billing_country');
                
                if (!billingAddress.value || !billingCity.value || !billingState.value || !billingZip.value || !billingCountry.value) {
                    alert('Please fill in all billing address fields.');
                    return;
                }
            }
            
            // Hide payment section, show review section
            paymentSection.classList.add('d-none');
            reviewSection.classList.remove('d-none');
            
            // Update step indicators
            stepPayment.classList.remove('active');
            stepReview.classList.add('active');
            
            // Populate review section with form data
            document.getElementById('review-name').textContent = document.getElementById('name').value;
            document.getElementById('review-email').textContent = document.getElementById('email').value;
            document.getElementById('review-phone').textContent = document.getElementById('phone').value;
            document.getElementById('review-address').textContent = document.getElementById('address').value;
            document.getElementById('review-city').textContent = document.getElementById('city').value;
            document.getElementById('review-state').textContent = document.getElementById('state').value;
            document.getElementById('review-zip').textContent = document.getElementById('zip').value;
            document.getElementById('review-country').textContent = document.getElementById('country').options[document.getElementById('country').selectedIndex].text;
            
            // Payment method
            let paymentMethod = '';
            if (paymentCreditCard.checked) {
                paymentMethod = 'Credit Card';
                document.getElementById('review-credit-card-info').classList.remove('d-none');
                document.getElementById('review-card-last4').textContent = document.getElementById('card_number').value.slice(-4);
            } else if (paymentPaypal.checked) {
                paymentMethod = 'PayPal';
                document.getElementById('review-credit-card-info').classList.add('d-none');
            } else if (paymentBankTransfer.checked) {
                paymentMethod = 'Bank Transfer';
                document.getElementById('review-credit-card-info').classList.add('d-none');
            }
            document.getElementById('review-payment-method').textContent = paymentMethod;
        });
        
        // Navigate back to payment step
        reviewBackBtn.addEventListener('click', function() {
            // Hide review section, show payment section
            reviewSection.classList.add('d-none');
            paymentSection.classList.remove('d-none');
            
            // Update step indicators
            stepReview.classList.remove('active');
            stepPayment.classList.add('active');
        });
        
        // Toggle payment method details
        paymentCreditCard.addEventListener('change', function() {
            if (this.checked) {
                creditCardDetails.classList.remove('d-none');
                paypalInstructions.classList.add('d-none');
                bankTransferInstructions.classList.add('d-none');
            }
        });
        
        paymentPaypal.addEventListener('change', function() {
            if (this.checked) {
                creditCardDetails.classList.add('d-none');
                paypalInstructions.classList.remove('d-none');
                bankTransferInstructions.classList.add('d-none');
            }
        });
        
        paymentBankTransfer.addEventListener('change', function() {
            if (this.checked) {
                creditCardDetails.classList.add('d-none');
                paypalInstructions.classList.add('d-none');
                bankTransferInstructions.classList.remove('d-none');
            }
        });
        
        // Toggle billing address section
        billingSameAsShipping.addEventListener('change', function() {
            if (this.checked) {
                billingAddressSection.classList.add('d-none');
            } else {
                billingAddressSection.classList.remove('d-none');
            }
        });
        
        // Update totals when shipping method changes
        shippingStandard.addEventListener('change', updateTotals);
        shippingExpress.addEventListener('change', updateTotals);
        
        // Initialize
        updateTotals();
    });
</script>
@endsection