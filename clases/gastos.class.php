<?php 
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class gastos extends conexion{

	private $table = "gestion_gastos";
	private $gastoId = "";
	private $fecha = "0000'00'00";
	private $cuenta = "";
	private $descripcion = "";
	private $gestionId = " ";
	private $empleadoId = "";
	private $conceptoId = "";
	private $departamentoId = "";
	private $posicionId = "";
	private $supervisoresId = "";
    private $total = "";
    private $aprobadopor = "";
    private $estado = "";
  

	public function obtenerGastosEmpleado($idempleado,$fechaInicio,$fechaFinal){ //funcion traer gastos empleado
		
		$query = " SELECT g.fecha, g.cuenta, g.descripcion, gg.total FROM gastos AS g 
		           INNER JOIN gestion_gastos AS gg ON g.idgasto = gg.idgasto
		           WHERE gg.idempleado = '$idempleado'
		           AND gg.fecha BETWEEN '$fechaInicio' AND '$fechaFinal' ORDER BY gg.fecha";
		//echo $query;
		//exit();
		return parent::obtenerDatos($query);
                                                                  
	}

	public function obtenerGastosDepartamento($iddepartamento,$mes){ //funcion traer gastos departamento
		
		$query = " SELECT gg.fecha, g.cuenta, g.descripcion, gg.total, d.departamento
				   FROM gastos AS g 
		           INNER JOIN gestion_gastos AS gg ON g.idgasto = gg.idgasto
		           INNER JOIN departamentos AS d ON gg.iddepartamento = d.iddepartamento
		           WHERE MONTH(gg.fecha) = '$mes' AND gg.iddepartamento = '$iddepartamento' ORDER BY gg.fecha";
		//echo $query;
		//exit();
		return parent::obtenerDatos($query);
                                                                  
	}

	public function obtenerDatosEmpleado($idempleado){ //funcion traer datos empleado
		
		$query = " SELECT e.nombre, d.departamento, p.posicion, s.supervisor
				   FROM empleados AS e 
		           INNER JOIN departamentos AS d ON e.iddepartamento = d.iddepartamento
		           INNER JOIN posiciones AS p ON e.idposicion = p.idposicion
		           INNER JOIN supervisores AS s ON e.idsupervisor = s.idsupervisor
		           WHERE e.idempleado = '$idempleado'
		           ORDER BY e.idempleado";
		//echo $query;
		//exit();
		return parent::obtenerDatos($query);
                                                                  
	}


	public function post($json)
	{
		$_respuestas = new respuestas;
		$datos = json_decode($json,true);
		
		//datos requeridos para insertar registro 
		if(!isset($datos['idgasto']) || !isset($datos['idempleado']) || !isset($datos['idconcepto']) || !isset($datos['iddepartamento']) || !isset($datos['idposicion']) || !isset($datos['idsupervisores']) || !isset($datos['total']) || !isset($datos['aprobadopor']) || !isset($datos['fecha']))
		{
			return $_respuestas->error_400();
		}
		else
		{
			$this->gastoId = $datos['idgasto'];
			$this->empleadoId = $datos['idempleado'];
			$this->conceptoId = $datos['idconcepto'];
			$this->departamentoId = $datos['iddepartamento'];
			$this->posicionId = $datos['idposicion'];
			$this->supervisoresId = $datos['idsupervisores'];
			$this->total = $datos['total'];
			$this->aprobadopor = $datos['aprobadopor'];
			$this->fecha = $datos['fecha'];

			$resp = $this->insertarRegistro();
			//echo $resp;
			//exit();
			if($resp){
				$respuesta = $_respuestas->response; 
				$respuesta["result"] = array(
					"gastoId" => $resp //contiene el id de la ultima fila que se inserto
				);
				return $respuesta;
			}
			else
			{
				return $_respuestas->error_500();
			}

		} 
	}

	public function put($json)
	{
		$_respuestas = new respuestas;
		$datos = json_decode($json,true);

		//datos requerido
		if(!isset($datos['gestionid'])){
			return $_respuestas->error_400();
		}
		else
		{
			$this->gestionId = $datos['gestionid'];
		    if(isset($datos['idgasto'])) {$this->gastoId = $datos['idgasto'];}
			if(isset($datos['idempleado'])) {$this->empleadoId = $datos['idempleado'];}
			if(isset($datos['idconcepto'])) {$this->conceptoId = $datos['idconcepto'];}
			if(isset($datos['iddepartamento'])) {$this->departamentoId = $datos['iddepartamento'];}
			if(isset($datos['idposicion'])) {$this->posicionId = $datos['idposicion'];}
			if(isset($datos['idsupervisores'])) {$this->supervisoresId = $datos['idsupervisores'];}
			if(isset($datos['total'])) {$this->total = $datos['total'];}
			if(isset($datos['aprobadopor'])) {$this->aprobadopor = $datos['aprobadopor'];}
			if(isset($datos['fecha'])) {$this->fecha = $datos['fecha'];}

			$resp = $this->modificarRegistro();
			if($resp){
				$respuesta = $_respuestas->response; 
				$respuesta["result"] = array(
					"gestionId" => $this->gestionId //contiene el id del paciente actualizado
				);
				return $respuesta;
			}
			else
			{
				return $_respuestas->error_500();
			} 

		} 
	}

	public function delete($json)
	{
		$_respuestas = new respuestas;
		$datos = json_decode($json,true);

		//datos requerido
		if(!isset($datos['gestionid'])){
			return $_respuestas->error_400();
		}
		else
		{
			$this->gestionId = $datos['gestionid'];
			$resp = $this->eliminarRegistro();
			if($resp){
				$respuesta = $_respuestas->response; 
				$respuesta["result"] = array(
					"gestionId" => $this->gestionId //contiene el id del registr actualizado
				);
				return $respuesta;
			}
			else
			{
				return $_respuestas->error_500();
			} 

		} 
	}


	private function eliminarRegistro(){
		//$query = "DELETE FROM " . $this->table . " WHERE PacienteId =  '" . $this->pacienteId . "' ";
		$query = "UPDATE " . $this->table . " SET estado = 0  WHERE idgestion = '" . $this->gestionId . "' "; 
		$resp = parent::nonQuery($query);
		if($resp){
			return $resp; //la fila afectada
		}
		else
		{
			return 0;
		}
	}

	private function insertarRegistro()
	{
		$query = "INSERT INTO " .$this->table . " (idgasto,idempleado,idconcepto,iddepartamento,idposicion,idsupervisores,total,aprobadopor,fecha)
		values('" . $this->gastoId ."','" . $this->empleadoId ."','" . $this->conceptoId ."','" . $this->departamentoId ."','" . $this->posicionId ."','" . $this->supervisoresId ."','" . $this->total ."','" . $this->aprobadopor ."','" . $this->fecha ."')"; 
		//print_r($query);
		//exit();
		$resp = parent::nonQueryId($query); //devuelve las filas afectadas en el insert 
		if($resp){
			return $resp;
		}
		else
		{
			return 0;
		}

	}

	private function modificarRegistro()
	{
		$query = "UPDATE " . $this->table . " SET idgasto ='". $this->gastoId ."', idempleado ='". $this->empleadoId ."', idconcepto ='". $this->conceptoId ."', iddepartamento ='". $this->departamentoId ."', idposicion ='". $this->posicionId ."', idsupervisores ='". $this->supervisoresId ."', total ='". $this->total ."', aprobadopor ='". $this->aprobadopor ."', fecha ='". $this->fecha ."' WHERE idgestion = '" . $this->gestionId . "' AND estado = 1 "; 
		//print_r($query);	  	
		$resp = parent::nonQuery($query); //retorna las filas afectadas
		if($resp){
			return $resp;
		}
		else
		{
			return 0;
		} 

	} 

}


 ?>