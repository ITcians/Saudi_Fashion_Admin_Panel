<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\CustomerAddressModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\Finally_;
use Validator;
use stdClass;
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
            
            $customerId = Auth::id();

            foreach ($request->product_id as $ProductId) {
                OrderModel::create([
                    'product_id' => $ProductId,
                    'customer_id' => $customerId,
                    'address_id' => $request->address_id,
                ]);
            }

            return response()->json(['message' => 'Orders have been created successfully']);

        } catch (\Throwable $th) {
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

