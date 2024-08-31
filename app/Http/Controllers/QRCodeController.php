<?php

namespace App\Http\Controllers;

use App\Helpers\QRCodeHelper;

class QRCodeController extends Controller
{
    public function index()
    {
        $new_qrCode = $this->validateRequest();

        if (request()->wantsJson()) {
            return (new QRCodeHelper())->generateQRCodeByString($new_qrCode['url'], $new_qrCode['size']);
        }
    }

    protected function validateRequest()
    {
        return request()->validate([
            'url' => 'sometimes',
            'title' => 'sometimes',
            'description' => 'sometimes',
            'size' => 'sometimes',

        ]);
    }
}
