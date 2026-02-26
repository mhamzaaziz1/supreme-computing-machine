@extends('layouts.ecommerce')

@section('title', 'Order Confirmation')

@section('content')
    <div class="container mt-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.cart') }}">Shopping Cart</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.checkout') }}">Checkout</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order Confirmation</li>
            </ol>
        </nav>
        
        <div class="order-confirmation text-center">
            <div class="icon">
                <i class="fas fa-check-circle text-success"></i>
            </div>
            <h1 class="title">Thank You for Your Order!</h1>
            <p class="order-id">Order #{{ $order_id }}</p>
            <p class="message">
                Your order has been placed successfully. We've sent a confirmation email to your email address.<br>
                You can track your order status in your account or using the tracking link below.
            </p>
            
            <div class="row justify-content-center mb-5">
                <div class="col-md-6">
                    <div class="d-grid gap-3">
                        <a href="{{ route('ecommerce.track_order', ['order_id' => $order_id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-truck me-2"></i> Track Order
                        </a>
                        <a href="{{ route('ecommerce.home') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Order Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h6>Shipping Information</h6>
                        <p class="mb-1">John Doe</p>
                        <p class="mb-1">123 Main St</p>
                        <p class="mb-1">New York, NY 10001</p>
                        <p class="mb-1">United States</p>
                        <p class="mb-1">Email: john.doe@example.com</p>
                        <p class="mb-1">Phone: (123) 456-7890</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Order Summary</h6>
                        <p class="mb-1"><strong>Order Date:</strong> {{ date('F j, Y') }}</p>
                        <p class="mb-1"><strong>Order Number:</strong> {{ $order_id }}</p>
                        <p class="mb-1"><strong>Payment Method:</strong> Credit Card</p>
                        <p class="mb-1"><strong>Shipping Method:</strong> Standard Shipping</p>
                        <p class="mb-1"><strong>Estimated Delivery:</strong> {{ date('F j, Y', strtotime('+5 days')) }}</p>
                    </div>
                </div>
                
                <div class="table-responsive mt-4">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/50x50" class="me-3" alt="Product Image">
                                        <div>
                                            <p class="mb-0">Sample Product 1</p>
                                            <small class="text-muted">Variation: Small</small>
                                        </div>
                                    </div>
                                </td>
                                <td>$19.99</td>
                                <td>2</td>
                                <td class="text-end">$39.98</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://via.placeholder.com/50x50" class="me-3" alt="Product Image">
                                        <div>
                                            <p class="mb-0">Sample Product 2</p>
                                            <small class="text-muted">Variation: Medium</small>
                                        </div>
                                    </div>
                                </td>
                                <td>$24.99</td>
                                <td>1</td>
                                <td class="text-end">$24.99</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">$64.97</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                <td class="text-end">$5.00</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end">$4.55</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end"><strong>$74.52</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- What's Next -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">What's Next?</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="text-center">
                            <i class="fas fa-envelope fa-3x mb-3 text-primary"></i>
                            <h5>Order Confirmation</h5>
                            <p>We've sent a confirmation email with your order details to your email address.</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="text-center">
                            <i class="fas fa-box fa-3x mb-3 text-primary"></i>
                            <h5>Order Processing</h5>
                            <p>We're preparing your order for shipment. You'll receive an email when it's on its way.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <i class="fas fa-truck fa-3x mb-3 text-primary"></i>
                            <h5>Delivery</h5>
                            <p>Your order will be delivered to your shipping address within the estimated delivery time.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Customer Support -->
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Need Help?</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h6>Customer Support</h6>
                        <p>If you have any questions about your order, please contact our customer support team.</p>
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i> support@example.com</p>
                        <p class="mb-1"><i class="fas fa-phone me-2"></i> (123) 456-7890</p>
                        <p class="mb-1"><i class="fas fa-clock me-2"></i> Monday - Friday, 9am - 5pm EST</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Helpful Links</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="{{ route('ecommerce.track_order') }}" class="text-decoration-none"><i class="fas fa-truck me-2"></i> Track Your Order</a></li>
                            <li class="mb-2"><a href="{{ route('ecommerce.help') }}" class="text-decoration-none"><i class="fas fa-question-circle me-2"></i> FAQs</a></li>
                            <li class="mb-2"><a href="{{ route('ecommerce.terms') }}" class="text-decoration-none"><i class="fas fa-file-alt me-2"></i> Return Policy</a></li>
                            <li class="mb-2"><a href="{{ route('ecommerce.contact') }}" class="text-decoration-none"><i class="fas fa-comments me-2"></i> Contact Us</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recommended Products -->
        <div class="mt-5">
            <h2 class="text-center mb-4">You May Also Like</h2>
            <div class="row">
                @php
                    // Get some random products
                    $recommended_products = App\Product::where('business_id', request()->session()->get('user.business_id'))
                        ->active()
                        ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
                        ->inRandomOrder()
                        ->take(4)
                        ->get();
                @endphp
                
                @foreach($recommended_products as $product)
                    <div class="col-6 col-md-3 mb-4">
                        <div class="card product-card h-100">
                            @if($product->on_sale)
                                <span class="badge-sale">Sale</span>
                            @endif
                            
                            @php
                                $image_url = 'https://via.placeholder.com/300x300?text=Product+Image';
                                
                                // Try to get the first variation image
                                foreach($product->product_variations as $product_variation) {
                                    foreach($product_variation->variations as $variation) {
                                        if($variation->media->isNotEmpty()) {
                                            $image_url = $variation->media->first()->display_url;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            
                            <a href="{{ route('ecommerce.product_details', $product->id) }}">
                                <img src="{{ $image_url }}" class="card-img-top" alt="{{ $product->name }}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="{{ route('ecommerce.product_details', $product->id) }}" class="text-decoration-none text-dark">
                                        {{ $product->name }}
                                    </a>
                                </h5>
                                <p class="card-text small">
                                    {{ Str::limit($product->product_description, 50) }}
                                </p>
                                <div class="mt-auto">
                                    <p class="price mb-0">
                                        ${{ number_format($product->sell_price_inc_tax, 2) }}
                                        @if($product->on_sale)
                                            <span class="original-price">${{ number_format($product->sell_price_inc_tax * 1.2, 2) }}</span>
                                        @endif
                                    </p>
                                    <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addToCart('{{ $product->product_variations->first()->variations->first()->id }}', 1)">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection