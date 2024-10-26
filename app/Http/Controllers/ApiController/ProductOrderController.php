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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Finally_;
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
                'cart_data' => 'required|array',
                'cart_data.*.product_id' => 'required|integer',
                'cart_data.*.size_id' => 'required|integer',
                'cart_data.*.color_id' => 'required|integer',
                'cart_data.*.quantity' => 'required|integer|min:1',
            ]);
    
            // Generate a 5-digit invoice ID
            $invoiceID = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    
            $user = Auth::user();
            $totalAmount = 0;
            $designerId = null;
    
            // Order Model
            $orderData = [
                'customer_id' => $user->id,
                'invoice_id' => $invoiceID,
                'total_amount' => 0,
                'desginer_id' => 0,
            ];
            $order = OrderModel::create($orderData);
    
            // Order Details Model
            foreach ($data['cart_data'] as $item) {
                $product = ProductModel::find($item['product_id']);
                if ($product) {
                    // Check product stock (assuming 'stock' is the available quantity)
                    // if ($item['quantity'] > $product->stock) {
                    //     return response()->json(['error' => 'Insufficient stock for product ID ' . $product['title']], 422);
                    // }
    
                    $productPrice = $product->price ?? 0;
                    $itemTotal = $productPrice * $item['quantity'];
                    $totalAmount += $itemTotal;
    
                    OrderDetails::create([
                        'product_id' => $item['product_id'],
                        'order_id' => $order->id,
                        'customer_id' => $user->id,
                        'designer_id' => $product->created_by,
                        'address_id' => $request->address_id,
                        'color_id' => $item['color_id'],
                        'size_id' => $item['size_id'],
                        'quantity' => $item['quantity'],
                        'invoice_id' => $invoiceID,
                    ]);
    
                    // Update designerId from the first product or keep track of multiple designers as needed
                    $designerId = $product->created_by;
                }
            }
    
            // Update the total amount and designer_id in the order
            $order->update([
                'total_amount' => $totalAmount,
                'designer_id' => $designerId,
            ]);
    
            // Fetch the default currency setting
            $defaultCurrency = SettingModel::where('key', 'default_currency')->value('value');
    
            // Prepare payment data
            $paymentData = [
                'amount' => $totalAmount,
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
                'redirect' => ['url' => "http://192.168.100.8:8000/api/callback/$invoiceID"],
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
        // return $request->all();
        try {
            $data = $this->validate($request, [
                'product_id' => 'required',
                'color_id' => 'required',
                'size_id' => 'required',
                'quantity' => 'required|integer',
            ]);
    
            AddToCart::create([
                'customer_id' => Auth::id(),
                'product_id' => $request->product_id,
                  'color_id' => $request->color_id, 
                'size_id' => $request->size_id,

                'quantity' => $request->quantity,
            ]);
    
            $this->res->message = 'Add to Cart Added Successfully!';
            
        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally {
            return $this->res;
        }
    }
    
    

    public function getAddToCart()
    {
        $order = AddToCart::where('customer_id',Auth::id())->with('Product','Color','Size','Media')->latest()->paginate(10);
        return response()->json($order);
    }

    public function destroy(string $id)
    {

        try {
            // Find the cart item based on customer ID and product ID
            $addToCart = AddToCart::where('customer_id', Auth::id())
                ->where('product_id', $id); // Use first() to get a single record
    
            if ($addToCart) {
                $addToCart->delete(); // Delete the item if it exists
                return response()->json(['success', 'Item removed from cart successfully.']);
            } else {
                return response()->json(['warning', 'Item not found in cart.']);
            }
        } catch (Exception $e) {
            return response()->json(['danger', 'Failed to delete: ' . $e->getMessage()]);
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
        $order = OrderModel::where('customer_id',Auth::id())->with('orderDetails')->latest()->paginate(10);
        return response()->json($order);
    }
    

}

