<?php

namespace App\Http\Controllers\ApiController\Auth;

use App\Http\Controllers\Controller;
use App\Models\FellowsModel;
use App\Models\User;
use Exception;
use  Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\emails\RegistrationOtp;
use App\Mail\emails\ResendRegistrationOtp;
use App\Models\otps;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use stdClass;
use Carbon\Carbon;



class Register extends Controller
{
    protected $res;
    function __construct()
    {
        $this->res = new stdClass;
    }



    public function login(Request $request)
    {
        try {
            // Validate the request data
            $data = $this->validate($request, [
                'email' => "required|email|exists:users,email",
                'password' => 'required',
            ]);

            // Attempt to authenticate the user
            if (Auth::attempt($data)) {
                // Authentication successful
                $user = Auth::user();
                // Generate a bearer token
                $token =  $user->createToken("API TOKEN")->plainTextToken;

                // You can return any additional data you need along with the token
                $user->token = $token;


                $this->res->user = $user;
                $this->res->message = 'Login successful';
            } else {
                // Authentication failed
                $this->res->error = 'Invalid credentials';
            }
        } catch (Exception $ex) {
            // Validation error occurred
            $this->res->error = $ex->getMessage();
        } catch (Exception $ex) {
            // Other exception occurred
            $this->res->error = $ex->getMessage();
        } finally {
            // Return the response
            return response()->json($this->res);
        }
    }


    /**
     * Store a newly created resource in storage.
     */


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'username' => 'required',
                'email' => 'required|email',
                'country_code' => 'required',
                'phone' => 'required',
                'bio' => 'nullable',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Generate 4-digit numeric OTP
            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $userData = [
                'fullname' => $request->fullname,
                'username' => $request->username,
                'email' => $request->email,
                'country_code' => $request->country_code,
                'phone' => $request->phone,
            ];

            // Create user record
            $register = User::create($userData);

            // Calculate expiration time (24 hours from now)
            $expirationTime = Carbon::now()->addHours(24);

            $is_used = 0;
            $otps = [
                'otp' => $otp,
                'is_expired' => $expirationTime,
                'is_used' => $is_used,
                'email' => $request->email,
            ];

            // Save OTP details to the database
            // Assuming you have an Otp model and otps relationship defined in the User model
            $otps = otps::create($otps);

            // Generate a bearer token
            $token =  $register->createToken("API TOKEN")->plainTextToken;

            // Send OTP to the user's email
            Mail::to($request->email)->send(new RegistrationOtp($otp));

            return response()->json(['message' => 'You are registered successfully', 'UserData' => $userData, 'access_token' => $token]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function email_otp_verified(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Retrieve the authenticated user's email
            $email = Auth::user()->email;

            // Find the OTP record based on the provided email
            $otpRecord = otps::where('email', $email)
            ->where('is_used',0)
            ->where('otp',$request->otp)
            ->first();

            if (!$otpRecord) {
                return response()->json(['error' => 'OTP record not found for this user'], 404);
            }

            // Now compare the provided OTP with the stored OTP
            if ($otpRecord->otp == $request->otp) {
                // OTP is verified, you can update the user's status or any other actions
                $otpRecord->is_used = 1; //make it 1
                $otpRecord->save();

                return response()->json(['message' => 'OTP verified successfully']);
            } else {
                return response()->json(['error' => 'Invalid OTP','otp'=>$otpRecord,'found'=>$request->otp], 400);
            }
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    public function create_password(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6', // Minimum 6 characters required for password
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $AuthId = Auth::id();
            $user = User::find($AuthId);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Assuming you have password and confirm_password fields in the request
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['message' => 'Password created successfully']);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function images(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $AuthId = Auth::id();
            $user = User::find($AuthId);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Upload profile image
            if ($request->hasFile('image')) {
                $profileImage = $request->file('image');
                $profileImageName = '/upload_images/profile_' . time() . '.' . $profileImage->getClientOriginalExtension();
                $profileImage->move(public_path('upload_images'), $profileImageName);
                $user->image = $profileImageName;
            }



            $user->save();

            return response()->json(['message' => 'Images uploaded successfully','image'=>$user->image]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }



    public function forget_password_using_email(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Generate 4-digit numeric OTP
            $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            // Find the user based on the provided email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            // Calculate expiration time (24 hours from now)
            $expirationTime = Carbon::now()->addHours(24);

            // Check if an OTP record already exists for the given email
            $existingOtpRecord = otps::where('email', $request->email)
            ->where('otp',$request->otp)->get()->first();

            if ($existingOtpRecord) {
                // Update the existing OTP record
                $existingOtpRecord->otp = $otp;
                $existingOtpRecord->is_used = false;
                $existingOtpRecord->is_expired = $expirationTime;
                $existingOtpRecord->save();
            } else {
                // Create a new OTP record
                $otpRecord = new otps();
                $otpRecord->otp = $otp;
                $otpRecord->is_used = false;
                $otpRecord->is_expired = $expirationTime;
                $otpRecord->email = $request->email;
                $otpRecord->save();
            }

            // Send OTP to the user's email
            Mail::to($request->email)->send(new RegistrationOtp($otp));

            return response()->json([
                'message' => 'OTP sent successfully',
                'otp' => $otp, // For testing purposes, remove in production
            ]);
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }


    // resend otp verification




    public function resendOtp(Request $request)
    {
        try {
            // Get the authenticated user's email
            $email = Auth::user()->email;

            // Find the OTP record based on the authenticated user's email and status is not used
            $otpRecord = otps::where('email', $email)
                ->where('is_used', 0)
                ->first();

            if (!$otpRecord) {
                return response()->json(['error' => 'No available OTP to resend'], 404);
            }

            // Send OTP to the user's email
            Mail::to($email)->send(new ResendRegistrationOtp($otpRecord->otp));

            // Resend the OTP to the user's email
            return response()->json(['message' => 'OTP resent successfully', 'otp' => $otpRecord->otp]);

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function topDesigner(Request $request)
    {
        $username = $request->input('username');
    
        $query = User::where('account_type', 1);
    
        // Check if a username is provided
        if ($username) {
            $query->where('username', 'like', '%' . $username . '%'); // Use 'like' for partial matching
        }
    
        $designers = $query->latest()->get();
    
        return response()->json($designers);
    }
    
    

}
