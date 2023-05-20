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
$server->configureWSDL("ws_crmlineadirecta_ivr");
$server->wsdl->schemaTargetNamespace = 'urn:SaludoXMLwsdl';
$server->soap_defencoding = "UTF-8";

// Parametros de Entrada
$server->wsdl->addComplexType('datos_asesora_entrada',
		'complexType',
		'struct',
		'all',
		'',
		array(
				'documento'   => array('name' => 'documento','type' => 'xsd:string'),
				'tipodocumento'    => array('name' => 'tipodocumento','type' => 'xsd:string'),
				'marca' => array('name' => 'marca','type' => 'xsd:string')
		)
);
	
// Parametros de Salida
$server->wsdl->addComplexType(  'datos_asesora_salida',
		'complexType',
		'struct',
		'all',
		'',
		array(
				'Nombre'   => array('name' => 'Nombre','type' => 'xsd:string'),
				'Tipo_documento'   => array('name' => 'Tipo_documento','type' => 'xsd:string'),
				'CamActual'   => array('name' => 'CamActual','type' => 'xsd:string'),
				'PcamActual'   => array('name' => 'PcamActual','type' => 'xsd:string'),
				'PcamAnt1'   => array('name' => 'PcamAnt1','type' => 'xsd:string'),
				'PcamAnt2'   => array('name' => 'PcamAnt2','type' => 'xsd:string'),
				'PcamAnt3'   => array('name' => 'PcamAnt3','type' => 'xsd:string'),
				'SaldoFactPen'   => array('name' => 'SaldoFactPen','type' => 'xsd:string'),
				'Mensaje'   => array('name' => 'Mensaje','type' => 'xsd:string')
		)
);
	
$server->register("consultarAsesora",
	array('datos_asesora_entrada' => 'tns:datos_asesora_entrada'), // parametros de entrada
	array('return' => 'tns:datos_asesora_salida'),
	false,
	'urn:SaludoXMLwsdl#Saludar', // Acción soap
	'rpc', // Estilo
	'encoded', // Uso
	'Saluda a la persona' // Documentación
);
	
	
	
function consultarAsesora($datos_asesora){

// CARMEL = 0
// PACIFIKA = 1
// LOGUIN = 2

	$arr_marcas = array('cf860fc4-136a-7876-beca-571794589fbe', '134b3392-3bf8-cabc-3357-571794f796d3', 'c0e725ab-12da-c076-b4da-57e9830f1a45');
	$marca = $arr_marcas[$datos_asesora['marca']];
	//require_once('datos.php');
	$datos = new Datos();

	$resultado = $datos->obtenerInfoAsesoras($datos_asesora['documento'], $datos_asesora['tipodocumento'], $marca);
	$totalRegistros = $resultado->rowCount();

	if($totalRegistros == 1) {
		$asesora = $resultado->fetch();
	
		return array('Nombre' => $asesora['nombre'],
			'Tipo_documento' => $asesora['tipo_documento_c'],
			'CamActual' => substr($asesora['campana_actual'], -2),
			'PcamActual' => $asesora['puntos_campana_actual'],
			'PcamAnt1' => $asesora['puntos_campana_anterior'],
			'PcamAnt2' => $asesora['puntos_campana_tras_anterior'],
			'PcamAnt3' => 0,
			'SaldoFactPen' => $asesora['saldo_pagar'],
			'Mensaje' => EXISTE);
	}else{
		return array('Nombre' => null,
			'Tipo_documento' => null,
			'CamActual' => null,
			'PcamActual' => null,
			'PcamAnt1' => null,
			'PcamAnt2' => null,
			'PcamAnt3' => null,
			'SaldoFactPen' => null,
			'Mensaje' => 'a');
	}


	




}
	
	
// Disparar el servicio

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
	
	

?>