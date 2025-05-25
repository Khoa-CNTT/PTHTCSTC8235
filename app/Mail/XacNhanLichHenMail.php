<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class XacNhanLichHenMail extends Mailable
{
    use Queueable, SerializesModels;

    public $lichHen;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        // Định dạng ngày nếu có
        if (isset($data['ngay'])) {
            $data['ngay_goc'] = $data['ngay']; // Lưu lại giá trị gốc
            // Không định dạng ở đây vì sẽ dùng Carbon trong view
        }
        
        $this->lichHen = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Xác nhận đặt lịch thành công - PetCare')
            ->view('XacNhanDatLich');
    }
} 