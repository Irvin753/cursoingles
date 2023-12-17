<?php
include('header.php');

$conexion = new Conexion();
$conn = $conexion->conectar();

// Llama al procedimiento almacenado
$sqlInteresados = "CALL ObtenerInteresados()";
$stmtInteresados = $conn->prepare($sqlInteresados);
$stmtInteresados->execute();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-size: 18px;
        }

        h1 {
            text-align: center;
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

        #search-container {
            text-align: center;
            margin-bottom: 20px;
        }

        #search-input {
            padding: 8px;
            font-size: 16px;
            border: 1px solid;
            border-radius: 5px;
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Lista de interesados</h1>
    <div id="search-container">
        <input type="text" id="search-input" placeholder="Buscar interesado">
    </div>

    <table id="interesados-table">
        <tr>
            <th class="hidden">Folio Intento</th>
            <th>Nombre del interesado</th>
            <th>Telefono</th>
            <th>Opciones</th>
        </tr>
        <?php
        while ($rowInteresado = $stmtInteresados->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td class='hidden'>" . $rowInteresado['folioIntento'] . "</td>";
            echo "<td>" . $rowInteresado['NombreInteresado'] . "</td>";
            echo "<td>" . $rowInteresado['telefonoInteresado'] . "</td>";

            echo "<td class='action-buttons'>";
    
            // Botón de ver detalles de los interesados
            echo '<a href="detalles/detalles_interesado.php?interesadoID=' . $rowInteresado['pkInteresado'] . '">
            <button>Details</button>
            </a>';
    
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <script>
        // Función para realizar la búsqueda automáticamente al escribir
        document.getElementById("search-input").addEventListener("input", function() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search-input");
            filter = input.value.toUpperCase();

            table = document.getElementById("interesados-table");
            tr = table.getElementsByTagName("tr");

            // Itera sobre las filas de la tabla y muestra u oculta según la búsqueda
            for (i = 0; i < tr.length; i++) {
                // Cambiamos el índice para buscar tanto por nombre como por teléfono y folioIntento
                tdNombre = tr[i].getElementsByTagName("td")[1];
                tdTelefono = tr[i].getElementsByTagName("td")[2];
                tdFolioIntento = tr[i].getElementsByTagName("td")[0];

                if (tdNombre && tdTelefono && tdFolioIntento) {
                    txtValueNombre = tdNombre.textContent || tdNombre.innerText;
                    txtValueTelefono = tdTelefono.textContent || tdTelefono.innerText;
                    txtValueFolioIntento = tdFolioIntento.textContent || tdFolioIntento.innerText;

                    // Usa normalize para quitar tildes y toUpperCase para hacer la búsqueda insensible a mayúsculas
                    if (txtValueNombre.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().indexOf(filter) > -1 ||
                        txtValueTelefono.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().indexOf(filter) > -1 ||
                        txtValueFolioIntento.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        });
    </script>
    <br><br><br><br>
    <?php
    // Cierra la conexión
    $conn = null;
    ?>
</body>
</html>
