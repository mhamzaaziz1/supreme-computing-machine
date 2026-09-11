@extends('layouts.ecommerce')

@section('title', 'Home')

@section('content')

<!-- CSS for Home Layout (Demo 3 WCFM) -->
<style>
    /* Two-column Hero Section */
    .hero-section {
        margin-top: 20px;
        margin-bottom: 30px;
    }

    /* Category Sidebar */
    .category-sidebar {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .category-sidebar-list {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
    }

    .category-sidebar-list li {
        border-bottom: 1px solid var(--border-color);
    }
    
    .category-sidebar-list li:last-child {
        border-bottom: none;
    }

    .category-sidebar-list a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px;
        font-size: 13px;
        color: var(--text-main);
        transition: 0.2s;
    }

    .category-sidebar-list a:hover {
        color: var(--accent-yellow);
        padding-left: 25px;
    }
    
    .sidebar-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 2px;
        color: #fff;
    }

    .sidebar-shop-all {
        background-color: var(--accent-yellow);
        color: #000;
        font-weight: 600;
        padding: 15px;
        text-align: center;
        border-radius: 0 0 var(--radius-sm) var(--radius-sm);
        display: block;
        transition: 0.3s;
    }

    .sidebar-shop-all:hover {
        background-color: var(--accent-yellow-hover);
        color: #000;
    }

    /* Hero Slider Banner */
    .hero-slider-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 1920 / 600;
        border-radius: var(--radius-sm);
        overflow: hidden;
        background-color: #f5f5f5;
    }

    .hero-slider-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-item {
        height: 100%;
    }

    .hero-content {
        position: absolute;
        top: 50%;
        right: 10%;
        transform: translateY(-50%);
        max-width: 450px;
        text-align: right;
    }
    
    .hero-title {
        font-size: 48px;
        font-weight: 800;
        line-height: 1.1;
        color: #000;
        margin-bottom: 10px;
    }

    .hero-title span {
        color: var(--accent-red);
    }
    
    .red-circle-badge {
        position: absolute;
        top: 30%;
        left: 25%;
        background-color: var(--accent-red);
        color: white;
        height: 120px;
        width: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: 800;
        font-size: 14px;
        line-height: 1.2;
        box-shadow: 0 10px 20px rgba(255, 0, 0, 0.3);
        z-index: 5;
    }

    .hero-yellow-bar {
        background-color: var(--accent-yellow);
        padding: 15px 30px;
        display: inline-block;
        color: #000;
        font-weight: 700;
        font-size: 18px;
        margin-top: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Brand Carousel */
    .brand-carousel-area {
        background: #ffffff;
        border-radius: var(--radius-sm);
        padding: 20px 0;
        margin-bottom: 30px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .brand-item {
        text-align: center;
        padding: 15px;
    }
    
    .brand-item img {
        max-height: 60px;
        opacity: 0.7;
        transition: 0.3s;
    }
    
    .brand-item:hover img {
        opacity: 1;
    }

    /* Promo Banners */
    .promo-banner {
        position: relative;
        border-radius: var(--radius-sm);
        overflow: hidden;
        margin-bottom: 30px;
        height: 250px;
        background-color: #222;
        background-size: cover;
        background-position: center;
    }
    
    .promo-content {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: linear-gradient(to right, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.1) 100%);
    }

    .promo-content.center {
        background: rgba(0,0,0,0.5);
        align-items: center;
        text-align: center;
    }

    .promo-subtitle {
        color: var(--text-light);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }

    .promo-title {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
    }

    /* Section Headings */
    .section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #000;
        position: relative;
        padding-bottom: 10px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--accent-yellow);
    }
</style>

<div class="container hero-section">
    <div class="row g-4">
        <!-- Left Sidebar Categories -->
        <div class="col-lg-3 d-none d-lg-block">
            <div class="category-sidebar">
                <ul class="category-sidebar-list">
                    @foreach($categories ?? [] as $category)
                        <li>
                            <a href="{{ action([\App\Http\Controllers\EcommerceController::class, 'products']) }}?category_id={{ $category->id }}">
                                <span>{{ $category->name }}</span>
                                @if($loop->index == 0)
                                    <span class="sidebar-badge" style="background-color: var(--accent-red);">Sale</span>
                                @elseif($loop->index == 1)
                                    <span class="sidebar-badge" style="background-color: #ff5722;">Hot</span>
                                @elseif($loop->index == 2)
                                    <i class="fas fa-chevron-right text-muted" style="font-size: 10px;"></i>
                                @elseif($loop->index == 8)
                                    <span class="sidebar-badge" style="background-color: #10b981;">New</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('ecommerce.products') }}" class="sidebar-shop-all">
                    <i class="fas fa-cog fa-spin me-2" style="font-size:12px;"></i> Shop All Departments
                </a>
            </div>
        </div>

        <!-- Right Hero Slider -->
        <div class="col-lg-9">
            <div class="hero-slider-wrap">
                <div id="heroCarousel" class="carousel slide h-100" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        @php
                            $slider_images = !empty($ecom_settings['slider_images']) ? $ecom_settings['slider_images'] : [];
                        @endphp
                        @forelse($slider_images as $image)
                            <div class="carousel-item {{ $loop->first ? 'active' : '' }} h-100">
                                <img src="{{ asset('uploads/ecommerce/' . $image) }}" class="d-block w-100" alt="Slider Image">
                            </div>
                        @empty
                            <div class="carousel-item active h-100">
                                <img src="https://dummyimage.com/1920x600/1a1a1a/ffffff&text=Please+Upload+Banners+in+Settings" class="d-block w-100" alt="Placeholder">
                            </div>
                        @endforelse
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
            </div>
        </div>
    </div>
