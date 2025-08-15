<?php

namespace App\Helpers;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class QRCodeHelper
{
    /**
     * Maps text lengths to base image sizes
     *
     * @var array
     */
    protected array $qrSizingData = [
        'H' => [
            7 => 21,
            14 => 25,
            24 => 29,
            34 => 33,
            44 => 37,
            58 => 41,
            64 => 45,
            84 => 49,
            98 => 53,
            119 => 57,
            137 => 61,
            155 => 65,
            177 => 69,
            194 => 73,
            220 => 77
        ],
        'M' => [
            14 => 21,
            26 => 25,
            42 => 29,
            62 => 33,
            84 => 37,
            106 => 41,
            122 => 45,
            152 => 49,
            180 => 53,
            213 => 57,
            251 => 61,
            287 => 65,
            331 => 69,
            362 => 73,
            412 => 77
        ]
    ];

    /**
     * Target correction level (currently either M or H)
     *
     * @var string
     */
    protected string $correctionLevel;

    public function __construct()
    {
        $this->correctionLevel = "H";//config('qrcode.pdfQrCodesCorrectionLevel');
    }

    /**
     * Gives the approximate dot resolution of the qr code given a text length
     *
     * @param integer $textLength
     * @return integer
     */
    public function getApproximateQrCodeBaseSize(int $textLength): int
    {
        // Set high default value in case we are asked to size unexpectetly big content
        $foundSizing = 100;
        foreach ($this->qrSizingData[$this->correctionLevel] as $sizingLength => $sizing) {
            if ($textLength <= $sizingLength) {
                // Set the approximate sizing
                // Most likely the resulting QR code will have as many dots in a row as the chosen sizing
                $foundSizing = $sizing;
                break;
            }
        }
        return $foundSizing;
    }

    /**
     * Gets a size that is close to the given target size.
     * Usually is atleast 30 pixel larger than the given size.
     *
     * @param integer $textLength
     * @param integer $targetSize
     * @return integer
     */
    public function getApproximateQrCodeSizeCloseToTargetSize(int $textLength, int $targetSize): int
    {
        $baseSize = $this->getApproximateQrCodeBaseSize($textLength);
        // Calculate the size as a multiple of the base size close to the target size
        // We round up the number to prevent to small image sizes.
        $multiplicator = intval(ceil($targetSize / $baseSize));
        return $multiplicator * $baseSize;
    }

    /**
     * Generate a SVG string for the login link that includes
     * required GET parameters to prefill certain fields.
     *
     * Resulting image can be base64 encoded and inlined
     *
     * @param string|null $loginName
     * @param string|null $code
     * @param integer $targetSize A approximate target size. Resulting qr code is usually atleast 30 pixels bigger to fit the matrix without distorting the image.
     * @param bool $pngImage -if false a svg image will be generated
     * @return array<string,int|string> Return array with the keys "image" for the actual image data, 'baseSize' for the approximate count of dots in the QR code and 'recommendedSize' a size that is calculated to match the wished target size
     */
    public function generateLoginPreFillQRCode(?string $loginName, ?string $code, int $targetSize = 180, bool $pngImage = false): array
    {
        $additionalParameters = [];

        // Add get parameters to url
        if ($loginName !== null) {
            $additionalParameters['id'] = $loginName;
        }

        if ($code !== null) {
            $additionalParameters['code'] = $code;
        }

        $loginUrl = route('login', $additionalParameters);

        $writer = $this->createWriter($loginUrl, $pngImage);

        // Determine data redundancy - L: 7% M:15% Q:25% H:30%
        // We are using a higher level of error correction because the
        // printed QR codes might be used for a long time and can get
        // battered over time.
        // The sizing information is also calculated based on the assumption of the given error correction level.
        if ($this->correctionLevel == 'H') {
            $errorCorrection = ErrorCorrectionLevel::H();
        } else {
            $errorCorrection = ErrorCorrectionLevel::M();
        }

        // Generate a svg in string representation
        // which can be used as simple file or can be encoded as base64 to inline it
        return [
            'baseSize' => $this->getApproximateQrCodeBaseSize(strlen($loginUrl)),
            'recommendedSize' => $this->getApproximateQrCodeSizeCloseToTargetSize(strlen($loginUrl), $targetSize),
            'image' => $writer->writeString($loginUrl, Encoder::DEFAULT_BYTE_MODE_ECODING, $errorCorrection)
        ];
    }

    /**
     * Generate a SVG string
     *
     * Resulting image can be base64 encoded and inlined
     *
     * @param string $text
     * @param integer $targetSize A approximate target size. Resulting qr code is usually atleast 30 pixels bigger to fit the matrix without distorting the image.
     * @return array<string, int|string> Return array with the keys "image" for the actual image data, 'baseSize' for the approximate count of dots in the QR code and 'recommendedSize' a size that is calculated to match the wished target size
     */
    public function generateQRCodeByString(string $text, int $targetSize = 180): array
    {
        $writer = $this->createWriter($text);

        // Determine data redundancy - L: 7% M:15% Q:25% H:30%
        // We are using a higher level of error correction because the
        // printed QR codes might be used for a long time and can get
        // battered over time.
        // The sizing information is also calculated based on the assumption of the given error correction level.
        if ($this->correctionLevel == 'H') {
            $errorCorrection = ErrorCorrectionLevel::H();
        } else {
            $errorCorrection = ErrorCorrectionLevel::M();
        }

        // Generate a svg in string representation
        // which can be used as simple file or can be encoded as base64 to inline it
        return [
            'baseSize' => $this->getApproximateQrCodeBaseSize(strlen($text)),
            'recommendedSize' => $this->getApproximateQrCodeSizeCloseToTargetSize(strlen($text), $targetSize),
            'image' => $writer->writeString($text, Encoder::DEFAULT_BYTE_MODE_ECODING, $errorCorrection)
        ];
    }

    /**
     * Turns a SVG image string to a base64 representation which can be used inside of src attributes of img tags
     *
     * @param string $svgString
     * @return string
     */
    public function convertSVGImageToBase64(string $svgString): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode($svgString);
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param bool $pngImage
     * @return Writer
     */
    private function createWriter(string $content, bool $pngImage = false): Writer
    {
        $imageBackEnd = new SvgImageBackEnd();
        if ($pngImage) {
            $imageBackEnd = new ImagickImageBackEnd('png');
        }
        /** @var ImageRenderer */
        $renderer = new ImageRenderer(
            // multiply by 4 for good measures. The given size here will not result in bigger/smaller files as it only determines the viewbox definition of the svg image
            new RendererStyle($this->getApproximateQrCodeBaseSize(strlen($content)) * 4, 0),
            $imageBackEnd
        );

        return new Writer($renderer);
    }
}
