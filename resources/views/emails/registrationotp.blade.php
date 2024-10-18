<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }
        .note {
            font-size: 14px;
            text-align: center;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Registration OTP</h1>
        <p>Your One-Time Password (OTP) for registration is:</p>
        <p style="font-size: 32px; font-weight: bold; text-align: center;"><?php echo $otp; ?></p>
        <p class="note">Please use this OTP to complete your registration process. This OTP is valid for a limited time.</p>
    </div>
</body>
</html>
