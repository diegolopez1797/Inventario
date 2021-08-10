<?php 


$controllers=array(
	'Material'=>['index','register','save','show','updateshow','update','delete','search', 'error'],
	'Area'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Contratista'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Destino'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Casa'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Manzana'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Proyecto'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'RegistroEntradas'=>['save','show','searchMaterial','quitarMaterial'],
	'RegistroSalidas'=>['save','show', 'searchMaterial','quitarMaterial'],
	'InformeGeneral'=>['show','search','generarPDF','eliminar','error'],
	'InformeEntrada'=>['show','detalle','generarPDF','search','eliminar','generarEntradaPDF'],
	'InformeSalida'=>['show','detalle','generarPDF','search','eliminar','generarSalidaPDF'],
	'InformeDetallado'=>['show','searchDescripcion','detalladoMaterial'],
	'Login'=>['show','verificar','entrar','salir'],
	'Usuario'=>['show','register','save','delete','update','updateshow','search'],
	'InformeMaterialPorExistencia'=>['show','search','generarPDF','eliminar'],
	'InformeMaterialPorCompra'=>['show','search','generarPDF','eliminar'],
	'InformeEntradaPorMaterialFecha'=>['show','search','eliminar','generarPDF'],
	'InformeSalidaPorMaterialFecha'=>['show','search','eliminar','generarPDF'],
	'InformeEntradaPorFecha'=>['show','search','eliminar','generarPDF'],
	'InformeSalidaPorFecha'=>['show','search','eliminar','generarPDF'],
	'InformeSalidaPorCasa'=>['show','search','eliminar','generarPDF'],
	'InformeSalidaPorContratistaFecha'=>['show','search','eliminar','generarPDF']
);

if (array_key_exists($controller,  $controllers)) {
	if (in_array($action, $controllers[$controller])) {
		call($controller, $action);
	}
	else{
		call('Material','error');
	}		
}else{
	call('Material','error');
}

function call($controller, $action){
	require_once('Controllers/'.$controller.'Controller.php');
	
	switch ($controller) {
		case 'Material':
		require_once('Model/Material.php');
		$controller = new MaterialController();
		break;
		case 'Area':
		require_once('Model/Area.php');
		$controller = new AreaController();
		break;
		case 'Contratista':
		require_once('Model/Contratista.php');
		$controller = new ContratistaController();
		break;	
		case 'Destino':
		require_once('Model/Destino.php');
		$controller = new DestinoController();
		break;	
		case 'Casa':
		require_once('Model/Casa.php');
		$controller = new CasaController();
		break;	
		case 'Manzana':
		require_once('Model/Manzana.php');
		$controller = new ManzanaController();
		break;	
		case 'Proyecto':
		require_once('Model/Proyecto.php');
		$controller = new ProyectoController();
		break;
		case 'RegistroEntradas':
		require_once('Model/RegistroEntradas.php');
		$controller = new RegistroEntradasController();
		break;	
		case 'RegistroSalidas':
		require_once('Model/RegistroSalidas.php');
		$controller = new RegistroSalidasController();
		break;
		case 'InformeGeneral':
		require_once('Model/InformeGeneral.php');
		$controller = new InformeGeneralController();
		break;
		case 'InformeEntrada':
		require_once('Model/InformeEntrada.php');
		$controller = new InformeEntradaController();
		break;
		case 'InformeSalida':
		require_once('Model/InformeSalida.php');
		$controller = new InformeSalidaController();
		break;
		case 'InformeDetallado':
		require_once('Model/InformeDetallado.php');
		$controller = new InformeDetalladoController();
		break;
		case 'Login':
		require_once('Model/Usuario.php');
		$controller = new LoginController();
		break;
		case 'Usuario':
		require_once('Model/Usuario.php');
		$controller = new UsuarioController();
		break;		
		case 'InformeMaterialPorExistencia';
		require_once('Model/InformeMaterialPorExistencia.php');
		$controller = new InformeMaterialPorExistenciaController();
		break;
		case 'InformeMaterialPorCompra';
		require_once('Model/InformeMaterialPorCompra.php');
		$controller = new InformeMaterialPorCompraController();
		break;
		case 'InformeEntradaPorMaterialFecha';
		require_once('Model/InformeEntradaPorMaterialFecha.php');
		$controller = new InformeEntradaPorMaterialFechaController();
		break;
		case 'InformeSalidaPorMaterialFecha';
		require_once('Model/InformeSalidaPorMaterialFecha.php');
		$controller = new InformeSalidaPorMaterialFechaController();
		break;
		case 'InformeEntradaPorFecha';
		require_once('Model/InformeEntradaPorFecha.php');
		$controller = new InformeEntradaPorFechaController();
		break;
		case 'InformeSalidaPorFecha';
		require_once('Model/InformeSalidaPorFecha.php');
		$controller = new InformeSalidaPorFechaController();
		break;
		case 'InformeSalidaPorContratistaFecha';
		require_once('Model/InformeSalidaPorFecha.php');
		$controller = new InformeSalidaPorContratistaFechaController();
		break;
		case 'InformeSalidaPorCasa';
		require_once('Model/InformeDetallado.php');
		$controller = new InformeSalidaPorCasaController();
		break;
		default:
				# code...
		break;
	}
	$controller->{$action}();
}

?>