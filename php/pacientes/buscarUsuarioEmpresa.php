<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_empresa_id = $_POST['pacientes_empresa_id'];
$usuario = $_SESSION['colaborador_id'];
$estado = 1; //1. Activo 2. Inactivo
$fecha_registro = date("Y-m-d H:i:s");

//OBTENEMOS LOS VALORES DEL REGISTRO

//CONSULTA EN LA ENTIDAD CORPORACION
$query = "SELECT nombre,  rtn
			 FROM pacientes_empresa
			 WHERE pacientes_empresa_id = '$pacientes_empresa_id'";
$result = $mysqli->query($query);

$nombre = "";
$rtn = "";

if($result->num_rows>0){
	$consulta_expediente1 = $result->fetch_assoc();
	$nombre = $consulta_expediente1['nombre'];
	$rtn = $consulta_expediente1['rtn'];
}			 
	 
$datos = array(
				0 => $nombre, 
				1 => $rtn, 			
				);
echo json_encode($datos);

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>