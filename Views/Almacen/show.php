<div class="container">
	<h2>Gestion Destino / Almacén</h2>
	<div class="container">
		<a class="btn btn-success" href="?controller=Almacen&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Almacén</a>
	</div>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Tipo</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaAlmacen as $almacen) {?>
					<tr>
						<td><?php echo $almacen->getId(); ?></td>
						<td><?php echo h($almacen->getDescripcion()); ?></td>
						<td><?php echo h($almacen->getTipo()); ?></td>
						<td><a id="boton-editar" class="btn btn-warning btn-xs" href="?controller=Almacen&&action=updateshow&&id=<?php echo $almacen->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger btn-xs" href="?controller=Almacen&&action=delete&&id=<?php echo $almacen->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
