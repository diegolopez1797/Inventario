<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class AlmacenController
{

	function __construct()
	{

	}

	function register(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		require_once('Views/Almacen/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$almacen = new Almacen(null,$_POST['descripcion'],$_POST['tipo']);
		$respuesta = Almacen::save($almacen);
		if(isset($respuesta)){
			$nuevoId = Db::getConnect()->lastInsertId();
			Auditoria::registrar('Almacen', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, ['Descripcion' => $almacen->getDescripcion(), 'Tipo' => $almacen->getTipo()]);
		    flash_now('success', 'Almacén creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el almacén. Inténtelo nuevamente.');
		}
			$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaAlmacen = Almacen::all();

		require_once('Views/Almacen/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$almacen = Almacen::searchById($id);
		require_once('Views/Almacen/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$antes = Almacen::searchById($_POST['id']);
		$almacen = new Almacen($_POST['id'],$_POST['descripcion'],$_POST['tipo']);
		Almacen::update($almacen);
		Auditoria::registrar('Almacen', $almacen->getId(), 'EDITAR', $_SESSION['usuario']->getId(),
			['Descripcion' => $antes->getDescripcion(), 'Tipo' => $antes->getTipo()],
			['Descripcion' => $almacen->getDescripcion(), 'Tipo' => $almacen->getTipo()]
		);
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
			$antes = Almacen::searchById($id);
			Almacen::delete($id);
			Auditoria::registrar('Almacen', $id, 'ELIMINAR', $_SESSION['usuario']->getId(), ['Descripcion' => $antes->getDescripcion(), 'Tipo' => $antes->getTipo()], null);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el almacén (puede tener existencias registradas).');
		}


		$this->show();
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