</div>

<!-- Brand Carousel Section (Static representation) -->
<div class="container">
    <div class="brand-carousel-area">
        <div class="row align-items-center justify-content-center">
            @php
                $brands = !empty($ecom_settings['brands']) ? $ecom_settings['brands'] : [];
            @endphp
            @forelse($brands as $brand)
                <div class="col-4 col-md-2">
                    <a href="{{ $brand['link'] ?? '#' }}" target="_blank" class="brand-item d-block">
                        <img src="{{ asset('uploads/ecommerce/' . $brand['logo']) }}" class="img-fluid" alt="Brand">
                    </a>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-3">
                    <small>No brands added. Add them in Ecommerce Settings.</small>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        @php
            $promo_1 = !empty($ecom_settings['promo_banners'][1]) ? $ecom_settings['promo_banners'][1] : null;
            $promo_2 = !empty($ecom_settings['promo_banners'][2]) ? $ecom_settings['promo_banners'][2] : null;
        @endphp
        <div class="col-md-6">
            <a href="{{ $promo_1['link'] ?? '#' }}" class="d-block promo-banner" style="background-image: url('{{ !empty($promo_1['image']) ? asset('uploads/ecommerce/' . $promo_1['image']) : 'https://dummyimage.com/600x300/111/fff&text=Promo+Banner+1' }}');">
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ $promo_2['link'] ?? '#' }}" class="d-block promo-banner" style="background-image: url('{{ !empty($promo_2['image']) ? asset('uploads/ecommerce/' . $promo_2['image']) : 'https://dummyimage.com/600x300/333/fff&text=Promo+Banner+2' }}');">
            </a>
        </div>
    </div>
</div>

<!-- Products Section -->
<div class="container py-4">
    <h3 class="section-title">Trending Products</h3>
    <div class="row g-4">
        @forelse($featured_products ?? [] as $product)
            <div class="col-6 col-md-4 col-lg-3 mb-4">
                <div class="card h-100 product-card border-0 shadow-sm">
                    @php
                        $variation = $product->product_variations->first()->variations->first() ?? null;
                        $price = $variation->sell_price_inc_tax ?? 0;
                        
                        $image_url = asset('img/default.png');
                        if ($variation && $variation->media->isNotEmpty()) {
                            $image_url = $variation->media->first()->display_url;
                        } elseif (!empty($product->image)) {
                            $image_url = asset('uploads/img/' . $product->image);
                        }
                    @endphp
                    
                    <div class="position-relative overflow-hidden">
                        @if($loop->iteration % 2 == 0)
                            <span class="badge position-absolute top-0 start-0 m-2 z-1" style="background: var(--accent-red); font-size: 10px; font-weight: 700;">SALE</span>
                        @elseif($loop->iteration % 3 == 0)
                            <span class="badge position-absolute top-0 start-0 m-2 z-1" style="background: var(--accent-yellow); color: #000; font-size: 10px; font-weight: 700;">HOT</span>
                        @endif
                        
                        <a href="{{ route('ecommerce.product_details', $product->id) }}" class="d-block">
                            <div class="product-image-container" style="aspect-ratio: 1/1; background: #f9f9f9;">
                                <img src="{{ $image_url }}" class="card-img-top w-100 h-100 object-fit-contain p-3 transition-transform" alt="{{ $product->name }}">
                            </div>
                        </a>
                    </div>

                    <div class="card-body d-flex flex-column p-3">
                        <div class="text-muted small mb-1">{{ $product->category->name ?? 'Uncategorized' }}</div>
                        <h6 class="card-title mb-2">
                            <a href="{{ route('ecommerce.product_details', $product->id) }}" class="text-decoration-none text-dark fw-bold product-name-link">
                                {{ $product->name }}
                            </a>
                        </h6>
                        
                        <div class="mb-2 text-warning mt-auto" style="font-size: 11px;">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="far fa-star"></i>
                        </div>
                        
                        <div class="product-price mb-3">
                            <span class="fw-bold text-dark fs-5">
                                {{ Session::get('currency.symbol', '$') }}{{ number_format($price, 2) }}
                            </span>
                            @if($loop->iteration % 2 == 0)
                                <span class="text-muted text-decoration-line-through ms-2 small">
                                    {{ Session::get('currency.symbol', '$') }}{{ number_format($price * 1.2, 2) }}
                                </span>
                            @endif
                        </div>
                        
                        <form action="{{ route('ecommerce.add_to_cart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="variation_id" value="{{ $variation->id ?? '' }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-theme w-100 rounded-1 fw-bold py-2 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products available at the moment.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
