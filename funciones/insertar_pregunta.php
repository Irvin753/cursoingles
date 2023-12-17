<?php
include("../clases/Conexion.php");

// Crear una instancia de la clase Conexion
$bd = new Conexion();
$conexion = $bd->conectar();

// Inicia una transacción
$conexion->beginTransaction();

try {
    // Llama al procedimiento almacenado para la inserción de la pregunta
    $sqlInsertarPregunta = "CALL InsertarPregunta(:contenidoPregunta, :fkBloque, :fkTipoPregunta, @idPregunta)";
    
    $stmtInsertarPregunta = $conexion->prepare($sqlInsertarPregunta);
    $stmtInsertarPregunta->bindValue(":contenidoPregunta", $_POST['contenidoPregunta']);
    $stmtInsertarPregunta->bindValue(":fkBloque", $_POST['fkBloque']);
    $stmtInsertarPregunta->bindValue(":fkTipoPregunta", $_POST['fkTipoPregunta']);
    $stmtInsertarPregunta->execute();

    // Obtiene el ID de la pregunta recién insertada
    $stmtObtenerIdPregunta = $conexion->query("SELECT @idPregunta AS idPregunta");
    $idPregunta = $stmtObtenerIdPregunta->fetch(PDO::FETCH_ASSOC)['idPregunta'];

    // Llama al procedimiento almacenado para la inserción de las opciones
    $sqlInsertarOpciones = "CALL InsertarOpcionesPregunta(:contenidoOpcion, :fkPregunta, :esCorrecta)";
    
    $stmtInsertarOpciones = $conexion->prepare($sqlInsertarOpciones);
    
    // Recorre el array de opciones y las inserta una por una
    foreach ($_POST['contenidoOpcion'] as $index => $opcion) {
        $esCorrecta = ($index == 0) ? 1 : 0; // La primera opción es correcta, las demás no
        $stmtInsertarOpciones->bindValue(":contenidoOpcion", $opcion);
        $stmtInsertarOpciones->bindValue(":fkPregunta", $idPregunta);
        $stmtInsertarOpciones->bindValue(":esCorrecta", $esCorrecta); // Nuevo parámetro para indicar si es correcta o no
        $stmtInsertarOpciones->execute();
    }
    

    // Confirma la transacción
    $conexion->commit();

    echo "<div style='background-color: green'>Guardado</div>";
} catch (PDOException $e) {
    // En caso de error, revierte la transacción
    $conexion->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
