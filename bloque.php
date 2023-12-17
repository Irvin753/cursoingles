<?php
include('header.php');

$idBloque = $_GET['id'];

$conexion = new Conexion();
$conn = $conexion->conectar();

$sql = "CALL ObtenerNombreBloque(:idBloque)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":idBloque", $idBloque, PDO::PARAM_INT);
$stmt->execute();

$stmt->bindColumn("nombreBloque", $nombreBloque);
$stmt->fetch();
$stmt->closeCursor();

$sqlOpciones = "CALL ObtenerOpcionesPreguntas()";
$stmtOpciones = $conn->prepare($sqlOpciones);
$stmtOpciones->execute();


// Almacenar opciones en un array asociativo
$opcionesArray = array();
while ($rowOpcion = $stmtOpciones->fetch(PDO::FETCH_ASSOC)) {
    $preguntaID = $rowOpcion['fkPregunta'];
    if (!isset($opcionesArray[$preguntaID])) {
        $opcionesArray[$preguntaID] = array();
    }
    $opcionesArray[$preguntaID][] = $rowOpcion['contenidoOpcion'];
}
$stmt->closeCursor();

$sqlPreguntas = "CALL ObtenerPreguntasPorBloque(:idBloque)";
$stmtPreguntas = $conn->prepare($sqlPreguntas);
$stmtPreguntas->bindParam(":idBloque", $idBloque, PDO::PARAM_INT);
$stmtPreguntas->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <p>¿Estás seguro de que deseas eliminar esta pregunta?</p>
        <button id="confirmYes">Sí</button>
        <button id="confirmNo">No</button>
    </div>
</div>
<style>
          body {
            font-size: 18px; /* Ajusta el tamaño de letra del cuerpo del documento según tus preferencias */
        }

        table {
            width: 100%; /* Asegura que la tabla ocupe todo el ancho de la pantalla */
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000; /* Agrega bordes a las celdas */
            padding: 10px; /* Espacio interior en las celdas */
            text-align: center; /* Centra el contenido en las celdas */
        }

        th {
            background-color: #f2f2f2; /* Color de fondo para las celdas del encabezado */
        }

        .action-buttons {
            display: flex;
            justify-content: center;
        }

        .action-buttons button {
            margin: 5px;
        }

        /* Estilos para el modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: 20% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        text-align: center;
    }

    .close {
        float: right;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
    }

    #confirmYes, #confirmNo {
        padding: 10px 20px;
        margin: 10px;
        cursor: pointer;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $(".delete-btn").click(function() {
            var preguntaID = $(this).data("pregunta-id");

            // Mostrar el modal de confirmación
            $("#confirmModal").css("display", "block");

            // Configurar acciones para botones de confirmación
            $("#confirmYes").click(function() {
                // Cierra el modal
                $("#confirmModal").css("display", "none");

                // Envía la solicitud AJAX para eliminar la pregunta
                $.ajax({
                    type: "POST",
                    url: "funciones/borrar_pregunta.php",
                    data: { preguntaID: preguntaID },
                    success: function(response) {
                        // Actualizar la página o realizar otras acciones si es necesario
                        location.reload();
                    }
                });
            });

            $("#confirmNo").click(function() {
                // Cierra el modal sin hacer nada
                $("#confirmModal").css("display", "none");
            });
        });

        // Cerrar el modal si se hace clic en la "X"
        $(".close").click(function() {
            $("#confirmModal").css("display", "none");
        });
    });
</script>

</head>
<body>


<h2 style="text-align: center;">Bloque seleccionado: <?php echo $nombreBloque; ?></h2>
<!-- Otro contenido de la página común aquí -->
<h2 style="text-align: center;">Lista de preguntas</h2>
<table>
        <tr>
            <th>Pregunta</th>
            <th>Opciones preguntas</th>
            <th>Opciones</th>
        </tr>
        <?php
        while ($rowPregunta = $stmtPreguntas->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $rowPregunta['contenidoPregunta'] . "</td>";
            echo "<td class='options-column'>";
    
            // Mostrar opciones de la pregunta actual
            if (isset($opcionesArray[$rowPregunta['pkPregunta']])) {
                foreach ($opcionesArray[$rowPregunta['pkPregunta']] as $opcion) {
                    echo $opcion . "<br>";
                }
            }
    
            echo "</td>";
            echo "<td class='action-buttons'>";
    
            // Botón de editar
            echo '<a href="formularios/formulario_editar_pregunta.php?preguntaID=' . $rowPregunta['pkPregunta'] . '">
            <button>Edit</button>
            </a>';
    
            // Botón de eliminar
            echo "<button class='delete-btn' data-pregunta-id='" . $rowPregunta['pkPregunta'] . "'>Delete</button>";
    
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <br><br><br><br>
    <?php
    // Cierra la conexión
    $conn = null;
    ?>
</body>
</html>
