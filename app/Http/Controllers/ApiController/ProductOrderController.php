<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\CustomerAddressModel;
use App\Models\Info;
use App\Models\OrderDetails;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\SettingModel;
use App\Models\TapModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Finally_;
use Psy\Readline\Hoa\ExceptionIdle;
use Validator;
use stdClass;
use Ixudra\Curl\Facades\Curl;
use function GuzzleHttp\json_encode;
class ProductOrderController extends Controller
{
    protected $res;

    function __construct()
    {
        $this->res = new stdClass();
    }
    /**
     * Display a listing of the resource.
     */
    public function getAddToOrder(Request $request)
    {
        $searchTerm = $request->input('search');
    
        $query = OrderModel::where('customer_id', Auth::id())
            ->with('product');
    
        if ($searchTerm) {
            $query->whereHas('product', function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%');
            });
        }
    
        $orders = $query->paginate(10);
    
        return response()->json($orders);
    }
    

 

    /**
     * Show the form for creating a new resource.
     */
    public function orderCount()
    {
        $orderCount = AddToCart::where('customer_id',Auth::id())->groupBy('product_id')->count();
        return response()->json(['order_count'=>$orderCount]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validate($request, [
                'address_id' => 'required|integer',
                'bill_amount' => 'required|integer',
                'cart_data' => 'required|array',
                'cart_data.*.product_id' => 'required|integer',
                'cart_data.*.size_id' => 'required|integer',
                'cart_data.*.color_id' => 'required|integer',
                'cart_data.*.total_amount' => 'required|integer',
                'cart_data.*.quantity' => 'required|integer|min:1',
            ]);
    
            // Generate a 5-digit invoice ID
            $invoiceID = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    
            $user = Auth::user();
            $totalAmount = 0;
    
            // To track orders by designer
            $ordersByDesigner = [];
            // To track processed product combinations
            $processedCombinations = [];
    
            // Loop through the cart data to create orders
            foreach ($data['cart_data'] as $item) {
                $product = ProductModel::find($item['product_id']);
    
                if ($product) {
                    // $productPrice = $product->price ?? 0;
                    // $itemTotal = $productPrice * $item['quantity'];
                    // $totalAmount += $itemTotal;
    
                    // Check if the designer (created_by) already has an order created
                    if (!isset($ordersByDesigner[$product->created_by])) {
                        // If not, create a new order for this designer
                        $order = OrderModel::create([
                            'customer_id' => $user->id,
                            'invoice_id' => $invoiceID,
                            'desginer_id' => $product->created_by,
                            'total_amount' => 0, // Total will be updated later
                        ]);
    
                        // Store the order ID in the array, indexed by designer ID
                        $ordersByDesigner[$product->created_by] = [
                            'order' => $order,
                            'total_amount' => 0, // Initialize total amount for this order
                        ];
                    } else {
                        // Retrieve the existing order for this designer
                        $order = $ordersByDesigner[$product->created_by]['order'];
                    }
    
                    // Create a unique combination key for product_id, color_id, and size_id
                    $combinationKey = $product->id . '-' . $item['color_id'] . '-' . $item['size_id'];
    
                    // Check if this combination has already been processed for this product
                    if (!isset($processedCombinations[$combinationKey])) {
                        // Create OrderDetail for this product if the combination is unique
                        OrderDetails::create([
                            'product_id' => $item['product_id'],
                            'order_id' => $order->id,
                            'customer_id' => $user->id,
                            'designer_id' => $product->created_by,
                            'address_id' => $request->address_id,
                            'color_id' => $item['color_id'],
                            'size_id' => $item['size_id'],
                            'quantity' => $item['quantity'],
                            'total_amount' => $item['total_amount'],
                            'invoice_id' => $invoiceID,
                        ]);
    
                        // Mark this combination as processed
                        $processedCombinations[$combinationKey] = true;
    
                        // Update the total amount for this designer's order

                        // $ordersByDesigner[$product->created_by]['total_amount'] += $itemTotal;
                    }
                }
            }
    
            // After all items are processed, update the total amount for each order
            // foreach ($ordersByDesigner as $designerId => $orderData) {
                // Update the total amount for each order
                $order->update([
                    'total_amount' => $request->bill_amount,
                ]);
            // }
    
            // Fetch the default currency setting
            $defaultCurrency = SettingModel::where('key', 'default_currency')->value('value');
    
            // Prepare payment data
            $paymentData = [
                'amount' => $request->bill_amount,
                'currency' => $defaultCurrency,
                'customer' => [
                    'first_name' => $user->first_name,
                    'email' => $user->email,
                    'phone' => [
                        'country_code' => $user->country_code,
                        'number' => $user->phone,
                    ],
                ],
                'source' => ['id' => 'src_card'],
                'redirect' => ['url' => "http://192.168.100.8:8001/api/callback/$invoiceID"],
            ];
    
            // Make the API request to the payment gateway
            $response = Curl::to('https://api.tap.company/v2/charges')
                        ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
                        ->withData($paymentData)
                        ->asJson()
                        ->post();
    
            return response()->json([
                'message' => 'Orders have been created successfully',
                'payment_gateway_url' => $response->transaction->url ?? null,
            ]);
    
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 422);
        } catch (Exception $ex) {
            return response()->json(['error' => 'An unexpected error occurred: ' . $ex->getMessage()], 500);
        }
    }
    
    
    


    /**
     * Display the specified resource.
     */
    public function updateOrderStatus(Request $request, string $id)
    {
        try {
            $data = $this->validate($request, [
                'status' => 'required',
            ]);
    
            $order = OrderModel::where('id',$id)->where('desginer_id',Auth::id())->first();
    
            if ($order) {
                $order->update([
                    'status' => $request->status,
                ]);
                $this->res->message = 'Order Status Updated Successfully!';
            } else {
                $this->res->error = 'Sorry! You are not a Designer for this order!';
            }
        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally {
            return $this->res;
        }
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function cancelOrder(Request $request, string $id)
    {
        try {
            $data = $this->validate($request, [
                'status' => 'required', 
            ]);
    
            $order = OrderModel::where('id', $id)->where('status', 201)->orWhere('status',202)->first();
    
            if ($order) {
                // Update order status
                $order->update([
                    'status' => $request->status,
                ]);
                $this->res->message = 'Your order has been canceled!';
            } else {
                $this->res->error = 'Your order cannot be canceled right now because it is in logistics or already processed!';
            }
        } catch (Exception $ex) {
            // Catch and return the error message
            $this->res->error = 'An error occurred: ' . $ex->getMessage();
        } finally {
            // Return the response (success or error)
            return $this->res;
        }
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
    public function addToCart(Request $request)
    {
        try {
            // Validate incoming request data
            $data = $this->validate($request, [
                'product_id' => 'required',
                'color_id' => 'required',
                'size_id' => 'required', // Ensure size exists
                'quantity' => 'required', // Ensure quantity is a positive integer
            ]);
    
            $incre = 1;

            $addToCart = AddToCart::where('customer_id', Auth::id())
                ->where('product_id', $request->product_id)
                ->where('color_id', $request->color_id)
                ->where('size_id', $request->size_id)
                ->first(); 
    
            if ($addToCart) {
                $addToCart->quantity += $incre; 
                $addToCart->save(); 
                $this->res->message = 'Item updated successfully!';
            } else {
                // Item does not exist, create a new entry
                AddToCart::create([
                    'customer_id' => Auth::id(), // Include the customer ID
                    'product_id' => $request->product_id,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id,
                    'quantity' => $request->quantity,
                ]);
                $this->res->message = 'Item added successfully!';
            }
        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage(); // Capture any exception message
        } finally {
            return $this->res; // Return the response object
        }
    }
    
    
    
    public function updateAddToCart(Request $request, string $id)
{
    try {
        // Validate the request data
        $data = $this->validate($request, [
            'color_id' => 'required',
            'size_id' => 'required',
            'quantity' => 'required|integer',
        ]);

        $addToCart = AddToCart::where('id',$id)->update([
            'color_id' => $request->color_id,
            'size_id' => $request->size_id,
            'quantity' => $request->quantity,
        ]);
        if ($addToCart) {

            $this->res->message = 'Cart updated successfully!';
        } else {
            $this->res->error = 'Item not found in cart.';
        }
    } catch (Exception $ex) {
        $this->res->error = $ex->getMessage();
    } finally {
        return $this->res;
    }
}


    public function getAddToCart(Request $request)
    {
        $searchTerm = $request->input('search');

        $query = AddToCart::where('customer_id', Auth::id())
            ->with('Product', 'Color', 'Size', 'product.media');

        if ($searchTerm) {
            $query->whereHas('Product', function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        // Get the latest items and paginate
        $order = $query->latest()->paginate(10);

        return response()->json($order);
    }


    public function destroy(string $id)
    {
        try {
            // Find the cart item based on product ID
            $addToCart = AddToCart::where('id', $id)->first();
        
            if ($addToCart) {
                $addToCart->delete(); // Delete the item if it exists
                return response()->json(['status' => 'success', 'message' => 'Item removed from cart successfully.']);
            } else {
                return response()->json(['status' => 'warning', 'error' => 'Item not found in cart.']);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 'danger', 'error' => 'Failed to delete: ' . $e->getMessage()]);
        }
    }
    

    public function orderDetails(string $id)
    {
        $AddToCart = AddToCart::where('product_id',$id)->orWhere('customer_id',Auth::id())->first();
        // return $AddToCart->product_id;
        $orderDetails =  ProductModel::where('id',$AddToCart->product_id)->with([
            'media',
            'category',
            'sub_category',
            'sizes',
            'colors',
        ])->get();
        return response()->json($orderDetails);
    }
    
    public function getOrder()
    {
        $order = OrderModel::where('customer_id', Auth::id())
        ->with([
            'customer', // Relationship with Customer model (may be a belongsTo or hasOne)
            'desginer', // Relationship with Designer model (belongsTo or hasOne)
            'orderDetails.product.media', // Relationship with Product through OrderDetail (hasMany or belongsTo)
            'orderDetails.address', // Relationship with Address through OrderDetail (belongsTo)
            'orderDetails.color', // Relationship with Color through OrderDetail (belongsTo)
            'orderDetails.size' // Relationship with Size through OrderDetail (belongsTo)
        ])

        ->latest()
        ->paginate(10);
    
    return response()->json($order);
    
    }
    
    public function getPaymentMethod ($id)
    {
        return TapModel::where('invoice_id',$id)->first();
        
    }

    public function getOrderForDesginer()
    {
        $order = OrderModel::where('desginer_id', Auth::id())
        ->with([
            'customer', // Relationship with Customer model (may be a belongsTo or hasOne)
            'desginer', // Relationship with Designer model (belongsTo or hasOne)
            'orderDetails.product.media', // Relationship with Product through OrderDetail (hasMany or belongsTo)
            'orderDetails.address', // Relationship with Address through OrderDetail (belongsTo)
            'orderDetails.color', // Relationship with Color through OrderDetail (belongsTo)
            'orderDetails.size' // Relationship with Size through OrderDetail (belongsTo)
        ])
        ->latest()
        ->paginate(10);

        return response()->json($order);
    }

}

