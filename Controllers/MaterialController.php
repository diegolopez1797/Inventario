<?php 
/**
* 
*/
if (isset($_SESSION['usuario'])) {

	class MaterialController
{
	
	function __construct()
	{
		
	}

	function index(){
		require_once('Views/Material/bienvenido.php');
	}

	function register(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		require_once('Views/Material/register.php');

	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$material = new Material(null,$_POST['codigo'],$_POST['descripcion'],$_POST['unidad'],null,$_POST['min'],$_POST['max']);
		$respuesta = Material::save($material);

		if(isset($respuesta)){
		    $nuevoId = Db::getConnect()->lastInsertId();
		    Auditoria::registrar('Material', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, [
		        'Codigo' => $material->getCodigo(),
		        'Descripcion' => $material->getDescripcion(),
		        'Unidad' => $material->getUnidad(),
		        'Min_Almacen' => $material->getMinAlmacen(),
		        'Max_Casa' => $material->getMaxCasa(),
		    ]);
		    flash_now('success', 'Material creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el material. Inténtelo nuevamente.');
		}

		$this->show();

	}

	const TAMANO_PAGINA = 25;
	const ORDEN_DEFECTO = 'Codigo';
	const DIRECCION_DEFECTO = 'ASC';

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		// Filtros de Inventario Actual (Fase 4.1) via GET, para que paginacion/orden/filtros
		// convivan en la misma URL y los filtros sobrevivan al cambiar de pagina.
		$texto = isset($_GET['texto']) ? trim($_GET['texto']) : '';
		$estado = isset($_GET['estado']) ? $_GET['estado'] : '';
		$orden = isset($_GET['orden']) ? $_GET['orden'] : self::ORDEN_DEFECTO;
		$direccion = isset($_GET['direccion']) ? $_GET['direccion'] : self::DIRECCION_DEFECTO;

		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
		$totalMateriales = Material::contarTotal($texto, $estado);
		$totalPaginas = max(1, (int) ceil($totalMateriales / self::TAMANO_PAGINA));
		$pagina = min($pagina, $totalPaginas);

		$listaMaterial = Material::paginado($pagina, self::TAMANO_PAGINA, $texto, $estado, $orden, $direccion);
		require_once('Views/Material/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$material = Material::searchById($id);
		require_once('Views/Material/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$antes = Material::searchById($_POST['id']);
		$material = new Material($_POST['id'],$_POST['codigo'],$_POST['descripcion'],$_POST['unidad'],$_POST['saldo'],$_POST['min'],$_POST['max']);
		Material::update($material);

		Auditoria::registrar('Material', $material->getId(), 'EDITAR', $_SESSION['usuario']->getId(), [
			'Codigo' => $antes->getCodigo(),
			'Descripcion' => $antes->getDescripcion(),
			'Unidad' => $antes->getUnidad(),
			'Saldo' => $antes->getSaldo(),
			'Min_Almacen' => $antes->getMinAlmacen(),
			'Max_Casa' => $antes->getMaxCasa(),
		], [
			'Codigo' => $material->getCodigo(),
			'Descripcion' => $material->getDescripcion(),
			'Unidad' => $material->getUnidad(),
			'Saldo' => $material->getSaldo(),
			'Min_Almacen' => $material->getMinAlmacen(),
			'Max_Casa' => $material->getMaxCasa(),
		]);

		$this->show();
	}
	function delete(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id=$_GET['id'];

		try{
			$antes = Material::searchById($id);
			Material::delete($id);
			Auditoria::registrar('Material', $id, 'ELIMINAR', $_SESSION['usuario']->getId(), [
				'Codigo' => $antes->getCodigo(),
				'Descripcion' => $antes->getDescripcion(),
				'Unidad' => $antes->getUnidad(),
				'Saldo' => $antes->getSaldo(),
			], null);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el material.');
		}
		
		
		$this->show();

	}

	// El listado ahora filtra por texto via GET directamente en show() (Fase 4.1), lo que permite
	// que paginacion/orden/filtros convivan en la misma URL. Esta accion existente se conserva
	// registrada en routing.php y solo redirige hacia ese filtro, para no dejar un enlace roto si
	// algo externo aun apunta a ella.
	function search(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$texto = isset($_POST['codigo']) ? trim($_POST['codigo']) : '';
		$destino = '?controller=Material&action=show';
		if ($texto !== '') {
			$destino .= '&texto=' . urlencode($texto);
		}
		echo "<script>window.location.href = '" . $destino . "';</script>";
	}

	const UMBRAL_DEFECTO = 60;

	// Materiales sin Movimiento (Fase 2.1 seccion 6-7 #9): el noveno informe, el mas
	// independiente de los 9 - no modifica ningun archivo/consulta existente, solo agrega este
	// metodo nuevo. Mismo permiso que Inventario Actual (catalogo.ver).
	function sinMovimiento(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$umbralDias = (isset($_GET['umbral']) && is_numeric($_GET['umbral']) && $_GET['umbral'] >= 0) ? (int) $_GET['umbral'] : self::UMBRAL_DEFECTO;

		$listaCompleta = Material::sinMovimiento($umbralDias);
		$totalMateriales = count($listaCompleta);
		$totalPaginas = max(1, (int) ceil($totalMateriales / self::TAMANO_PAGINA));
		$pagina = isset($_GET['pagina']) ? max(1, (int) $_GET['pagina']) : 1;
		$pagina = min($pagina, $totalPaginas);

		$listaMaterial = array_slice($listaCompleta, ($pagina - 1) * self::TAMANO_PAGINA, self::TAMANO_PAGINA);

		require_once('Views/Material/sinMovimiento.php');
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}
	
}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}



?>