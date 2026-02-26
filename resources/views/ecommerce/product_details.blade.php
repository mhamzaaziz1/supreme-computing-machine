@extends('layouts.ecommerce')

@section('title', $product->name)

@section('content')
    <div class="container mt-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.home') }}">Home</a></li>
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('ecommerce.products', ['category_id' => $product->category->id]) }}">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @endif
                @if($product->sub_category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('ecommerce.products', ['category_id' => $product->sub_category->id]) }}">
                            {{ $product->sub_category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
        
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6 mb-4">
                <div class="product-gallery">
                    @php
                        $images = [];
                        $main_image = 'https://via.placeholder.com/600x600?text=Product+Image';
                        
                        // Collect all variation images
                        foreach($product->product_variations as $product_variation) {
                            foreach($product_variation->variations as $variation) {
                                foreach($variation->media as $media) {
                                    $images[] = $media->display_url;
                                }
                            }
                        }
                        
                        // Set main image if we have any
                        if(count($images) > 0) {
                            $main_image = $images[0];
                        }
                    @endphp
                    
                    <img src="{{ $main_image }}" class="main-image img-fluid" alt="{{ $product->name }}">
                    
                    @if(count($images) > 1)
                        <div class="thumbnails mt-3">
                            @foreach($images as $index => $image)
                                <img src="{{ $image }}" class="thumbnail {{ $index === 0 ? 'active' : '' }}" alt="{{ $product->name }} - Image {{ $index + 1 }}">
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Product Details -->
            <div class="col-md-6">
                <div class="product-details">
                    <h1 class="product-title">{{ $product->name }}</h1>
                    
                    @if($product->brand)
                        <p class="text-muted mb-2">Brand: <a href="{{ route('ecommerce.products', ['brand_id' => $product->brand->id]) }}">{{ $product->brand->name }}</a></p>
                    @endif
                    
                    <div class="product-price mb-3">
                        ${{ number_format($product->sell_price_inc_tax, 2) }}
                        @if($product->on_sale)
                            <span class="product-original-price">${{ number_format($product->sell_price_inc_tax * 1.2, 2) }}</span>
                            <span class="badge bg-danger ms-2">{{ round(100 - (($product->sell_price_inc_tax / ($product->sell_price_inc_tax * 1.2)) * 100)) }}% OFF</span>
                        @endif
                    </div>
                    
                    @if($product->product_description)
                        <div class="product-description mb-4">
                            <h5>Description</h5>
                            <p>{{ $product->product_description }}</p>
                        </div>
                    @endif
                    
                    <div class="product-meta mb-4">
                        @if($product->sku)
                            <p><strong>SKU:</strong> {{ $product->sku }}</p>
                        @endif
                        
                        @if($product->unit)
                            <p><strong>Unit:</strong> {{ $product->unit->short_name }}</p>
                        @endif
                        
                        <p>
                            <strong>Availability:</strong> 
                            @php
                                $in_stock = false;
                                foreach($product->product_variations as $product_variation) {
                                    foreach($product_variation->variations as $variation) {
                                        if(isset($variation->variation_location_details) && $variation->variation_location_details->qty_available > 0) {
                                            $in_stock = true;
                                            break 2;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if($in_stock)
                                <span class="text-success">In Stock</span>
                            @else
                                <span class="text-danger">Out of Stock</span>
                            @endif
                        </p>
                    </div>
                    
                    <!-- Product Variations -->
                    @if(count($product->product_variations) > 0)
                        <div class="product-variants mb-4">
                            @foreach($product->product_variations as $product_variation)
                                @if($product_variation->name != 'DUMMY')
                                    <h5 class="mb-2">{{ $product_variation->name }}</h5>
                                    <div class="btn-group mb-3" role="group" aria-label="{{ $product_variation->name }}">
                                        @foreach($product_variation->variations as $variation)
                                            <button type="button" class="btn btn-variant" 
                                                data-variation-id="{{ $variation->id }}" 
                                                data-price="{{ $variation->sell_price_inc_tax }}"
                                                data-name="{{ $variation->name }}"
                                                onclick="selectVariation(this)">
                                                {{ $variation->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    
                    <!-- Quantity and Add to Cart -->
                    <div class="product-quantity mb-4">
                        <h5 class="mb-2">Quantity</h5>
                        <div class="d-flex">
                            <div class="input-group me-3" style="width: 130px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="product_quantity" value="1" min="1">
                                <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">+</button>
                            </div>
                            <button class="btn btn-primary flex-grow-1" id="add_to_cart_button" onclick="addToCartFromDetails()">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="additional-info mb-4">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-truck me-2"></i>
                            <span>Free shipping on orders over $50</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-undo me-2"></i>
                            <span>30 days return policy</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lock me-2"></i>
                            <span>Secure checkout</span>
                        </div>
                    </div>
                    
                    <!-- Share -->
                    <div class="product-share">
                        <h5 class="mb-2">Share</h5>
                        <div class="d-flex">
                            <a href="#" class="me-2 btn btn-sm btn-outline-primary"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="me-2 btn btn-sm btn-outline-info"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="me-2 btn btn-sm btn-outline-danger"><i class="fab fa-pinterest"></i></a>
                            <a href="#" class="btn btn-sm btn-outline-secondary"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications" type="button" role="tab" aria-controls="specifications" aria-selected="false">Specifications</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                        <div class="row">
                            <div class="col-md-8">
                                @if($product->product_description)
                                    <p>{{ $product->product_description }}</p>
                                @else
                                    <p>No detailed description available for this product.</p>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <img src="{{ $main_image }}" class="img-fluid rounded" alt="{{ $product->name }}">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="specifications" role="tabpanel" aria-labelledby="specifications-tab">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    @if($product->sku)
                                        <tr>
                                            <th style="width: 30%">SKU</th>
                                            <td>{{ $product->sku }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->brand)
                                        <tr>
                                            <th>Brand</th>
                                            <td>{{ $product->brand->name }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->unit)
                                        <tr>
                                            <th>Unit</th>
                                            <td>{{ $product->unit->short_name }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->category)
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ $product->category->name }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->sub_category)
                                        <tr>
                                            <th>Sub Category</th>
                                            <td>{{ $product->sub_category->name }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->weight)
                                        <tr>
                                            <th>Weight</th>
                                            <td>{{ $product->weight }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->product_custom_field1)
                                        <tr>
                                            <th>Custom Field 1</th>
                                            <td>{{ $product->product_custom_field1 }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->product_custom_field2)
                                        <tr>
                                            <th>Custom Field 2</th>
                                            <td>{{ $product->product_custom_field2 }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->product_custom_field3)
                                        <tr>
                                            <th>Custom Field 3</th>
                                            <td>{{ $product->product_custom_field3 }}</td>
                                        </tr>
                                    @endif
                                    
                                    @if($product->product_custom_field4)
                                        <tr>
                                            <th>Custom Field 4</th>
                                            <td>{{ $product->product_custom_field4 }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                        <p>No reviews yet. Be the first to review this product.</p>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Write a Review</h5>
                            </div>
                            <div class="card-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="reviewName" class="form-label">Your Name</label>
                                        <input type="text" class="form-control" id="reviewName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reviewEmail" class="form-label">Your Email</label>
                                        <input type="email" class="form-control" id="reviewEmail" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="rating" id="rating5" value="5">
                                                <label class="form-check-label" for="rating5">5 Stars</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="rating" id="rating4" value="4">
                                                <label class="form-check-label" for="rating4">4 Stars</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="rating" id="rating3" value="3">
                                                <label class="form-check-label" for="rating3">3 Stars</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="rating" id="rating2" value="2">
                                                <label class="form-check-label" for="rating2">2 Stars</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="rating" id="rating1" value="1">
                                                <label class="form-check-label" for="rating1">1 Star</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reviewText" class="form-label">Your Review</label>
                                        <textarea class="form-control" id="reviewText" rows="4" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        @if($related_products->isNotEmpty())
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="text-center mb-4">Related Products</h2>
                    <div class="row">
                        @foreach($related_products as $related_product)
                            <div class="col-6 col-md-3 mb-4">
                                <div class="card product-card h-100">
                                    @if($related_product->on_sale)
                                        <span class="badge-sale">Sale</span>
                                    @endif
                                    
                                    @php
                                        $image_url = 'https://via.placeholder.com/300x300?text=Product+Image';
                                        
                                        // Try to get the first variation image
                                        foreach($related_product->product_variations as $product_variation) {
                                            foreach($product_variation->variations as $variation) {
                                                if($variation->media->isNotEmpty()) {
                                                    $image_url = $variation->media->first()->display_url;
                                                    break 2;
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    <a href="{{ route('ecommerce.product_details', $related_product->id) }}">
                                        <img src="{{ $image_url }}" class="card-img-top" alt="{{ $related_product->name }}">
                                    </a>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="{{ route('ecommerce.product_details', $related_product->id) }}" class="text-decoration-none text-dark">
                                                {{ $related_product->name }}
                                            </a>
                                        </h5>
                                        <p class="card-text small">
                                            {{ Str::limit($related_product->product_description, 50) }}
                                        </p>
                                        <div class="mt-auto">
                                            <p class="price mb-0">
                                                ${{ number_format($related_product->sell_price_inc_tax, 2) }}
                                                @if($related_product->on_sale)
                                                    <span class="original-price">${{ number_format($related_product->sell_price_inc_tax * 1.2, 2) }}</span>
                                                @endif
                                            </p>
                                            <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addToCart('{{ $related_product->product_variations->first()->variations->first()->id }}', 1)">
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
        @endif
        
        <!-- Recently Viewed Products -->
        @php
            $recently_viewed = Session::get('recently_viewed', []);
            $recently_viewed = array_filter($recently_viewed, function($id) use ($product) {
                return $id != $product->id;
            });
            
            if(count($recently_viewed) > 0) {
                $recently_viewed_products = App\Product::whereIn('id', $recently_viewed)
                    ->active()
                    ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
                    ->take(4)
                    ->get();
            } else {
                $recently_viewed_products = collect([]);
            }
        @endphp
        
        @if($recently_viewed_products->isNotEmpty())
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="text-center mb-4">Recently Viewed</h2>
                    <div class="row">
                        @foreach($recently_viewed_products as $recently_viewed_product)
                            <div class="col-6 col-md-3 mb-4">
                                <div class="card product-card h-100">
                                    @if($recently_viewed_product->on_sale)
                                        <span class="badge-sale">Sale</span>
                                    @endif
                                    
                                    @php
                                        $image_url = 'https://via.placeholder.com/300x300?text=Product+Image';
                                        
                                        // Try to get the first variation image
                                        foreach($recently_viewed_product->product_variations as $product_variation) {
                                            foreach($product_variation->variations as $variation) {
                                                if($variation->media->isNotEmpty()) {
                                                    $image_url = $variation->media->first()->display_url;
                                                    break 2;
                                                }
                                            }
                                        }
                                    @endphp
                                    
                                    <a href="{{ route('ecommerce.product_details', $recently_viewed_product->id) }}">
                                        <img src="{{ $image_url }}" class="card-img-top" alt="{{ $recently_viewed_product->name }}">
                                    </a>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="{{ route('ecommerce.product_details', $recently_viewed_product->id) }}" class="text-decoration-none text-dark">
                                                {{ $recently_viewed_product->name }}
                                            </a>
                                        </h5>
                                        <p class="card-text small">
                                            {{ Str::limit($recently_viewed_product->product_description, 50) }}
                                        </p>
                                        <div class="mt-auto">
                                            <p class="price mb-0">
                                                ${{ number_format($recently_viewed_product->sell_price_inc_tax, 2) }}
                                                @if($recently_viewed_product->on_sale)
                                                    <span class="original-price">${{ number_format($recently_viewed_product->sell_price_inc_tax * 1.2, 2) }}</span>
                                                @endif
                                            </p>
                                            <button class="btn btn-primary btn-sm mt-2 w-100" onclick="addToCart('{{ $recently_viewed_product->product_variations->first()->variations->first()->id }}', 1)">
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
        @endif
    </div>
@endsection

@section('scripts')
<script>
    // Global variables
    let selectedVariationId = '{{ $product->product_variations->first()->variations->first()->id }}';
    let selectedVariationPrice = '{{ $product->product_variations->first()->variations->first()->sell_price_inc_tax }}';
    let selectedVariationName = '{{ $product->product_variations->first()->variations->first()->name }}';
    
    // Select variation
    function selectVariation(element) {
        // Update selected variation
        selectedVariationId = element.dataset.variationId;
        selectedVariationPrice = element.dataset.price;
        selectedVariationName = element.dataset.name;
        
        // Update UI
        document.querySelectorAll('.btn-variant').forEach(btn => {
            btn.classList.remove('active');
        });
        element.classList.add('active');
        
        // Update price display
        document.querySelector('.product-price').innerHTML = 
            '$' + parseFloat(selectedVariationPrice).toFixed(2);
    }
    
    // Increment quantity
    function incrementQuantity() {
        const quantityInput = document.getElementById('product_quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
    }
    
    // Decrement quantity
    function decrementQuantity() {
        const quantityInput = document.getElementById('product_quantity');
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    }
    
    // Add to cart from details page
    function addToCartFromDetails() {
        const quantity = parseInt(document.getElementById('product_quantity').value);
        addToCart(selectedVariationId, quantity);
    }
    
    // Product gallery
    document.addEventListener('DOMContentLoaded', function() {
        const thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.querySelector('.main-image');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image
                mainImage.src = this.src;
                
                // Update active state
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
        
        // Initialize first variation as active
        const firstVariant = document.querySelector('.btn-variant');
        if (firstVariant) {
            firstVariant.classList.add('active');
        }
    });
</script>
@endsection