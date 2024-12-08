<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        $serviceAccount = [

            "type"=> "service_account",
            "project_id"=> "saudifashion-a3b87",
            "private_key_id"=> "96b4d1cc49bcbce73461d3c66c11758b444ab81b",
            "private_key"=> "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDLjBztFAr2Xix1\ni/CRzbULlzl05/3I14utkYw4yMJ8IA+wnFE74HR3A9Zv+zXMNqBdX1HFZBvA4lS3\njrLUeOYhzvpR4VyGIdzbRf6uKa5hyYXo2hW7A6ngAJqQFxQlOIzSr9xCUuKbe3tC\nElpSkcMzj0ZGhq578C5M8mtoeQ7sD1FYAXSd0r0pX7aaxwd1nLB2L+URE9aCLWiv\nXMIlGExOVPRqMKOijRwuWjsifTfBFG4ACVlPxq1C5p3Dy1n1y8jnu756xXwxndNt\npbU8d6pQ2VXauAFnDVway3IwDF61hfxrHf4i0rrQKd4euUf8+5qXUDgIKpiNgW1/\nGv62sOUJAgMBAAECggEADf3mqyFak47rrXTeJ5elX1OY3oFn/5tZfafopKqgO9oU\nWgaUyvHl75xy7CWvkAcdRO3deB0mjz6/phKIFnTtFNvedyxvqMTBIs2P8TbqRsyM\niPIc1kstLTm9ZjQ/7UMS4xS1plV+QEPfuC38yXo9mdi6cvSlH93lwOJVIcxYiz9u\nuOnVCQ9Eh7lVHTIr5REQ35QnFyRlTHO4P4V2IRmkEOR6EU7q9OfgcpeTeAHyeTSu\nMHnWGXPg+X+/wwJKlb4kd35T28GdKkbavL+xXQKp8KulSqXVx9KvxMX25ii9Tg6M\n0PRTuOT79Fu3ohDI2JOI+SfFTcom2xmy6sucTbaT9QKBgQD/xANzB4MNNB2eXw0L\nBXi7Vi/CxShlvQxZvm5IvWTY5AsfRtE5ApPiN3v1slm0Hj4apZl0Wk1HgJqhutPL\nIRJrkCkBgM5+1MXeUdNyNOmd6ERHNf67hV7yO7VY5rpmVcZhgkCXx4eObYf4JQ03\ncqmDaTqK5tCteur8uWlzBtsnPwKBgQDLu9o1eadkjP2g+5xf7SjKhqBYNRrdbfUX\nny0AnlLWbqIV0W5KL/q9mlrF400Gx/4Ux3v3Lvustn4llqFu5JgNhcjCbUtS1bX5\nkEooZSU1ML7/K5BkE1IJwMGsoOjCZrB/JmYkFc/71i2FCvrxm0o3pSYvvacqIoYv\n9noT/lFptwKBgEgezZoyFBI1D+UIiHUDJfgtZWpWjf2iQUlNGWwJe9Zs93XqUjTx\nNSIk4x91GzZfEMQSziKxE/mFmhTgybgNq1QJYoa5+4BoHyOBj040/Ws5g4dRAnN+\nODe1/n0DEqyoozf7spsZ+G+BE0Y1rHRmMMaNdaSVUWKZtols/v8gf8uhAoGAcgxb\nz9mlMi2XAZo4ZJ6vWghZFGfN/SzDqhCKMbFvb+iIxavykIchVyhkLijbArPL8Tfm\nlm3vpzSVk1cqUZiX4eIFi4mBRAKsluMGEGzmkksScGejsdH698i24ntFMSYpIVSO\nkx6+yhyQaMzHw56JwnKp2MnTHUwsUAcrOawBSSsCgYEAkWvvt0+AeVc/3pFWPlDs\nkoDTAfxZ/tvQ2Afk6LewEoQL4T2NTgopd1j+XAnv6eGnzFLh4dLxW2zzjNRpgyop\nhyy/U4dvcsLRjY/JD6XndDcDu006D2gBjD1QnqbT/4Jm7UWSGrpUqrkog1HXLP79\n3rKUOTukXag3OiTW10zHRN0=\n-----END PRIVATE KEY-----\n",
            "client_email"=> "firebase-adminsdk-86v25@saudifashion-a3b87.iam.gserviceaccount.com",
            "client_id"=> "106742533301200337068",
            "auth_uri"=> "https://accounts.google.com/o/oauth2/auth",
            "token_uri"=> "https://oauth2.googleapis.com/token",
            "auth_provider_x509_cert_url"=> "https://www.googleapis.com/oauth2/v1/certs",
            "client_x509_cert_url"=> "https://www.googleapis.com/robot/v1/metadata/x509/firebase-adminsdk-86v25%40saudifashion-a3b87.iam.gserviceaccount.com",
            "universe_domain"=> "googleapis.com"

        ];

        $factory = (new Factory)
            ->withServiceAccount($serviceAccount);

        $this->messaging = $factory->createMessaging();
    }

    public function sendNotification($token, $title , $body,$data =[])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withData($data);

        try {
            return   $this->messaging->send($message);
        } catch (\Throwable $e) {
            // Handle the error
            return $e->getMessage();
        }
    }
    public function sendNotificationWithPayload($title, $body, $token,$payload)
    {

        $title = "$title sent you a message";

    
        $notification = Notification::create($title, $body);
        
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification)
            ->withData($payload);

        try {
            return   $this->messaging->send($message);
        } catch (\Throwable $e) {
            // Handle the error
            return $e->getMessage();
        }
    }

    public function sendNotificationToTopic($title, $body, $topic)
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification($notification);

        try {
            return $this->messaging->send($message);
        } catch (\Throwable $e) {
            // Handle the error
            return $e->getMessage();
        }
    }



}
