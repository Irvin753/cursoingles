<?php
include("../clases/Conexion.php");


// Crear una instancia de la clase Conexion
$bd = new Conexion();
$conexion = $bd->conectar();

// Inicia una transacción
$conexion->beginTransaction();

try {
    // Llama al procedimiento almacenado para cambiar el estado de la pregunta
    $sqlCambiarEstado = "CALL CambiarEstadoPregunta(:preguntaID)";
    $stmtCambiarEstado = $conexion->prepare($sqlCambiarEstado);
    $stmtCambiarEstado->bindValue(":preguntaID", $_POST['preguntaID']);
    $stmtCambiarEstado->execute();

    // Confirma la transacción
    $conexion->commit();

} catch (PDOException $e) {
    // En caso de error, revierte la transacción
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
