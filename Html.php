<?php
/**
* Escape de salida para HTML (previene XSS almacenado/reflejado).
* Envolver con h() cualquier dato proveniente de usuario/BD que se imprima en una vista.
*/
function h($value){
	return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
* Mensajes del sistema (reemplaza los alert() nativos de JS). Hay dos formas de mostrarlos
* porque el layout ya emitio el HTML del header/flash ANTES de que routing.php ejecute el
* Controller (ver layout.php):
*   - flash(): guarda el mensaje en sesion, para los casos que redirigen a otra pagina con
*     window.location.href - se muestra al inicio de ESA pagina, en la siguiente peticion.
*   - flash_now(): lo imprime de inmediato en el punto donde se llama - para los casos que
*     NO redirigen, sino que vuelven a mostrar la misma pagina dentro de la misma peticion
*     (ej. error de validacion antes de $this->show()).
*/
function flash($tipo, $mensaje){
	if (!isset($_SESSION['flash'])) {
		$_SESSION['flash'] = [];
	}
	$_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function flash_now($tipo, $mensaje){
	echo flash_html($tipo, $mensaje);
}

function flash_render(){
	if (empty($_SESSION['flash'])) {
		return;
	}

	foreach ($_SESSION['flash'] as $item) {
		echo flash_html($item['tipo'], $item['mensaje']);
	}

	unset($_SESSION['flash']);
}

function flash_html($tipo, $mensaje){
	$clase = in_array($tipo, ['success', 'danger', 'warning', 'info'], true) ? $tipo : 'info';
	$html = '<div class="container"><div class="alert alert-' . $clase . ' alert-dismissible" role="alert">';
	$html .= '<button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>';
	$html .= h($mensaje);
	$html .= '</div></div>';
	return $html;
}

?>
