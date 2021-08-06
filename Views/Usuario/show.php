<div class="container">
	<h2>Gestion Usuario</h2>
	<form class="form-inline" action="?controller=Usuario&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" id="identificacion" name="identificacion" type="text" placeholder="Identificacion">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-search"> </span> Buscar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=Usuario&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Usuario</a>
			</div>
		</div>
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Identificacion</th>
					<th>Nombre</th>
					<th>Apellido</th>
					<th>Rol</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaUsuario as $usuario) {

						$rol = Rol::searchById($usuario->getRolId());

					?>
					<tr>
						<td><?php echo $usuario->getIdentificacion(); ?></td>
						<td><?php echo $usuario->getNombre(); ?></td>
						<td><?php echo $usuario->getApellido(); ?></td>
						<td><?php echo $rol->getDescripcion(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=Usuario&&action=updateshow&&id=<?php echo $usuario->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=Usuario&&action=delete&&id=<?php echo $usuario->getId(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>