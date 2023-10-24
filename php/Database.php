<?php
class Database {
    private $host = SERVER;
    private $usuario = USER;
    private $contrasena = PASS;
    private $conexion;  

    public function __construct() {
        $this->conexion = new mysqli(SERVER, USER, PASS);
    
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    
        // Intenta seleccionar la base de datos
        if (!$this->conexion->select_db(DB)) {
            die("Error al seleccionar la base de datos desde Database.php: " . $this->conexion->error);
        }
    }

    public function __destruct() {
        $this->conexion->close();
    }

    public function obtenerCorrelativo($tabla, $campoCorrelativo) {
        $tabla = $this->conexion->real_escape_string($tabla);
        $campoCorrelativo = $this->conexion->real_escape_string($campoCorrelativo);

        $query = "SELECT MAX($campoCorrelativo) AS max_correlativo FROM $tabla";
        $result = $this->conexion->query($query);

        if ($result !== false && $result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $correlativo = (int)$row['max_correlativo'] + 1;
            return $correlativo;
        } else {
            // Si no se encuentra ningún registro, se asume que el correlativo empieza en 1
            return 1;
        }
    }    

    public function consultarTabla($tabla, $campos = array(), $condiciones = array(), $orderBy = '', $tablaJoin = '', $condicionesJoin = array()) {
        $tabla = $this->conexion->real_escape_string($tabla);
    
        // Construir la consulta SELECT básica
        $query = "SELECT ";

        if (empty($campos)) {
            $query .= "*"; // Si no se especifican campos, seleccionar todos (*)
        } else {
            //$campos = array_map([$this->conexion, 'real_escape_string'], $campos);

            $query .= implode(",", $campos);
        }

        $query .= " FROM $tabla";
    
        // Agregar cláusula INNER JOIN si se especifica
        if (!empty($tablaJoin) && !empty($condicionesJoin)) {
            $tablaJoin = $this->conexion->real_escape_string($tablaJoin);
    
            $clausesJoin = array();
            foreach ($condicionesJoin as $campoJoin => $valorJoin) {
                $campoJoin = $this->conexion->real_escape_string($campoJoin);
                $valorJoin = $this->conexion->real_escape_string($valorJoin);
                $clausesJoin[] = "$tabla.$campoJoin = $tablaJoin.$valorJoin"; // Corregir el INNER JOIN
            }
    
            // Agregar cláusula INNER JOIN separada
            $query .= " INNER JOIN $tablaJoin ON " . implode(" AND ", $clausesJoin);
        }
    
        // Si se especifican condiciones, agregarlas a la consulta
        if (!empty($condiciones)) {
            $clauses = array();
            foreach ($condiciones as $campo => $valores) {
                $campo = $this->conexion->real_escape_string($campo);
    
                if (is_array($valores)) {
                    // Evitar el uso de real_escape_string para valores numéricos en IN
                    $valores = array_map(function ($valor) {
                        return is_numeric($valor) ? $valor : $this->conexion->real_escape_string($valor);
                    }, $valores);
                    $valores = implode("', '", $valores);
                    $clauses[] = "$campo IN ('$valores')";
                } else {
                    $valores = $this->conexion->real_escape_string($valores);
                    $clauses[] = "$campo = '$valores'";
                }
            }
            $query .= " WHERE " . implode(" AND ", $clauses); // Concatenar las condiciones usando AND
        }
    
        // Agregar cláusula ORDER BY si se especifica
        if (!empty($orderBy)) {
            $query .= " ORDER BY $orderBy";
        }
    
        // Ejecutar la consulta
        $result = $this->conexion->query($query);
    
        $resultados = array();
    
        if ($result) { // Verificar si la consulta se ejecutó correctamente
            while ($row = $result->fetch_assoc()) {
                $resultados[] = $row;
            }
        } else {
            echo "Error en la consulta: " . $this->conexion->error; // Mostrar mensaje de error
        }
    
        return $resultados;
    }
               
    public function insertarRegistro($tabla, $campos, $valores) {
        $tabla = $this->conexion->real_escape_string($tabla);

        // Escapar y formatear los campos para la consulta
        $campos = implode(",", array_map([$this->conexion, 'real_escape_string'], $campos));

        // Escapar y formatear los valores para la consulta
        $valores = "'" . implode("','", array_map([$this->conexion, 'real_escape_string'], $valores)) . "'";

        // Construir la consulta INSERT
        $query = "INSERT INTO $tabla ($campos) VALUES ($valores)";

        // Ejecutar la consulta
        if ($this->conexion->query($query) === TRUE) {
            return true;
        } else {
            // Si hay un error en la consulta, imprime el mensaje de error
            echo "Error en la consulta: " . $this->conexion->error;
            return false;
        }
    }

    public function actualizarRegistros($tabla, $datos, $condiciones = array()) {
        $tabla = $this->conexion->real_escape_string($tabla);
    
        // Construir la consulta UPDATE
        $query = "UPDATE $tabla SET ";
    
        $actualizaciones = array();
        foreach ($datos as $campo => $valor) {
            $campo = $this->conexion->real_escape_string($campo);
            $valor = $this->conexion->real_escape_string($valor);
            $actualizaciones[] = "$campo = '$valor'";
        }
        $query .= implode(", ", $actualizaciones);
    
        // Si se especifican condiciones, agregarlas a la consulta
        if (!empty($condiciones)) {
            $clauses = array();
            foreach ($condiciones as $campo => $valor) {
                $campo = $this->conexion->real_escape_string($campo);
                $valor = $this->conexion->real_escape_string($valor);
                $clauses[] = "$campo = '$valor'";
            }
            $query .= " WHERE " . implode(" AND ", $clauses); // Concatenar las condiciones usando AND
        }
    
        // Ejecutar la consulta
        if ($this->conexion->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    }    

  /*
    // Datos a actualizar
    $datos_actualizar = array(
        'nombre' => 'Nuevo Nombre',
        'email' => 'nuevo_email@example.com',
        'activo' => 1
    );

    // Condiciones para seleccionar los registros que se actualizarán
    $condiciones_actualizar = array(
        'id = 1' // Actualizar el usuario con id = 1
    );

    // Llamar a la función para actualizar los registros
    if ($database->actualizarRegistros('usuarios', $datos_actualizar, $condiciones_actualizar)) {
        echo "Registros actualizados correctamente.";
    } else {
        echo "Error al actualizar registros.";
    }   
  */

    public function eliminarRegistros($tabla, $condiciones = array()) {
        $tabla = $this->conexion->real_escape_string($tabla);

        // Construir la consulta DELETE
        $query = "DELETE FROM $tabla";

        // Si se especifican condiciones, agregarlas a la consulta
        if (!empty($condiciones)) {
            $clauses = array();
            foreach ($condiciones as $campo => $valor) {
                $campo = $this->conexion->real_escape_string($campo);
                $valor = $this->conexion->real_escape_string($valor);
                $clauses[] = "$campo = '$valor'";
            }
            $query .= " WHERE " . implode(" AND ", $clauses); // Concatenar las condiciones usando AND
        }

        // Ejecutar la consulta
        if ($this->conexion->query($query) === TRUE) {
            return true;
        } else {
            return false;
        }
    }

  /*
      // Condiciones para seleccionar los registros que se eliminarán
      $condiciones_eliminar = array(
          'activo = 0' // Eliminar los usuarios inactivos
      );

      // Llamar a la función para eliminar los registros
      if ($database->eliminarRegistros('usuarios', $condiciones_eliminar)) {
          echo "Registros eliminados correctamente.";
      } else {
          echo "Error al eliminar registros.";
      }  
  */
}
?>