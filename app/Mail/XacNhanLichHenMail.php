<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class XacNhanLichHenMail extends Mailable
{

    use Queueable, SerializesModels;

    public $lichHen;

    public function __construct($lichHen)
    {
        $this->lichHen = $lichHen;
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt lịch- PetCare')
            ->view('emails.xac_nhan_lich_hen');
    }
}
