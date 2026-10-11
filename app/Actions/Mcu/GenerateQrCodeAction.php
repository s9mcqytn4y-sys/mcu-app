<?php

declare(strict_types=1);

namespace App\Actions\Mcu;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

final class GenerateQrCodeAction
{
    public function generateSvgDataUri(string $url): string
    {
        return Builder::create()
            ->writer(new SvgWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->size(260)
            ->margin(10)
            ->build()
            ->getDataUri();
    }

    public function generateSvgString(string $url, int $size = 512): string
    {
        return Builder::create()
            ->writer(new SvgWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->size($size)
            ->margin(10)
            ->build()
            ->getString();
    }

    public function generatePngDataUri(string $url, int $size = 512): string
    {
        return Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->size($size)
            ->margin(16)
            ->build()
            ->getDataUri();
    }

    public function generatePngString(string $url, int $size = 1024): string
    {
        return Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->size($size)
            ->margin(16)
            ->build()
            ->getString();
    }
}

