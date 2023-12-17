<?php
include('header.php');

// Crear una instancia de la clase Conexion
$conexion = new Conexion();

// Llamar al método conectar en la instancia
$conn = $conexion->conectar();

// Llama al procedimiento almacenado ObtenerBloques
$sql = "CALL ObtenerBloques()";
$stmt = $conn->prepare($sql);
$stmt->execute();

// Recupera los resultados de la llamada al procedimiento
$bloques = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Cierra la conexión
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
/* Estilo para centrar las imágenes en los bloques y mostrarlos en línea */
body {
    font-size: 18px;

}

#contenido {
    text-align: center;
    padding: 20px;
}

h1 {
    font-size: 36px;
    color: #333; 
}

h2 {
    font-size: 24px;
    color: #333; 
}

.block-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    margin: 0 auto;
    max-width: 800px; /* Ajusta según sea necesario */
}

.block {
    flex: 0 0 22%; /* Ajusta según sea necesario para el ancho deseado */
    margin: 10px;
    text-align: center;
    background-color: white; 
    border: 1px solid #000; 
    border-radius: 5px; /* Bordes redondeados */
    padding: 15px; /* Espaciado interno */
}

.block img {
    display: block;
    margin: 0 auto;
    width: 100%;
    border-radius: 5px; /* Bordes redondeados para la imagen */
}

.block h3 {
    font-size: 30px;
    margin: 10px 0;
    color: #333; 
}

a {
    text-decoration: none;
    color: #007BFF; 
}




    </style>
    <title>Index</title>
</head>

<body>

    
    <!-- <Button><a href="javascript:history.go(-1)"><---- Regresar</a></Button><br><br> -->
    <h1 style="text-align: center;">Bienvenido, <?php echo $_SESSION["name"]; ?></h1>

    
    <h1 style="text-align: center;">Bloques Disponibles:</h1>
    <div class="block-container">
        <?php
        // Itera sobre los bloques disponibles y muestra su información
        foreach ($bloques as $bloque) {
            echo '<div class="block">
                <a href="bloque.php?id=' . $bloque["pkBloque"] . '">
                    <h3>' . $bloque["nombreBloque"] . '</h3>
                    <img src="' . $bloque["img"] . '" alt="' . $bloque["nombreBloque"] . '"><br><br><br>
                </a>
            </div>';
        }
        ?>
    </div>
</body>
</html>
