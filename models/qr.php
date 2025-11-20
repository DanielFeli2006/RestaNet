<?php
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

function ensure_dir(string $dir): void {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

function resolve_error_level(string $level): ErrorCorrectionLevel {
    return match (strtolower($level)) {
        'low' => ErrorCorrectionLevel::Low,
        'medium' => ErrorCorrectionLevel::Medium,
        'quartile' => ErrorCorrectionLevel::Quartile,
        default => ErrorCorrectionLevel::High,
    };
}

function generate_qr(string $text, string $outputPath, array $options = []): array {
    $opts = array_merge([
        'size' => 300,
        'margin' => 10,
        'errorCorrection' => 'high',
        'format' => 'png',
        'force' => false,
    ], $options);

    try {
        if (!$opts['force'] && file_exists($outputPath)) {
            return ['ok' => true, 'path' => $outputPath, 'created' => false, 'error' => null, 'dataUri' => null];
        }

        if (!class_exists(QrCode::class)) {
            return ['ok' => false, 'path' => null, 'created' => false, 'error' => 'endroid/qr-code not installed', 'dataUri' => null];
        }

        $format = strtolower($opts['format']);
        $writer = ($format === 'svg' && class_exists(SvgWriter::class))
            ? new SvgWriter()
            : new PngWriter();

        $qrCode = new QrCode(
            data: $text,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: resolve_error_level($opts['errorCorrection']),
            size: (int) $opts['size'],
            margin: (int) $opts['margin']
        );

        $result = $writer->write($qrCode);
        ensure_dir(dirname($outputPath));
        $result->saveToFile($outputPath);

        $dataUri = method_exists($result, 'getDataUri') ? $result->getDataUri() : null;

        return ['ok' => true, 'path' => $outputPath, 'created' => true, 'error' => null, 'dataUri' => $dataUri];
    } catch (Throwable $e) {
        error_log('QR generation failed: ' . $e->getMessage());
        return ['ok' => false, 'path' => null, 'created' => false, 'error' => $e->getMessage(), 'dataUri' => null];
    }
}
