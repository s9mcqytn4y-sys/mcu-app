<?php

declare(strict_types=1);

namespace App\Actions\Mcu;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\SvgWriter;

final class GenerateQrCodeAction
{
    public function generateSvgDataUri(string $url): string
    {
        $builder = new Builder(
            writer: new SvgWriter(),
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            size: 260,
            margin: 10,
        );

        return $builder->build()->getDataUri();
    }
}
