@extends('layouts.ecommerce')

@section('title', 'Privacy Policy')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <h1 class="mb-4 fw-bold border-bottom pb-3">Privacy Policy</h1>
                    <p class="text-muted">Last updated: {{ date('M d, Y') }}</p>

                    <h4 class="mt-4 fw-bold">1. Introduction</h4>
                    <p>Welcome to {{ config('app.name', 'Our Store') }} ("we," "our," or "us"). We are committed to protecting your personal information and your right to privacy.</p>

                    <h4 class="mt-4 fw-bold">2. Information We Collect</h4>
                    <p>We collect personal information that you voluntarily provide to us when you register on the website, express an interest in obtaining information about us or our products, or otherwise contact us.</p>
                    <ul>
                        <li>Names, Email addresses, Phone numbers</li>
                        <li>Billing addresses, Shipping addresses</li>
                        <li>Payment and order data (processed securely by our payment providers)</li>
                    </ul>

                    <h4 class="mt-4 fw-bold">3. How We Use Your Information</h4>
                    <p>We use personal information collected via our website for a variety of business purposes described below.</p>
                    <ul>
                        <li>To facilitate account creation and logon process</li>
                        <li>To fulfill and manage your orders</li>
                        <li>To send administrative and marketing communications</li>
                    </ul>

                    <h4 class="mt-4 fw-bold">4. How We Protect Your Information</h4>
                    <p>We have implemented appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, despite our safeguards, no internet transmission can be guaranteed to be 100% secure.</p>
                    
                    <h4 class="mt-4 fw-bold">5. Contact Us</h4>
                    <p>If you have questions or comments about this notice, you may email us at support@{{ strtolower(config('app.name', 'example.com')) }}.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
