<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://accounts.google.com/gsi/client" async></script>

    <title>Login</title>
    <link rel="stylesheet" href="css/estilo.css?=6"> <!-- Enlace al archivo CSS -->
    
   
</head>
<body>
   <div class="login-container"> <!-- Clase para el contenedor principal -->
      <div class="login-box"> <!-- Clase para el cuadro del formulario de inicio de sesión -->
         <div class="login-logo"> <!-- Clase para el logo -->
            <img src="img/Logo.png" alt="Logo">
         </div>
         <br><br>
         <form action="validar_usuario.php" method="post">
            

            <center>
            <div id="g_id_onload"
               data-client_id="195230904845-41ka1uqvf8a5bgl02ai0sigcbasephcj.apps.googleusercontent.com"
               data-login_uri="http://localhost/cursoingles/validar_login.php"
               data-auto_prompt="false">
            </div>
            <div class="g_id_signin"
               data-type="standard"
               data-size="large"
               data-theme="outline"
               data-text="sign_in_with"
               data-shape="rectangular"
               data-logo_alignment="left">
            </div>
         </center>
         </form> 
         

            </div>
   </div>

   
</body>
</html>
