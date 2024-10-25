<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\CustomerAddressModel;
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
    public function getOrder(Request $request)
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
        $orderCount = OrderModel::where('customer_id',Auth::id())->groupBy('product_id')->count();
        return response()->json(['order_count'=>$orderCount]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validate($request, [
                'cart_data' => 'required|array',
                'cart_data.*.product_id' => 'required|integer',
                'cart_data.*.address_id' => 'required|integer',
                'cart_data.*.size_id' => 'required|integer',
                'cart_data.*.color_id' => 'required|integer',
                'cart_data.*.quantity' => 'required|integer|min:1',
            ]);
    
            // Generate a 5-digit invoice ID
            $invoiceID = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
    
            $user = Auth::user();
            $totalAmount = 0;
    
            // Loop through the cart data and create orders
            foreach ($data['cart_data'] as $item) {
                $product = ProductModel::find($item['product_id']);
    
                if ($product) {
                    OrderModel::create([
                        'product_id' => $item['product_id'],
                        'customer_id' => $user->id,
                        'designer_id' => $product->created_by, // Use created_by from the product
                        'address_id' => $item['address_id'],
                        'color_id' => $item['color_id'],
                        'size_id' => $item['size_id'],
                        'quantity' => $item['quantity'],
                        'invoice_id' => $invoiceID,
                    ]);
    
                    // Assuming you have a way to get the product price
                    $productPrice = $product->price ?? 0;
                    $totalAmount += $productPrice * $item['quantity'];
                }
            }
    
            // Fetch the default currency setting
            $defaultCurrency = SettingModel::where('key', 'default_currency')->first();
    
            // Prepare payment data
            $paymentData = [
                'amount' => $totalAmount,
                'currency' => $defaultCurrency->value,
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
                'payment_gateway_url' => $response->transaction->url,
            ]);
    
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 422);
        } catch (Exception $th) {
            return response()->json(['error' => $th->getMessage()], 500);
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
        try {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'color_id' => 'required',
            'size_id' => 'required',
            'quantity' => 'required',
        ]);

        AddToCart::create([
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
            'size_id' => $request->size_id,
            'quantity' => $request->quantity,
        ]);

        $this->res->message = 'Add TO Cart Added Successfully!';

        } catch (Exception $ex) {
            $this->res->error = $ex->getMessage();
        } finally {
            return $this->res;
        }
    }


    public function getAddToCart(Request $request)
    {
        $searchTerm = $request->input('search'); 
    
        $productCounts = AddToCart::where('customer_id', Auth::id())
            ->select('product_id')
            ->groupBy('product_id')
            ->selectRaw('count(*) as count')
            ->orderBy('count', 'desc')
            ->get();
    
        // Convert the collection to an array
        $productCountsArray = $productCounts->toArray();
        $productIds = array_column($productCountsArray, 'product_id');
    
        // Start building the query for products
        $productQuery = ProductModel::whereIn('id', $productIds);
    
        // Apply the search filter if it exists
        if ($searchTerm) {
            $productQuery->where('title', 'like', '%' . $searchTerm . '%'); 
        }
    
        // Eager load relationships
        $product = $productQuery->with([
            'media',
            'category',
            'sub_category',
            'sizes',
            'colors',
        ])->get();
    
        // Order the products based on their counts
        $orderedProduct = $product->sortBy(function ($product) use ($productCountsArray) {
            $count = collect($productCountsArray)->firstWhere('product_id', $product->id);
            return $count ? $count['count'] : 0;
        })->reverse();
    
        $formattedproduct = [];
        foreach ($orderedProduct as $product) {
        $formattedproduct[] = $product; // Add the entire product object
        }
        return response()->json($formattedproduct);
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
    
    
}

