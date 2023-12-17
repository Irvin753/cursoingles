<?php
include("../clases/Conexion.php");

// Crear una instancia de la clase Conexion
$bd = new Conexion();
$conexion = $bd->conectar();

// Inicia una transacción
$conexion->beginTransaction();

try {
    // Llama al procedimiento almacenado para actualizar la pregunta
    $sqlActualizarPregunta = "CALL ActualizarPregunta(:contenidoPregunta, :fkBloque, :fkTipoPregunta, :preguntaID)";

    $stmtActualizarPregunta = $conexion->prepare($sqlActualizarPregunta);

    $stmtActualizarPregunta->bindValue(":contenidoPregunta", $_POST['contenidoPregunta']);
    $stmtActualizarPregunta->bindValue(":fkBloque", $_POST['fkBloque']);
    $stmtActualizarPregunta->bindValue(":fkTipoPregunta", $_POST['fkTipoPregunta']);
    $stmtActualizarPregunta->bindValue(":preguntaID", $_POST['preguntaID']);

    $stmtActualizarPregunta->execute();

    // Desactivar temporalmente la restricción de clave externa
    // Se tiene que hacer esto porque la opción a editar se encuentra en la tabla de "preguntainteresado"
    // Y al estar ahí, no se puede eliminar, porque es clave foránea
    $conexion->exec('SET foreign_key_checks = 0;');

    // Eliminar las opciones de respuesta actuales de la pregunta
    $sqlEliminarOpciones = "CALL EliminarOpcionesPregunta(:preguntaID)";
    $stmtEliminarOpciones = $conexion->prepare($sqlEliminarOpciones);
    $stmtEliminarOpciones->bindValue(":preguntaID", $_POST['preguntaID']);
    $stmtEliminarOpciones->execute();

    // Volver a activar la restricción de clave externa
    $conexion->exec('SET foreign_key_checks = 1;');

    // Insertar las nuevas opciones de respuesta
    $sqlInsertarOpciones = "CALL InsertarOpcionesPregunta2(:preguntaID, :opcionRespuesta)";
    $stmtInsertarOpciones = $conexion->prepare($sqlInsertarOpciones);

    foreach ($_POST['opcionRespuesta'] as $opcionRespuesta) {
        $stmtInsertarOpciones->bindValue(":preguntaID", $_POST['preguntaID']);
        $stmtInsertarOpciones->bindValue(":opcionRespuesta", $opcionRespuesta);
        $stmtInsertarOpciones->execute();
    }

    // Confirma la transacción
    $conexion->commit();

    echo "Pregunta y opciones actualizadas correctamente.";
} catch (PDOException $e) {
    // En caso de error, revierte la transacción
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
