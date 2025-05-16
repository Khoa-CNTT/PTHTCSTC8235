<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Xác nhận đặt lịch - PetCare</title>
    <style scoped>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4a6cf7;
            padding-bottom: 15px;
        }
        .header h2 {
            color: #4a6cf7;
            margin-bottom: 5px;
            font-size: 24px;
        }
        .content {
            padding: 10px 0;
        }
        .info-section {
            background-color: #f0f7ff;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .payment-section {
            background-color: #f0fff5;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .payment-success {
            background-color: #dff5e5; 
            border-radius: 6px;
            padding: 10px 15px;
            margin-top: 10px;
            border-left: 4px solid #28a745;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        ul {
            padding-left: 20px;
        }
        li {
            margin-bottom: 10px;
        }
        .highlight {
            color: #4a6cf7;
            font-weight: bold;
        }
        .thank-you {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <h2>Xác Nhận Đặt Lịch Thành Công</h2>
            <p>Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của PetCare</p>
        </div>
        
        <div class="content">
            <p>Xin chào <strong>{{ $lichHen['ten_khach_hang'] }}</strong>,</p>
            
            <p>Chúng tôi rất vui thông báo rằng lịch hẹn của bạn đã được xác nhận thành công. Dưới đây là thông tin chi tiết:</p>
            
            <div class="info-section">
                <h3>Thông tin lịch hẹn:</h3>
                <ul>
                    <li><strong>Dịch vụ:</strong> {{ $lichHen['ten_dv'] }}</li>
                    <li><strong>Ngày:</strong> {{ $lichHen['ngay'] }}</li>
                    <li><strong>Giờ:</strong> {{ $lichHen['gio'] }}</li>
                    <li><strong>Thú cưng:</strong> {{ $lichHen['ten_pet'] }} (ID: {{ $lichHen['id_pet'] }})</li>
                    @if(isset($lichHen['bac_si']))
                    <li><strong>Bác sĩ phụ trách:</strong> {{ $lichHen['bac_si'] }}</li>
                    @endif
                </ul>
            </div>
            
            <div class="payment-section">
                <h3>Thông tin thanh toán:</h3>
                <ul>
                    <li><strong>Phương thức thanh toán:</strong> PayPal</li>
                    <li><strong>Mã giao dịch:</strong> {{ $lichHen['payment_id'] ?? 'Không có' }}</li>
                    <li><strong>Tổng tiền dịch vụ:</strong> {{ number_format($lichHen['gia']) }} VNĐ</li>
                    <li><strong>Tiền cọc đã thanh toán:</strong> {{ number_format($lichHen['tien_coc']) }} VNĐ</li>
                    <li><strong>Số tiền dự kiến còn lại:</strong> {{ number_format($lichHen['gia'] - $lichHen['tien_coc']) }} VNĐ (thanh toán tại cơ sở)</li>
                </ul>
                
                @if(isset($lichHen['payment_details']))
                <div class="payment-success">
                    <p>Thanh toán đã được xác nhận bởi PayPal!</p>
                    @if(isset($lichHen['payment_details']['id']))
                    <p>ID giao dịch PayPal: {{ $lichHen['payment_details']['id'] }}</p>
                    @endif
                    @if(isset($lichHen['payment_details']['status']))
                    <p>Trạng thái: {{ $lichHen['payment_details']['status'] }}</p>
                    @endif
                    @if(isset($lichHen['payment_details']['create_time']))
                    <p>Thời gian: {{ $lichHen['payment_details']['create_time'] }}</p>
                    @endif
                </div>
                @endif
            </div>
            
            <p>Vui lòng đến đúng giờ hẹn. Nếu bạn cần thay đổi lịch hẹn, vui lòng liên hệ với chúng tôi trước ít nhất 24 giờ.</p>
            
            <p class="highlight">Lưu ý: Hãy mang theo thông tin thú cưng và đến trước giờ hẹn 15 phút để hoàn tất thủ tục.</p>
            
            <div class="thank-you">
                <p>Cảm ơn bạn đã thanh toán đặt cọc dịch vụ!</p>
            </div>
        </div>
        
        <div class="footer">
            <p>Trân trọng,<br><strong>PetCare Team</strong></p>
            <p>Hotline: 0905676869 | Email: support@petcare.com</p>
            <p>Địa chỉ: 03 Quang Trung, Hải Châu 1, Hải Châu, Đà Nẵng</p>
        </div>
    </div>
</body>

</html>
