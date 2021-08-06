<div class="container">
	<h2>Gestion Destino / Area</h2>
	<div class="container">
		<form class="form-inline" action="?controller=Area&action=search" method="post">
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
					<a class="btn btn-success" href="?controller=Area&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Area</a>
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
					<?php foreach ($listaArea as $area) {?>
					<tr>
						<td><?php echo $area->getId(); ?></td>
						<td><?php echo $area->getDescripcion(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=Area&&action=updateshow&&id=<?php echo $area->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=Area&&action=delete&&id=<?php echo $area->getId(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>