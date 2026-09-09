<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="E-commerce store for {{ config('app.name', 'UltimatePOS') }}">
    <meta name="author" content="{{ config('app.name', 'UltimatePOS') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') - {{ config('app.name', 'UltimatePOS') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom styles -->
    <style>
        :root {
            --primary-bg: #f5f5f5;
            --header-dark: #000000;
            --header-darker: #111111;
            --accent-yellow: #ffb800;
            --accent-yellow-hover: #e2a300;
            --accent-red: #ff0000;
            --text-main: #333333;
            --text-light: #777777;
            --border-color: #e5e5e5;
            --radius-sm: 4px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text-main);
            background-color: var(--primary-bg);
            font-size: 14px;
        }
        
        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
        }
        
        a:hover {
            color: var(--accent-yellow);
        }

        /* --- HEADER STYLES --- */
        .header-top {
            background-color: var(--header-dark);
            color: #ffffff;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .header-logo {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff !important;
            letter-spacing: -0.5px;
            margin-right: 20px;
        }

        .header-logo span {
            color: var(--accent-yellow);
        }

        /* Search Bar */
        .search-container {
            display: flex;
            width: 100%;
            max-width: 650px;
            background: #ffffff;
            border-radius: var(--radius-sm);
            overflow: hidden;
            height: 42px;
        }

        .search-category {
            background: #f5f5f5;
            border: none;
            border-right: 1px solid #ddd;
            padding: 0 15px;
            font-size: 13px;
            color: #555;
            outline: none;
            min-width: 140px;
            cursor: pointer;
        }

        .search-input {
            flex-grow: 1;
            border: none;
            padding: 0 15px;
            font-size: 13px;
            outline: none;
        }

        .search-btn {
            background-color: var(--accent-yellow);
            color: #000;
            border: none;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .search-btn:hover {
            background-color: var(--accent-yellow-hover);
        }

        /* Header Actions (Right) */
        .header-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
        }

        .header-action-item {
            color: #ffffff;
            position: relative;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .header-action-text {
            font-size: 13px;
            margin-left: 5px;
            font-weight: 500;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--accent-red);
            color: white;
            font-size: 10px;
            font-weight: 700;
            height: 16px;
            width: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* --- MAIN NAVBAR --- */
        .header-nav {
            background-color: var(--header-dark);
            color: #ffffff;
        }

        /* All Departments Dropdown */
        .departments-menu {
            position: relative;
        }
        
        .departments-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--accent-yellow);
            font-weight: 600;
            padding: 15px 0;
            cursor: pointer;
        }

        .departments-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            width: 300px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0 0 var(--radius-sm) var(--radius-sm);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .departments-dropdown.show {
            display: flex;
        }

        .department-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: var(--text-main);
            text-decoration: none;
            border-bottom: 1px solid #f0f0f0;
            transition: background 0.2s;
            font-size: 14px;
        }

        .department-item:last-child {
            border-bottom: none;
        }

        .department-item:hover {
            background-color: #f9f9f9;
            color: var(--accent-dark);
        }

        .department-item i.fa-chevron-right {
            font-size: 10px;
            color: #999;
        }

        .badge-mini {
            padding: 2px 6px;
            font-size: 10px;
            font-weight: 700;
            border-radius: 2px;
            text-transform: uppercase;
            color: white;
            margin-left: 10px;
        }
        
        .badge-mini.sale { background-color: #ff0000; }
        .badge-mini.hot { background-color: #ff5722; }
        .badge-mini.new { background-color: #10b981; }

        .departments-footer {
            background-color: var(--accent-yellow);
            padding: 12px;
            text-align: center;
            border-radius: 0 0 var(--radius-sm) var(--radius-sm);
        }

        .departments-footer a {
            color: #000;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }

        /* Nav Links */
        .main-nav-links {
            display: flex;
            gap: 25px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .main-nav-links a {
            color: #ffffff;
            font-weight: 500;
            font-size: 14px;
            padding: 15px 0;
            display: block;
        }

        .main-nav-links a.active, .main-nav-links a:hover {
            color: var(--accent-yellow);
        }

        .nav-right-links {
            display: flex;
            gap: 20px;
            font-size: 13px;
        }
        
        .nav-right-links a {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* --- GENERAL COMPONENTS --- */
        .btn-theme {
            background-color: var(--accent-yellow);
            color: #000;
            font-weight: 600;
            border: none;
            border-radius: var(--radius-sm);
            padding: 10px 20px;
        }

        .btn-theme:hover {
            background-color: var(--accent-yellow-hover);
            color: #000;
        }

        /* Card / Product styles mapped to new variables */
        .card {
            border: none;
            border-radius: var(--radius-sm);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            background: #ffffff;
        }
        
        .card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .product-card {
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        
        .product-card .card-img-top {
            height: 220px;
            object-fit: contain;
            padding: 10px;
        }
        
        .product-card .card-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-main);
            margin-bottom: 5px;
        }
        
        .product-card .price {
            font-weight: 700;
            color: var(--accent-red);
            font-size: 16px;
        }
        
        .product-card .original-price {
            text-decoration: line-through;
            color: var(--text-light);
            font-size: 12px;
            margin-left: 5px;
        }
        
        .badge-sale, .badge-new, .badge-hot {
            position: absolute;
            top: 10px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 2px;
            text-transform: uppercase;
            z-index: 10;
        }
        
        .badge-sale {
            right: 10px;
            background: var(--accent-red);
            color: white;
        }
        
        .badge-new {
            left: 10px;
            background: #green; /* TBD based exactly on demo if present, fallback standard */
            background: #10b981;
            color: white;
        }

        .badge-hot {
            right: 10px;
            background: #ff5722;
            color: white;
        }

        /* Footer */
        .footer {
            background-color: var(--header-dark);
            color: #cccccc;
            padding: 40px 0 20px;
            margin-top: 60px;
            border-top: 2px solid var(--accent-yellow);
        }
        
        .footer h5 {
            color: #ffffff;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .footer ul {
            list-style: none;
            padding-left: 0;
        }
        
        .footer ul li {
            margin-bottom: 0.75rem;
        }

        .footer ul li a {
            color: #999999;
        }

        .footer ul li a:hover {
            color: var(--accent-yellow);
        }
        
        .footer-bottom {
            background-color: var(--header-darker);
            padding: 1.5rem 0;
            margin-top: 2rem;
            border-top: 1px solid #333;
        }
        
        /* Mobile adjustments */
        @media (max-width: 991px) {
            .search-container {
                margin: 15px 0;
            }
            .header-actions {
                justify-content: flex-start;
                margin-top: 10px;
            }
            
            .sticky-cart {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #ffffff;
                border-top: 1px solid var(--border-color);
                box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.05);
                padding: 0.75rem 1rem;
                z-index: 1000;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            body {
                padding-bottom: 60px;
            }
        }
        
        /* Product details page */
        .product-gallery {
            position: relative;
        }
        
        .product-gallery .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            cursor: zoom-in;
        }
        
        .product-gallery .thumbnails {
            display: flex;
            margin-top: 1rem;
        }
        
        .product-gallery .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 0.5rem;
            cursor: pointer;
            border: 2px solid transparent;
        }
        
        .product-gallery .thumbnail.active {
            border-color: var(--primary-color);
        }
        
        .product-details .product-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .product-details .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .product-details .product-original-price {
            text-decoration: line-through;
            color: var(--secondary-color);
            font-size: 1.25rem;
            margin-left: 0.5rem;
        }
        
        .product-details .product-description {
            margin-bottom: 1.5rem;
        }
        
        .product-details .product-meta {
            margin-bottom: 1.5rem;
        }
        
        .product-details .product-meta p {
            margin-bottom: 0.25rem;
        }
        
        .product-details .product-variants {
            margin-bottom: 1.5rem;
        }
        
        .product-details .product-variants .btn-variant {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            border: 2px solid #ddd;
            background-color: white;
            color: #333;
        }
        
        .product-details .product-variants .btn-variant.active {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
        }
        
        .product-details .product-quantity {
            margin-bottom: 1.5rem;
        }
        
        .product-details .product-quantity .input-group {
            width: 150px;
        }
        
        /* Cart page */
        .cart-item {
            padding: 1rem 0;
            border-bottom: 1px solid #ddd;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item .cart-item-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }
        
        .cart-item .cart-item-title {
            font-weight: 600;
        }
        
        .cart-item .cart-item-price {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .cart-item .cart-item-quantity {
            width: 80px;
        }
        
        .cart-summary {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }
        
        .cart-summary .cart-summary-title {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .cart-summary .cart-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .cart-summary .cart-summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: 700;
            font-size: 1.25rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #ddd;
        }
        
        /* Checkout page */
        .checkout-form .form-label {
            font-weight: 600;
        }
        
        .checkout-form .form-control {
            border-radius: 0.25rem;
        }
        
        .checkout-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 144, 220, 0.25);
        }
        
        .checkout-form .btn-checkout {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
        }
        
        /* Order confirmation page */
        .order-confirmation {
            text-align: center;
            padding: 3rem 0;
        }
        
        .order-confirmation .icon {
            font-size: 5rem;
            color: var(--success-color);
            margin-bottom: 1.5rem;
        }
        
        .order-confirmation .title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .order-confirmation .order-id {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .order-confirmation .message {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        /* Account page */
        .account-sidebar {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .account-sidebar .account-sidebar-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .account-sidebar .nav-link {
            color: #333;
            padding: 0.5rem 0;
        }
        
        .account-sidebar .nav-link.active {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .account-content {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .account-content .account-content-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        /* Order tracking page */
        .order-tracking {
            padding: 2rem 0;
        }
        
        .order-tracking .order-tracking-title {
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .order-tracking .order-tracking-form {
            max-width: 500px;
            margin: 0 auto;
        }
        
        .order-tracking .order-tracking-form .form-control {
            border-radius: 0.25rem;
        }
        
        .order-tracking .order-tracking-form .btn {
            font-weight: 600;
        }
        
        .order-tracking .order-tracking-result {
            margin-top: 2rem;
        }
        
        .order-tracking .order-tracking-result .order-id {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .order-tracking .order-tracking-result .order-status {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .order-tracking .order-tracking-result .order-timeline {
            margin-top: 1.5rem;
        }
        
        .order-tracking .order-tracking-result .order-timeline-item {
            display: flex;
            margin-bottom: 1rem;
        }
        
        .order-tracking .order-tracking-result .order-timeline-item .order-timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .order-tracking .order-tracking-result .order-timeline-item .order-timeline-content {
            flex: 1;
        }
        
        .order-tracking .order-tracking-result .order-timeline-item .order-timeline-content .order-timeline-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .order-tracking .order-tracking-result .order-timeline-item .order-timeline-content .order-timeline-date {
            font-size: 0.875rem;
            color: var(--secondary-color);
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Header -->
    <header>
        <!-- Top Bar (Dark) -->
        <div class="header-top">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Logo -->
                    <div class="col-12 col-lg-3 text-center text-lg-start mb-3 mb-lg-0">
                        <a href="{{ route('ecommerce.home') }}" class="header-logo text-decoration-none">
                            @if(Session::has('business.logo') && !empty(Session::get('business.logo')))
                                <img src="{{ asset('uploads/business_logos/' . Session::get('business.logo')) }}" alt="{{ Session::get('business.name') }}" style="max-height: 50px; width: auto;">
                            @elseif(Session::has('business.name'))
                                <span class="text-white">{{ Session::get('business.name') }}</span>
                            @else
                                urna<span>auto</span>
                            @endif
                        </a>
                    </div>
                    
                    <!-- Search Bar -->
                    <div class="col-12 col-lg-6 d-flex justify-content-center justify-content-lg-start mb-3 mb-lg-0">
                        <form class="search-container" action="{{ route('ecommerce.products') }}" method="GET">
                            <select class="search-category" name="category_id">
                                <option value="">All Categories</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="text" class="search-input" name="search" placeholder="I'm shopping for..." value="{{ request('search') }}">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- User Actions -->
                    <div class="col-12 col-lg-3">
                        <div class="header-actions">
                            <div class="header-action-item">
                                <span class="header-action-text me-2">USD <i class="fas fa-chevron-down ms-1" style="font-size:10px;"></i></span>
                            </div>
                            <a href="{{ route('ecommerce.account') }}" class="header-action-item" title="Account">
                                <i class="far fa-user"></i>
                            </a>
                            <a href="#" class="header-action-item" title="Wishlist">
                                <i class="far fa-heart"></i>
                                <span class="cart-badge bg-danger">0</span>
                            </a>
                            <a href="{{ route('ecommerce.cart') }}" class="header-action-item" title="Cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-badge">{{ count(session('cart', [])) }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Bar (Darker) -->
        <div class="header-nav d-none d-lg-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3">
                        <div class="departments-menu">
                            <div class="departments-btn" id="all-departments-btn">
                                <i class="fas fa-bars"></i>
                                All Departments <i class="fas fa-chevron-down ms-auto" style="font-size:10px;"></i>
                            </div>
                            <div class="departments-dropdown" id="all-departments-sidebar">
                                @foreach($categories ?? [] as $category)
                                    <a href="{{ action([\App\Http\Controllers\EcommerceController::class, 'products']) }}?category_id={{ $category->id }}" class="department-item">
                                        <span>{{ $category->name }}</span>
                                        <div class="d-flex align-items-center">
                                            @if($loop->index == 0)
                                                <span class="badge-mini sale">Sale</span>
                                            @elseif($loop->index == 1)
                                                <span class="badge-mini hot">Hot</span>
                                            @elseif($loop->index == 2)
                                                <i class="fas fa-chevron-right"></i>
                                            @elseif($loop->index == 8)
                                                <span class="badge-mini new">New</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                                <div class="departments-footer">
                                    <a href="{{ action([\App\Http\Controllers\EcommerceController::class, 'products']) }}">
                                        <i class="fas fa-cog"></i> Shop All Departments
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <ul class="main-nav-links">
                            <li><a href="{{ route('ecommerce.home') }}" class="{{ request()->routeIs('ecommerce.home') ? 'active' : '' }}">Home</a></li>
                            <li><a href="{{ route('ecommerce.products') }}" class="{{ request()->routeIs('ecommerce.products') ? 'active' : '' }}">Shop</a></li>
                            <li><a href="{{ route('ecommerce.products') }}">Product</a></li>
                            <li><a href="#">Blog</a></li>
                            <li><a href="{{ route('ecommerce.about') }}" class="{{ request()->routeIs('ecommerce.about') ? 'active' : '' }}">Pages</a></li>
                            <li><a href="{{ route('ecommerce.contact') }}">Become Vendor</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3">
                        <div class="nav-right-links justify-content-end">
                            <a href="#"><i class="fas fa-history"></i> Recently Viewed <i class="fas fa-chevron-down ms-1" style="font-size:10px;"></i></a>
                            <a href="{{ route('ecommerce.track_order') }}"><i class="fas fa-truck"></i> Order Tracking</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile sticky cart (visible on mobile only) -->
    <div class="sticky-cart d-md-none">
        <div>
            <span class="fw-bold">{{ count(session('cart', [])) }} items</span>
        </div>
        <a href="{{ route('ecommerce.cart') }}" class="btn btn-theme">
            <i class="fas fa-shopping-cart me-1"></i> View Cart
        </a>
    </div>
    
    <!-- Main content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>{{ config('app.name', 'UltimatePOS') }}</h5>
                    <p>Your one-stop shop for all your needs. Quality products, competitive prices, and excellent customer service.</p>
                    <div class="mt-3">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="{{ route('ecommerce.home') }}">Home</a></li>
                        <li><a href="{{ route('ecommerce.products') }}">Products</a></li>
                        <li><a href="{{ route('ecommerce.about') }}">About Us</a></li>
                        <li><a href="{{ route('ecommerce.contact') }}">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5>Customer Service</h5>
                    <ul>
                        <li><a href="{{ route('ecommerce.help') }}">Help & FAQs</a></li>
                        <li><a href="{{ route('ecommerce.terms') }}">Terms & Conditions</a></li>
                        <li><a href="{{ route('ecommerce.privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('ecommerce.track_order') }}">Track Order</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Contact Info</h5>
                    <ul>
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Main St, City, Country</li>
                        <li><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope me-2"></i> info@example.com</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer-bottom mt-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="mb-md-0">&copy; {{ date('Y') }} {{ config('app.name', 'UltimatePOS') }}. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <img src="https://via.placeholder.com/300x50?text=Payment+Methods" alt="Payment Methods" class="img-fluid" style="max-height: 30px;">
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Set CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Add to cart functionality
        function addToCart(variationId, quantity) {
            $.ajax({
                url: '{{ route('ecommerce.add_to_cart') }}',
                method: 'POST',
                data: {
                    variation_id: variationId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        // Update cart count
                        $('.cart-count').text(response.cart_count);
                        
                        // Show success message
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
        
        // Update cart functionality
        function updateCart(variationId, quantity) {
            $.ajax({
                url: '{{ route('ecommerce.update_cart') }}',
                method: 'POST',
                data: {
                    variation_id: variationId,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to update cart
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
        
        // Remove from cart functionality
        function removeFromCart(variationId) {
            $.ajax({
                url: '{{ route('ecommerce.remove_from_cart') }}',
                method: 'POST',
                data: {
                    variation_id: variationId
                },
                success: function(response) {
                    if (response.success) {
                        // Reload page to update cart
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                }
            });
        }
        
        // Product gallery functionality
        $(document).ready(function() {
            $('.product-gallery .thumbnail').on('click', function() {
                var src = $(this).attr('src');
                $('.product-gallery .main-image').attr('src', src);
                $('.product-gallery .thumbnail').removeClass('active');
                $(this).addClass('active');
            });

            // Toggle Departments Sidebar
            $('#all-departments-btn').on('click', function(e) {
                e.stopPropagation();
                $('#all-departments-sidebar').toggleClass('show');
            });

            // Close sidebar when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.departments-menu').length) {
                    $('#all-departments-sidebar').removeClass('show');
                }
            });
        });
    </script>
    
    @yield('scripts')
</body>
</html>