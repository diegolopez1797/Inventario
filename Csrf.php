<?php
/**
* Proteccion CSRF por token de sesion (patron "synchronizer token").
* Un solo token por sesion, valido tanto para formularios POST como para
* enlaces GET que mutan datos (ej. accion=delete, accion=quitarMaterial).
*/
class Csrf
{
	public static function token(){
		if (empty($_SESSION['csrf_token'])) {
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		return $_SESSION['csrf_token'];
	}

	public static function field(){
		return '<input type="hidden" name="csrf_token" value="'.self::token().'">';
	}

	public static function validate(){
		$token = isset($_REQUEST['csrf_token']) ? $_REQUEST['csrf_token'] : '';
		return isset($_SESSION['csrf_token']) && $token !== '' && hash_equals($_SESSION['csrf_token'], $token);
	}
}

?>
