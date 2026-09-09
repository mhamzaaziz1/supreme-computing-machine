@extends('layouts.ecommerce')

@section('title', 'Terms & Conditions')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="mb-4 fw-bold border-bottom pb-3">Terms & Conditions</h1>
                    <p class="text-muted">Last updated: {{ date('M d, Y') }}</p>

                    <h4 class="mt-4 fw-bold">1. Agreement to Terms</h4>
                    <p>These Terms and Conditions constitute a legally binding agreement made between you, whether personally or on behalf of an entity ("you") and {{ config('app.name', 'Our Store') }}, concerning your access to and use of the website.</p>

                    <h4 class="mt-4 fw-bold">2. Products and Pricing</h4>
                    <p>All products are subject to availability. We reserve the right to discontinue any products at any time for any reason. Prices for all products are subject to change.</p>

                    <h4 class="mt-4 fw-bold">3. Purchases and Payment</h4>
                    <p>You agree to provide current, complete, and accurate purchase and account information for all purchases made via the site. You agree to promptly update account and payment information, including email address, payment method, and payment card expiration date.</p>

                    <h4 class="mt-4 fw-bold">4. Return Policy</h4>
                    <p>Please review our Return Policy posted on the Site prior to making any purchases. We maintain a 30-day standard return window for undamaged items in original packaging.</p>
                    
                    <h4 class="mt-4 fw-bold">5. License</h4>
                    <p>Unless otherwise stated, {{ config('app.name', 'Our Store') }} and/or its licensors own the intellectual property rights for all material on the site. All intellectual property rights are reserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
