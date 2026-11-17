<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;

class QRCodeController extends Controller
{
    public function index()
    {
        $new_qrCode = $this->validateRequest();

        return response((new QRCodeHelper())->generateQRCodeByString($new_qrCode['url'], $new_qrCode['size'])['image'])
            ->header('Content-Type', 'image/svg+xml');
    }

    protected function validateRequest()
    {
        return request()->validate([
            'url'  => 'required|string',
            'size' => 'required|integer',
        ]);
    }
}