<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\OrderModel;
use App\Models\PaymentGatewayModel;
use App\Models\SettingModel;
use App\Models\TapModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ixudra\Curl\Facades\Curl;
class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentGateway = PaymentGatewayModel::where('status' , 200)->get();
        return response()->json($paymentGateway);
    }

    /**
     * Show the form for creating a new resource.
     */
   
    public function callback(Request $request,string $invoiceID)
    {
        // return response()->json($invoiceID);
        $response = Curl::to('https://api.tap.company/v2/charges/'.$_GET['tap_id'])
                    ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
                    ->asJson()
                    ->get();

                    // dd($response);
       if ($response->status == 'CAPTURED') {
            $TapModel = new TapModel();
            $TapModel->name = $response->customer->first_name;
            $TapModel->email = $response->customer->email;
            $TapModel->tran_id = $response->id;
            $TapModel->payment_method = $response->card->scheme;
            $TapModel->amount = $response->amount;
            $TapModel->save();
            
            // Update the order status
            $orderUpdated = OrderModel::where('invoice_id', $invoiceID)->update(['status' => 200]);

            // Retrieve the order to get the customer_id
            $order = OrderModel::where('invoice_id', $invoiceID)->first();

            if ($order) {
                // Delete items from the cart for the specific customer
                $addToCartDeleted = AddToCart::where('customer_id', $order->customer_id)->delete();
            }


       } else {
        return view('paymentdeclained',compact('response'));
       }

        // return response()->json($order);


    //    dd( $response);
    return view('invoice',compact('response'));
    // return 'Successfully Payment ADDED!';
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
