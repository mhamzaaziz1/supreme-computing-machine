@extends('layouts.ecommerce')

@section('title', 'Help & FAQ')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold">Help & FAQ</h1>
            <p class="lead text-muted">Find answers to common questions about our store.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="accordion accordion-flush shadow-sm rounded" id="accordionFAQ">
                <div class="accordion-item border-0 mb-2 rounded">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button fw-bold collapsed rounded bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            How do I place an order?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body text-muted">
                            Simply browse our product catalog, click "Add to Cart" on the items you wish to purchase, and then navigate to your cart to proceed with the secure checkout process.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 mb-2 rounded">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button fw-bold collapsed rounded bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            What payment methods do you accept?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body text-muted">
                            We accept major credit cards (Visa, MasterCard, Amex), PayPal, and direct bank transfers. All payments are securely processed.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 mb-2 rounded">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button fw-bold collapsed rounded bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            What is your return policy?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body text-muted">
                            We offer a 30-day return window for most items. The product must remain in its original, unopened packaging. Please contact our support team to initiate a return.
                        </div>
                    </div>
                </div>
                <div class="accordion-item border-0 mb-2 rounded">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button fw-bold collapsed rounded bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            How can I track my order?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                        <div class="accordion-body text-muted">
                            Once your order is placed, you can visit the "Track Order" page from our header/footer navigation and enter your Order ID to see real-time status updates.
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 text-center">
                <p>Still have questions?</p>
                <a href="{{ route('ecommerce.contact') }}" class="btn btn-outline-primary px-4 fw-bold">Contact Support</a>
            </div>
        </div>
    </div>
</div>
@endsection
