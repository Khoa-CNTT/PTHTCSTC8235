<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaiKhoan extends Mailable
{
    use Queueable, SerializesModels;
    public $tieu_de;
    public $giao_dien;
    public $du_lieu;
    public function __construct($tieu_de, $giao_dien, $du_lieu)
    {
        $this->tieu_de    =   $tieu_de;
        $this->giao_dien  =   $giao_dien;
        $this->du_lieu    =   $du_lieu;
    }
    public function build()
    {
        return $this->view($this->giao_dien, ['data' => $this->du_lieu])
                    ->subject($this->tieu_de);
    }
}
