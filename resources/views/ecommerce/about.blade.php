@extends('layouts.ecommerce')

@section('title', 'About Us')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4 text-center fw-bold">About {{ config('app.name', 'Our Store') }}</h1>
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4 p-md-5">
                    <p class="lead">Welcome to {{ config('app.name', 'Our Store') }}, your number one source for all quality products. We're dedicated to giving you the very best of our merchandise, with a focus on dependability, customer service, and uniqueness.</p>
                    <hr class="my-4">
                    <h3 class="fw-bold mt-4">Our Story</h3>
                    <p>Founded with a passion for delivering high-quality goods, {{ config('app.name', 'Our Store') }} has come a long way from its beginnings. When we first started out, our passion for helping customers find the best equipment drove us to do intense research, and gave us the impetus to turn hard work and inspiration into to a booming online store. We now serve customers all over the world and are thrilled to be a part of the fair trade wing of the industry.</p>
                    <h3 class="fw-bold mt-4">Our Promise</h3>
                    <p>We hope you enjoy our products as much as we enjoy offering them to you. If you have any questions or comments, please don't hesitate to contact us.</p>
                    <div class="mt-5 text-center">
                        <a href="{{ route('ecommerce.products') }}" class="btn btn-theme btn-lg px-5 rounded-pill shadow">Start Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
