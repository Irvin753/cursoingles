<?php
include('header.php');

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Llamar al método conectar en la instancia
$conn = $conexion->conectar();

// Obtener los bloques desde la base de datos
$sqlBloques = "CALL ObtenerBloques()";
$resultBloques = $conn->query($sqlBloques);

$bloques = array();

// Almacenar los resultados en un arreglo asociativo
while ($row = $resultBloques->fetch(PDO::FETCH_ASSOC)) {
    $bloques[] = $row;
}

// Liberar el resultado de la primera consulta
$resultBloques->closeCursor();

// Obtener los tipos de pregunta desde la base de datos
$sqlTiposPregunta = "CALL ObtenerTiposPregunta()";
$resultTiposPregunta = $conn->query($sqlTiposPregunta);

$tiposPregunta = array();

// Almacenar los resultados en un arreglo asociativo
while ($row = $resultTiposPregunta->fetch(PDO::FETCH_ASSOC)) {
    $tiposPregunta[] = $row;
}

// Liberar el resultado de la segunda consulta
$resultBloques->closeCursor();

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
            text-align: center;
           
        }

        #formulario_pregunta {
            display: inline-block;
            text-align: left;
            margin: 0 auto; /* Centra el formulario horizontalmente */
        }
        

        #formulario_pregunta {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            width: 80%;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }

        #formulario_pregunta label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }

        #formulario_pregunta input[type="text"],
        #formulario_pregunta select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        #formulario_pregunta select {
            height: 35px;
        }

        #formulario_pregunta input[type="submit"] {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        #formulario_pregunta input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h3 {
            text-align: center;
            margin-top: 0;
        }
        .opcionDiv {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .opcionDiv input {
            flex: 1;
            margin-right: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .opcionDiv button {
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 15px; /* Ajusta el padding para hacer los botones más grandes */
            cursor: pointer;
            margin-right: 5px;
        }

        .opcionDiv button.eliminarOpcion {
            background-color: #dc3545;
        }
        /* Estilos del modal */
        .modal {
            display: none; /* Por defecto, el modal estará oculto */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro semitransparente */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }

        /* Estilos del botón que abre el modal */
        #openModalBtn {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        #openModalBtn:hover {
            background-color: #0056b3;
        }
    </style>
    
    <title>Document</title>
</head>
<body>
   
    <h3 style="text-align: center;">Agregar preguntas</h3><br>
    <form id="formulario_pregunta">
        <label>Pregunta: </label>
        <input type="text" name="contenidoPregunta" required>
        <br><br>

        <label>Bloque: </label>
        <select name="fkBloque" style="height: 50px" required>
            <?php
            foreach ($bloques as $bloque) {
                echo "<option value='" . $bloque['pkBloque'] . "'>" . $bloque['nombreBloque'] . "</option>";
            }
            ?>
        </select><br><br>

        <label>Tipo de pregunta: </label>
        <select name="fkTipoPregunta" style="height: 50px" required>
            <?php
            foreach ($tiposPregunta as $tipo) {
                echo "<option value='" . $tipo['pkTipoPregunta'] . "'>" . $tipo['contenidoTipo'] . "</option>";
            }
            ?>
        </select><br><br>
        <label>Opciones de respuesta: </label>
        <div id="opcionesContainer">
            <div class="opcionDiv">
                <input type="text" name="contenidoOpcion[]" required>
                <button type="button" class="agregarOpcion" onclick="agregarOpcion()">+</button>
                <button type="button" class="eliminarOpcion" onclick="eliminarOpcion(this)">X</button>
            </div>
        </div>
        

        <!-- Script para agregar y eliminar opciones dinámicamente -->

<script>
    // Función para agregar una nueva opción
    function agregarOpcion() {
        // Obtiene el contenedor de opciones
        var container = document.getElementById("opcionesContainer");

        // Crea un nuevo div para la opción
        var newOptionDiv = document.createElement("div");
        newOptionDiv.classList.add("opcionDiv");

        // Agrega un campo de texto para el contenido de la opción
        newOptionDiv.innerHTML = '<input type="text" name="contenidoOpcion[]" required>' +
            // Botón para agregar más opciones
            '<button type="button" class="agregarOpcion" onclick="agregarOpcion()">+</button>' +
            // Botón para eliminar la opción
            '<button type="button" class="eliminarOpcion" onclick="eliminarOpcion(this)">X</button>';

        // Agrega el nuevo div al contenedor
        container.appendChild(newOptionDiv);
    }

    // Función para eliminar una opción
    function eliminarOpcion(element) {
        // Obtiene el contenedor de opciones
        var container = document.getElementById("opcionesContainer");

        // Obtiene todos los divs de opción en el contenedor
        var optionDivs = container.getElementsByClassName("opcionDiv");

        // Verifica si hay más de una opción antes de eliminar
        if (optionDivs.length > 1) {
            container.removeChild(element.parentNode);
        } else {
            // Muestra un mensaje en lugar de la alerta
            var messageDiv = document.createElement("div");
            messageDiv.innerHTML = '<p style="color: red;">Debe haber al menos una opción.</p>';
            container.appendChild(messageDiv);

            // Elimina el mensaje después de unos segundos
            setTimeout(function() {
                container.removeChild(messageDiv);
            }, 3000); // 3000 milisegundos (3 segundos)
        }
    }
</script>





        <br><br>
        <input type="submit">
    </form>
    <div id="resultado"></div>
    <br><br><br><br>
</body>
 <!-- Modal -->
 <div class="modal" id="myModal">
        <div class="modal-content">
            <p>¡Guardado exitoso!</p>
            <button id="closeModalBtn">Cerrar</button>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $("#formulario_pregunta").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "../funciones/insertar_pregunta.php",
                    data: $(this).serialize(),
                    dataType: "html",
                    error: function(){
                        alert("Error");
                    },
                    success: function(data){
                        // Mostrar el modal al completar la operación
                        $("#myModal").show();
                        // Ocultar el modal después de 2 segundos (2000 milisegundos)
                        setTimeout(function() {
                            $("#myModal").hide();
                            location.href='formulario_pregunta.php';
                        }, 1000);
                    }
                });
            });

            // Cerrar el modal al hacer clic en el botón "Cerrar"
            $("#closeModalBtn").click(function() {
                $("#myModal").hide();
            });
        });
    </script>
</html>
