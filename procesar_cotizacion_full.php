<?php
// Habilitar la visualización de todos los errores y advertencias para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluye el archivo para enviar correos (PHPMailer).
// Asegúrate de que 'enviar_correo.php' esté en la misma carpeta.
require 'enviar_correo.php';

// --- Configuración de la Conexión a la Base de Datos ---
$servidor = "localhost";
$usuario = "root";
$password = "Chocapic2024"; // ¡Asegúrate de que esta sea la contraseña correcta!
$nombre_bd = "inventario_db";

// Establecer conexión con la base de datos
$conexion = new mysqli($servidor, $usuario, $password, $nombre_bd);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

// --- Captura y Sanitización de Datos del Formulario ---
// Es vital sanitizar los datos de entrada para prevenir ataques (ej. XSS, inyección SQL).
// htmlspecialchars() convierte caracteres especiales a entidades HTML, haciéndolos seguros para mostrar en HTML.
// $conexion->real_escape_string() escapa caracteres para prevenir inyecciones SQL.

// Datos del Cliente
// Usamos ?? '' para asegurar que la variable siempre sea una cadena, incluso si $_POST no la envía.
$nombre_cliente = $conexion->real_escape_string(htmlspecialchars($_POST['cliente_nombre'] ?? ''));
$apellido = $conexion->real_escape_string(htmlspecialchars($_POST['apellido'] ?? ''));
$tipo_documento = $conexion->real_escape_string(htmlspecialchars($_POST['tipo_documento'] ?? ''));
$numero_documento = $conexion->real_escape_string(htmlspecialchars($_POST['numero_documento'] ?? ''));
$email = $conexion->real_escape_string(htmlspecialchars($_POST['email'] ?? ''));
$telefono = $conexion->real_escape_string(htmlspecialchars($_POST['telefono'] ?? ''));
$direccion = $conexion->real_escape_string(htmlspecialchars($_POST['direccion'] ?? ''));

// Datos del Vehículo
$placa = $conexion->real_escape_string(htmlspecialchars(strtoupper($_POST['placa'] ?? ''))); // Placa en mayúsculas
$marca = $conexion->real_escape_string(htmlspecialchars($_POST['marca'] ?? ''));
$modelo = $conexion->real_escape_string(htmlspecialchars($_POST['modelo'] ?? ''));
$anio = (int) ($_POST['anio'] ?? 0); // Convertir a entero, 0 si no se envía
$color = $conexion->real_escape_string(htmlspecialchars($_POST['color'] ?? ''));
$cilindraje = $conexion->real_escape_string(htmlspecialchars($_POST['cilindraje'] ?? ''));
$tipo_vehiculo = $conexion->real_escape_string(htmlspecialchars($_POST['tipo_vehiculo'] ?? ''));

// --- Procesamiento del Valor del Vehículo (CRÍTICO) ---
// Aseguramos que $_POST['valor_vehiculo'] existe y sea una cadena
$valor_vehiculo_raw = isset($_POST['valor_vehiculo']) ? (string)$_POST['valor_vehiculo'] : '';

// Eliminamos espacios al inicio y al final
$valor_vehiculo_trimmed = trim($valor_vehiculo_raw);

// Limpiamos todos los caracteres no numéricos (excepto punto)
$valor_vehiculo_limpio = preg_replace('/[^\d.]/', '', $valor_vehiculo_trimmed);

// Convertimos a float
$valor_vehiculo_numerico = floatval($valor_vehiculo_limpio);


$id_aseguradora = (int) ($_POST['id_aseguradora'] ?? 0); // Convertir a entero, 0 si no se seleccionó

    // Validación del valor del vehículo
    if ($valor_vehiculo_numerico <= 0 || empty($nombre_cliente) || empty($email) || empty($placa)) {
        echo "Error: Faltan datos obligatorios o el valor del vehículo no es válido.";
        exit;
    }

