<?php
/**
*
*/

if (isset($_SESSION['usuario'])) {

class ConfiguracionController
{
	// Whitelist estricta de extension -> MIME real esperado (evita subir un .php disfrazado de .png).
	private static $extensionesPermitidas = [
		'png'  => 'image/png',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'gif'  => 'image/gif',
		'svg'  => 'image/svg+xml',
	];
	private static $tamanoMaximoBytes = 2097152; // 2 MB
	private static $carpetaDestino = 'Imagenes/logo';

	function __construct()
	{

	}

	function show(){
		if (!Permiso::usuarioPuede('configuracion.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar la configuración del sistema.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$logoActual = Configuracion::logoUrl();

		require_once('Views/Configuracion/show.php');
	}

	function guardarLogo(){
		if (!Permiso::usuarioPuede('configuracion.gestionar')) {
			flash('danger', 'No tiene permiso para gestionar la configuración del sistema.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		if (!isset($_FILES['logo']) || $_FILES['logo']['error'] === UPLOAD_ERR_NO_FILE) {
			flash('warning', 'Debe seleccionar una imagen para el logo.');
			echo "<script>window.location.href = '?controller=Configuracion&action=show';</script>";
			return;
		}

		$archivo = $_FILES['logo'];

		if ($archivo['error'] !== UPLOAD_ERR_OK) {
			flash('danger', 'No se pudo subir el archivo. Inténtelo nuevamente.');
			echo "<script>window.location.href = '?controller=Configuracion&action=show';</script>";
			return;
		}

		if ($archivo['size'] > self::$tamanoMaximoBytes) {
			flash('danger', 'La imagen supera el tamaño máximo permitido (2 MB).');
			echo "<script>window.location.href = '?controller=Configuracion&action=show';</script>";
			return;
		}

		$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
		$mimeReal = mime_content_type($archivo['tmp_name']);

		if (!isset(self::$extensionesPermitidas[$extension]) || $mimeReal !== self::$extensionesPermitidas[$extension]) {
			flash('danger', 'Formato de imagen no permitido. Use PNG, JPG, GIF o SVG.');
			echo "<script>window.location.href = '?controller=Configuracion&action=show';</script>";
			return;
		}

		if (!is_dir(self::$carpetaDestino)) {
			mkdir(self::$carpetaDestino, 0755, true);
		}

		$nombreArchivo = 'logo_' . time() . '.' . $extension;
		$rutaRelativa = self::$carpetaDestino . '/' . $nombreArchivo;

		if (!move_uploaded_file($archivo['tmp_name'], $rutaRelativa)) {
			flash('danger', 'No se pudo guardar la imagen en el servidor.');
			echo "<script>window.location.href = '?controller=Configuracion&action=show';</script>";
			return;
		}

		$anterior = Configuracion::logoPathGuardado();
		Configuracion::actualizarLogo($rutaRelativa);

		// Si el logo anterior tambien era uno subido desde esta pantalla (no el archivo por
		// defecto del codigo), se elimina para no acumular archivos huerfanos en el servidor.
		if ($anterior && strpos($anterior, self::$carpetaDestino . '/') === 0 && is_file($anterior)) {
			unlink($anterior);
		}

		Auditoria::registrar('Configuracion', Configuracion::ID_UNICO, 'EDITAR', $_SESSION['usuario']->getId(), ['LogoPath' => $anterior], ['LogoPath' => $rutaRelativa]);

		flash('success', 'Logo actualizado exitosamente.');
		echo "<script>window.location.href = '?controller=Configuracion&action=show';</script>";
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
