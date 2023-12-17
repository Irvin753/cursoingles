<?php
include 'clases/Conexion.php';

$conexion = new Conexion();
$conn = $conexion->conectar();

try {
    // Llama al procedimiento almacenado
    $sqlInteresados = "CALL ObtenerInteresadosListaPublico()";
    $stmtInteresados = $conn->prepare($sqlInteresados);
    $stmtInteresados->execute();
} catch (PDOException $e) {
    // Manejo de errores
    echo "Error en la consulta: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-size: 18px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .result-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
        }

        #search-container {
            margin-bottom: 20px;
        }

        #search-input {
            padding: 8px;
            font-size: 16px;
            border: 1px solid;
            border-radius: 5px;
        }

        #search-button {
            padding: 8px;
            font-size: 16px;
            border: 1px solid;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
        }

        .action-buttons button {
            margin: 5px;
        }

        .hidden {
            display: none;
        }

        #folio-not-found {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        .modal-title {
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

<div class='result-container'>
    <h1>Lista de interesados</h1>
    <h3>Ingresa el folio</h3>
    <div id="search-container">
        <input type="text" id="search-input" placeholder="Buscar">
        <button id="search-button">Buscar</button>
    </div>

    <table id="interesados-table" class="hidden">
        <tr>
            <th class="hidden">Folio Intento</th>
            <th>Nombre del interesado</th>
            <th>Telefono</th>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Hora de inicio / Hora final</th>
            <th>Aciertos</th>
            <th>Nombre del nivel</th>
        </tr>
        <?php
        while ($rowInteresado = $stmtInteresados->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td class='hidden'>" . $rowInteresado['folioIntento'] . "</td>";
            echo "<td>" . $rowInteresado['NombreInteresado'] . "</td>";
            echo "<td>" . $rowInteresado['telefonoInteresado'] . "</td>";
            echo "<td>" . $rowInteresado['folioIntento'] . "</td>";
            echo "<td>" . $rowInteresado['fechaIntento'] . "</td>";
            echo "<td>" . $rowInteresado['horaInicio'] . " / " . $rowInteresado['horaFinal'] . "</td>";
            echo "<td>" . $rowInteresado['aciertos'] . "</td>";
            echo "<td>" . $rowInteresado['nombreNivel'] . "</td>";

            echo "</tr>";
        }
        ?>
    </table>

    <!-- Mensaje si no se encuentra el folio -->
    <p id="folio-not-found" class="hidden">Folio no encontrado</p>
</div>

<!-- Modal para mostrar detalles del interesado -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Detalles del Interesado</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <p id="modal-nombre"></p>
                <p id="modal-telefono"></p>
                <p id="modal-folio"></p>
                <p id="modal-fecha"></p>
                <p id="modal-hora"></p>
                <p id="modal-aciertos"></p>
                <p id="modal-nivel"></p>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Script para manejar la búsqueda y mostrar detalles en el modal -->

<script>
    // Función para mostrar detalles del interesado en un modal
    function mostrarDetallesModal(nombre, telefono, folio, fecha, hora, aciertos, nivel) {
        // Actualiza el contenido del modal con la información del interesado
        document.getElementById("modal-nombre").innerHTML = "<b>Nombre:</b> " + nombre;
        document.getElementById("modal-telefono").innerHTML = "<b>Teléfono:</b> " + telefono;
        document.getElementById("modal-folio").innerHTML = "<b>Folio:</b> " + folio;
        document.getElementById("modal-fecha").innerHTML = "<b>Fecha:</b> " + fecha;
        document.getElementById("modal-hora").innerHTML = "<b>Hora de inicio / Hora final:</b> " + hora;
        document.getElementById("modal-aciertos").innerHTML = "<b>Aciertos:</b> " + aciertos;
        document.getElementById("modal-nivel").innerHTML = "<b>Nivel:</b> " + nivel;

        // Muestra el modal
        $('#myModal').modal('show');
    }

    // Función para realizar la búsqueda al hacer clic en el botón
    document.getElementById("search-button").addEventListener("click", function() {
        // Obtiene el valor del input de búsqueda y lo convierte a mayúsculas
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("search-input");
        filter = input.value.toUpperCase();

        // Obtiene la tabla y sus filas
        table = document.getElementById("interesados-table");
        tr = table.getElementsByTagName("tr");

        // Oculta el mensaje de "Folio no encontrado" al realizar una nueva búsqueda
        document.getElementById("folio-not-found").classList.add("hidden");

        // Encuentra el primer interesado que cumple con la búsqueda
        for (i = 0; i < tr.length; i++) {
            // Obtiene las celdas relevantes de cada fila
            tdNombre = tr[i].getElementsByTagName("td")[1];
            tdTelefono = tr[i].getElementsByTagName("td")[2];
            tdFolioIntento = tr[i].getElementsByTagName("td")[0];
            tdFechaIntento = tr[i].getElementsByTagName("td")[4];
            tdHoraIntento = tr[i].getElementsByTagName("td")[5];
            tdAciertos = tr[i].getElementsByTagName("td")[6];
            tdNivel = tr[i].getElementsByTagName("td")[7];

            // Si las celdas existen, obtiene su contenido
            if (tdNombre && tdTelefono && tdFolioIntento && tdFechaIntento && tdHoraIntento && tdAciertos && tdNivel) {
                txtValueNombre = tdNombre.textContent || tdNombre.innerText;
                txtValueTelefono = tdTelefono.textContent || tdTelefono.innerText;
                txtValueFolioIntento = tdFolioIntento.textContent || tdFolioIntento.innerText;
                txtValueFechaIntento = tdFechaIntento.textContent || tdFechaIntento.innerText;
                txtValueHoraIntento = tdHoraIntento.textContent || tdHoraIntento.innerText;
                txtValueAciertos = tdAciertos.textContent || tdAciertos.innerText;
                txtValueNivel = tdNivel.textContent || tdNivel.innerText;

                // Compara el contenido con el filtro
                if (txtValueNombre.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().indexOf(filter) > -1 ||
                    txtValueTelefono.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().indexOf(filter) > -1 ||
                    txtValueFolioIntento.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().indexOf(filter) > -1) {
                    // Muestra detalles en el modal si hay coincidencias
                    mostrarDetallesModal(txtValueNombre, txtValueTelefono, txtValueFolioIntento, txtValueFechaIntento, txtValueHoraIntento, txtValueAciertos, txtValueNivel);
                    break;
                }
            }
        }

        // Muestra el mensaje de "Folio no encontrado" si no hay coincidencias
        if (i === tr.length) {
            document.getElementById("folio-not-found").classList.remove("hidden");

            // Oculta el mensaje después de 5 segundos
            setTimeout(function() {
                document.getElementById("folio-not-found").classList.add("hidden");
            }, 5000);
        }
    });
</script>
<?php
// Cierra la conexión
$conn = null;
?>
</body>
</html>
