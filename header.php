<?php
session_start();
if(!isset($_SESSION["pkUsuario"])){
  header("location: login.php");
  }
include 'clases/Conexion.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<header>
 
        <div class="container">
  <header class="border-bottom lh-1 py-3">
    <div class="row flex-nowrap justify-content-between align-items-center">
      <div class="col-4 pt-1">
      </div>
      <div class="col-4 text-center">
      <img href="index.php" src="img/logo.png" alt="Logo" width="180" height="180" >
      </div>
      <div class="col-4 d-flex justify-content-end align-items-center">
        <a class="link-secondary" href="#" aria-label="Search">
        </a>
      </div>
    </div>
  </header>

  <div class="nav-scroller py-1 mb-3 border-bottom">
    <nav class="nav nav-underline justify-content-between">
      <a style="font-size: 18px;" class="nav-item nav-link link-body-emphasis" href="index.php">Inicio</a>
      <a style="font-size: 18px;" class="nav-item nav-link link-body-emphasis" href="formularios/formulario_pregunta.php">Agregar pregunta</a>
      <a style="font-size: 18px;" class="nav-item nav-link link-body-emphasis" href="lista_interesados.php">Lista de interesados</a>     
      <a style="font-size: 18px;" class="nav-item nav-link link-body-emphasis" href="funciones/cerrar_sesion.php">Cerrar Sesión</a>
    </nav>
  </div>
</div>
    </header>
    <!-- Resto del contenido de la página -->
<body>
    
</body>
</html>
