@extends('layouts.ecommerce')

@section('title', 'Products')

@section('content')
    <div class="container mt-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 pb-2 border-bottom">Filters</h5>
                        <form action="{{ route('ecommerce.products') }}" method="GET">
                            <!-- Categories Filter -->
                            <div class="mb-4">
                                <h6 class="fw-bold">Categories</h6>
                                <div class="mt-2">
                                    @foreach($categories as $category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="category_id" id="category_{{ $category->id }}" value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                        @if($category->sub_categories->isNotEmpty())
                                            @foreach($category->sub_categories as $sub_category)
                                                <div class="form-check ms-3">
                                                    <input class="form-check-input" type="radio" name="category_id" id="category_{{ $sub_category->id }}" value="{{ $sub_category->id }}" {{ request('category_id') == $sub_category->id ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="category_{{ $sub_category->id }}">
                                                        {{ $sub_category->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Brands Filter -->
                            <div class="mb-4">
                                <h6 class="fw-bold">Brands</h6>
                                <div class="mt-2">
                                    @foreach($brands as $brand)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="brand_id" id="brand_{{ $brand->id }}" value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'checked' : '' }}>
                                            <label class="form-check-label" for="brand_{{ $brand->id }}">
                                                {{ $brand->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <!-- Price Range Filter -->
                            <div class="mb-4">
                                <h6 class="fw-bold">Price Range</h6>
                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="min_price" placeholder="Min" value="{{ request('min_price') }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control form-control-sm" name="max_price" placeholder="Max" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields to preserve other query parameters -->
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            
                            @if(request('sort_by'))
                                <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                            @endif
                            
                            <!-- Apply Filters Button -->
                            <button type="submit" class="btn btn-theme w-100">Apply Filters</button>
                            
                            <!-- Reset Filters Link -->
                            <a href="{{ route('ecommerce.products') }}" class="btn btn-outline-secondary w-100 mt-2">Reset Filters</a>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-md-9">
                <!-- Search and Sort Controls -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <form action="{{ route('ecommerce.products') }}" method="GET" class="d-flex">
                                    <!-- Preserve other query parameters -->
                                    @if(request('category_id'))
                                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                                    @endif
                                    
                                    @if(request('brand_id'))
                                        <input type="hidden" name="brand_id" value="{{ request('brand_id') }}">
                                    @endif
                                    
                                    @if(request('min_price'))
                                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                    @endif
                                    
                                    @if(request('max_price'))
                                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                    @endif
                                    
                                    @if(request('sort_by'))
                                        <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                                    @endif
                                    
                                    <input type="text" class="form-control me-2" name="search" placeholder="Search products..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-theme px-4">Search</button>
                                </form>
                            </div>
                            <div class="col-md-6 d-flex justify-content-md-end">
                                <div class="d-flex align-items-center">
                                    <label for="sort_by" class="me-2">Sort by:</label>
                                    <select id="sort_by" class="form-select" onchange="updateSortBy(this.value)">
                                        <option value="name_asc" {{ request('sort_by') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                        <option value="name_desc" {{ request('sort_by') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>Price (Low to High)</option>
                                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>Price (High to Low)</option>
                                        <option value="date_desc" {{ request('sort_by', 'date_desc') == 'date_desc' ? 'selected' : '' }}>Newest First</option>
                                        <option value="date_asc" {{ request('sort_by') == 'date_asc' ? 'selected' : '' }}>Oldest First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Active Filters -->
                @if(request('category_id') || request('brand_id') || request('min_price') || request('max_price') || request('search'))
                    <div class="card mb-4">
                        <div class="card-body">
                            <h6 class="mb-3">Active Filters:</h6>
                            <div class="d-flex flex-wrap">
                                @if(request('category_id'))
                                    @php
                                        $category_name = '';
                                        foreach($categories as $category) {
                                            if($category->id == request('category_id')) {
                                                $category_name = $category->name;
                                                break;
                                            }
                                            foreach($category->sub_categories as $sub_category) {
                                                if($sub_category->id == request('category_id')) {
                                                    $category_name = $sub_category->name;
                                                    break 2;
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="badge bg-primary me-2 mb-2 p-2">
                                        Category: {{ $category_name }}
                                        <a href="{{ route('ecommerce.products', array_merge(request()->except('category_id'), ['page' => 1])) }}" class="text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                
                                @if(request('brand_id'))
                                    @php
                                        $brand_name = '';
                                        foreach($brands as $brand) {
                                            if($brand->id == request('brand_id')) {
                                                $brand_name = $brand->name;
                                                break;
                                            }
                                        }
                                    @endphp
                                    <span class="badge bg-primary me-2 mb-2 p-2">
                                        Brand: {{ $brand_name }}
                                        <a href="{{ route('ecommerce.products', array_merge(request()->except('brand_id'), ['page' => 1])) }}" class="text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                
                                @if(request('min_price') || request('max_price'))
                                    <span class="badge bg-primary me-2 mb-2 p-2">
                                        Price: 
                                        @if(request('min_price') && request('max_price'))
                                            ${{ request('min_price') }} - ${{ request('max_price') }}
                                        @elseif(request('min_price'))
                                            Min ${{ request('min_price') }}
                                        @elseif(request('max_price'))
                                            Max ${{ request('max_price') }}
                                        @endif
                                        <a href="{{ route('ecommerce.products', array_merge(request()->except(['min_price', 'max_price']), ['page' => 1])) }}" class="text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                
                                @if(request('search'))
                                    <span class="badge bg-primary me-2 mb-2 p-2">
                                        Search: "{{ request('search') }}"
                                        <a href="{{ route('ecommerce.products', array_merge(request()->except('search'), ['page' => 1])) }}" class="text-white ms-1">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Products Count -->
                <div class="mb-3">
                    <p class="mb-0">Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products</p>
                </div>
                
                <!-- Products Grid -->
                <div class="row g-4">
                    @forelse($products as $product)
                        <div class="col-6 col-md-4 col-lg-4 mb-4">
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
                                    @if($product->on_sale)
                                        <span class="badge position-absolute top-0 start-0 m-2 z-1" style="background: var(--accent-red); font-size: 10px; font-weight: 700;">SALE</span>
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
                                        @if($product->on_sale)
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
                        <div class="col-12">
                            <div class="alert alert-info">
                                No products found matching your criteria. Try adjusting your filters or search terms.
                            </div>
                        </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function updateSortBy(value) {
        // Get current URL
        let url = new URL(window.location.href);
        
        // Set the sort_by parameter
        url.searchParams.set('sort_by', value);
        
        // Reset to page 1 when sorting changes
        url.searchParams.set('page', 1);
        
        // Redirect to the new URL
        window.location.href = url.toString();
    }
</script>
@endsection