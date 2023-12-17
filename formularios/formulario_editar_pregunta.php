<?php
include('header.php');

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Llamar al método conectar en la instancia
$conn = $conexion->conectar();

// Obtén el ID de la pregunta que deseas editar desde la URL
$preguntaID = isset($_GET['preguntaID']) ? $_GET['preguntaID'] : null;

// Realiza una consulta para obtener los datos de la pregunta seleccionada
$sqlObtenerPregunta = "CALL ObtenerPreguntaPorID(?)";
$stmtObtenerPregunta = $conn->prepare($sqlObtenerPregunta);
$stmtObtenerPregunta->bindValue(1, $preguntaID, PDO::PARAM_INT);
$stmtObtenerPregunta->execute();
$pregunta = $stmtObtenerPregunta->fetch(PDO::FETCH_ASSOC);
$stmtObtenerPregunta->closeCursor();

// Obtener los bloques desde la base de datos
$sqlBloques = "CALL ObtenerBloques()";
$resultBloques = $conn->query($sqlBloques);
$bloques = array();
// Almacenar los resultados en un arreglo asociativo
while ($row = $resultBloques->fetch(PDO::FETCH_ASSOC)) {
    $bloques[] = $row;
}
// Cerrar el cursor
$resultBloques->closeCursor();

// Obtener los tipos de pregunta desde la base de datos
$sqlTiposPregunta = "CALL ObtenerTiposPregunta()";
$resultTiposPregunta = $conn->query($sqlTiposPregunta);
$tiposPregunta = array();
// Almacenar los resultados en un arreglo asociativo
while ($row = $resultTiposPregunta->fetch(PDO::FETCH_ASSOC)) {
    $tiposPregunta[] = $row;
}
// Cerrar el cursor
$resultTiposPregunta->closeCursor();

$sqlOpcionesRespuesta = "CALL ObtenerOpcionesPreguntaPorID(?)";
$stmtOpcionesRespuesta = $conn->prepare($sqlOpcionesRespuesta);
$stmtOpcionesRespuesta->bindValue(1, $preguntaID, PDO::PARAM_INT);
$stmtOpcionesRespuesta->execute();
$opcionesRespuesta = array();
// Almacenar los resultados en un arreglo asociativo
while ($row = $stmtOpcionesRespuesta->fetch(PDO::FETCH_ASSOC)) {
    $opcionesRespuesta[] = $row['contenidoOpcion'];
}
// Cerrar el cursor
$stmtOpcionesRespuesta->closeCursor();

// Cierra la conexión
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="../jquery.min.js" type="text/javascript"></script>
    <style>
        body {
            font-size: 18px; /* Ajusta el tamaño de letra del cuerpo del documento según tus preferencias */
            text-align: center; /* Centra el contenido horizontalmente */
        }

        #formulario_editar_pregunta {
            display: inline-block;
            text-align: left;
            margin: 0 auto; /* Centra el formulario horizontalmente */
        }

        #formulario_editar_pregunta {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        #formulario_editar_pregunta label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        #formulario_editar_pregunta input[type="text"],
        #formulario_editar_pregunta select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #formulario_editar_pregunta select {
            height: 35px;
        }

        #formulario_editar_pregunta input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        #formulario_editar_pregunta input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h3 {
            text-align: center;
            margin-top: 0;
        }
    </style>
    <title>Editar Pregunta</title>
</head>
<body>
    <br>
    <div style="text-align: center;">
    <a style="background-color: gray; 
    color: white; padding: 10px 20px; border: none; border-radius: 5px; 
    cursor: pointer; margin-top: 15px; margin-left: 30px;" onclick="history.back()">Regresar</a>
    </div>
    <br><br>
    <h3 style="text-align: center;">Editar pregunta</h3><br>
    <form id="formulario_editar_pregunta" method="post" action="../funciones/editar_pregunta.php">
        <input type="hidden" name="preguntaID" value="<?php echo $preguntaID; ?>">
        <label>Pregunta: </label>
        <input type="text" name="contenidoPregunta" value="<?php echo $pregunta['contenidoPregunta']; ?>">
        <br><br>

        <label>Bloque: </label>
        <select name="fkBloque" style="height: 50px">
            <?php
            foreach ($bloques as $bloque) {
                $selected = ($bloque['pkBloque'] == $pregunta['fkBloque']) ? 'selected' : '';
                echo "<option value='" . $bloque['pkBloque'] . "' $selected>" . $bloque['nombreBloque'] . "</option>";
            }
            ?>
        </select><br><br>

        <label>Tipo de pregunta: </label>
        <select name="fkTipoPregunta" style="height: 50px">
            <?php
            foreach ($tiposPregunta as $tipo) {
                $selected = ($tipo['pkTipoPregunta'] == $pregunta['fkTipoPregunta']) ? 'selected' : '';
                echo "<option value='" . $tipo['pkTipoPregunta'] . "' $selected>" . $tipo['contenidoTipo'] . "</option>";
            }
            ?>
        </select><br><br>
<!-- es para que muestre todas las opciones que tiene cada pregunta en el formulario-->
<label>Opciones de respuesta: </label>

<?php
foreach ($opcionesRespuesta as $index => $opcion) {
    echo "<input type='text' name='opcionRespuesta[]' value='$opcion'>";
    echo "<br>";
}
?>

<br>



        <input type="submit" value="Guardar Cambios">
    </form>
    <div id="resultado"></div>
    <br><br><br><br>
</body>
<script type="text/javascript">
    $("#formulario_editar_pregunta").on("submit", function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "../funciones/editar_pregunta.php",
            data: $("#formulario_editar_pregunta").serialize(),
            dataType: "html",
            error: function(){
                alert("Error");
            },
            success: function(data){
                $("#resultado").html(data).delay(1000).fadeOut();
                setTimeout(function() {
                history.back();
            }, 1000);

            }
        });
    })
</script>
</html>
