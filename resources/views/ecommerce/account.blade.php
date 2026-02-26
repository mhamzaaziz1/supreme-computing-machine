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
                        <a href="{{ route('ecommerce.home') }}" class="nav-link">Logout</a>
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
                            <p>Hello <strong>John Doe</strong> (not John? <a href="{{ route('ecommerce.home') }}">Logout</a>)</p>
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
                                        <tr>
                                            <td>#ORD-12345</td>
                                            <td>June 15, 2023</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>$74.52</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-12346</td>
                                            <td>June 10, 2023</td>
                                            <td><span class="badge bg-info">Shipped</span></td>
                                            <td>$125.00</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-12347</td>
                                            <td>June 5, 2023</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>$49.99</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
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
                                        <tr>
                                            <td>#ORD-12345</td>
                                            <td>June 15, 2023</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>$74.52</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-12346</td>
                                            <td>June 10, 2023</td>
                                            <td><span class="badge bg-info">Shipped</span></td>
                                            <td>$125.00</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-12347</td>
                                            <td>June 5, 2023</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>$49.99</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-12348</td>
                                            <td>May 28, 2023</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>$89.99</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                        <tr>
                                            <td>#ORD-12349</td>
                                            <td>May 20, 2023</td>
                                            <td><span class="badge bg-success">Delivered</span></td>
                                            <td>$35.50</td>
                                            <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Order Details Modal -->
                            <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="orderDetailsModalLabel">Order #ORD-12345</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-4">
                                                <div class="col-md-6">
                                                    <h6>Order Information</h6>
                                                    <p class="mb-1"><strong>Order Number:</strong> #ORD-12345</p>
                                                    <p class="mb-1"><strong>Date:</strong> June 15, 2023</p>
                                                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Delivered</span></p>
                                                    <p class="mb-1"><strong>Payment Method:</strong> Credit Card</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Shipping Address</h6>
                                                    <p class="mb-1">John Doe</p>
                                                    <p class="mb-1">123 Main St</p>
                                                    <p class="mb-1">New York, NY 10001</p>
                                                    <p class="mb-1">United States</p>
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
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Track Order</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Addresses -->
                    <div class="tab-pane fade" id="addresses">
                        <div class="account-content">
                            <h4 class="account-content-title">My Addresses</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Shipping Address</h5>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editAddressModal">Edit</button>
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1">John Doe</p>
                                            <p class="mb-1">123 Main St</p>
                                            <p class="mb-1">New York, NY 10001</p>
                                            <p class="mb-1">United States</p>
                                            <p class="mb-1">Phone: (123) 456-7890</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Billing Address</h5>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editAddressModal">Edit</button>
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1">John Doe</p>
                                            <p class="mb-1">123 Main St</p>
                                            <p class="mb-1">New York, NY 10001</p>
                                            <p class="mb-1">United States</p>
                                            <p class="mb-1">Phone: (123) 456-7890</p>
                                        </div>
                                    </div>
                                </div>
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
                            
                            <form>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="profile_first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="profile_first_name" value="John" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="profile_last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="profile_last_name" value="Doe" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="profile_email" value="john.doe@example.com" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="profile_phone" value="(123) 456-7890">
                                </div>
                                
                                <h5 class="mt-4 mb-3">Password Change</h5>
                                
                                <div class="mb-3">
                                    <label for="profile_current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="profile_current_password">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="profile_new_password">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="profile_confirm_password">
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
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