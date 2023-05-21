<?php
  
require_once('datos.php');
require_once('nusoap/lib/nusoap.php');  

const NOEXISTE  			= 0;
const DUPLICADO   			= 1;
const EXISTE 				= 2;

const CARMEL 				= 0;
const PACIFIKA 				= 1;
const LOGUIN 				= 2;



$server = new nusoap_server();
$server->configureWSDL("ws_crmlineaxxxx_ivr");
$server->wsdl->schemaTargetNamespace = 'urn:ws_crmlineaxxxx_ivr_wsdl';
$server->soap_defencoding = "UTF-8";

	
$server->register("WebServiceCarmel",
	array('documento' => 'xsd:string', 'tipodocumento' => 'xsd:string', 'marca' => 'xsd:int'),
	array('Nombre' => 'xsd:string', 'Tipo_documento' => 'xsd:string', 'CamActual' => 'xsd:string', 'PcamActual' => 'xsd:string', 'PcamAnt1' => 'xsd:string', 'PcamAnt2' => 'xsd:string', 'PcamAnt3' => 'xsd:string', 'SaldoFactPen' => 'xsd:string', 'Mensaje' => 'xsd:string'),
	false,
	'urn:ws_crmlineaxxxx_ivr_wsdl#WebServiceCarmel', // Acción soap
	'rpc', // Estilo
	'encoded', // Uso
	'Obtiene la información de una asesora de línea directa' // Documentación
);
	

$server->register("getStatus",
		array(),
		array('Status' => 'xsd:string'),
		false,
		'urn:ws_crmlineaxxxx_ivr_wsdl#getStatus', // Acción soap
		'rpc', // Estilo
		'encoded', // Uso
		'Devuelve el estado del servicio' // Documentación
);



	
function WebServiceCarmel($documento, $tipodocumento, $marca){

	// CARMEL = 0
	// PACIFIKA = 1
	// LOGUIN = 2

	$arr_marcas = array('cf860fc4-136a-7876-beca-571794589fbe', '134b3392-3bf8-cabc-3357-571794f796d3', 'c0e725ab-12da-c076-b4da-57e9830f1a45');
	$marca = $arr_marcas[$marca];
	$datos = new Datos();

	$resultado = $datos->obtenerInfoAsesoras($documento, $tipodocumento, $marca);
	$totalRegistros = $resultado->rowCount();
	
	if($totalRegistros == 1) {
		$asesora = $resultado->fetch();
		return array($asesora['nombre'], $asesora['tipo_documento_c'], substr($asesora['campana_actual'], -2), $asesora['puntos_campana_actual'], $asesora['puntos_campana_anterior'], $asesora['puntos_campana_tras_anterior'], 0, $asesora['saldo_pagar'], EXISTE);
	}else{
		return array(null, null, null, null, null, null, null, null, ($totalRegistros > 1) ? DUPLICADO : NOEXISTE);
	}
	

}


function getStatus(){
	try {
		return 'Current Satus: Service UP...';
	} catch (Exception $e) {
	}
}



	
// Disparar el servicio

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
	
	

?>