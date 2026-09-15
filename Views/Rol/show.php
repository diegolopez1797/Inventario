<div class="container">
	<h2>Gestion Roles</h2>
	<div class="container">
		<a class="btn btn-success" href="?controller=Rol&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Rol</a>
	</div>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaRol as $rol) {?>
					<tr>
						<td><?php echo $rol->getId(); ?></td>
						<td><?php echo h($rol->getDescripcion()); ?></td>
						<td>
							<a class="btn btn-info btn-xs" href="?controller=Rol&&action=permisos&&id=<?php echo $rol->getId(); ?>"><span class="glyphicon glyphicon-list-alt"> </span> Permisos</a>
							<a id="boton-editar" class="btn btn-warning btn-xs" href="?controller=Rol&&action=updateshow&&id=<?php echo $rol->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a>
							<a id="boton-eliminar" class="btn btn-danger btn-xs" href="?controller=Rol&&action=delete&&id=<?php echo $rol->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
