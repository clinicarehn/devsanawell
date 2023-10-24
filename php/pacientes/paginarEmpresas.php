<?php
session_start();   
include "../funtions.php";

header("Content-Type: text/html;charset=utf-8");

//CONEXION A DB
$mysqli = connect_mysqli();

$paginaActual = $_POST['partida'];
$estado = $_POST['estado'];
$paciente = $_POST['paciente'];
$dato = $_POST['dato'];

$query_row = "SELECT pacientes_empresa_id, nombre, RTN, telefono, direccion,
(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado', correo
FROM pacientes_empresa
WHERE estado = '$estado' AND (nombre LIKE '$dato%' OR rtn LIKE '$dato%')
ORDER BY nombre";	

$result = $mysqli->query($query_row);     

$nroProductos=$result->num_rows; 
$nroLotes = 15;
$nroPaginas = ceil($nroProductos/$nroLotes);
$lista = '';
$tabla = '';

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationEmpresa('.(1).');void(0);">Inicio</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationEmpresa('.($paginaActual-1).');void(0);">Anterior '.($paginaActual-1).'</a></li>';
}

if($paginaActual < $nroPaginas){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationEmpresa('.($paginaActual+1).');void(0);">Siguiente '.($paginaActual+1).' de '.$nroPaginas.'</a></li>';
}

if($paginaActual > 1){
	$lista = $lista.'<li class="page-item"><a class="page-link" href="javascript:paginationEmpresa('.($nroPaginas).');void(0);">Ultima</a></li>';
}

if($paginaActual <= 1){
	$limit = 0;
}else{
	$limit = $nroLotes*($paginaActual-1);
}

$query = "SELECT pacientes_empresa_id, nombre, rtn, telefono, direccion,
	(CASE WHEN estado = '1' THEN 'Activo' ELSE 'Inactivo' END) AS 'estado', correo
	FROM pacientes_empresa
	WHERE estado = '$estado' AND (nombre LIKE '$dato%' OR rtn LIKE '$dato%')
	ORDER BY nombre LIMIT $limit, $nroLotes
";
$result = $mysqli->query($query);    
  
$tabla = $tabla.'<table class="table table-striped table-condensed table-hover">
					<tr>
					   <th width="4.33%">N°</th>
					   <th width="8.33%">RTN</th>
					   <th width="8.33%">Empresa</th>
					   <th width="20.33%">Telefono</th>
					   <th width="4.33%">Correo</th>					   
					   <th width="8.33%">Direccion</th>
					   <th width="4.33%">Estado</th>
					   <th width="8.33%">Editar</th>
					   <th width="8.33%">Eliminar</th>
					</tr>';

$i=1;						
while($registro2 = $result->fetch_assoc()){
 
	$tabla = $tabla.'<tr>
	   <td>'.$i.'</td>
	   <td><a style="text-decoration:none" title = "Información de Usuario" href="javascript:showExpediente('.$registro2['pacientes_empresa_id'].');">'.$registro2['rtn'].'</a></td>
	   <td>'.$registro2['nombre'].'</td>
	   <td>'.$registro2['telefono'].'</td>
	   <td>'.$registro2['correo'].'</td>
	   <td>'.$registro2['direccion'].'</td>
	   <td>'.$registro2['estado'].'</td>
	   <td>
			<a class="btn btn btn-secondary ml-2" href="javascript:editarRegistroEmpresa('.$registro2['pacientes_empresa_id'].');void(0);"><div class="sb-nav-link-icon"></div><i class="fas fa-user-edit fa-lg"></i> Editar</a>
		</td>
		<td>
			<a class="btn btn btn-secondary ml-2" href="javascript:modal_eliminar_empresa('.$registro2['pacientes_empresa_id'].');void(0);"><div class="sb-nav-link-icon"></div><i class="fas fa-trash fa-lg"></i> Eliminar</a>
		</td>	   	              		  
	</tr>';
	$i++;
}

if($nroProductos == 0){
	$tabla = $tabla.'<tr>
	   <td colspan="11" style="color:#C7030D">No se encontraron resultados</td>
	</tr>';		
}else{
   $tabla = $tabla.'<tr>
	  <td colspan="11"><b><p ALIGN="center">Total de Registros Encontrados '.number_format($nroProductos).'</p></b>
   </tr>';		
}   

$tabla = $tabla.'</table>';

$array = array(0 => $tabla,
			   1 => $lista);

echo json_encode($array);
?>