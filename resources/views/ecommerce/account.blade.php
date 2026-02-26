@extends('layouts.ecommerce')

@section('title', 'My Account')

@section('content')
    <div class="container mt-4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ecommerce.home') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Account</li>
            </ol>
        </nav>

        <h1 class="mb-4">My Account</h1>

        <div class="row">
            <!-- Account Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="account-sidebar">
                    <h5 class="account-sidebar-title">Account Navigation</h5>
                    <div class="nav flex-column">
                        <a href="#dashboard" class="nav-link active" data-bs-toggle="tab">Dashboard</a>
                        <a href="#orders" class="nav-link" data-bs-toggle="tab">Orders</a>
                        <a href="#addresses" class="nav-link" data-bs-toggle="tab">Addresses</a>
                        <a href="#wishlist" class="nav-link" data-bs-toggle="tab">Wishlist</a>
                        <a href="#profile" class="nav-link" data-bs-toggle="tab">Account Details</a>
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Content -->
            <div class="col-lg-9">
                <div class="tab-content">
                    <!-- Dashboard -->
                    <div class="tab-pane fade show active" id="dashboard">
                        <div class="account-content">
                            <h4 class="account-content-title">Dashboard</h4>
                            <p>Hello <strong>{{ $user->user_full_name }}</strong> (not {{ $user->first_name }}? <a href="{{ route('logout') }}">Logout</a>)</p>
                            <p>From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>

                            <div class="row mt-4">
                                <div class="col-md-4 mb-4">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <i class="fas fa-shopping-bag fa-3x mb-3 text-primary"></i>
                                            <h5 class="card-title">Orders</h5>
                                            <p class="card-text">View and track your orders</p>
                                            <a href="#orders" class="btn btn-outline-primary" data-bs-toggle="tab">View Orders</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <i class="fas fa-map-marker-alt fa-3x mb-3 text-primary"></i>
                                            <h5 class="card-title">Addresses</h5>
                                            <p class="card-text">Manage your addresses</p>
                                            <a href="#addresses" class="btn btn-outline-primary" data-bs-toggle="tab">View Addresses</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card text-center h-100">
                                        <div class="card-body">
                                            <i class="fas fa-user fa-3x mb-3 text-primary"></i>
                                            <h5 class="card-title">Account Details</h5>
                                            <p class="card-text">Edit your account information</p>
                                            <a href="#profile" class="btn btn-outline-primary" data-bs-toggle="tab">Edit Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-4 mb-3">Recent Orders</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders->take(3) as $order)
                                        <tr>
                                            <td>#{{ $order->invoice_no }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->transaction_date)->format('M d, Y') }}</td>
                                            <td>
                                                @if($order->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($order->payment_status == 'partial')
                                                    <span class="badge bg-info">Partially Paid</span>
                                                @elseif($order->payment_status == 'due')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($order->final_total, 2) }}</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary view-order" data-order-id="{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#orderDetailsModal{{ $order->id }}">View</a></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No orders found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <a href="#orders" class="btn btn-primary" data-bs-toggle="tab">View All Orders</a>
                            </div>
                        </div>
                    </div>

                    <!-- Orders -->
                    <div class="tab-pane fade" id="orders">
                        <div class="account-content">
                            <h4 class="account-content-title">My Orders</h4>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                        <tr>
                                            <td>#{{ $order->invoice_no }}</td>
                                            <td>{{ \Carbon\Carbon::parse($order->transaction_date)->format('M d, Y') }}</td>
                                            <td>
                                                @if($order->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($order->payment_status == 'partial')
                                                    <span class="badge bg-info">Partially Paid</span>
                                                @elseif($order->payment_status == 'due')
                                                    <span class="badge bg-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ number_format($order->final_total, 2) }}</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary view-order" data-order-id="{{ $order->id }}" data-bs-toggle="modal" data-bs-target="#orderDetailsModal{{ $order->id }}">View</a></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No orders found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Order Details Modals -->
                            @foreach($orders as $order)
                            <div class="modal fade" id="orderDetailsModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderDetailsModalLabel{{ $order->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="orderDetailsModalLabel{{ $order->id }}">Order #{{ $order->invoice_no }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <h6>Order Information</h6>
                                                    <p class="mb-1"><strong>Order Number:</strong> #{{ $order->invoice_no }}</p>
                                                    <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($order->transaction_date)->format('M d, Y') }}</p>
                                                    <p class="mb-1"><strong>Status:</strong> 
                                                        @if($order->payment_status == 'paid')
                                                            <span class="badge bg-success">Paid</span>
                                                        @elseif($order->payment_status == 'partial')
                                                            <span class="badge bg-info">Partially Paid</span>
                                                        @elseif($order->payment_status == 'due')
                                                            <span class="badge bg-warning">Pending</span>
                                                        @else
                                                            <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                                        @endif
                                                    </p>
                                                    <p class="mb-1"><strong>Payment Method:</strong> 
                                                        @if($order->payment_lines->isNotEmpty())
                                                            {{ ucfirst($order->payment_lines->first()->method) }}
                                                        @else
                                                            Not specified
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Shipping Address</h6>
                                                    @php
                                                        $shipping_address = $order->shipping_address(true);
                                                    @endphp
                                                    @if(!empty($shipping_address))
                                                        @if(!empty($shipping_address['name']))
                                                            <p class="mb-1">{{ $shipping_address['name'] }}</p>
                                                        @endif
                                                        @if(!empty($shipping_address['address_line_1']))
                                                            <p class="mb-1">{{ $shipping_address['address_line_1'] }}</p>
                                                        @endif
                                                        @if(!empty($shipping_address['address_line_2']))
                                                            <p class="mb-1">{{ $shipping_address['address_line_2'] }}</p>
                                                        @endif
                                                        <p class="mb-1">
                                                            @if(!empty($shipping_address['city']))
                                                                {{ $shipping_address['city'] }}, 
                                                            @endif
                                                            @if(!empty($shipping_address['state']))
                                                                {{ $shipping_address['state'] }} 
                                                            @endif
                                                            @if(!empty($shipping_address['zipcode']))
                                                                {{ $shipping_address['zipcode'] }}
                                                            @endif
                                                        </p>
                                                        @if(!empty($shipping_address['country']))
                                                            <p class="mb-1">{{ $shipping_address['country'] }}</p>
                                                        @endif
                                                    @else
                                                        <p class="mb-1">No shipping address provided</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <h6>Order Items</h6>
                                            <div class="table-responsive">
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
                                                        @foreach($order->sell_lines as $line)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    @if($line->product && $line->product->image)
                                                                        <img src="{{ asset('storage/products/' . $line->product->image) }}" class="me-3" alt="Product Image" style="width: 50px; height: 50px; object-fit: cover;">
                                                                    @else
                                                                        <img src="https://via.placeholder.com/50x50" class="me-3" alt="Product Image">
                                                                    @endif
                                                                    <div>
                                                                        <p class="mb-0">{{ $line->product ? $line->product->name : 'Unknown Product' }}</p>
                                                                        @if($line->variations)
                                                                            <small class="text-muted">Variation: {{ $line->variations->name }}</small>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ number_format($line->unit_price_inc_tax, 2) }}</td>
                                                            <td>{{ $line->quantity }}</td>
                                                            <td class="text-end">{{ number_format($line->unit_price_inc_tax * $line->quantity, 2) }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                                            <td class="text-end">{{ number_format($order->total_before_tax, 2) }}</td>
                                                        </tr>
                                                        @if($order->shipping_charges > 0)
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Shipping:</strong></td>
                                                            <td class="text-end">{{ number_format($order->shipping_charges, 2) }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($order->tax_amount > 0)
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                                                            <td class="text-end">{{ number_format($order->tax_amount, 2) }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                            <td class="text-end"><strong>{{ number_format($order->final_total, 2) }}</strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <a href="{{ route('ecommerce.track_order') }}?order_id={{ $order->id }}" class="btn btn-primary">Track Order</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Addresses -->
                    <div class="tab-pane fade" id="addresses">
                        <div class="account-content">
                            <h4 class="account-content-title">My Addresses</h4>

                            <div class="row">
                                @if(isset($addresses['shipping']))
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Shipping Address</h5>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editShippingAddressModal">Edit</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($addresses['shipping']['name']))
                                                <p class="mb-1">{{ $addresses['shipping']['name'] }}</p>
                                            @endif
                                            @if(!empty($addresses['shipping']['address_line_1']))
                                                <p class="mb-1">{{ $addresses['shipping']['address_line_1'] }}</p>
                                            @endif
                                            @if(!empty($addresses['shipping']['address_line_2']))
                                                <p class="mb-1">{{ $addresses['shipping']['address_line_2'] }}</p>
                                            @endif
                                            <p class="mb-1">
                                                @if(!empty($addresses['shipping']['city']))
                                                    {{ $addresses['shipping']['city'] }}, 
                                                @endif
                                                @if(!empty($addresses['shipping']['state']))
                                                    {{ $addresses['shipping']['state'] }} 
                                                @endif
                                                @if(!empty($addresses['shipping']['zipcode']))
                                                    {{ $addresses['shipping']['zipcode'] }}
                                                @endif
                                            </p>
                                            @if(!empty($addresses['shipping']['country']))
                                                <p class="mb-1">{{ $addresses['shipping']['country'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(isset($addresses['billing']))
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Billing Address</h5>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editBillingAddressModal">Edit</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if(!empty($addresses['billing']['name']))
                                                <p class="mb-1">{{ $addresses['billing']['name'] }}</p>
                                            @endif
                                            @if(!empty($addresses['billing']['address_line_1']))
                                                <p class="mb-1">{{ $addresses['billing']['address_line_1'] }}</p>
                                            @endif
                                            @if(!empty($addresses['billing']['address_line_2']))
                                                <p class="mb-1">{{ $addresses['billing']['address_line_2'] }}</p>
                                            @endif
                                            <p class="mb-1">
                                                @if(!empty($addresses['billing']['city']))
                                                    {{ $addresses['billing']['city'] }}, 
                                                @endif
                                                @if(!empty($addresses['billing']['state']))
                                                    {{ $addresses['billing']['state'] }} 
                                                @endif
                                                @if(!empty($addresses['billing']['zipcode']))
                                                    {{ $addresses['billing']['zipcode'] }}
                                                @endif
                                            </p>
                                            @if(!empty($addresses['billing']['country']))
                                                <p class="mb-1">{{ $addresses['billing']['country'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if(!isset($addresses['shipping']) && !isset($addresses['billing']))
                                <div class="col-md-12 mb-4">
                                    <div class="alert alert-info">
                                        You don't have any saved addresses yet. Add an address to make checkout faster.
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-dashed">
                                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                                            <i class="fas fa-plus-circle fa-3x mb-3 text-primary"></i>
                                            <h5>Add New Address</h5>
                                            <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addAddressModal">Add Address</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Address Modal -->
                            <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="address_name" class="form-label">Full Name</label>
                                                    <input type="text" class="form-control" id="address_name" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="address_line" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="address_line" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="address_city" class="form-label">City</label>
                                                        <input type="text" class="form-control" id="address_city" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="address_state" class="form-label">State/Province</label>
                                                        <input type="text" class="form-control" id="address_state" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="address_zip" class="form-label">Zip/Postal Code</label>
                                                        <input type="text" class="form-control" id="address_zip" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="address_country" class="form-label">Country</label>
                                                        <select class="form-select" id="address_country" required>
                                                            <option value="">Select Country</option>
                                                            <option value="US">United States</option>
                                                            <option value="CA">Canada</option>
                                                            <option value="UK">United Kingdom</option>
                                                            <option value="AU">Australia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="address_phone" class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="address_phone" required>
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" id="address_default">
                                                    <label class="form-check-label" for="address_default">Set as default address</label>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary">Save Address</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Address Modal -->
                            <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editAddressModalLabel">Edit Address</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                                <div class="mb-3">
                                                    <label for="edit_address_name" class="form-label">Full Name</label>
                                                    <input type="text" class="form-control" id="edit_address_name" value="John Doe" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_address_line" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="edit_address_line" value="123 Main St" required>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_address_city" class="form-label">City</label>
                                                        <input type="text" class="form-control" id="edit_address_city" value="New York" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_address_state" class="form-label">State/Province</label>
                                                        <input type="text" class="form-control" id="edit_address_state" value="NY" required>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_address_zip" class="form-label">Zip/Postal Code</label>
                                                        <input type="text" class="form-control" id="edit_address_zip" value="10001" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="edit_address_country" class="form-label">Country</label>
                                                        <select class="form-select" id="edit_address_country" required>
                                                            <option value="">Select Country</option>
                                                            <option value="US" selected>United States</option>
                                                            <option value="CA">Canada</option>
                                                            <option value="UK">United Kingdom</option>
                                                            <option value="AU">Australia</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="edit_address_phone" class="form-label">Phone Number</label>
                                                    <input type="tel" class="form-control" id="edit_address_phone" value="(123) 456-7890" required>
                                                </div>
                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" id="edit_address_default" checked>
                                                    <label class="form-check-label" for="edit_address_default">Set as default address</label>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-primary">Update Address</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wishlist -->
                    <div class="tab-pane fade" id="wishlist">
                        <div class="account-content">
                            <h4 class="account-content-title">My Wishlist</h4>

                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <div class="card product-card h-100">
                                        <span class="badge-sale">Sale</span>
                                        <img src="https://via.placeholder.com/300x300?text=Product+Image" class="card-img-top" alt="Product Image">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">
                                                <a href="#" class="text-decoration-none text-dark">Sample Product 1</a>
                                            </h5>
                                            <p class="card-text small">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            <div class="mt-auto">
                                                <p class="price mb-0">
                                                    $19.99
                                                    <span class="original-price">$24.99</span>
                                                </p>
                                                <div class="d-flex mt-2">
                                                    <button class="btn btn-primary btn-sm flex-grow-1 me-2">Add to Cart</button>
                                                    <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card product-card h-100">
                                        <img src="https://via.placeholder.com/300x300?text=Product+Image" class="card-img-top" alt="Product Image">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">
                                                <a href="#" class="text-decoration-none text-dark">Sample Product 2</a>
                                            </h5>
                                            <p class="card-text small">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            <div class="mt-auto">
                                                <p class="price mb-0">$29.99</p>
                                                <div class="d-flex mt-2">
                                                    <button class="btn btn-primary btn-sm flex-grow-1 me-2">Add to Cart</button>
                                                    <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-4">
                                    <div class="card product-card h-100">
                                        <img src="https://via.placeholder.com/300x300?text=Product+Image" class="card-img-top" alt="Product Image">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title">
                                                <a href="#" class="text-decoration-none text-dark">Sample Product 3</a>
                                            </h5>
                                            <p class="card-text small">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                                            <div class="mt-auto">
                                                <p class="price mb-0">$39.99</p>
                                                <div class="d-flex mt-2">
                                                    <button class="btn btn-primary btn-sm flex-grow-1 me-2">Add to Cart</button>
                                                    <button class="btn btn-outline-danger btn-sm"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="tab-pane fade" id="profile">
                        <div class="account-content">
                            <h4 class="account-content-title">Account Details</h4>

                            <form action="{{ route('user.updateProfile') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="surname" class="form-label">Surname</label>
                                        <input type="text" class="form-control" id="surname" name="surname" value="{{ $user->surname }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="contact_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number" value="{{ $user->contact_number }}">
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">Save Profile</button>
                                </div>
                            </form>

                            <h5 class="mt-4 mb-3">Password Change</h5>

                            <form action="{{ route('user.updatePassword') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <small class="text-muted">Password must be at least 8 characters long</small>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle tab navigation from dashboard cards
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function(element) {
            element.addEventListener('click', function(event) {
                const tabId = this.getAttribute('href');
                const tab = new bootstrap.Tab(document.querySelector(`a[href="${tabId}"]`));
                tab.show();
            });
        });
    });
</script>
@endsection
