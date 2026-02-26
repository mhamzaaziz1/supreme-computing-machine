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
    
    <!-- Custom styles -->
    <style>
        :root {
            --primary-color: #3490dc;
            --secondary-color: #6c757d;
            --accent-color: #f39c12;
            --success-color: #38c172;
            --danger-color: #e3342f;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-nav .nav-link {
            font-weight: 500;
        }
        
        .dropdown-menu {
            border-radius: 0;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2779bd;
            border-color: #2779bd;
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .product-card {
            height: 100%;
        }
        
        .product-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        .product-card .card-title {
            font-weight: 600;
            font-size: 1rem;
        }
        
        .product-card .price {
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .product-card .original-price {
            text-decoration: line-through;
            color: var(--secondary-color);
            font-size: 0.875rem;
        }
        
        .badge-sale {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--danger-color);
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
        }
        
        .badge-new {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--success-color);
            color: white;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
        }
        
        .footer {
            background-color: var(--dark-color);
            color: white;
            padding: 3rem 0;
        }
        
        .footer h5 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .footer ul {
            list-style: none;
            padding-left: 0;
        }
        
        .footer ul li {
            margin-bottom: 0.5rem;
        }
        
        .footer ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
        }
        
        .footer ul li a:hover {
            color: white;
        }
        
        .footer-bottom {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1rem 0;
        }
        
        .cart-icon {
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0.75rem 0;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
        }
        
        /* Mobile sticky cart */
        @media (max-width: 767.98px) {
            .sticky-cart {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background-color: white;
                box-shadow: 0 -0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                padding: 0.5rem 1rem;
                z-index: 1000;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .sticky-cart .btn {
                padding: 0.375rem 0.75rem;
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
        <!-- Top bar -->
        <div class="bg-dark text-white py-2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small>Free shipping on orders over $50</small>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <small>
                            <a href="{{ route('ecommerce.help') }}" class="text-white me-3">Help</a>
                            <a href="{{ route('ecommerce.contact') }}" class="text-white me-3">Contact</a>
                            <a href="{{ route('ecommerce.track_order') }}" class="text-white">Track Order</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('ecommerce.home') }}">
                    {{ config('app.name', 'UltimatePOS') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ecommerce.home') ? 'active' : '' }}" href="{{ route('ecommerce.home') }}">Home</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownCategories" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categories
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownCategories">
                                @foreach($categories ?? [] as $category)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('ecommerce.products', ['category_id' => $category->id]) }}">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ecommerce.products') ? 'active' : '' }}" href="{{ route('ecommerce.products') }}">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('ecommerce.about') ? 'active' : '' }}" href="{{ route('ecommerce.about') }}">About</a>
                        </li>
                    </ul>
                    <form class="d-flex me-3" action="{{ route('ecommerce.products') }}" method="GET">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search products..." aria-label="Search" value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Search</button>
                    </form>
                    <ul class="navbar-nav">
                        <li class="nav-item me-3">
                            <a class="nav-link cart-icon" href="{{ route('ecommerce.cart') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="cart-count">{{ count(session('cart', [])) }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('ecommerce.account') }}">
                                <i class="fas fa-user"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    
    <!-- Mobile sticky cart (visible on mobile only) -->
    <div class="sticky-cart d-md-none">
        <div>
            <span class="fw-bold">{{ count(session('cart', [])) }} items</span>
        </div>
        <a href="{{ route('ecommerce.cart') }}" class="btn btn-primary">
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
        });
    </script>
    
    @yield('scripts')
</body>
</html>