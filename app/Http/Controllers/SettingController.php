<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Admin;
use App\Models\SettingModel;
use App\Models\EmailSetup;
use App\Models\StripeSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Validator;

class SettingController extends Controller
{
    protected $res;
    public function update_setting(Request $request)
    {
        // return $request->all();
        try {
            // Validation rules
            $data = $request->validate([
                'key' => "required|exists:settings,key",
                'value' => "required",
            ]);
    
            if ($data['key'] == 'brand_logo') {
                if ($request->hasFile('brand_logo')) {
                    $image = $request->file('brand_logo');
                    $BrandImageName = time() . '.' . $image->getClientOriginalExtension();
                    $path = public_path('/upload_image');
                    $image->move($path, $BrandImageName);
                } else {
                    return back()->with('danger','Image not found');
                }
    
                SettingModel::where('key', $data['key'])->update(['value' => $BrandImageName]);
    
            } elseif ($data['key'] == 'brand_logo_white') {
                if ($request->hasFile('brand_logo_white')) {
                    $image = $request->file('brand_logo_white');
                    $BrandLogoWhiteName = time() . '.' . $image->getClientOriginalExtension();
                    $path = public_path('/upload_image');
                    $image->move($path, $BrandLogoWhiteName);
                } else {
                    return back()->with('danger','Image not found');
                }
    
                SettingModel::where('key', $data['key'])->update(['value' => $BrandLogoWhiteName]);
    
            } else {
                SettingModel::where('key', $data['key'])->update(['value' => $data['value']]);
            }
    
            return back()->with('success', 'Settings updated successfully');
        } catch (Exception $ex) {
            return back()->withErrors(['danger' => $ex->getMessage()])->withInput();
        }
    }


    
    // this is the previous code 
    // public function update_setting(Request $request)
    // {
    //     try {
    //         $fields = [
    //             'brand_name',
    //             'brand_logo',
    //             'brand_logo_white',
    //             'smtp_email',
    //             'smtp_password',
    //             'smtp_server',
    //             'smtp_port',
    //             'smtp_encryption',
    //             'smtp_sender_name',
    //             'fcm_server_key'
    //         ];
    
    //         $data = $request->validate([
    //             'brand_name' => '',
    //             'brand_logo' => '',
    //             'brand_logo_white' => '',
    //             'smtp_email' => '',
    //             'smtp_password' => '',
    //             'smtp_server' => '',
    //             'smtp_port' => '',
    //             'smtp_encryption' => '',
    //             'smtp_sender_name' => '',
    //             'fcm_server_key' => '',
    //         ]);
    
    //         foreach ($fields as $field) {
    //             SettingModel::where('key', $field)->update(['value' => $data[$field]]);
    //         }
    
    //         // Update application name
    //         config(['app.name' => $request->app_name]);
    
    //         return back()->with('success', 'Settings updated successfully');
    //     } catch (Exception $ex) {
    //         return back()->withErrors('success', 'Settings updated successfully')->withInput();
    //     } catch (Exception $ex) {
    //         return back()->withErrors(['danger' => $ex->getMessage()])->withInput();
    //     }
    // }
    



    public function settings(){

        $settings = SettingModel::all();

        return view('Settings.setting',compact('settings'));
    }





}
