<?php 


$controllers=array(
	'Material'=>['index','register','save','show','updateshow','update','delete','search', 'error','sinMovimiento'],
	'Contratista'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Destino'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Rubro'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Proyecto'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'RegistroEntradas'=>['save','show','searchMaterial','quitarMaterial'],
	'RegistroSalidas'=>['save','show', 'searchMaterial','quitarMaterial'],
	// InformeEntrada/InformeSalida ya no tienen listado propio (show/search/eliminar/generarXPDF
	// se eliminaron - Movimientos los reemplaza) - detalle()/generarPDF() se conservan porque
	// Movimientos, Kardex, Por Usuario, Salidas por Ubicacion y el Informe de Solicitudes
	// reutilizan ese drill-down para mostrar el documento completo.
	'InformeEntrada'=>['detalle','generarPDF'],
	'InformeSalida'=>['detalle','generarPDF'],
	'InformeDetallado'=>['show','detalle','generarPDF'],
	'Login'=>['show','verificar','entrar','salir'],
	'Usuario'=>['show','register','save','delete','update','updateshow','search'],
	'Auditoria'=>['show'],
	'Ubicacion'=>['show','register','save','updateshow','update','delete','desactivar','generarMasivoShow','generarMasivoPreview','generarMasivoConfirmar'],
	'TipoUbicacion'=>['show','register','save','updateshow','update','desactivar'],
	'Proveedor'=>['register','save','show','updateshow','update','delete','search', 'error'],
	'Almacen'=>['register','save','show','updateshow','update','delete'],
	'Dashboard'=>['show'],
	'Kardex'=>['show','buscar'],
	'InformePorUsuario'=>['show','detalle'],
	'Rol'=>['show','register','save','updateshow','update','delete','permisos','guardarPermisos'],
	'Solicitud'=>['register','save','show','searchMaterial','quitarMaterial','aprobar','rechazar','mostrarEntrega','procesarEntrega','reporte'],
	'AjusteInventario'=>['show','register','save'],
	'Movimientos'=>['show','generarPDF'],
	'NotificacionDestinatario'=>['show','register','save','updateshow','update','activar','desactivar']
);

// Acciones que mutan datos o sesion de autenticacion: exigen token CSRF valido.
// El nombre de accion es consistente entre controllers (save/update/delete/...),
// por eso la validacion se centraliza aqui en vez de repetirla en cada Controller.
$accionesMutantes = ['save', 'update', 'delete', 'searchMaterial', 'quitarMaterial', 'verificar', 'guardarPermisos', 'aprobar', 'rechazar', 'procesarEntrega', 'desactivar', 'generarMasivoConfirmar', 'activar'];

if (array_key_exists($controller,  $controllers)) {
	if (in_array($action, $controllers[$controller])) {
		if (in_array($action, $accionesMutantes) && !Csrf::validate()) {
			flash('warning', 'La sesión de este formulario expiró o no es válida. Por favor intente de nuevo.');
			echo "<script>window.location.href = '?controller=".$controller."&action=show';</script>";
		}else{
			call($controller, $action);
		}
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
		case 'Contratista':
		require_once('Model/Contratista.php');
		$controller = new ContratistaController();
		break;
		case 'Destino':
		require_once('Model/Destino.php');
		$controller = new DestinoController();
		break;
		case 'Rubro':
		require_once('Model/Rubro.php');
		$controller = new RubroController();
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
		case 'InformeEntrada':
		require_once('Model/InformeMaterialEntrada.php');
		$controller = new InformeEntradaController();
		break;
		case 'InformeSalida':
		require_once('Model/InformeMaterialSalida.php');
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
		case 'Auditoria';
		require_once('Model/Auditoria.php');
		$controller = new AuditoriaController();
		break;
		case 'Ubicacion';
		require_once('Model/Ubicacion.php');
		$controller = new UbicacionController();
		break;
		case 'TipoUbicacion';
		require_once('Model/TipoUbicacion.php');
		$controller = new TipoUbicacionController();
		break;
		case 'Proveedor';
		require_once('Model/Proveedor.php');
		$controller = new ProveedorController();
		break;
		case 'Almacen';
		require_once('Model/Almacen.php');
		$controller = new AlmacenController();
		break;
		case 'Dashboard';
		require_once('Model/Dashboard.php');
		$controller = new DashboardController();
		break;
		case 'Kardex';
		require_once('Model/Kardex.php');
		$controller = new KardexController();
		break;
		case 'InformePorUsuario';
		require_once('Model/InformePorUsuario.php');
		$controller = new InformePorUsuarioController();
		break;
		case 'Rol';
		require_once('Model/Rol.php');
		require_once('Model/Permiso.php');
		$controller = new RolController();
		break;
		case 'Solicitud';
		require_once('Model/Solicitud.php');
		$controller = new SolicitudController();
		break;
		case 'AjusteInventario';
		require_once('Model/AjusteInventario.php');
		$controller = new AjusteInventarioController();
		break;
		case 'Movimientos';
		require_once('Model/Movimientos.php');
		$controller = new MovimientosController();
		break;
		case 'NotificacionDestinatario';
		require_once('Model/NotificacionDestinatario.php');
		$controller = new NotificacionDestinatarioController();
		break;
		default:
				# code...
		break;
	}
	$controller->{$action}();
}

?>