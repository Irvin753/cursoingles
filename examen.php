<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba curso</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/font.css">
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
</head>
<style>
        .error {
            color: red;
        }

        .mi-boton {
            background-color: #25736d;
            color: #fff;  /* Color del texto */
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Estilo del botón al pasar el ratón (hover) */
        .mi-boton:hover {
            background-color: #378e85;  /* Color más claro en hover */
        }
        .header-container {
    text-align: center; /* Centra el contenido horizontalmente */
    padding: 20px 10px; /* Ajusta el espacio desde el borde de la pantalla */
    margin-top: 20px; /* Ajusta la distancia vertical desde el borde de la pantalla */
}

.folio-title, .temporizador-container {
    color: #9C9C9C;
}

.temporizador-container {
    position: relative; /* Para que el position: absolute funcione dentro de este contenedor */
}

.temporizador-container span {
    position: absolute;
    top: 0;
    right: 0;
}

textarea {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    resize: vertical; /* Permite que el usuario redimensione verticalmente el textarea */
}

#tabla-container {
            background-color: #EEEEEE; /* Color de fondo para pantallas grandes */
            padding: 20px; /* Espaciado interno para el contenedor */
            margin: 20px auto; /* Centra el contenedor y agrega márgenes a los lados */
            max-width: 100%; /* Establece un ancho máximo para el contenedor */
        }

        table {
            border-collapse: collapse;
            width: 100%; /* Ocupa el 100% del ancho disponible */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #555;
        }

        /* Estilos para pantallas grandes */
        @media screen and (min-width: 768px) {
            #tabla-container {
                position: sticky;
                top: 0;
                z-index: 1;
            }
        }

        /* Estilos para filas de la tabla */
        tr {
            text-align: center; /* Centra el contenido de las filas */
        }

        /* Estilos para las celdas de encabezado */
        td[colspan='3'] {
            background-color: #f8f8f8; /* Color de fondo para las celdas de encabezado */
        }

        /* Estilos para las celdas de la tabla */
        td {
            border: 1px solid #ddd;
            padding: 8px;
            width: 33%; /* Divide las celdas en tercios */
        }

    </style>
<body>
<?php
include("conexion.php");
date_default_timezone_set('America/Mazatlan');
if (isset($_GET['folio'])) {
    $folioIntento = $_GET['folio'];
    $consultaInteresado = "SELECT fkInteresado, interesado.telefonoInteresado,  concat(interesado.nombreInteresado, ' ', interesado.apellidoInteresado) AS infInteresado FROM intentointeresado INNER JOIN interesado on intentointeresado.fkInteresado=interesado.pkInteresado WHERE folioIntento='$folioIntento'";
    $resultadoInteresado = mysqli_query($link, $consultaInteresado);

    // Verifica si hay resultados
    if ($resultadoInteresado) {
        $rowInteresado = mysqli_fetch_assoc($resultadoInteresado);
        $fkInteresado = $rowInteresado['fkInteresado'];
        $telefonoInteresado = $rowInteresado['telefonoInteresado'];
        $nombreInteresado = $rowInteresado['infInteresado'];

    } else {
        echo "<br>Error al realizar la consulta: " . mysqli_error($link);
    }
} else {
    echo "Folio no disponible.";
}
?>

    <div id="bloques-container">
        <?php
        $result = $link->query('SELECT DISTINCT pkBloque FROM bloque ORDER BY pkBloque');
        $bloques = $result->fetch_all(MYSQLI_ASSOC);
        echo "<div class='header-container'>";
echo "</div>
";
?>
<div id="tabla-container">
    <table>
            <tr>
                <td class="mi-boton">Folio</td>
                <td class="mi-boton">Nombre</td>
                <td class="mi-boton">Celular</td>
            </tr>
            <tr>
                <td><?php echo $folioIntento; ?></td>
                <td><?php echo $nombreInteresado; ?></td>
                <td><?php echo $telefonoInteresado; ?></td>
            </tr>
        </table>
