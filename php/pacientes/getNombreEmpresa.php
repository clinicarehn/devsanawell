<?php
session_start();   
include "../funtions.php";

//CONEXION A DB
$mysqli = connect_mysqli();
 
$pacientes_empresa_id = $_POST['pacientes_empresa_id'];

$query = "SELECT nombre 
    FROM pacientes_empresa 
	WHERE pacientes_empresa_id = '$pacientes_empresa_id'";
$result = $mysqli->query($query);   
$consulta2 = $result->fetch_assoc(); 

$nombre = $consulta2['nombre'];

echo $nombre;

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>