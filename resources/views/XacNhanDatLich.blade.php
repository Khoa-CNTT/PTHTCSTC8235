<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận đặt lịch thành công</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #002855;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            border: 1px solid #ddd;
            border-top: none;
            padding: 20px;
            border-radius: 0 0 5px 5px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .payment-info {
            background-color: #f9f9f9;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            border-left: 4px solid #002855;
        }
        .footer {
            margin-top: 30px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Đặt lịch thành công</h2>
    </div>
    <div class="content">
        <p>Xin chào <strong>{{ $lichHen['ten_khach_hang'] }}</strong>,</p>
        <p>Cảm ơn bạn đã đặt lịch tại PetCare. Chúng tôi xác nhận bạn đã đặt lịch thành công với thông tin chi tiết như sau:</p>
        
        <div class="info-item">
            <span class="info-label">Dịch vụ:</span> {{ $lichHen['ten_dv'] }}
        </div>
        <div class="info-item">
            <span class="info-label">Thời gian:</span> {{ $lichHen['gio'] }}
        </div>
        <div class="info-item">
            <span class="info-label">Ngày:</span> {{ $lichHen['ngay'] }}
        </div>
        <div class="info-item">
            <span class="info-label">Thú cưng:</span> {{ $lichHen['ten_pet'] }} (ID: {{ $lichHen['id_pet'] }})
        </div>
        
        <div class="payment-info">
            <h3>Thông tin thanh toán</h3>
            <div class="info-item">
                <span class="info-label">Giá dịch vụ:</span> {{ number_format($lichHen['gia'], 0, ',', '.') }} VNĐ
            </div>
            <div class="info-item">
                <span class="info-label">Tiền cọc đã thanh toán:</span> {{ number_format($lichHen['tien_coc'], 0, ',', '.') }} VNĐ
            </div>
            <div class="info-item">
                <span class="info-label">Phương thức thanh toán:</span> PayPal
            </div>
            <div class="info-item">
                <span class="info-label">Mã thanh toán:</span> {{ $lichHen['payment_id'] ?? 'Không có' }}
            </div>
        </div>
        
        <p>Vui lòng đến đúng giờ. Nếu bạn cần thay đổi lịch hẹn, vui lòng liên hệ với chúng tôi trước ít nhất 24 giờ.</p>
        
        <p>Địa chỉ phòng khám: 03 Quang Trung, Hải Châu 1, Hải Châu, Đà Nẵng</p>
        <p>Số điện thoại: 0123 456 789</p>
    </div>
    
    <div class="footer">
        <p>Trân trọng, <br>PetCare Team</p>
        <p>Email này được gửi tự động, vui lòng không trả lời.</p>
    </div>
</body>

</html>
