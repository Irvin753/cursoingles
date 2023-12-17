<?php
session_start();
require './clases/Conexion.php';
include('./vendor/autoload.php');

// Almacena el ID del cliente de Google.
$correogmail = "195230904845-41ka1uqvf8a5bgl02ai0sigcbasephcj.apps.googleusercontent.com"; 

$bd = new Conexion(); 
$conn = $bd->conectar(); 

// Crea una instancia de la clase "Google_Client" y configura el ID del cliente.
$client = new Google_Client(["client_id" => $correogmail]); 

// Verifica el token de inicio de sesión enviado desde el formulario.
$payload = $client->verifyIdToken($_POST["credential"]); 

if ($payload && $payload["aud"] == $correogmail) { // Verifica si el token es válido y si coincide con el ID del cliente de Google.
    $correo = $payload["email"]; // Obtiene el correo electrónico del usuario a partir de los datos verificados.
    $name = $payload['name']; // Saca el nombre completo del usuario del correo al iniciar sesión y lo almacena en la variable "$name".
    // Almacena el nombre del usuario en una variable de sesión
    $_SESSION["name"] = $name;

    // Verifica si el correo tiene la extensión "@gmail.com"
    if (strpos($correo, "@gmail.com") === false) { // Comprueba si el correo no contiene "@gmail.com".
        header('location: login.php'); // Redirige al usuario de vuelta a la página de inicio de sesión.
    } else {
        $sql = 'CALL ObtenerUsuarioPorCorreo(?)'; // Define la consulta SQL para llamar al procedimiento almacenado "ObtenerUsuarioPorCorreo".
        $insert = $conn->prepare($sql); // Prepara la consulta SQL.
        $insert->bindParam(1, $correo); // Vincula el valor del correo a la consulta SQL.
        $insert->execute(); // Ejecuta la consulta SQL.

        $datos = $insert->fetchAll(); // Obtiene los resultados de la consulta y los almacena en la variable "$datos".

        if (empty($datos)) { // Verifica si no se encontraron datos en la base de datos.
            header('location: login.php'); // Redirige al usuario de vuelta a la página de inicio de sesión.
        } else {
            $_SESSION['pkUsuario'] = $datos[0]['pkUsuario']; // Almacena el ID del usuario en una variable de sesión.
            header('location: index.php'); // Redirige al usuario a la página principal.
        }
    }
} else {
    echo "Token is invalid"; // Si el token no es válido, muestra un mensaje de error.
}


    ?>
