<?php
session_start();   
include "../funtions.php";
	
//CONEXION A DB
$mysqli = connect_mysqli();

$pacientes_empresa_id = $_POST['pacientes_empresa_id'];
$estado = 1; //1. Activo 2. Inactivo
$fecha_registro = date("Y-m-d H:i:s");
$usuario = $_SESSION['colaborador_id'];	

$consulta_expediente = "SELECT nombre, rtn, telefono, pais_id, departamento_id, municipio_id, direccion, correo
	FROM pacientes_empresa
	WHERE pacientes_empresa_id = '$pacientes_empresa_id'";
$result = $mysqli->query($consulta_expediente);   

$nombre = "";
$rtn = "";
$telefono = "";
$pais_id = "";
$departamento_id = "";
$municipio_id = "";
$direccion = "";
$correo = "";
	
if($result->num_rows>0){
	$consulta_expediente1 = $result->fetch_assoc();
	$nombre = $consulta_expediente1['nombre'];
	$rtn = $consulta_expediente1['rtn'];
	$telefono = $consulta_expediente1['telefono'];
	$pais_id = $consulta_expediente1['pais_id'];
	$departamento_id = $consulta_expediente1['departamento_id'];
	$municipio_id = $consulta_expediente1['municipio_id'];
	$direccion = $consulta_expediente1['direccion'];
	$correo = $consulta_expediente1['correo'];	
}

$datos = array(
	0 => $nombre, 
	1 => $rtn,	
	2 => $telefono,
	3 => $pais_id,
	4 => $departamento_id,
	5 => $municipio_id,
	6 => $direccion,
	7 => $correo		
);
echo json_encode($datos);
?>