@extends('layouts.ecommerce')

@section('title', 'Shopping Cart')

@section('content')
    <div class="container mt-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
            </ol>
        </nav>
        
        <h1 class="mb-4">Shopping Cart</h1>
        
        @if(count($cart_items) > 0)
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            @foreach($cart_items as $variation_id => $item)
                                <div class="cart-item">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            @php
                                                $image_url = 'https://via.placeholder.com/100x100?text=Product+Image';
                                                if($item['variation']->media->isNotEmpty()) {
                                                    $image_url = $item['variation']->media->first()->display_url;
                                                }
                                            @endphp
                                            <img src="{{ $image_url }}" class="cart-item-image img-fluid rounded" alt="{{ $item['variation']->product->name }}">
                                        </div>
                                        <div class="col-md-4 mb-2 mb-md-0">
                                            <h5 class="cart-item-title">
                                                <a href="{{ route('ecommerce.product_details', $item['variation']->product->id) }}" class="text-decoration-none text-dark">
                                                    {{ $item['variation']->product->name }}
                                                </a>
                                            </h5>
                                            @if($item['variation']->name != 'DUMMY')
                                                <p class="text-muted small mb-0">Variation: {{ $item['variation']->name }}</p>
                                            @endif
                                            @if($item['variation']->product->brand)
                                                <p class="text-muted small mb-0">Brand: {{ $item['variation']->product->brand->name }}</p>
                                            @endif
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <span class="cart-item-price">${{ number_format($item['variation']->sell_price_inc_tax, 2) }}</span>
                                        </div>
                                        <div class="col-md-2 mb-2 mb-md-0">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity('{{ $variation_id }}', {{ $item['quantity'] - 1 }})">-</button>
                                                <input type="number" class="form-control text-center cart-item-quantity" value="{{ $item['quantity'] }}" min="1" onchange="updateCartQuantity('{{ $variation_id }}', this.value)">
                                                <button class="btn btn-outline-secondary" type="button" onclick="updateCartQuantity('{{ $variation_id }}', {{ $item['quantity'] + 1 }})">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-1 mb-2 mb-md-0 text-end">
                                            <span class="fw-bold">${{ number_format($item['subtotal'], 2) }}</span>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart('{{ $variation_id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Continue Shopping and Clear Cart -->
                    <div class="d-flex justify-content-between mb-4">
                        <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-dark">
                            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                        </a>
                        <button class="btn btn-outline-danger" onclick="clearCart()">
                            <i class="fas fa-trash me-2"></i> Clear Cart
                        </button>
                    </div>
                </div>
                
                <!-- Cart Summary -->
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
                                <span>{{ $total >= 50 ? 'Free' : '$5.00' }}</span>
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
                                <span>${{ number_format($grand_total, 2) }}</span>
                            </div>
                            
                            <!-- Promo Code -->
                            <div class="mt-3">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" placeholder="Promo code">
                                    <button class="btn btn-outline-secondary" type="button">Apply</button>
                                </div>
                            </div>
                            
                            <!-- Checkout Button -->
                            <a href="{{ route('ecommerce.checkout') }}" class="btn btn-theme w-100 mt-3">
                                Proceed to Checkout
                            </a>
                            
                            <!-- Payment Methods -->
                            <div class="mt-3 text-center">
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
        @else
            <!-- Empty Cart -->
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-shopping-cart fa-4x mb-3 text-muted"></i>
                    <h3>Your cart is empty</h3>
                    <p class="mb-4 text-muted">Looks like you haven't added any products to your cart yet.</p>
                    <a href="{{ route('ecommerce.products') }}" class="btn btn-theme px-4 py-2">Start Shopping</a>
                </div>
            </div>
        @endif
        
        <!-- You May Also Like -->
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
                        <div class="card product-card">
                            @if($product->on_sale)
                                <span class="badge-sale">Sale</span>
                            @endif
                            
                            @php
                                $image_url = 'https://dummyimage.com/300x300/f8f9fa/6c757d.png&text=No+Image';
                                
                                // Try to get the first variation image
                                if(isset($product->product_variations) && is_iterable($product->product_variations)) {
                                    foreach($product->product_variations as $product_variation) {
                                        if(isset($product_variation->variations) && is_iterable($product_variation->variations)) {
                                            foreach($product_variation->variations as $variation) {
                                                if(isset($variation->media) && $variation->media->isNotEmpty()) {
                                                    $image_url = $variation->media->first()->display_url;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                }
                            @endphp
                            
                            <a href="{{ route('ecommerce.product_details', $product->id) }}">
                                <img src="{{ $image_url }}" class="card-img-top" alt="{{ $product->name }}">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <div class="text-muted small mb-1">{{ $product->category->name ?? 'Uncategorized' }}</div>
                                <h5 class="card-title text-truncate">
                                    <a href="{{ route('ecommerce.product_details', $product->id) }}" class="text-decoration-none text-dark">
                                        {{ $product->name }}
                                    </a>
                                </h5>
                                
                                <div class="mb-2 text-warning" style="font-size: 12px;">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="price mb-3">
                                        @if(isset($product->sell_price_inc_tax))
                                            ${{ number_format($product->sell_price_inc_tax, 2) }}
                                            @if($product->on_sale)
                                                <span class="original-price">${{ number_format($product->sell_price_inc_tax * 1.2, 2) }}</span>
                                            @endif
                                        @else
                                            $0.00
                                        @endif
                                    </div>
                                    <button class="btn w-100" style="background:#f5f5f5; border:1px solid #e5e5e5; font-size:13px; font-weight:600;" onclick="addToCart('{{ isset($product->product_variations) && $product->product_variations->isNotEmpty() && $product->product_variations->first()->variations->isNotEmpty() ? $product->product_variations->first()->variations->first()->id : 0 }}', 1)">
                                        ADD TO CART
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

@section('scripts')
<script>
    // Update cart quantity
    function updateCartQuantity(variationId, quantity) {
        if (quantity < 1) {
            quantity = 1;
        }
        
        updateCart(variationId, quantity);
    }
    
    // Clear cart
    function clearCart() {
        if (confirm('Are you sure you want to clear your cart?')) {
            // Remove all items one by one
            @foreach($cart_items as $variation_id => $item)
                removeFromCart('{{ $variation_id }}');
            @endforeach
        }
    }
</script>
@endsection