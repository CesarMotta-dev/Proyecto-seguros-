<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "Chocapic2024", "inventario_db"); //

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta de datos - AHORA CON JOINS
// Seleccionamos las columnas relevantes de cotizaciones, cliente y vehiculo
$sql = "SELECT
            coti.id_cotizacion,
            cl.nombre AS cliente_nombre,
            cl.apellido AS cliente_apellido,
            cl.correo,
            cl.telefono,
            cl.numero_documento,
            v.placa,
            v.marca,
            v.modelo,
            v.año AS vehiculo_anio,
            v.color,
            v.cilindraje,
            v.tipo_vehiculo,
            coti.valor_vehiculo_cotizado,
            coti.precio_seguro_cotizado,
            coti.fecha_solicitud
        FROM cotizaciones AS coti  -- Changed from Cotizaciones to cotizaciones
        JOIN cliente AS cl ON coti.id_cliente = cl.id_cliente -- Changed from Cliente to cliente
        JOIN vehiculo AS v ON coti.id_vehiculo = v.id_vehiculo -- Changed from Vehiculo to vehiculo
        ORDER BY coti.fecha_solicitud DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Opcional: Centrar la tabla en pantallas grandes */
        .container {
            max-width: 1200px; /* Ajusta el ancho máximo para más columnas */
        }
        table {
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Listado de Cotizaciones Detalladas</h2>

        <?php if ($resultado->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Cotización</th>
                            <th>Cliente (Nombre)</th>
                            <th>Cliente (Apellido)</th>
                            <th>Email Cliente</th>
                            <th>Teléfono Cliente</th>
                            <th>Documento Cliente</th>
                            <th>Placa Vehículo</th>
                            <th>Marca Vehículo</th>
                            <th>Modelo Vehículo</th>
                            <th>Año Vehículo</th>
                            <th>Color Vehículo</th>
                            <th>Cilindraje</th>
                            <th>Tipo Vehículo</th>
                            <th>Valor Vehículo Cotizado</th>
                            <th>Precio Seguro Cotizado</th>
                            <th>Fecha Solicitud</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $fila["id_cotizacion"] ?></td>
                                <td><?= $fila["cliente_nombre"] ?></td>
                                <td><?= $fila["cliente_apellido"] ?></td>
                                <td><?= $fila["correo"] ?></td>
                                <td><?= $fila["telefono"] ?></td>
                                <td><?= $fila["numero_documento"] ?></td>
                                <td><?= $fila["placa"] ?></td>
                                <td><?= $fila["marca"] ?></td>
                                <td><?= $fila["modelo"] ?></td>
                                <td><?= $fila["vehiculo_anio"] ?></td>
                                <td><?= $fila["color"] ?></td>
                                <td><?= $fila["cilindraje"] ?></td>
                                <td><?= $fila["tipo_vehiculo"] ?></td>
                                <td>$<?= number_format($fila["valor_vehiculo_cotizado"], 2, ',', '.') ?></td>
                                <td>$<?= number_format($fila["precio_seguro_cotizado"], 2, ',', '.') ?></td>
                                <td><?= $fila["fecha_solicitud"] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">No hay cotizaciones registradas.</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.html" class="btn btn-primary">Volver a la página principal</a>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos al final del script
$conexion->close();
?>