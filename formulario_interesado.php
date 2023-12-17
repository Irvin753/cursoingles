<?php
include("clases/Conexion.php");
date_default_timezone_set('America/Mazatlan');
$bd = new Conexion();
$conexion = $bd->conectar();

if (isset($_POST['nombreInteresado'])) {
    $nombreInteresado = trim($_POST['nombreInteresado']);
    $apellidoInteresado = trim($_POST['apellidoInteresado']);
    $telefonoInteresado = trim($_POST['telefonoInteresado']);

    // Validar nombre
    if (preg_match('/^[A-Z][a-z]+/', $nombreInteresado) !== 1) {
        ?>
        <h3 class="error">El nombre no es válido</h3>
        <?php
    }
    // Validar apellido
    elseif (preg_match('/^[A-Z][a-z]+/', $apellidoInteresado) !== 1) {
        ?>
        <h3 class="error">El apellido no es válido</h3>
        <?php
    }
    // Validar teléfono
    elseif (preg_match('/^\d{10}$/', $telefonoInteresado) !== 1) {
        ?>
        <h3 class="error">El número de teléfono no es válido</h3>
        <?php
    } else {
        // Todos los campos son válidos, proceder con la inserción en la base de datos

        try {
            // Llamada al procedimiento almacenado para insertar un nuevo interesado y obtener su ID
            $stmtInsertarInteresado = $conexion->prepare("CALL InsertarInteresado(:nombreInteresado, :apellidoInteresado, :telefonoInteresado, @idInteresado)");
            $stmtInsertarInteresado->bindParam(':nombreInteresado', $nombreInteresado, PDO::PARAM_STR);
            $stmtInsertarInteresado->bindParam(':apellidoInteresado', $apellidoInteresado, PDO::PARAM_STR);
            $stmtInsertarInteresado->bindParam(':telefonoInteresado', $telefonoInteresado, PDO::PARAM_STR);
            $stmtInsertarInteresado->execute();

            // Obtener el ID del interesado
            $stmtObtenerIdInteresado = $conexion->query("SELECT @idInteresado AS idInteresado");
            $idInteresado = $stmtObtenerIdInteresado->fetch(PDO::FETCH_ASSOC)['idInteresado'];

            // Generar el folio único
            $totalIntentos = $conexion->query("SELECT COUNT(*) as total FROM intentointeresado")->fetch(PDO::FETCH_ASSOC)['total'] + 1;
            $folioIntento = "INT" . str_pad($totalIntentos, 4, '0', STR_PAD_LEFT);

            // Llamada al procedimiento almacenado para insertar un nuevo intento
            $stmtInsertarIntento = $conexion->prepare("CALL InsertarIntento(:folioIntento, :fkInteresado, :fechaIntento, :horaInicio, '00:00:00', 0)");
            $stmtInsertarIntento->bindParam(':folioIntento', $folioIntento, PDO::PARAM_STR);
            $stmtInsertarIntento->bindParam(':fkInteresado', $idInteresado, PDO::PARAM_INT);
            $stmtInsertarIntento->bindParam(':fechaIntento', date("Y-m-d"), PDO::PARAM_STR);
            $stmtInsertarIntento->bindParam(':horaInicio', date("H:i:s"), PDO::PARAM_STR);
            $stmtInsertarIntento->execute();

            // Redirigir al usuario a examen.php con el folio
            header("Location: examen.php?folio=$folioIntento");
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>


<style>
    body, html {
    margin: 0;
    padding: 0;
}

/* Estilos base */
.login-container {
    font-family: Arial, sans-serif;
    background-color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh; /* Cambiado de height a min-height */
    margin: 0;
}

.error {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
    }


.login-box {
    width: 100%;
    max-width: 500px;
    padding: 15px;
    background-color: white;
    border: 1px solid #000;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.login-logo img {
    width: 200px;
    height: 200px;
    margin-bottom: 10px;
}

.login-form-group {
    margin: 10px 0;
    text-align: center;
}

.login-label {
    display: block;
    font-weight: bold;
    font-size: 14px;
} 

.login-input {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.login-button {
    width: 100%;
    padding: 8px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.login-button:hover {
    background-color: #0056b3;
}


/* Media query para pantallas medianas (ejemplo: tablets) */
@media (min-width: 481px) and (max-width: 768px) {
    .login-box {
        max-width: 400px;
        /* Ajusta el ancho máximo del formulario en pantallas medianas */
    }

    .login-logo img {
        width: 160px;
        height: 160px;
        /* Ajusta el tamaño del logo en pantallas medianas */
    }
}



/* Media query para pantallas más pequeñas (ejemplo: teléfonos) */
@media (max-width: 480px) {
    .login-box {
        padding: 10px;
        /* Reduce el espacio de relleno en pantallas más pequeñas */
    }


    .login-logo img {
        width: 120px;
        height: 120px;
    }

    .login-label,
    .login-input,
    .login-button {
        font-size: 16px;
    }
}



</style>

<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <center>
            <img src="img/Logo.png" alt="Logo">
            </center>
        </div>
        <form method="POST">
            <div class="login-form-group">
                <label for="nombre" class="login-label">Nombre(s)</label><br>
                <input type="text" placeholder="Ingresa tu nombre" name="nombreInteresado" class="login-input" required><br>
            </div>
            <div class="login-form-group">
                <label for="apellido" class="login-label">Apellido</label><br>
                <input type="text" placeholder="Ingresa tu apellido" name="apellidoInteresado" class="login-input" required><br>
            </div>
            <div class="login-form-group">
                <label for="telefono" class="login-label">Teléfono celular</label><br>
                <input type="text" placeholder="Ingresa tu número telefónico" name="telefonoInteresado" class="login-input" required><br>
            </div>
            <div class="login-form-group">
                <input type="submit" value="Ir al examen" class="login-button">
            </div>
        </form>
        <div>
            <center>
                <a href="lista_interesados_publico.php"><h5>¿Ya completaste el examen y no recuerdas tu puntaje? Verifícalo mediante tu folio.</h5></a>
            </center>
        </div>
    </div>
</div>
