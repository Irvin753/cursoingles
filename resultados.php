<?php
include("conexion.php");

if (isset($_GET['folio'])) {
    $folioIntento = $_GET['folio'];
    $consultanombre = "SELECT concat(nombreInteresado, ' ', apellidoInteresado) AS nombreCompleto, pkInteresado, fechaIntento, horaInicio, horaFinal FROM interesado i INNER JOIN intentointeresado ii on i.pkInteresado=ii.fkInteresado WHERE folioIntento='$folioIntento'";
    $resultado = mysqli_query($link, $consultanombre);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        $nombreCompleto = $row['nombreCompleto'];
        $pkInteresado = $row['pkInteresado'];
        $fecha = $row['fechaIntento'];
        $horaInicio = new DateTime($row['horaInicio']);
        $horaFinal = new DateTime($row['horaFinal']);

        // Consulta para contar las filas
        $consultaConteo = "SELECT COUNT(*) as total FROM preguntainteresado pi INNER JOIN opcionpregunta op ON pi.fkOpcionPregunta = op.pkOpcionPregunta WHERE op.esCorrecta = 1 AND pi.fkInteresado = $pkInteresado";

        $resultadoConteo = mysqli_query($link, $consultaConteo);

        if ($resultadoConteo && mysqli_num_rows($resultadoConteo) > 0) {
            $rowConteo = mysqli_fetch_assoc($resultadoConteo);
            $conteoCorrectas = $rowConteo['total'];
        } else {
            echo "Error al realizar la consulta de conteo.";
        }
        $diferencia = $horaInicio->diff($horaFinal);
        $tiempoTotal = $diferencia->format('%H:%I:%S');
    } else {
        echo "No se encontraron resultados para el folio proporcionado.";
    }
} else {
    echo "Folio no disponible.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Examen</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
    <style>
        /* Agrega estilos CSS personalizados aquí */
        body {
            font-size: 18px;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        .card-body {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 20px;
        }

        .card-text {
            margin-bottom: 20px;
        }

        img {
            max-width: 100%;
            height: auto;
        }
    </style>
    <!-- Agrega cualquier otro enlace a hojas de estilo que necesites -->
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Resultados del Examen</h2>
                <small><p><strong>*Importante* </strong>guardar esta información</p></small>
            </div>
            <div class="card-body">
                <div class="card-text">
                    <?php
                    // Aquí obtienes la información necesaria de la base de datos o donde sea que la tengas almacenada

                    // Traducción de los nombres de los meses
                    $meses = array(
                        'January' => 'enero',
                        'February' => 'febrero',
                        'March' => 'marzo',
                        'April' => 'abril',
                        'May' => 'mayo',
                        'June' => 'junio',
                        'July' => 'julio',
                        'August' => 'agosto',
                        'September' => 'septiembre',
                        'October' => 'octubre',
                        'November' => 'noviembre',
                        'December' => 'diciembre'
                    );

                    // Formatea la fecha en español
                    $fechaFormateada = date("d", strtotime($fecha)) . ' de ' . $meses[date("F", strtotime($fecha))] . ' del ' . date("Y", strtotime($fecha));

                    $url_imgen = '';
                    // Muestra la información en la tarjeta
                    echo '<p><strong>Interesado:</strong>' . $nombreCompleto . '</p>';
                    echo '<p><strong>Aciertos:</strong> ' . $conteoCorrectas . '/68' . '</p>';
                    if ($conteoCorrectas >= 59 && $conteoCorrectas <= 68) {
                        echo '<p><strong>Nivel:</strong> Nivel 7 asignado</p>';
                        $url_imgen = 'img/Block7.gif';
                    } elseif ($conteoCorrectas >= 49 && $conteoCorrectas <= 58) {
                        echo '<p><strong>Nivel:</strong> Nivel 6 asignado</p>';
                        $url_imgen = 'img/Block6.gif';
                    } elseif ($conteoCorrectas >= 39 && $conteoCorrectas <= 48) {
                        echo '<p><strong>Nivel:</strong> Nivel 5 asignado</p>';
                        $url_imgen = 'img/Block5.gif';
                    } elseif ($conteoCorrectas >= 29 && $conteoCorrectas <= 38) {
                        echo '<p><strong>Nivel:</strong> Nivel 4 asignado</p>';
                        $url_imgen = 'img/Block4.gif';
                    } elseif ($conteoCorrectas >= 19 && $conteoCorrectas <= 28) {
                        echo '<p><strong>Nivel:</strong> Nivel 3 asignado</p>';
                        $url_imgen = 'img/Block3.gif';
                    } elseif ($conteoCorrectas >= 1 && $conteoCorrectas <= 18) {
                        echo '<p><strong>Nivel:</strong> Nivel 2 asignado</p>';
                        $url_imgen = 'img/Block2.gif';
                    } else {
                        echo "<p>Nivel 1 asignado</p>"; // Manejo de valores fuera de los rangos definidos
                        $url_imgen = 'img/Block1.gif';
                    }
                    echo '<p><strong>Fecha:</strong> ' . $fechaFormateada . '</p>';
                    echo '<p><strong>Tiempo total del examen:</strong> ' . $tiempoTotal . '</p>';
                    ?>
                </div>
                <img src="<?= $url_imgen ?>" alt="Imagen">
            </div>
        </div>
    </div>

    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <!-- Agrega cualquier otro enlace a scripts que necesites -->
</body>

</html>