// --- Validación básica (puedes añadir más validaciones si es necesario) ---
if (empty($nombre_cliente) || empty($email) || empty($placa) || empty($tipo_vehiculo) || $valor_vehiculo_numerico <= 0) {
    echo "Error: Faltan datos obligatorios del formulario o el valor del vehículo no es válido. Revise la información proporcionada.";
    exit; // Detiene el script si faltan datos
}

// --- Cálculo del Porcentaje del Seguro (simulación) ---
$porcentaje = 0.05; // Porcentaje por defecto

switch ($tipo_vehiculo) {
    case 'Automóvil':
        $porcentaje = 0.04;
        break;
    case 'Motocicleta':
        $porcentaje = 0.06; // Suelen ser más caros de asegurar
        break;
    case 'Camioneta':
        $porcentaje = 0.055;
        break;
    case 'Camión':
        $porcentaje = 0.07;
        break;
    default:
        $porcentaje = 0.05; // Porcentaje por defecto si el tipo no coincide
}

// Calcula el precio estimado del seguro
$precioSeguro = round($valor_vehiculo_numerico * $porcentaje, 2); // Redondea a 2 decimales

// --- Insertar la Cotización en la Base de Datos ---
// Usar Prepared Statements para seguridad y eficiencia (MUY RECOMENDADO)
// Preparar la consulta con los nombres correctos de columnas
$stmt = $conexion->prepare("INSERT INTO cotizaciones (
    cliente_nombre, apellido, tipo_documento, numero_documento, email, telefono, direccion,
    placa, marca, modelo, anio, color, cilindraje, tipo_vehiculo,
    valor_vehiculo, id_aseguradora, fecha_registro
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

// Verifica que la preparación haya sido exitosa
if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

// Asignar variables con tipos correctos (s=string, i=integer, d=double)
$stmt->bind_param(
    "ssssssssssisssdi",
    $nombre_cliente,  // s
    $apellido,        // s
    $tipo_documento,  // s
    $numero_documento,// s
    $email,           // s
    $telefono,        // s
    $direccion,       // s
    $placa,           // s
    $marca,           // s
    $modelo,          // s
    $anio,            // i
    $color,           // s
    $cilindraje,      // s (según tabla es VARCHAR(20))
    $tipo_vehiculo,   // s
    $valor_vehiculo_numerico, // d
    $id_aseguradora   // i
);

// Ejecutar la consulta
if (!$stmt->execute()) {
    die("Error en execute: " . $stmt->error);
}

echo "Cotizacion insertada correctamente.";


// Ejecutar la consulta preparada
if ($stmt->execute()) {
    // Si la cotización se guardó con éxito, intenta enviar el correo
    // Los parámetros para enviarCorreoCotizacion son: destinatario, nombre, vehiculo, modelo, valor, precioSeguro
    if (enviarCorreoCotizacion($email, $nombre_cliente, $tipo_vehiculo, $modelo, $valor_vehiculo_numerico, $precioSeguro)) {
        // Redirección y mensaje de éxito
  
  $precio_formateado = number_format($precioSeguro, 0, ',', '.');

  echo <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="refresh" content="20;url=index.html"> <meta charset="UTF-8">
    <title>Cotizacion Exitosa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="card shadow p-4">
            <h2 class="text-success">¡Cotizacion enviada y guardada con exito!</h2>
            <p class="fs-5">Gracias, <strong>{$nombre_cliente}</strong>.</p>
            <p class="fs-5">El precio aproximado de tu seguro es: <strong>$ {$precio_formateado}</strong></p>
            <p>Se ha enviado un correo de confirmación a: <strong>{$email}</strong></p>
            <p>Serás redirigido a la página principal en unos segundos...</p>
            <a href="index.html" class="btn btn-primary mt-3">Ir ahora</a>
        </div>
    </div>
</body>
</html>
HTML;
    } else {
        echo "La cotización fue guardada en la base de datos, pero hubo un problema al enviar el correo. Por favor, revise los logs del servidor.";
        // El error del correo ya se loggea en enviar_correo.php
    }
} else {
    echo "Error al guardar la cotización en la base de datos: " . $stmt->error;
}

// Cerrar el statement y la conexión
$stmt->close();
$conexion->close();
?>