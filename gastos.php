<?php 
require_once 'clases/respuestas.class.php';
require_once 'clases/gastos.class.php';

$_respuestas = new respuestas;
$_gastos = new gastos;

if($_SERVER['REQUEST_METHOD'] == "GET"){
	
	if(isset($_GET["iddepartamento"])  || isset($_GET["mes"]))
	{
		$departamentoid = $_GET["iddepartamento"]; //gastos por departamento
		$mes = $_GET["mes"];
		$datosDepartamento = $_gastos->obtenerGastosDepartamento($departamentoid,$mes);
		header('Content-Type: application/json'); 
		echo json_encode($datosDepartamento);
		http_response_code(200);
	}

	if(!isset($_GET["fechainicio"])  || !isset($_GET["fechafinal"]))
	{
		$empleadoid = $_GET["idempleado"];  //datos del empleado
		$datosEmpleado = $_gastos->obtenerDatosEmpleado($empleadoid);
		header('Content-Type: application/json'); 
		echo json_encode($datosEmpleado);
		http_response_code(200);
	}

	else if(isset($_GET["fechainicio"]) && isset($_GET["fechafinal"]))
	{
		$empleadoid = $_GET["idempleado"];
		$fechaInicio = $_GET["fechainicio"];  //gastos por empleado
		$fechaFinal = $_GET["fechafinal"];
		$datosEmpleado = $_gastos->obtenerGastosEmpleado($empleadoid,$fechaInicio,$fechaFinal);
		header('Content-Type: application/json'); 
		echo json_encode($datosEmpleado);
		http_response_code(200);
	}
}
else if($_SERVER['REQUEST_METHOD'] == "POST"){
	//recibimos los datos enviados
	$postBody = file_get_contents("php://input");
	//enviamos los datos al manejador
	$datosArray = $_gastos->post($postBody);
	//print_r($resp);
	//devolvemos una respuesta
	header('Content-Type: application/json');  //Content-Type dice al cliente que tipo de contenido será retornado en POST o PUT
	if(isset($datosArray["result"]["error_id"])){ //si la respuesta trae algun error
		$responseCode = $datosArray["result"]["error_id"]; //se obtiene el codigo del error por medio del error_id
		http_response_code($responseCode); // Obtener el código de la respuesta actual y establecer uno nuevo
	}
	else
	{
		http_response_code(200);
	}
	echo json_encode($datosArray);
}
else if($_SERVER['REQUEST_METHOD'] == "PUT"){
	//recibimos los datos enviados
	$postBody = file_get_contents("php://input");
	//enviamos datos al manejador
	$datosArray = $_gastos->put($postBody);
	//print_r($postBody);
	//devolvemos una respuesta
	header('Content-Type: application/json');  //Content-Type dice al cliente que tipo de contenido será retornado en POST o PUT
	if(isset($datosArray["result"]["error_id"])){ //si la respuesta trae algun 
		$responseCode = $datosArray["result"]["error_id"]; //se obtiene el codigo del error por medio del error_id
		http_response_code($responseCode); // Obtener el código de la respuesta actual y establecer uno nuevo
	}
	else
	{
		http_response_code(200);
	}
	echo json_encode($datosArray);
	
}
else if($_SERVER['REQUEST_METHOD'] == "DELETE")
{
	$headers = getallheaders(); //recibe los headers que son enviados
	if(isset($headers["gestionid"]))
	{
		//recibimos los datos enviados por el header
		$send = [
			"gestionId" => $headers["gestionid"]
		];
		$postBody = json_encode($send);
	}
	else
	{
		//recibimos los datos enviados por el body
		$postBody = file_get_contents("php://input");
	}
	//enviamos datos al manejador
	$datosArray = $_gastos->delete($postBody);
	//print_r($postBody);
	//devolvemos una respuesta
	header('Content-Type: application/json');  //Content-Type dice al cliente que tipo de contenido será retornado 
	if(isset($datosArray["result"]["error_id"])){ //si la respuesta trae algun 
		$responseCode = $datosArray["result"]["error_id"]; //se obtiene el codigo del error por medio del error_id
		http_response_code($responseCode); // Obtener el código de la respuesta actual y establecer uno nuevo
	}
	else
	{
		http_response_code(200);
	}
	echo json_encode($datosArray);  
}
else
{
	header('Content-Type: application/json');
	$datosArray = $_respuestas->error_405(); //metodo no permitido
	echo json_encode($datosArray);
}

 ?>