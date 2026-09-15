<?php
/**
*
*/
if (isset($_SESSION['usuario'])) {

	class ProveedorController
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

		require_once('Views/Proveedor/register.php');
	}

	function save(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$proveedor = new Proveedor(null,$_POST['descripcion']);
		$respuesta = Proveedor::save($proveedor);
		if(isset($respuesta)){
			$nuevoId = Db::getConnect()->lastInsertId();
			Auditoria::registrar('Proveedor', $nuevoId, 'CREAR', $_SESSION['usuario']->getId(), null, ['Descripcion' => $proveedor->getDescripcion()]);
		    flash_now('success', 'Proveedor creado exitosamente.');
		}
		else{
		    flash_now('danger', 'No se ha podido guardar el proveedor. Inténtelo nuevamente.');
		}
			$this->show();
	}

	function show(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$listaProveedor = Proveedor::all();

		require_once('Views/Proveedor/show.php');
	}

	function updateshow(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$id = $_GET['id'];
		$proveedor = Proveedor::searchByIdUpdate($id);
		require_once('Views/Proveedor/updateshow.php');
	}

	function update(){
		if (!Permiso::usuarioPuede('catalogo.gestionar')) {
			flash('danger', 'No tiene permiso para esta acción.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		$antes = Proveedor::searchById($_POST['id']);
		$proveedor = new Proveedor($_POST['id'],$_POST['descripcion']);
		Proveedor::update($proveedor);
		Auditoria::registrar('Proveedor', $proveedor->getId(), 'EDITAR', $_SESSION['usuario']->getId(), ['Descripcion' => $antes->getDescripcion()], ['Descripcion' => $proveedor->getDescripcion()]);
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
			$antes = Proveedor::searchById($id);
			Proveedor::delete($id);
			Auditoria::registrar('Proveedor', $id, 'ELIMINAR', $_SESSION['usuario']->getId(), ['Descripcion' => $antes->getDescripcion()], null);
		}catch (Exception $e) {
			flash_now('danger', 'No se puede eliminar el proveedor.');
		}


		$this->show();
	}

	function search(){
		if (!Permiso::usuarioPuede('catalogo.ver')) {
			flash('danger', 'No tiene permiso para ver este catálogo.');
			echo "<script>window.location.href = '?controller=Dashboard&action=show';</script>";
			return;
		}

		if ((!empty($_POST['id'])) and ($_POST['id']>=1)) {
			$id = $_POST['id'];
			$proveedor = Proveedor::searchById($id);
			if ($proveedor->getId() == $id) {
				$listaProveedor[] = $proveedor;
				require_once('Views/Proveedor/show.php');
			}else{
				flash_now('warning', 'El proveedor buscado no existe.');
				$this->show();
			}
		} else {
			flash_now('warning', 'No ha ingresado un código o el valor ingresado no es válido.');
			$this->show();
		}
	}

	function error(){
		require_once('Views/Material/error.php');
	}

}

}else{
	echo "<script>window.location.href = '?controller=Login&action=show';</script>";
}

?>
