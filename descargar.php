    <?php
    require 'vendor/autoload.php';
    session_start();
    if(!isset($_SESSION["pkUsuario"])){
    header("location: login.php");
    }
    include 'clases/Conexion.php';
    // Crear una instancia de la clase Conexion
    $conexion = new Conexion();

    // Llamar al método conectar en la instancia
    $conn = $conexion->conectar();

    $pkInteresado = $_GET['interesadoID'];
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('curso-inglés-documento.docx');

    $sql = $conn->prepare("CALL ObtenerDatosParaWord(:pkInteresado)");

    $sql->bindParam(':pkInteresado', $pkInteresado, PDO::PARAM_INT);
    $sql->execute();

    $filas = $sql->fetch(PDO::FETCH_ASSOC);

    $templateProcessor->setValue('NombreInteresado', $filas['NombreInteresado']);
    $templateProcessor->setValue('folioIntento', $filas['folioIntento']);
    $templateProcessor->setValue('fechaIntento', $filas['fechaIntento']);
    $templateProcessor->setValue('horaInicio', $filas['horaInicio']);
    $templateProcessor->setValue('horaFinal', $filas['horaFinal']);
    $templateProcessor->setValue('aciertos', $filas['aciertos']);
    $templateProcessor->setValue('nombreNivel', $filas['nombreNivel']);
    $templateProcessor->setValue('descripcionNivel', $filas['descripcionNivel']);



    $templateProcessor->saveAs('Reporte interesado.docx');
    header('Content-Disposition: attachment; filename=Reporte interesado.docx; charset=iso-8859-1; charset=utf-8');
    echo file_get_contents('Reporte interesado.docx');

    exit;
    ?>