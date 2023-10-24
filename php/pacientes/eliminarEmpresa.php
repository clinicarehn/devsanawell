<?php
session_start();   
include "../funtions.php";
include "../Database.php";
	
//CONEXION A DB
$database = new Database();

$pacientes_empresa_id = $_POST['pacientes_empresa_id'];
$usuario = $_SESSION['colaborador_id'];
$date = date("Y-m-d");
$date_write = date("Y-m-d H:m:s");

//VERIFICAMOS SI EL REGISTRO CUENTA CON INFORMACION ALMACENADA
$tablaPacientes = "pacientes";
$camposPacientes = ["pacientes_id"];
$condicionesPacientes = ["pacientes_empresa_id" => $pacientes_empresa_id];
$orderBy = "";
$resultadoPacientes = $database->consultarTabla($tablaPacientes, $camposPacientes, $condicionesPacientes, $orderBy);

if (empty($resultadoPacientes)) {
	//PROCEDEMOS A ELIMINAR EL REGISTRO
	$condiciones_eliminar  = ["pacientes_empresa_id" => $pacientes_empresa_id];

	// Llamar a la función para eliminar los registros
	if ($database->eliminarRegistros('pacientes_empresa', $condiciones_eliminar)) {
		echo 1;//REGISTRO ELIMINADO CORRECTAMENTE

		//AGREGAMOS EL HISTORIAL DEL REGISTRO ELIMINADO
		$tablaHistorial = " historial";
		$camposHistorial = ["historial_id"];
		$campoCorrelativoHistorial = "historial";
		$orderBy = "";

		$historial_id = $database->obtenerCorrelativo($tablaHistorial, $campoCorrelativoHistorial);
		$camposHistorial = ["historial_id", "pacientes_id", "expediente", "modulo", "codigo", "colaborador_id", "servicio_id", "fecha", "status", "observacion", "usuario", "fecha_registro"];

		$observacion = "Se elimino la empresa con RTN";

		$valoresHistorial = [$historial_id, 0, 0, "Pacientes Empresa", $pacientes_empresa_id , $usuario, 0, $date, "Eliminar", $observacion, $usuario, $date_write];
	
		 //REGISTRAMOS LOS DATOS DE LA EMPRESA
		 if ($database->insertarRegistro($tablaHistorial, $camposHistorial, $valoresHistorial)) {
			
		 }
	} else {
		echo 2;//ERROR AL PROCESAR SU SOLICITUD
	}  
}else{
	echo 3;//ESTE REGISTRO CUENTA CON INFORMACIÓN, NO SE PUEDE ELIMINAR
}  
?>