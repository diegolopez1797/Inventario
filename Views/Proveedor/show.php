<div class="container">
	<h2>Gestion Destino / Proveedor</h2>
	<div class="container">
		<form class="form-inline" action="?controller=Proveedor&action=search" method="post">
			<div class="form-group row">
				<div class="col-xs-4">
					<input type="number" class="form-control" id="id" name="id" type="text" placeholder="Busqueda por Codigo">
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xs-4">
					<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-search"> </span> Buscar</button>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xs-4">
					<a class="btn btn-success" href="?controller=Proveedor&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Proveedor</a>
				</div>
			</div>
		</form>
	</div>
	<div  class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaProveedor as $proveedor) {?>
					<tr>
						<td><?php echo $proveedor->getId(); ?></td>
						<td><?php echo h($proveedor->getDescripcion()); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=Proveedor&&action=updateshow&&id=<?php echo $proveedor->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=Proveedor&&action=delete&&id=<?php echo $proveedor->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
