<?php
// Servicio de correo transaccional (Brevo SMTP / API)

require_once __DIR__ . '/config.php';

/**
 * Envía un correo transaccional usando Brevo.
 *
 * Prioridad:
 * 1) SMTP con PHPMailer si está configurado
 * 2) API HTTP de Brevo si existe BREVO_API_KEY
 */
function send_transactional_mail(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): bool
{
    // Intento 1: SMTP PHPMailer
    if (defined('MAIL_ENABLED') && MAIL_ENABLED
        && class_exists('PHPMailer\\PHPMailer\\PHPMailer')
        && defined('MAIL_HOST') && defined('MAIL_PORT')
        && defined('MAIL_USERNAME') && defined('MAIL_PASSWORD')
        && MAIL_USERNAME !== '' && MAIL_PASSWORD !== '') {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->Port = (int)MAIL_PORT;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            if (defined('MAIL_SMTP_SECURE') && MAIL_SMTP_SECURE !== '') {
                $mail->SMTPSecure = MAIL_SMTP_SECURE;
            }
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 15;

            $fromEmail = defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@example.com';
            $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'RestaNet';

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = $htmlBody;
            $mail->AltBody = $textBody !== '' ? $textBody : strip_tags($htmlBody);
            $mail->send();
            return true;
        } catch (\Throwable $e) {
            error_log('MAIL SMTP ERROR: ' . $e->getMessage());
        }
    }

    // Intento 2: API Brevo
    if (defined('BREVO_API_KEY') && BREVO_API_KEY !== '' && function_exists('curl_init')) {
        try {
            $fromEmail = defined('MAIL_FROM') ? MAIL_FROM : 'no-reply@example.com';
            $fromName = defined('MAIL_FROM_NAME') ? MAIL_FROM_NAME : 'RestaNet';

            $payload = [
                'sender' => [
                    'name' => $fromName,
                    'email' => $fromEmail,
                ],
                'to' => [[
                    'email' => $toEmail,
                    'name' => $toName,
                ]],
                'subject' => $subject,
                'htmlContent' => $htmlBody,
                'textContent' => $textBody !== '' ? $textBody : strip_tags($htmlBody),
            ];

            $ch = curl_init('https://api.brevo.com/v3/smtp/email');
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'accept: application/json',
                    'api-key: ' . BREVO_API_KEY,
                    'content-type: application/json',
                ],
                CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
                CURLOPT_TIMEOUT => 20,
            ]);

            $resp = curl_exec($ch);
            $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr = curl_error($ch);
            curl_close($ch);

            if ($curlErr !== '') {
                error_log('MAIL API CURL ERROR: ' . $curlErr);
                return false;
            }

            if ($httpCode >= 200 && $httpCode < 300) {
                return true;
            }

            error_log('MAIL API ERROR HTTP ' . $httpCode . ' RESPONSE: ' . (string)$resp);
            return false;
        } catch (\Throwable $e) {
            error_log('MAIL API ERROR: ' . $e->getMessage());
        }
    }

    return false;
}
