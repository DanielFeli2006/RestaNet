<?php
// Utilitario de generación de códigos QR
// Requiere composer: endroid/qr-code. Provee compatibilidad hacia atrás y nuevas utilidades.

function ensure_dir(string $dir): void {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

/**
 * Genera un QR y devuelve un array con resultado.
 * Opciones soportadas: size, margin, errorCorrection (low, medium, quartile, high), format (png|svg), force (bool)
 * Retorna: ['ok'=>bool, 'path'=>string|null, 'created'=>bool, 'error'=>string|null, 'dataUri'=>string|null]
 */
function generate_qr(string $text, string $outputPath, array $options = []): array {
    $opts = array_merge([
        'size' => 300,
        'margin' => 10,
        'errorCorrection' => 'high',
        'format' => 'png',
        'force' => false,
    ], $options);

    try {
        // Si el archivo ya existe y no se fuerza, no regenerar
        if (!$opts['force'] && file_exists($outputPath)) {
            return ['ok' => true, 'path' => $outputPath, 'created' => false, 'error' => null, 'dataUri' => null];
        }

        if (!class_exists('Endroid\\QrCode\\Writer\\PngWriter') && !class_exists('Endroid\\QrCode\\Writer\\SvgWriter')) {
            return ['ok' => false, 'path' => null, 'created' => false, 'error' => 'endroid/qr-code not installed', 'dataUri' => null];
        }

        $writer = null;
        $format = strtolower($opts['format']);
        if ($format === 'svg' && class_exists('Endroid\\QrCode\\Writer\\SvgWriter')) {
            $writer = new \Endroid\QrCode\Writer\SvgWriter();
        } else {
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            $format = 'png';
        }

        $encoding = new \Endroid\QrCode\Encoding\Encoding('UTF-8');
        // Mapear nivel de corrección
        switch (strtolower($opts['errorCorrection'])) {
            case 'low':
                $ec = new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow();
                break;
            case 'medium':
                $ec = new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium();
                break;
            case 'quartile':
                $ec = new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile();
                break;
            case 'high':
            default:
                $ec = new \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh();
                break;
        }

        $qrCode = new \Endroid\QrCode\QrCode(
            data: $text,
            encoding: $encoding,
            errorCorrectionLevel: $ec,
            size: (int)$opts['size'],
            margin: (int)$opts['margin']
        );

        $result = $writer->write($qrCode);

        ensure_dir(dirname($outputPath));
        $result->saveToFile($outputPath);

        $dataUri = null;
        if (method_exists($result, 'getDataUri')) {
            $dataUri = $result->getDataUri();
        }

        return ['ok' => true, 'path' => $outputPath, 'created' => true, 'error' => null, 'dataUri' => $dataUri];
    } catch (Throwable $e) {
        error_log('QR generation failed: ' . $e->getMessage());
        return ['ok' => false, 'path' => null, 'created' => false, 'error' => $e->getMessage(), 'dataUri' => null];
    }
}

/**
 * Compat wrapper para mantener compatibilidad con código existente.
 * Devuelve true si el archivo fue generado/existe.
 */
function generate_qr_png(string $text, string $outputPath, array $options = []): bool {
    $res = generate_qr($text, $outputPath, $options);
    return (bool)$res['ok'];
}

/**
 * Genera un data URI del QR (útil para incrustar en PDFs sin depender de rutas de archivo).
 * Retorna string data:image/... o null en error.
 */
function generate_qr_data_uri(string $text, array $options = []): ?string {
    // Escribimos a un writer en memoria usando writer->write y result->getDataUri()
    $tmpPath = sys_get_temp_dir() . '/qr_temp_' . uniqid() . '.' . (strtolower($options['format'] ?? 'png'));
    $res = generate_qr($text, $tmpPath, array_merge($options, ['force' => true]));
    if ($res['ok']) {
        if (!empty($res['dataUri'])) {
            // limpiar archivo temporal
            if (file_exists($tmpPath)) {@unlink($tmpPath);} 
            return $res['dataUri'];
        }
        // Fallback: leer fichero y codificar
        if (file_exists($tmpPath)) {
            $b = base64_encode(file_get_contents($tmpPath));
            $mime = $options['format'] === 'svg' ? 'image/svg+xml' : 'image/png';
            @unlink($tmpPath);
            return 'data:' . $mime . ';base64,' . $b;
        }
    }
    return null;
}
