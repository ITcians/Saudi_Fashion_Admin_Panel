<?php

namespace App\Http\Controllers;

use App\Services\FCMService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
   protected $fCMService;

   public function __construct(FCMService $fCMService) {
    $this->fCMService = $fCMService;
   }
   public function sendPushNotification(Request $request)
   {
       // Validate the incoming request data
       $request->validate([
           'token' => 'required',      // Device token is required
           'title' => 'required',      // Title for the notification is required
           'body' => 'required',       // Body of the notification is required
           'data' => 'required',       // Additional data to be sent with the notification is required
       ]);
   
       // Retrieve input values
       $token = $request->input('token');
       $title = $request->input('title');
       $body = $request->input('body');
       $data = $request->input('data');
   
       try {
           // Send notification via the FCMService
           $response = $this->fCMService->sendNotification($token, $title, $body, $data);
   
           // Return success response if notification is sent successfully
           return response()->json([
               'message' => 'Notification sent successfully!',
               'response' => $response  // You may want to include FCM's response here for debugging or confirmation
           ], 200);
       } catch (\Exception $e) {
           return response()->json([
               'error' => 'Failed to send notification',
               'message' => $e->getMessage()
           ], 500);
       }
   }
   
}
