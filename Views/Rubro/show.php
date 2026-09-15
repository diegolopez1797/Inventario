<div class="container">
	<h2>Gestion Destino / Actividad</h2>
	<form class="form-inline" action="?controller=Rubro&action=search" method="post">
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
				<a class="btn btn-success" href="?controller=Rubro&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Actividad</a>
			</div>
		</div>
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaRubro as $rubro) {?>
					<tr>
						<td><?php echo $rubro->getId(); ?></td>
						<td><?php echo h($rubro->getDescripcion()); ?></td>
						<td><a id="boton-editar" class="btn btn-warning btn-xs" href="?controller=Rubro&&action=updateshow&&id=<?php echo $rubro->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger btn-xs" href="?controller=Rubro&&action=delete&&id=<?php echo $rubro->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>