</div>
<?php


        foreach ($bloques as $bloque) {
            echo '<div class="bloque" id="bloque' . $bloque['pkBloque'] . '" data-hora-inicio="' . date('Y-m-d H:i:s') . '">';
            echo '<div class="panel" style="margin:5%">';
            echo "<div class='temporizador-container'><span class='tiempoRestante'></span></div>";
            if($bloque['pkBloque']==9){
                ?>
                <center>
                <audio id="audioPlayer" controls>
                    <source src="05_B_1.mp3" type="audio/mp3">
                    Tu navegador no soporta el elemento de audio.
                </audio>
                </center>
                <?php
            }
            ?>
            <br>
            <br>
            <?php
            $q = mysqli_query($link, "SELECT pkPregunta AS ID_Pregunta, contenidoPregunta AS Pregunta, fkTipoPregunta AS tipoPregunta FROM pregunta WHERE fkBloque = {$bloque['pkBloque']}");

            echo '<form onsubmit="return validateForm();" onsubmit="return confirmarGuardarRespuestas(' . $bloque['pkBloque'] . ', this)">';
            echo "<input type='hidden' name='interesado' value='{$fkInteresado}'>";
            echo "<input type='hidden' name='folio' value='{$folioIntento}'>";
            echo "<input type='hidden' name='bloque_id' value='{$bloque['pkBloque']}'>
            <input type='hidden' name='horaFin' value=''>";
            while ($row = mysqli_fetch_array($q)) {
                $qid = $row['ID_Pregunta'];
                $pregunta = $row['Pregunta'];
                $tpregunta = $row['tipoPregunta'];
            
                echo '<b>' . $pregunta . '</b><br /><br />';
            
                // Obtener opciones para la pregunta actual
                $qOpciones = mysqli_query($link, "SELECT fkPregunta AS ID_Pregunta, pkOpcionPregunta AS ID_Opcion, contenidoOpcion AS Opcion, esCorrecta FROM opcionpregunta WHERE fkPregunta = {$qid}");
            
                // Recoger opciones en un array y mezclarlas
                $opciones = [];
                while ($rowOpcion = mysqli_fetch_array($qOpciones)) {
                    $opciones[] = $rowOpcion;
                }
                shuffle($opciones);
            
                if ($tpregunta == 3) {
                    echo '<textarea id="texto_' . $qid . '" name="texto_' . $qid . '" data-tipo-pregunta="3" placeholder="Al menos 50 caracteres"></textarea>';
                    echo '<input type="hidden" name="pregunta_abierta[]" value="' . $qid . '">';
                    echo '<p id="errorMensaje_' . $qid . '" class="error"></p>';
                } else {
                    // Mostrar las opciones de respuesta
                    foreach ($opciones as $rowOpcion) {
                        $opcion = $rowOpcion['Opcion'];
                        $opcionid = $rowOpcion['ID_Opcion'];
                        $esCorrecta = $rowOpcion['esCorrecta'];
                        echo "<input type='radio' name='ans_{$qid}' value='{$opcionid}' data-esCorrecta='{$esCorrecta}'>{$opcion}<br /><br />";
                    }
                }
            }
            
            echo '<center>';
            echo '<button type="submit" class="mi-boton">Guardar Respuestas</button></form></div></div>';
            echo '</center>';
        }
        ?>
    </div>

    <script>
        var audioPlayer = document.getElementById('audioPlayer');
        var puntuacionTotal = 0;

        function validateForm() {
    var preguntasAbiertas = document.getElementsByName('pregunta_abierta[]');
    var isValid = true;

    for (var i = 0; i < preguntasAbiertas.length; i++) {
        var textareaValue = document.getElementById('texto_' + preguntasAbiertas[i].value).value;
        if (textareaValue.length < 50) {
            document.getElementById('errorMensaje_' + preguntasAbiertas[i].value).innerHTML = 'El texto debe tener al menos 50 caracteres.';
            isValid = false;
        } else {
            document.getElementById('errorMensaje_' + preguntasAbiertas[i].value).innerHTML = '';
        }
    }

    return isValid;
}
$(document).ready(function () {
    var bloques = $(".bloque");
    var currentBloque = 0;
    var timerInterval;

    function showBloque(index) {
        bloques.hide();
        $(bloques[index]).show();

        if (!$(bloques[index]).hasClass('temporizador-iniciado')) {
            var horaInicio = obtenerHoraInicio(); // Obtener la hora de inicio actual
            $(bloques[index]).data('hora-inicio', horaInicio);
            startTimer(index);
            $(bloques[index]).addClass('temporizador-iniciado');
        }
    }

    function obtenerHoraInicio() {
        // Obtener la hora actual
        var ahora = new Date();
        var horas = ahora.getHours();
        var minutos = ahora.getMinutes();
        var segundos = ahora.getSeconds();

        // Formatear la hora como "HH:mm:ss"
        var horaInicio = horas + ':' + minutos + ':' + segundos;
        return horaInicio;
    }

    function startTimer(index) {
        clearInterval(timerInterval);
        var tiempoRestante = 600; // 10 minutos
        var horaInicio = $(bloques[index]).data('hora-inicio');

        timerInterval = setInterval(function () {
            var minutos = Math.floor(tiempoRestante / 60);
            var segundos = tiempoRestante % 60;

            // Asegurarse de que los minutos y segundos tengan dos dígitos
            minutos = minutos < 10 ? '0' + minutos : minutos;
            segundos = segundos < 10 ? '0' + segundos : segundos;

            $(bloques[index]).find('.tiempoRestante').text('Tiempo restante: ' + minutos + ':' + segundos);

            if (tiempoRestante <= 0) {
                clearInterval(timerInterval);
                guardarRespuestas(index);
                moveToNextBloque();
            }

            tiempoRestante--;
        }, 1000);
    }

    function guardarRespuestas(index, horaInicio) {
        var formData = $('#bloque' + index + ' form').serialize();
        var puntuacionTotal = 0;

        $.ajax({
            type: 'POST',
            url: 'procesar_respuestas.php',
            data: formData + '&horaInicio=' + encodeURIComponent(horaInicio),
            success: function (response) {
            },
            error: function () {
                alert('Error al guardar las respuestas.');
            }
        });
    }

    function moveToNextBloque() {
        currentBloque++;
        if (currentBloque < bloques.length) {
            showBloque(currentBloque);
        } else {
            alert('¡Ha completado todos los bloques!');
            window.location.href = "resultados.php?folio=" + encodeURIComponent('<?php echo $folioIntento; ?>');
        }
    }

    showBloque(currentBloque);

    $('form').submit(function (e) {
        e.preventDefault();

        var horaInicio = $(bloques[currentBloque]).data('hora-inicio');

        if (confirm('¿Está seguro de guardar las respuestas? Una vez guardadas, no podrá volver al bloque anterior.')) {
            guardarRespuestas(currentBloque, horaInicio);
            moveToNextBloque();
        }
    });

    setTimeout(function () {
        guardarRespuestas(currentBloque, $(bloques[currentBloque]).data('hora-inicio'));
        moveToNextBloque();
    }, 90000); // 90 segundos para el primer bloque
});

</script>

</body>

</html>