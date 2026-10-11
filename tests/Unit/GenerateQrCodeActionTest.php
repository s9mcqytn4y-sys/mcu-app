<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Actions\Mcu\GenerateQrCodeAction;
use Tests\TestCase;

class GenerateQrCodeActionTest extends TestCase
{
    public function test_generate_svg_and_png_data_uri_dan_string(): void
    {
        $action = new GenerateQrCodeAction();
        $testUrl = 'https://klinikindosehat2003.com/v/00000000-0000-0000-0000-000000000000';

        $svgUri = $action->generateSvgDataUri($testUrl);
        $this->assertStringStartsWith('data:image/svg+xml', $svgUri);

        $pngUri = $action->generatePngDataUri($testUrl, 256);
        $this->assertStringStartsWith('data:image/png;base64,', $pngUri);

        $svgString = $action->generateSvgString($testUrl, 256);
        $this->assertStringContainsString('<svg', $svgString);

        $pngString = $action->generatePngString($testUrl, 256);
        $this->assertNotEmpty($pngString);
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $pngString);
    }
}
