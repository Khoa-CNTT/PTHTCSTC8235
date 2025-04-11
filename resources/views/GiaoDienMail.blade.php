<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kích Hoạt Tài Khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background-color: #ff6f61;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            transition: background-color 0.3s ease;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            color: #333333;
            margin: 10px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff6f61;
            color: #ffffff;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #e65a50;
        }
        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666666;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>Chào mừng đến với PetCare!</h1>
        </div>
        <div class="content">
            <p>Xin chào, {{$data ["ho_va_ten"]}}</p>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại PetCare. Hãy nhấp vào nút bên dưới để kích hoạt tài khoản của bạn:</p>
            <a href="{{ $data['link'] }}" class="btn">Kích Hoạt Tài Khoản</a>
            <p>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 PetCare.</p>
        </div>
    </div>
</body>
</html>
