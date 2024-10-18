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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->validate($request, [
                // 'cart_data' => 'required|array',
                'product_id' => 'required|array',
                'address_id' => 'required',
            ]);
            
            // Generate 4-digit numeric OTP
            $invoiceID = str_pad(random_int(0, 999999), 5, '0', STR_PAD_LEFT);

            $User = Auth::user();

            foreach ($request->product_id as $ProductId) {
                OrderModel::create([
                    'product_id' => $ProductId,
                    'customer_id' => $User->id,
                    'address_id' => $request->address_id,
                    'invoice_id' => $invoiceID,
                ]);
            }

            $default_currency = SettingModel::where('key', 'default_currency')->first();
    
            // $data['amount'] = floatval($input['amount']);
            $data['amount'] = 300;
            $data['currency'] = $default_currency->value;
            $data['customer']['first_name'] = $User->first_name;
            $data['customer']['email'] = $User->email;
            $data['customer']['phone']['country_code'] = $User->country_code;
            $data['customer']['phone']['number'] = $User->phone;
            $data['source']['id'] = 'src_card';
    
    
            $data['redirect']['url'] = "http://192.168.100.8:8000/api/callback/$invoiceID";
    
            
    
            $response = Curl::to('https://api.tap.company/v2/charges')
                        ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
                        ->withData($data)
                        ->asJson()
                        ->post();
    
            return response()->json([
                'message' => 'Orders have been created successfully',
                'payment_gateway_url' => $response->transaction->url,
            ]);
                        
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
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
        ]);

        AddToCart::create([
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
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

