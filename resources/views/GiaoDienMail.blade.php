<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kích Hoạt Tài Khoản</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 0;
            animation: fadeIn 1.2s ease-out both;
        }

        @keyframes fadeIn {
            0% { opacity: 0; transform: scale(0.98); }
            100% { opacity: 1; transform: scale(1); }
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            border: 2px solid #3498db; /* Viền màu xanh dương */
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: dropShadow 1.5s ease-in-out both;
        }

        @keyframes dropShadow {
            0% { box-shadow: 0 0 0 rgba(0,0,0,0); }
            100% { box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08); }
        }

        .header {
            background:#3498db ;
            background-size: cover;
            color: #ffffff;
            text-align: center;
            padding: 40px 20px;
            animation: slideDown 1s ease-out both;
        }

        @keyframes slideDown {
            0% { opacity: 0; transform: translateY(-30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        .content {
            padding: 30px 20px;
            text-align: center;
        }

        .content p {
            font-size: 16px;
            color: #333333;
            margin: 14px 0;
            line-height: 1.6;
        }

        .content strong {
            color: #3498db; /* Chữ in đậm màu xanh dương */
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 14px 32px;
            background-color: #3498db;
            color: #ffffff;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: #ff0000;
            transform: scale(1.05) translateY(-2px);
            box-shadow: 0 8px 18px rgba(0,0,0,0.2);
        }

        .footer {
            background-color: #fafafa;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #999999;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>CHÀO MỪNG BẠN ĐẾN VỚI PETCARE!</h1>
        </div>
        <div class="content">
            <p>Xin chào, <strong>{{$data["ho_va_ten"]}}</strong></p>
            <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>PetCare</strong>! Để hoàn tất quá trình đăng ký, vui lòng nhấn vào nút bên dưới để kích hoạt tài khoản:</p>
            <a href="{{ $data['link'] }}" class="btn"><span style="color:#ffffff">Kích Hoạt Tài Khoản</span></a>
            <p style="margin-top: 30px;">Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email này.</p>
        </div>
        <div class="footer">
            <p>&copy; 2025 PetCare. <br>
                Cảm ơn bạn đã đồng hành cùng <strong>PETCARE</strong>
            </p>
        </div>
    </div>
</body>
</html>
