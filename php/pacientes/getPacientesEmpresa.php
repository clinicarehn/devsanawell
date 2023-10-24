<?php
session_start();   
include('../funtions.php');
	
//CONEXION A DB
$mysqli = connect_mysqli();

$query = "SELECT pacientes_empresa_id, nombre 
    FROM pacientes_empresa
	ORDER BY nombre";

$result = $mysqli->query($query);	    
  
if($result->num_rows>0){
	while($consulta2 = $result->fetch_assoc()){
	     echo '<option value="'.$consulta2['pacientes_empresa_id'].'">'.$consulta2['nombre'].'</option>';
	}
}else{
	echo '<option value="">No hay registros</option>';
}

$result->free();//LIMPIAR RESULTADO
$mysqli->close();//CERRAR CONEXIÓN
?>