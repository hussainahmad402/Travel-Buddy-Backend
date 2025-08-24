<!-- resources/views/otp.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #2c7be5;
            text-align: center;
            margin: 20px 0;
            letter-spacing: 5px;
        }
        p {
            color: #555;
            font-size: 15px;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your OTP Code</h2>
        <p>Hello,</p>
        <p>Use the following One-Time Password (OTP) to complete your verification. This OTP is valid for <strong>5 minutes</strong>.</p>
        
        <div class="otp">
            {{ $otp }}
        </div>

        <p>If you did not request this, please ignore this email.</p>

        <div class="footer">
            &copy; {{ date('Y') }} YourApp. All rights reserved.
        </div>
    </div>
</body>
</html>
