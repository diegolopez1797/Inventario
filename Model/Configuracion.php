<?php
/**
* Configuracion general del sistema, de una sola fila fija (ID = 1). Por ahora solo guarda
* la ruta del logo de la cabecera, para que deje de estar quemado en el codigo (cabeceraNueva.php)
* y se pueda cambiar subiendo una imagen desde Configuracion > Logo.
*/
class Configuracion
{
	const ID_UNICO = 1;

	// Logo original del proyecto (el que estaba quemado en cabeceraNueva.php). Se usa como
	// respaldo mientras nadie haya subido uno propio, o si el campo en BD queda vacio.
	const LOGO_POR_DEFECTO = 'fpdf/tutorial/logo.png';

	// Ruta a mostrar en la cabecera: la guardada en BD, o el logo por defecto si no hay ninguna.
	public static function logoUrl(){
		$ruta = self::logoPathGuardado();
		return $ruta ? $ruta : self::LOGO_POR_DEFECTO;
	}

	// Ruta guardada tal cual (o null si nunca se ha subido un logo propio) - la usa la
	// pantalla de configuracion para saber si el logo actual es el de por defecto o uno subido.
	public static function logoPathGuardado(){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT LogoPath FROM configuracion WHERE ID = :id');
		$select->bindValue('id', self::ID_UNICO);
		$select->execute();
		$ruta = $select->fetchColumn();
		return $ruta !== false ? $ruta : null;
	}

	public static function actualizarLogo($rutaRelativa){
		$db = Db::getConnect();
		$update = $db->prepare('UPDATE configuracion SET LogoPath = :ruta WHERE ID = :id');
		$update->bindValue('ruta', $rutaRelativa);
		$update->bindValue('id', self::ID_UNICO);
		$update->execute();
	}
}

?>
