<?php
include("conexion.php");
date_default_timezone_set('America/Mazatlan');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fkInteresado = $_POST['interesado'];
    $folioIntento = $_POST['folio'];
    $bloqueId = $_POST['bloque_id'];
    $puntuacion = 0; // Variable para almacenar la puntuación
    $sumaPuntajes= 0;
    $horaFinal = date("H:i:s"); // Obtiene la hora actual
    $horaInicio = $_POST['horaInicio'];
    $fkNivel=0;

    // Realizar acciones con $horaInicio y $horaFin

    // Recorre las respuestas y guárdalas en la tabla preguntainteresado
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'ans_') === 0) {
            $preguntaId = substr($key, 4);
            $opcionId = $value;

            // Consulta la base de datos para obtener si la opción es correcta
            $consultaOpcion = "SELECT esCorrecta FROM opcionpregunta WHERE pkOpcionPregunta = '$opcionId'";
            $resultadoOpcion = mysqli_query($link, $consultaOpcion);

            if ($resultadoOpcion) {
                $rowOpcion = mysqli_fetch_assoc($resultadoOpcion);
                $esCorrecta = $rowOpcion['esCorrecta'];

                // Si la opción es correcta, incrementa la puntuación
                if ($esCorrecta == '1') {
                    $puntuacion++;
                }
            } else {
                echo "Error al obtener información de la opción: " . mysqli_error($link);
            }

            // Inserta en la tabla preguntainteresado
            $query = "INSERT INTO preguntainteresado (fkPregunta, fkInteresado, fkOpcionPregunta) VALUES ($preguntaId, $fkInteresado, $opcionId)";
            mysqli_query($link, $query);
        }
    }


    if (isset($_POST['pregunta_abierta'])) {
        // Procesar respuestas de preguntas abiertas (tipo 3)
        foreach ($_POST['pregunta_abierta'] as $preguntaAbiertaId) {
            $respuestaAbierta = mysqli_real_escape_string($link, $POST['texto' . $preguntaAbiertaId]);
            // Inserta la respuesta en la tabla opcionpregunta y recupera el ID
            $insertQuery = "INSERT INTO opcionpregunta (fkPregunta, contenidoOpcion, esCorrecta) VALUES ('$preguntaAbiertaId', '$respuestaAbierta', 0)";
            mysqli_query($link, $insertQuery);
            $idOpcionPregunta = mysqli_insert_id($link);
    
            // Inserta el ID de la opción en la tabla preguntainteresado
            $insertQuery = "INSERT INTO preguntainteresado (fkPregunta, fkInteresado, fkOpcionPregunta) VALUES ('$preguntaAbiertaId', '$fkInteresado', '$idOpcionPregunta')";
            mysqli_query($link, $insertQuery);
        }
    }
    
    $queryInsertBloque = "INSERT INTO bloqueinteresado (fkBloque, fkInteresado, horaInicio, horaFinal, puntaje) VALUES ('$bloqueId', '$fkInteresado', '$horaInicio', '$horaFinal', '$puntuacion')";
        $resultInsertBloque = mysqli_query($link, $queryInsertBloque);

        if ($resultInsertBloque) {
            // Realiza la consulta para obtener la suma de puntajes
            $consultaSumaPuntajes = "SELECT SUM(puntaje) AS sumaPuntajes FROM bloqueinteresado WHERE fkInteresado = $fkInteresado";
            $resultadoSumaPuntajes = mysqli_query($link, $consultaSumaPuntajes);
    
            // Verifica si se ejecutó correctamente la consulta
            if ($resultadoSumaPuntajes) {
                // Extrae el resultado como un arreglo asociativo
                $rowSumaPuntajes = mysqli_fetch_assoc($resultadoSumaPuntajes);
    
                // Almacena la suma de puntajes en una variable
                $sumaPuntajes = (int)$rowSumaPuntajes['sumaPuntajes'];
            } 
        }

        if ($sumaPuntajes >= 59 && $sumaPuntajes <= 68) {
            $fkNivel=6;
        } elseif ($sumaPuntajes >= 49 && $sumaPuntajes <= 58) {
            $fkNivel=5;
        } elseif ($sumaPuntajes >= 39 && $sumaPuntajes <= 48) {
            $fkNivel=4;
        } elseif ($sumaPuntajes >= 29 && $sumaPuntajes <= 38) {
            $fkNivel=3;
        } elseif ($sumaPuntajes >= 19 && $sumaPuntajes <= 28) {
            $fkNivel=2;
        } elseif ($sumaPuntajes >= 1 && $sumaPuntajes <= 18) {
            $fkNivel=1;
        } else {
            echo "Nivel no asignado"; // Manejo de valores fuera de los rangos definidos
        }

    // Actualiza los campos en la tabla intentointeresado
    $queryUpdateIntento = "UPDATE intentointeresado SET horaFinal='$horaFinal', aciertos='$sumaPuntajes' WHERE folioIntento='$folioIntento'";
    $resultUpdateIntento = mysqli_query($link, $queryUpdateIntento);

    $consultaExistencia = "SELECT COUNT(*) AS existe FROM interesadonivel WHERE fkInteresado = $fkInteresado";
    $resultadoExistencia = mysqli_query($link, $consultaExistencia);

    if ($resultadoExistencia) {
        $rowExistencia = mysqli_fetch_assoc($resultadoExistencia);
        $existeRegistro = (int)$rowExistencia['existe'];

        if ($existeRegistro == 0) {
            // Si no existe un registro, realizar la inserción en interesadonivel
            $queryINivel = "INSERT INTO interesadonivel (fkInteresado, fkNivel) VALUES ($fkInteresado, $fkNivel)";
            mysqli_query($link, $queryINivel);
        }
    }

} else {
    echo 'Acceso no autorizado';
}
?>