<?php
session_start();   
include "../funtions.php";
include "../Database.php";
	
//CONEXION A DB
$database = new Database();

$expediente = 0;
$nombre = cleanStringStrtolower($_POST['name']);
$rtn = $_POST['rtn_empresa'];
$telefono1 = $_POST['telefono1'];
$direccion = $_POST['direccion'];
$correo = strtolower(cleanString($_POST['correo']));
$usuario = $_SESSION['colaborador_id'];
$date_write = date("Y-m-d H:m:s");
$estado = 1; //0. Inactivo 1. Activo

if(isset($_POST['departamento_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['departamento_id'] == ""){
		$departamento_id = 0;
	}else{
		$departamento_id = $_POST['departamento_id'];
	}
}else{
	$departamento_id = 0;
}

if(isset($_POST['municipio_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['municipio_id'] == ""){
		$departamento_id = 0;
	}else{
		$municipio_id = $_POST['municipio_id'];
	}
}else{
	$municipio_id = 0;
}

if(isset($_POST['pais_id'])){//COMPRUEBO SI LA VARIABLE ESTA DIFINIDA
	if($_POST['pais_id'] == ""){
		$pais_id = 0;
	}else{
		$pais_id = $_POST['pais_id'];
	}
}else{
	$pais_id = 0;
}

//CONSULTAR IDENTIDAD DEL USUARIO
if($rtn === "0"){
	$flag_identidad = true;
	while($flag_identidad){
	   $d=rand(1,99999999);
	   $query_identidadRand = "SELECT pacientes_empresa_id 
	       FROM pacientes_empresa_id 
		   WHERE rtn = '$d'";
	   $result_identidad = $mysqli->query($query_identidadRand);
	   if($result_identidad->num_rows==0){
		  $rtn = $d;
		  $flag_identidad = false;
	   }else{
		  $flag_identidad = true;
	   }		
	}
}    

// Validamos si el cliente ya existe
$tablaEmpresa = "pacientes_empresa";
$camposEmpresa = ["pacientes_empresa_id", "nombre", "rtn", "telefono", "departamento_id", "pais_id", "municipio_id", "direccion", "correo", "colaborador_id", "estado", "date_write"];
$campoCorrelativoEmpresa = "pacientes_empresa_id";
$condicionesEmpresa = ["rtn" => $rtn];
$orderBy = "";
$resultadoEmpresas = $database->consultarTabla($tablaEmpresa, $camposEmpresa, $condicionesEmpresa, $orderBy);

if (empty($resultadoEmpresas)) {
	$pacientes_empresa_id = $database->obtenerCorrelativo($tablaEmpresa, $campoCorrelativoEmpresa);
	$valoresEmpresa = [$pacientes_empresa_id, $nombre, $rtn, $telefono1, $departamento_id, $pais_id, $municipio_id, $direccion, $correo, $usuario, $estado, $date_write];

	 //REGISTRAMOS LOS DATOS DE LA EMPRESA
	 if ($database->insertarRegistro($tablaEmpresa, $camposEmpresa, $valoresEmpresa)) {
		$datos = array(
			0 => "Almacenado", 
			1 => "Registro Almacenado Correctamente", 
			2 => "success",
			3 => "btn-primary",
			4 => "formulario_pacientes_empresas",
			5 => "Registro",
			6 => "PacientesEmpresas",//FUNCION DE LA TABLA QUE LLAMAREMOS PARA QUE ACTUALICE (DATATABLE BOOSTRAP)
			7 => "", //Modals Para Cierre Automatico		
		 );
	 }else{
		$datos = array(
			0 => "Error", 
			1 => "No se puedo almacenar la empresa $nombre, los datos son incorrectos por favor corregir", 
			2 => "error",
			3 => "btn-danger",
			4 => "",
			5 => "",			
		);
	 }
}else{
	$datos = array(
		0 => "Error", 
		1 => "Lo sentimos la empresa con el RTN $rtn ya existe, no se puede almacenar", 
		2 => "error",
		3 => "btn-danger",
		4 => "",
		5 => "",		
	);
}

echo json_encode($datos);
?>