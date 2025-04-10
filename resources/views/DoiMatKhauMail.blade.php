<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi Mật Khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background-color: #4CAF50;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .email-body {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }
        .email-body p {
            margin: 10px 0;
        }
        .email-footer {
            text-align: center;
            padding: 20px;
            background-color: #f4f4f4;
            font-size: 12px;
            color: #777777;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Đổi Mật Khẩu</h1>
        </div>
        <div class="email-body">
            <p>Xin chào,</p>
            <p>Bạn đã yêu cầu đổi mật khẩu. Vui lòng nhấn vào nút bên dưới để đặt lại mật khẩu của bạn:</p>
            <a href="{{ $resetLink }}" class="btn">Click vào đây để đổi mật khẩu</a>
            <p>Nếu bạn không yêu cầu đổi mật khẩu, vui lòng bỏ qua email này.</p>
        </div>
        <div class="email-footer">
            <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
        </div>
    </div>
