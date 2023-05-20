<?php
$conf = include 'wsconfig.php';

class Datos{
	
	var $log;
	var $db;
	
	public function __construct(){
		try {
			$this->db = $this->getConnection();
		} catch (Exception $e) {
		}
	}
	
	
	function getConnection(){
		try {
			global $conf;
			$dbhost = $conf['DB_config']['host'];
			$dbname = $conf['DB_config']['data_base'];
			$dbuser = $conf['DB_config']['user'];
			$dbpass = $conf['DB_config']['pass'];

			$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
			return $dbh;
		} catch (Exception $e) {
		}			
	}
	

	function obtenerInfoAsesoras($documento, $tipodocumento, $marca){
		try {
			$sql="SELECT
				CONCAT_WS(' ', ac.primer_nombre_c, ac.segundo_nombre_c, ac.primer_apellido_c, ac.segundo_apellido_c ) AS nombre,
				ac.tipo_documento_c,
				p.campana_actual,
				p.puntos_campana_actual,
				p.puntos_campana_anterior,
				p.puntos_campana_tras_anterior,
				ec.`saldo_pagar`
			FROM
				accounts_cstm ac
				JOIN `accounts_emt_puntos_1_c` aap ON aap.`accounts_emt_puntos_1accounts_ida` = ac.id_c
				JOIN `emt_puntos` p ON aap.`accounts_emt_puntos_1emt_puntos_idb` = p.id
				JOIN `accounts_emt_estado_cuenta_1_c` aec ON aec.`accounts_emt_estado_cuenta_1accounts_ida` = ac.id_c
				JOIN `emt_estado_cuenta` ec ON aec.`accounts_emt_estado_cuenta_1emt_estado_cuenta_idb` = ec.id
			WHERE
				(
				numero_documento_c = '{$documento}'
				OR numero_documento_c = CONCAT('E','{$documento}')
				)
				AND emt_maestro_marcas_id_c = '{$marca}';";

			$resultado  = $this->db->query($sql);
			return $resultado;
		} catch (Exception $e) {
		}
	}	
	

	
}// Fin Class
	

?>