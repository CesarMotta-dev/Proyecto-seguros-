<?php
// Incluir los archivos de PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Usar los namespaces de PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Definición de la función para enviar el correo de cotización
function enviarCorreoCotizacion($destinatario, $nombre, $vehiculo, $modelo, $valor, $precioSeguro) {
    // ESTA ES LA LÍNEA CRÍTICA:
    // Crea una nueva instancia de PHPMailer. Debe estar dentro de la función.
    $mail = new PHPMailer(true); // Pasar 'true' habilita las excepciones para el manejo de errores

    // --- OPCIONAL: Configuración de depuración (descomentar para ver los detalles) ---
    // $mail->SMTPDebug = 2; // Habilita la salida de depuración detallada (0 = off, 1 = client, 2 = client and server)
    // $mail->Debugoutput = 'html'; // O 'echo' si prefieres texto plano en la salida
    // ----------------------------------------------------------------------------------

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP(); // Habilita el envío por SMTP
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
        $mail->SMTPAuth = true; // Habilita la autenticación SMTP

        // Importante: Opciones SSL para evitar problemas de certificado
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        // Credenciales de tu cuenta de Gmail (correo y contraseña de aplicación)
        $mail->Username = 'bedoyamotta@gmail.com'; // Tu dirección de correo de Gmail
        $mail->Password = 'lmvt hdbs brij sjav';     // Tu contraseña de aplicación de Gmail (NO la de tu cuenta de Gmail)

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Habilita el cifrado TLS
        $mail->Port = 587; // Puerto TCP al que se conecta el servidor SMTP

        // Remitente y destinatario del correo
        $mail->setFrom('bedoyamotta@gmail.com', 'AutoSeguro Colombia'); // Tu nombre de remitente
        $mail->addAddress($destinatario, $nombre); // Añade el destinatario (cliente)

        // Contenido del correo
        $mail->isHTML(true); // Establece el formato del correo a HTML
        $mail->Subject = 'Tu Cotizacion de Seguro con AutoSeguro Colombia'; // Asunto del correo

        // Cuerpo HTML del correo con estilos en línea para mejor compatibilidad
        $mail->Body = '
            <div style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
                <div style="background-color: #007bff; color: #ffffff; padding: 20px; text-align: center;">
                    <h1 style="margin: 0; font-size: 24px;">AutoSeguro Colombia</h1>
                    <p style="margin: 5px 0 0;">Tu mejor opcion en seguros para vehiculos</p>
                </div>
                <div style="padding: 20px;">
                    <p>Hola <strong>' . htmlspecialchars($nombre) . '</strong>,</p>
                    <p>¡Gracias por solicitar tu cotizacion con <strong>AutoSeguro Colombia</strong>! Hemos procesado tu información y aquí tienes los detalles:</p>

                    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #f9f9f9; border-radius: 5px;">
                        <tr>
                            <td colspan="2" style="padding: 12px; background-color: #e9ecef; font-weight: bold; border-bottom: 1px solid #ddd;">Detalles de la Cotización</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #eee; width: 40%;"><strong>Vehículo:</strong></td>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #eee;">' . htmlspecialchars($vehiculo) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #eee; width: 40%;"><strong>Modelo:</strong></td>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #eee;">' . htmlspecialchars($modelo) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #eee; width: 40%;"><strong>Valor aproximado del vehículo:</strong></td>
                            <td style="padding: 8px 12px; border-bottom: 1px solid #eee;">$' . number_format($valor, 0, ',', '.') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 12px; font-weight: bold; width: 40%;"><strong>Precio estimado del seguro:</strong></td>
                            <td style="padding: 8px 12px; color: #28a745; font-weight: bold;">$' . number_format($precioSeguro, 0, ',', '.') . '</td>
                        </tr>
                    </table>

                    <p style="margin-top: 25px;">Pronto nos pondremos en contacto contigo para ofrecerte más detalles y finalizar tu cotización.</p>
                    <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                    <p style="margin-top: 30px;">Saludos cordiales,<br>
                    <strong>El equipo de AutoSeguro Colombia</strong></p>
                </div>
                <div style="background-color: #f8f9fa; color: #6c757d; padding: 15px; text-align: center; font-size: 12px; border-top: 1px solid #e9ecef;">
                    <p style="margin: 0;">Este es un correo automático, por favor no respondas a esta dirección.</p>
                    <p style="margin: 5px 0 0;">&copy; ' . date("Y") . ' AutoSeguro Colombia. Todos los derechos reservados.</p>
                </div>
            </div>';

        $mail->send(); // Envía el correo
        return true; // Indica éxito
    } catch (Exception $e) {
        // En caso de error, registra el mensaje de la excepción y devuelve false
        error_log("Error al enviar el correo: " . $e->getMessage());
        return false;
    }
}
?>