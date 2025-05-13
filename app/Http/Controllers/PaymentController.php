<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Khởi tạo thanh toán VNPay
     */
    public function createVnpayPayment(Request $request)
    {
        // Tìm thông tin lịch hẹn
        $lichHen = DB::table('lich_hen_pets')
            ->join('pets', 'pets.id', '=', 'lich_hen_pets.id_pet')
            ->join('khach_hangs', 'khach_hangs.id', '=', 'lich_hen_pets.id_kh')
            ->join('dich_vus', 'dich_vus.id', '=', 'lich_hen_pets.id_dv')
            ->where('lich_hen_pets.id', $request->id_lich_hen)
            ->select('lich_hen_pets.*', 'pets.ten_pet', 'khach_hangs.ho_va_ten', 'dich_vus.ten_dv')
            ->first();

        if (!$lichHen) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy lịch hẹn'
            ], 404);
        }

        // Cấu hình VNPAY
        $vnp_TmnCode = env('VNP_TMN_CODE'); // Mã website của merchant trên VNPAY
        $vnp_HashSecret = env('VNP_HASH_SECRET'); // Chuỗi bí mật để tạo checksum
        $vnp_Url = env('VNP_URL'); // URL thanh toán của VNPAY
        $vnp_Returnurl = env('VNP_RETURN_URL'); // URL nhận kết quả thanh toán từ VNPAY
        
        // Thông tin đơn hàng
        $vnp_TxnRef = "DEPPT" . time() . $lichHen->id; // Mã đơn hàng
        $vnp_OrderInfo = "Đặt cọc lịch hẹn cho thú cưng " . $lichHen->ten_pet . ", dịch vụ: " . $lichHen->ten_dv;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $lichHen->tien_coc * 100; // Số tiền * 100 (VNPay yêu cầu)
        $vnp_Locale = "vn";
        $vnp_IpAddr = request()->ip();
        $vnp_ExpireDate = Carbon::now('Asia/Ho_Chi_Minh')->addMinutes(30)->format('YmdHis');

        // Tạo mảng dữ liệu gửi đến VNPAY
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => Carbon::now('Asia/Ho_Chi_Minh')->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate
        ];

        // Thêm dữ liệu vào database để theo dõi giao dịch
        DB::table('payment_transactions')->insert([
            'transaction_code' => $vnp_TxnRef,
            'payment_method' => 'vnpay',
            'amount' => $lichHen->tien_coc,
            'id_lich_hen' => $lichHen->id,
            'status' => 'pending',
            'payment_info' => json_encode($inputData),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Sắp xếp dữ liệu theo thứ tự tăng dần của key (Yêu cầu của VNPAY)
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo URL thanh toán
        $vnp_Url = $vnp_Url . "?" . $query;
        
        // Tạo chữ ký (checksum)
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        // Trả về URL thanh toán để frontend chuyển hướng
        return response()->json([
            'status' => true,
            'message' => 'Tạo đường dẫn thanh toán thành công',
            'payment_url' => $vnp_Url
        ]);
    }

    /**
     * Xử lý kết quả thanh toán từ VNPAY
     */
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = env('VNP_HASH_SECRET'); // Chuỗi bí mật để kiểm tra checksum
        
        // Lấy dữ liệu từ request
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        // Lấy chữ ký từ request
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        // Sắp xếp dữ liệu theo thứ tự tăng dần của key
        ksort($inputData);
        
        // Tạo chuỗi hash data
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        // Tạo chữ ký
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // So sánh chữ ký từ VNPAY với chữ ký được tạo
        if ($secureHash == $vnp_SecureHash) {
            // Xác thực thành công, kiểm tra kết quả giao dịch
            if ($inputData['vnp_ResponseCode'] == '00') {
                // Thanh toán thành công
                // Cập nhật trạng thái giao dịch
                $transaction = DB::table('payment_transactions')
                    ->where('transaction_code', $inputData['vnp_TxnRef'])
                    ->first();
                
                if ($transaction) {
                    // Cập nhật transaction
                    DB::table('payment_transactions')
                        ->where('transaction_code', $inputData['vnp_TxnRef'])
                        ->update([
                            'status' => 'completed',
                            'response_data' => json_encode($inputData),
                            'updated_at' => now()
                        ]);
                    
                    // Cập nhật trạng thái đã thanh toán cho lịch hẹn
                    DB::table('lich_hen_pets')
                        ->where('id', $transaction->id_lich_hen)
                        ->update([
                            'da_thanh_toan' => 1,
                            'updated_at' => now()
                        ]);

                    return redirect(env('FRONTEND_URL') . '/payment/success?code=' . $inputData['vnp_TxnRef']);
                }
            } else {
                // Thanh toán thất bại hoặc bị hủy
                DB::table('payment_transactions')
                    ->where('transaction_code', $inputData['vnp_TxnRef'])
                    ->update([
                        'status' => 'failed',
                        'response_data' => json_encode($inputData),
                        'updated_at' => now()
                    ]);

                return redirect(env('FRONTEND_URL') . '/payment/error?code=' . $inputData['vnp_ResponseCode']);
            }
        } else {
            // Chữ ký không hợp lệ
            return redirect(env('FRONTEND_URL') . '/payment/error?code=97');
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkPaymentStatus(Request $request)
    {
        $transaction = DB::table('payment_transactions')
            ->where('transaction_code', $request->transaction_code)
            ->first();
        
        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy giao dịch'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'transaction_code' => $transaction->transaction_code,
                'payment_status' => $transaction->status,
                'amount' => $transaction->amount,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at
            ]
        ]);
    }
} 