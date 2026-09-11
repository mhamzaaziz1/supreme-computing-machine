@extends('layouts.ecommerce')

@section('title', 'Contact Us')

@section('content')
<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="fw-bold">Contact Us</h1>
            <p class="lead text-muted">We'd love to hear from you. Please reach out with any questions.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4 p-md-5">
                    <h3 class="mb-4 fw-bold">Send us a Message</h3>
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name</label>
                            <input type="text" class="form-control" placeholder="John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email Address</label>
                            <input type="email" class="form-control" placeholder="john@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Message</label>
                            <textarea class="form-control" rows="5" placeholder="How can we help you?" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-theme px-4 py-2 w-100 fw-bold">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 text-white bg-dark">
                <div class="card-body p-4 p-md-5 d-flex flex-column justify-content-center">
                    <h3 class="mb-4 fw-bold text-white">Contact Information</h3>
                    <p class="mb-4 opacity-75">Fill up the form and our Team will get back to you within 24 hours.</p>
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-phone-alt fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Phone Support</h5>
                            <p class="mb-0 text-white-50">+1 (234) 567-890</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-4">
                        <i class="fas fa-envelope fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Email Support</h5>
                            <p class="mb-0 text-white-50">support@{{ strtolower(config('app.name', 'example.com')) }}</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-map-marker-alt fa-2x text-primary me-3"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Our Location</h5>
                            <p class="mb-0 text-white-50">123 Street Name, City, Country</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
