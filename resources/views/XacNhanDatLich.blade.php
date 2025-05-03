<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>

<body>
    <h2>Xin chào {{ $lichHen['ten_khach_hang'] }},</h2>
    <p>Bạn đã đặt lịch tiêm chủng cho thú cưng thành công với thông tin sau:</p>
    <ul>
        <li><strong>Dịch vụ:</strong> {{ $lichHen['ten_dv'] }}</li>
        <li><strong>Thời gian:</strong> {{ $lichHen['gio'] }} ngày {{ $lichHen['ngay'] }}</li>
        <li><strong>Pet ID:</strong> {{ $lichHen['id_pet'] }}</li>
    </ul>
    <p>Chúng tôi sẽ liên hệ để xác nhận nếu cần thiết.</p>
    <p>Trân trọng, <br>PetCare Team</p>
</body>

</html>
