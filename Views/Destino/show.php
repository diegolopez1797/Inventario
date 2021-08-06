<div class="container">
	<h2>Gestion Destino / Destino</h2>
	<form class="form-inline" action="?controller=Destino&action=search" method="post">
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
				<a class="btn btn-success" href="?controller=Destino&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Destino</a>
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
					<?php foreach ($listaDestino as $destino) {?>
					<tr>
						<td><?php echo $destino->getId(); ?></td>
						<td><?php echo $destino->getDescripcion(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=Destino&&action=updateshow&&id=<?php echo $destino->getId(); ?>">Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=Destino&&action=delete&&id=<?php echo $destino->getId(); ?>">Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>