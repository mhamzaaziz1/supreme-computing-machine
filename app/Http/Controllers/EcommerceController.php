<?php

namespace App\Http\Controllers;

use App\Product;
use App\Category;
use App\Brands;
use App\Variation;
use App\Business;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class EcommerceController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $productUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtil $productUtil
     * @param ModuleUtil $moduleUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display e-commerce home page
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $business_id = request()->session()->get('user.business_id');
        //
        // // Get business and its ecommerce settings
        // $business = Business::find($business_id);
        // $ecom_settings = $business->ecom_settings ?: [];
        //
        // // Get store banner from ecommerce settings
        // $store_banner = !empty($ecom_settings['store_banner']) ? $ecom_settings['store_banner'] : null;
        //
        // // Get slider images from ecommerce settings
        // $slider_images = !empty($ecom_settings['slider_images']) ? $ecom_settings['slider_images'] : [];
        //
        // // Get featured products
        // $featured_products = Product::where('business_id', $business_id)
        //                     ->where('featured', 1)
        //                     ->active()
        //                     ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
        //                     ->take(8)
        //                     ->get();
        //
        // // Get new arrivals
        // $new_arrivals = Product::where('business_id', $business_id)
        //                     ->active()
        //                     ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
        //                     ->orderBy('created_at', 'desc')
        //                     ->take(8)
        //                     ->get();
        //
        // // Get best sellers
        // $best_sellers = Product::where('business_id', $business_id)
        //                     ->active()
        //                     ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
        //                     ->take(8)
        //                     ->get();
        //
        // // Get sale products
        // $sale_products = Product::where('business_id', $business_id)
        //                     ->where('on_sale', 1)
        //                     ->active()
        //                     ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
        //                     ->take(8)
        //                     ->get();
        //
        // // Get categories for navigation - only those with images
        // $categories = Category::where('business_id', $business_id)
        //                     ->where('parent_id', 0)
        //                     ->whereNotNull('image')
        //                     ->with('sub_categories')
        //                     ->get();
        //
        // return view('ecommerce.home', compact(
        //     'featured_products',
        //     'new_arrivals',
        //     'best_sellers',
        //     'sale_products',
        //     'categories',
        //     'store_banner',
        //     'slider_images',
        //     'ecom_settings'
        // ));
    }

    /**
     * Display product listing page
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function products(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $category_id = $request->input('category_id', null);
        $brand_id = $request->input('brand_id', null);
        $sort_by = $request->input('sort_by', 'name_asc');
        $search = $request->input('search', null);
        $per_page = $request->input('per_page', 24);

        $query = Product::where('business_id', $business_id)
                    ->active()
                    ->with(['brand', 'category', 'sub_category',
                        'product_variations', 'product_variations.variations',
                        'product_variations.variations.media']);

        // Filter by category
        if (!empty($category_id)) {
            $query->where(function($q) use ($category_id) {
                $q->where('category_id', $category_id)
                  ->orWhere('sub_category_id', $category_id);
            });
        }

        // Filter by brand
        if (!empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        // Search by name or sku
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Sort products
        switch ($sort_by) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('sell_price_inc_tax', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('sell_price_inc_tax', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $products = $query->paginate($per_page);

        // Get categories for filtering
        $categories = Category::where('business_id', $business_id)
                            ->where('parent_id', 0)
                            ->with('sub_categories')
                            ->get();

        // Get brands for filtering
        $brands = Brands::where('business_id', $business_id)->get();

        return view('ecommerce.products', compact(
            'products',
            'categories',
            'brands',
            'category_id',
            'brand_id',
            'sort_by',
            'search',
            'per_page'
        ));
    }

    /**
     * Display product details page
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function productDetails($id)
    {
        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
                    ->where('id', $id)
                    ->active()
                    ->with([
                        'brand',
                        'unit',
                        'category',
                        'sub_category',
                        'product_variations',
                        'product_variations.variations',
                        'product_variations.variations.media',
                        'product_tax'
                    ])
                    ->first();

        if (empty($product)) {
            abort(404);
        }

        // Get related products
        $related_products = Product::where('business_id', $business_id)
                            ->where('id', '!=', $id)
                            ->where(function($q) use ($product) {
                                $q->where('category_id', $product->category_id)
                                  ->orWhere('sub_category_id', $product->category_id)
                                  ->orWhere('brand_id', $product->brand_id);
                            })
                            ->active()
                            ->with(['product_variations', 'product_variations.variations', 'product_variations.variations.media'])
                            ->take(8)
                            ->get();

        // Add to recently viewed
        $recently_viewed = Session::get('recently_viewed', []);

        // Remove if already exists and add to the beginning
        if (($key = array_search($id, $recently_viewed)) !== false) {
            unset($recently_viewed[$key]);
        }

        // Add to the beginning of the array
        array_unshift($recently_viewed, $id);

        // Keep only the last 10 items
        $recently_viewed = array_slice($recently_viewed, 0, 10);

        Session::put('recently_viewed', $recently_viewed);

        return view('ecommerce.product_details', compact('product', 'related_products'));
    }

    /**
     * Display cart page
     *
     * @return \Illuminate\Http\Response
     */
    public function cart()
    {
        $cart = Session::get('cart', []);
        $cart_items = [];
        $total = 0;

        if (!empty($cart)) {
            foreach ($cart as $variation_id => $item) {
                $variation = Variation::with([
                    'product_variation',
                    'product',
                    'product.unit',
                    'media'
                ])->find($variation_id);

                if ($variation) {
                    $cart_items[$variation_id] = [
                        'variation' => $variation,
                        'quantity' => $item['quantity'],
                        'subtotal' => $variation->sell_price_inc_tax * $item['quantity']
                    ];

                    $total += $cart_items[$variation_id]['subtotal'];
                }
            }
        }

        return view('ecommerce.cart', compact('cart_items', 'total'));
    }

    /**
     * Add product to cart
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        $variation_id = $request->input('variation_id');
        $quantity = $request->input('quantity', 1);

        $variation = Variation::find($variation_id);

        if (!$variation) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $cart = Session::get('cart', []);

        // If product already in cart, update quantity
        if (isset($cart[$variation_id])) {
            $cart[$variation_id]['quantity'] += $quantity;
        } else {
            $cart[$variation_id] = [
                'quantity' => $quantity
            ];
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => count($cart)
        ]);
    }

    /**
     * Update cart item quantity
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateCart(Request $request)
    {
        $variation_id = $request->input('variation_id');
        $quantity = $request->input('quantity');

        $cart = Session::get('cart', []);

        if (isset($cart[$variation_id])) {
            if ($quantity > 0) {
                $cart[$variation_id]['quantity'] = $quantity;
            } else {
                unset($cart[$variation_id]);
            }

            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated',
                'cart_count' => count($cart)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Product not found in cart']);
    }

    /**
     * Remove item from cart
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart(Request $request)
    {
        $variation_id = $request->input('variation_id');

        $cart = Session::get('cart', []);

        if (isset($cart[$variation_id])) {
            unset($cart[$variation_id]);
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Product removed from cart',
                'cart_count' => count($cart)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Product not found in cart']);
    }

    /**
     * Display checkout page
     *
     * @return \Illuminate\Http\Response
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('ecommerce.cart')->with('error', 'Your cart is empty');
        }

        $cart_items = [];
        $total = 0;

        foreach ($cart as $variation_id => $item) {
            $variation = Variation::with([
                'product_variation',
                'product',
                'product.unit',
                'media'
            ])->find($variation_id);

            if ($variation) {
                $cart_items[$variation_id] = [
                    'variation' => $variation,
                    'quantity' => $item['quantity'],
                    'subtotal' => $variation->sell_price_inc_tax * $item['quantity']
                ];

                $total += $cart_items[$variation_id]['subtotal'];
            }
        }

        // Get business details
        $business_id = request()->session()->get('user.business_id');
        $business = Business::find($business_id);

        return view('ecommerce.checkout', compact('cart_items', 'total', 'business'));
    }

    /**
     * Process order
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function placeOrder(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer'
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('ecommerce.cart')->with('error', 'Your cart is empty');
        }

        // Process order logic would go here
        // This would typically involve:
        // 1. Creating a contact if the customer doesn't exist
        // 2. Creating a transaction (sale)
        // 3. Creating transaction sell lines for each cart item
        // 4. Processing payment
        // 5. Sending confirmation email

        // For now, we'll just clear the cart and redirect to a thank you page
        Session::forget('cart');

        return redirect()->route('ecommerce.order_confirmation')->with('order_id', 'ORD-' . time());
    }

    /**
     * Display order confirmation page
     *
     * @return \Illuminate\Http\Response
     */
    public function orderConfirmation()
    {
        $order_id = session('order_id');

        if (empty($order_id)) {
            return redirect()->route('ecommerce.home');
        }

        return view('ecommerce.order_confirmation', compact('order_id'));
    }

    /**
     * Display customer account page
     *
     * @return \Illuminate\Http\Response
     */
    public function account()
    {
        // Check if user is logged in
        if (!auth()->check()) {
            return redirect()->route('login')->with('status', 'Please login to access your account');
        }

        $user = auth()->user();
        $business_id = $user->business_id;

        // Get user's orders (transactions of type 'sell' and status 'final')
        $orders = \App\Transaction::where('business_id', $business_id)
                    ->where('type', 'sell')
                    ->where('status', 'final')
                    ->where(function($query) use ($user) {
                        // If user is a customer, only show their orders
                        if (!$user->hasRole('Admin#'.$user->business_id)) {
                            $query->where('contact_id', $user->contact_id);
                        }
                    })
                    ->with(['contact', 'payment_lines', 'sell_lines', 'sell_lines.product', 'sell_lines.variations'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Get user's addresses from the most recent order
        $addresses = [];
        $latest_order = $orders->first();
        if ($latest_order) {
            $shipping_address = $latest_order->shipping_address(true);
            $billing_address = $latest_order->billing_address(true);

            if (!empty($shipping_address)) {
                $addresses['shipping'] = $shipping_address;
            }

            if (!empty($billing_address)) {
                $addresses['billing'] = $billing_address;
            }
        }

        // Get user's wishlist (if implemented)
        $wishlist = []; // Placeholder for wishlist implementation

        return view('ecommerce.account', compact('user', 'orders', 'addresses', 'wishlist'));
    }

    /**
     * Display order tracking page
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function trackOrder(Request $request)
    {
        $order_id = $request->input('order_id');

        // Logic to fetch order details would go here

        return view('ecommerce.track_order', compact('order_id'));
    }

    /**
     * Display help/FAQ page
     *
     * @return \Illuminate\Http\Response
     */
    public function help()
    {
        return view('ecommerce.help');
    }

    /**
     * Display contact us page
     *
     * @return \Illuminate\Http\Response
     */
    public function contact()
    {
        return view('ecommerce.contact');
    }

    /**
     * Display about us page
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        return view('ecommerce.about');
    }

    /**
     * Display terms and conditions page
     *
     * @return \Illuminate\Http\Response
     */
    public function terms()
    {
        return view('ecommerce.terms');
    }

    /**
     * Display privacy policy page
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        return view('ecommerce.privacy');
    }
}
