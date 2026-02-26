@extends('layouts.ecommerce')

@section('title', 'Home')

@section('styles')
<style>
    .hero-banner {
        position: relative;
        overflow: hidden;
    }

    .hero-banner img {
        width: 100%;
        height: auto;
        max-height: 600px;
        object-fit: cover;
    }

    .banner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .banner-content {
        color: white;
        text-align: center;
        padding: 2rem;
    }

    .banner-content h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }

    .banner-content p.lead {
        font-size: 1.5rem;
        margin-bottom: 2rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    @media (max-width: 768px) {
        .banner-content h1 {
            font-size: 2rem;
        }

        .banner-content p.lead {
            font-size: 1.2rem;
        }
    }
</style>
@endsection

@section('content')
    <!-- Hero Banner -->
    <div class="container-fluid px-0">
        @if(!empty($slider_images) && count($slider_images) > 0)
            <!-- Dynamic slider carousel from admin ecommerce settings -->
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($slider_images as $key => $slider_image)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $key + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach($slider_images as $key => $slider_image)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <img src="{{ asset('uploads/ecommerce/' . $slider_image) }}" class="d-block w-100" alt="{{ !empty($ecom_settings['store_name']) ? $ecom_settings['store_name'] : 'Store Slider' }}">
                            <div class="carousel-caption d-none d-md-block">
                                @if(!empty($ecom_settings['store_name']))
                                    <h2>{{ $ecom_settings['store_name'] }}</h2>
                                @endif
                                @if(!empty($ecom_settings['store_tagline']))
                                    <p>{{ $ecom_settings['store_tagline'] }}</p>
                                @endif
                                <a href="{{ route('ecommerce.products') }}" class="btn btn-primary">Shop Now</a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(count($slider_images) > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        @elseif(!empty($store_banner))
            <!-- Dynamic banner from admin ecommerce settings -->
            <div class="hero-banner">
                <img src="{{ asset('uploads/ecommerce/' . $store_banner) }}" class="d-block w-100" alt="{{ !empty($ecom_settings['store_name']) ? $ecom_settings['store_name'] : 'Store Banner' }}">
                <div class="banner-overlay">
                    <div class="container">
                        <div class="banner-content text-center">
                            @if(!empty($ecom_settings['store_name']))
                                <h1>{{ $ecom_settings['store_name'] }}</h1>
                            @endif
                            @if(!empty($ecom_settings['store_tagline']))
                                <p class="lead">{{ $ecom_settings['store_tagline'] }}</p>
                            @endif
                            <a href="{{ route('ecommerce.products') }}" class="btn btn-primary btn-lg">Shop Now</a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Fallback carousel if no banner or slider images are set in admin -->
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://via.placeholder.com/1920x600?text=Summer+Collection" class="d-block w-100" alt="Summer Collection">
                        <div class="carousel-caption d-none d-md-block">
                            <h2>Summer Collection</h2>
                            <p>Discover our new summer collection with fresh styles and colors.</p>
                            <a href="{{ route('ecommerce.products') }}" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://via.placeholder.com/1920x600?text=New+Arrivals" class="d-block w-100" alt="New Arrivals">
                        <div class="carousel-caption d-none d-md-block">
                            <h2>New Arrivals</h2>
                            <p>Check out our latest products just added to our store.</p>
                            <a href="{{ route('ecommerce.products', ['sort_by' => 'date_desc']) }}" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://via.placeholder.com/1920x600?text=Special+Offers" class="d-block w-100" alt="Special Offers">
                        <div class="carousel-caption d-none d-md-block">
                            <h2>Special Offers</h2>
                            <p>Limited time offers with great discounts on selected items.</p>
                            <a href="{{ route('ecommerce.products') }}" class="btn btn-primary">Shop Now</a>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Featured Categories -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row">
            @foreach($categories->take(4) as $category)
                <div class="col-6 col-md-3 mb-4">
                    <a href="{{ route('ecommerce.products', ['category_id' => $category->id]) }}" class="text-decoration-none">
                        <div class="card h-100">
                            <img src="{{ asset('uploads/category/' . $category->image) }}" class="card-img-top" alt="{{ $category->name }}">
                            <div class="card-body text-center">
                                <h5 class="card-title">{{ $category->name }}</h5>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Featured Products -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row">
            @forelse($featured_products as $product)
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
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No featured products available at the moment.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- New Arrivals -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">New Arrivals</h2>
        <div class="row">
            @forelse($new_arrivals as $product)
                <div class="col-6 col-md-3 mb-4">
                    <div class="card product-card h-100">
                        <span class="badge-new">New</span>

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
                                </p>
                                <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addToCart('{{ $product->product_variations->first()->variations->first()->id }}', 1)">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No new arrivals available at the moment.
                    </div>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('ecommerce.products', ['sort_by' => 'date_desc']) }}" class="btn btn-outline-primary">View All New Arrivals</a>
        </div>
    </div>

    <!-- Best Sellers -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Best Sellers</h2>
        <div class="row">
            @forelse($best_sellers as $product)
                <div class="col-6 col-md-3 mb-4">
                    <div class="card product-card h-100">
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
                                </p>
                                <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addToCart('{{ $product->product_variations->first()->variations->first()->id }}', 1)">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No best sellers available at the moment.
                    </div>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-primary">View All Products</a>
        </div>
    </div>

    <!-- Sale Products -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">On Sale</h2>
        <div class="row">
            @forelse($sale_products as $product)
                <div class="col-6 col-md-3 mb-4">
                    <div class="card product-card h-100">
                        <span class="badge-sale">Sale</span>

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
                                    <span class="original-price">${{ number_format($product->sell_price_inc_tax * 1.2, 2) }}</span>
                                </p>
                                <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addToCart('{{ $product->product_variations->first()->variations->first()->id }}', 1)">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        No sale products available at the moment.
                    </div>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-primary">View All Products</a>
        </div>
    </div>

    <!-- Features -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-truck fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Free Shipping</h5>
                        <p class="card-text">Free shipping on all orders over $50</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-undo fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Easy Returns</h5>
                        <p class="card-text">30 days return policy for all products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <i class="fas fa-lock fa-3x mb-3 text-primary"></i>
                        <h5 class="card-title">Secure Payment</h5>
                        <p class="card-text">Safe & secure payment methods</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter -->
    <div class="container mt-5">
        <div class="card bg-light">
            <div class="card-body py-5">
                <div class="row justify-content-center">
                    <div class="col-md-8 text-center">
                        <h3>Subscribe to Our Newsletter</h3>
                        <p class="mb-4">Get the latest updates on new products and special offers</p>
                        <form class="row g-3 justify-content-center">
                            <div class="col-md-8">
                                <input type="email" class="form-control" placeholder="Enter your email">
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary">Subscribe</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
