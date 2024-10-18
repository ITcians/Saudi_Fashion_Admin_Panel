<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
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
    // public function store(Request $request)
    // {
    //     $input = $request->all();

    //     $productId = $request->product_id;
    //     $User = Auth::user();

    //     $default_currency = SettingModel::where('key', 'default_currency')->first();

    //     // $data['amount'] = floatval($input['amount']);
    //     $data['amount'] = 300;
    //     $data['currency'] = $default_currency->value;
    //     $data['customer']['first_name'] = $User->first_name;
    //     $data['customer']['email'] = $User->email;
    //     $data['customer']['phone']['country_code'] = $User->country_code;
    //     $data['customer']['phone']['number'] = $User->phone;
    //     $data['source']['id'] = 'src_card';

    //     $productIds = [1,2,3,4]; // Assuming this is an array // testing
    //     $productIdList = implode(',', $productIds); // Convert array to a comma-separated string
    //     // Generate 4-digit numeric OTP
    //     $invoiceID = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

    //     $data['redirect']['url'] = "http://192.168.100.8:8000/api/callback/$invoiceID";

        

    //     $response = Curl::to('https://api.tap.company/v2/charges')
    //                 ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
    //                 ->withData($data)
    //                 ->asJson()
    //                 ->post();

    //                 return response()->json([
    //                     'payment_gateway_url' => $response->transaction->url,
    //                 ]);
                    
    //     // dd($response);
    // }

  
    public function callback(Request $request,string $invoiceID)
    {
        // return response()->json($invoiceID);
        $response = Curl::to('https://api.tap.company/v2/charges/'.$_GET['tap_id'])
                    ->withBearer('sk_test_iaX0qZtJegkbK1LzOYoHlSmj')
                    ->asJson()
                    ->get();

       if ($response->status == 'CAPTURED') {
            $TapModel = new TapModel();
            $TapModel->name = $response->customer->first_name;
            $TapModel->email = $response->customer->email;
            $TapModel->tran_id = $response->id;
            $TapModel->amount = $response->amount;
            $TapModel->save();
            
            // Change order status
            OrderModel::where('invoice_id',$invoiceID)->update([
                'status' => 200
            ]);
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
