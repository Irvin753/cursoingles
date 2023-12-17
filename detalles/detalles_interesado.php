<?php
include('header.php');

$conexion = new Conexion();
$conn = $conexion->conectar();

// Obtén el pkInteresado deseado, por ejemplo, desde la URL
$pkInteresado = $_GET['interesadoID'];

// Llama al procedimiento almacenado pasando el parámetro
$sqlInteresados = "CALL ObtenerInteresadoPorID(:pkInteresado)";
$stmtInteresados = $conn->prepare($sqlInteresados);
$stmtInteresados->bindParam(":pkInteresado", $pkInteresado, PDO::PARAM_INT);
$stmtInteresados->execute();

$stmtInteresados->bindColumn("NombreInteresado", $NombreInteresado);
$stmtInteresados->fetch();
$stmtInteresados->closeCursor();


// Llama al procedimiento almacenado
$sqlInteresados2 = "CALL ObtenerDetallesInteresado(:pkInteresado)";
$stmtInteresados2 = $conn->prepare($sqlInteresados2);
$stmtInteresados2->bindParam(":pkInteresado", $pkInteresado, PDO::PARAM_INT);
$stmtInteresados2->execute();


?>
<!DOCTYPE html>
<html lang="en">
<head>
<style>
    body {
        font-size: 18px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000;
        padding: 10px;
        text-align: center;
    }

    th {
        background-color: #f2f2f2;
    }

    .table-container {
        max-height: 400px; /* Ajusta la altura máxima según tus necesidades */
        overflow-y: auto;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
    }

    .action-buttons button {
        margin: 5px;
    }
</style>


</head>
<body>
    <br>
    <div style="text-align: center;">
        <a style="background-color: gray; 
        color: white; padding: 10px 20px; border: none; border-radius: 5px; 
        cursor: pointer; margin-top: 15px; margin-left: 30px;" onclick="history.back()">Regresar</a>
    </div>
    <br><br>
    <!-- Otro contenido de la página común aquí -->
    <h1 style="text-align: center;">Detalles del interesado: <?php echo $NombreInteresado; ?></h1>
    <div class="table-container">
    <table>
        <tr>
            <th>Folio</th>
            <th>Fecha intento</th>
            <th>Hora inicio / Hora final</th>
            <th>Aciertos</th>
            <th>Nivel asignado</th>
            <th>Opciones</th>
        </tr>
        <?php
        while ($rowInteresado2 = $stmtInteresados2->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $rowInteresado2['folioIntento'] . "</td>";
            echo "<td>" . $rowInteresado2['fechaIntento'] . "</td>";
            echo "<td>" . $rowInteresado2['horaInicio'] . " - " . $rowInteresado2['horaFinal'] . "</td>";
            echo "<td>" . $rowInteresado2['aciertos'] . "</td>";
            echo "<td>" . $rowInteresado2['nombreNivel'] . " - " . $rowInteresado2['descripcionNivel'] . "</td>";

            echo "</td>";
            echo "<td class='action-buttons'>";
    
            // Botón de descargar información de los interesados
            echo '<a href="../descargar.php?interesadoID=' . $rowInteresado2['pkInteresado'] . '">
            <button>Download</button>
            </a>';
        //    <a href="detalles_interesados.php?id=' . $bloque["pkInteresado"] . '">

    
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>
    <br><br><br><br>
    <?php
    // Cierra la conexión
    $conn = null;
    ?>
</body>
</html>

