@extends('layouts.ecommerce')

@section('title', 'Track Order')

@section('content')
<div class="container py-5 order-tracking">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="order-tracking-title fw-bold">Track Your Order</h1>
            <p class="lead text-muted">Enter your Order ID below to check the real-time status.</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('ecommerce.track_order') }}" method="GET" class="order-tracking-form">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="order_id" class="form-control border-start-0 ps-0" placeholder="e.g. ORD-1612345678" value="{{ $order_id ?? '' }}" required>
                            <button class="btn btn-theme px-4 fw-bold" type="submit">Track</button>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($order_id))
                @if(isset($order) && $order)
                    <div class="card shadow-lg border-0 order-tracking-result">
                        <div class="card-header bg-white border-bottom p-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    <h4 class="order-id mb-1 text-primary">{{ $order->invoice_no }}</h4>
                                    <p class="mb-0 text-muted small">Placed on {{ \Carbon\Carbon::parse($order->transaction_date)->format('M d, Y') }}</p>
                                </div>
                                <div class="mt-2 mt-sm-0">
                                    <span class="badge {{ $order->status == 'final' ? 'bg-success' : 'bg-warning' }} fs-6 px-3 py-2 rounded-pill">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Order Status Timeline</h5>
                            <div class="order-timeline">
                                <!-- Step 1: Placed -->
                                <div class="order-timeline-item">
                                    <div class="order-timeline-icon"><i class="fas fa-check"></i></div>
                                    <div class="order-timeline-content">
                                        <h6 class="order-timeline-title">Order Placed</h6>
                                        <p class="order-timeline-date">{{ \Carbon\Carbon::parse($order->transaction_date)->format('M d, Y H:i A') }}</p>
                                    </div>
                                </div>
                                
                                <!-- Step 2: Processing -->
                                <div class="order-timeline-item">
                                    <div class="order-timeline-icon {{ $order->status == 'final' ? '' : 'bg-secondary' }}"><i class="fas fa-cogs"></i></div>
                                    <div class="order-timeline-content">
                                        <h6 class="order-timeline-title {{ $order->status == 'final' ? '' : 'text-muted' }}">Processing</h6>
                                        <p class="order-timeline-date {{ $order->status == 'final' ? '' : 'text-muted' }}">{{ $order->status == 'final' ? \Carbon\Carbon::parse($order->updated_at)->format('M d, Y H:i A') : 'Pending' }}</p>
                                    </div>
                                </div>
                                
                                <!-- Step 3: Shipped -->
                                <div class="order-timeline-item">
                                    <div class="order-timeline-icon bg-secondary"><i class="fas fa-shipping-fast"></i></div>
                                    <div class="order-timeline-content">
                                        <h6 class="order-timeline-title text-muted">Shipped</h6>
                                        <p class="order-timeline-date text-muted">Pending</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light p-4 text-center">
                            <p class="mb-0 fw-bold">Total Amount: <span class="text-primary fs-5">${{ number_format($order->final_total, 2) }}</span></p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning shadow-sm border-0 mt-4 p-4 text-center">
                        <i class="fas fa-exclamation-circle fa-2x mb-3 text-warning"></i>
                        <h5>Order Not Found</h5>
                        <p class="mb-0">We could not find an order matching the ID <strong>{{ $order_id }}</strong>. Please check and try again.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
