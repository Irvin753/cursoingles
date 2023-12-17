<?php

class Conexion
{
	public function conectar()
    {
        $usuario="root";
		$password="";
		$host="localhost";
		$bd="cursoingles";
		$conexion= new PDO("mysql:host=$host;dbname=$bd",$usuario,$password, array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES\'UTF8\''));
		return $conexion;
        }
}
?>
