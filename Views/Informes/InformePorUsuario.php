<div class="container">
	<h2>Informe / Movimientos por Usuario</h2>

	<form class="form-inline" action="?controller=InformePorUsuario&action=show" method="get">
		<input type="hidden" name="controller" value="InformePorUsuario">
		<input type="hidden" name="action" value="show">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaInicial" class="form-control" value="<?php echo h($fechaInicial); ?>" placeholder="Desde">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaFinal" class="form-control" value="<?php echo h($fechaFinal); ?>" placeholder="Hasta">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Filtrar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-default" href="?controller=InformePorUsuario&action=show">Limpiar filtros</a>
			</div>
		</div>
	</form>

	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Usuario</th>
					<th>Entradas registradas</th>
					<th>Salidas registradas</th>
					<th>Ajustes registrados</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php if (empty($resumen)) { ?>
					<tr><td colspan="5"><em>No hay movimientos con los filtros aplicados.</em></td></tr>
					<?php } ?>
					<?php foreach ($resumen as $fila) { ?>
					<tr>
						<td><?php echo h($fila['usuario']->getNombre().' '.$fila['usuario']->getApellido()); ?></td>
						<td><?php echo $fila['entradas']; ?></td>
						<td><?php echo $fila['salidas']; ?></td>
						<td><?php echo $fila['ajustes']; ?></td>
						<td><a class="btn btn-warning" href="?controller=InformePorUsuario&action=detalle&usuario=<?php echo $fila['usuario']->getId(); ?>&fechaInicial=<?php echo h($fechaInicial); ?>&fechaFinal=<?php echo h($fechaFinal); ?>"><span class="glyphicon glyphicon-eye-open"> </span> Detalle</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
