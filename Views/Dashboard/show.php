<div class="container">
	<h2>Inicio</h2>

	<div class="form-group row" style="margin-bottom: 20px;">
		<div class="col-xs-4" style="display:inline-block; margin-right:10px;">
			<a class="btn btn-success btn-lg" href="?controller=RegistroEntradas&action=show"><span class="glyphicon glyphicon-plus-sign"> </span> Registrar Entrada</a>
		</div>
		<div class="col-xs-4" style="display:inline-block;">
			<a class="btn btn-primary btn-lg" href="?controller=RegistroSalidas&action=show"><span class="glyphicon glyphicon-minus-sign"> </span> Registrar Salida</a>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-6" style="display:inline-block; vertical-align:top; width:48%;">
			<h3>Materiales críticos</h3>
			<?php if (empty($materialesCriticos)) { ?>
				<p><em>Ningún material está en o por debajo de su mínimo de almacén.</em></p>
			<?php } else { ?>
				<table class="table table-hover">
					<thead>
						<tr><th>Descripción</th><th>Saldo</th><th>Mínimo</th></tr>
					</thead>
					<tbody>
						<?php foreach ($materialesCriticos as $material) { ?>
						<tr>
							<td><?php echo h($material->getDescripcion()); ?></td>
							<td><span class="label label-danger"><?php echo $material->getSaldo(); ?> <?php echo h($material->getUnidad()); ?></span></td>
							<td><?php echo $material->getMinAlmacen(); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>

		<div class="col-xs-6" style="display:inline-block; vertical-align:top; width:48%;">
			<h3>Actividad reciente</h3>
			<?php if (empty($movimientosRecientes)) { ?>
				<p><em>Todavía no hay movimientos registrados.</em></p>
			<?php } else { ?>
				<table class="table table-hover">
					<thead>
						<tr><th></th><th>Fecha</th><th>Usuario</th><th>Líneas</th></tr>
					</thead>
					<tbody>
						<?php foreach ($movimientosRecientes as $mov) {
							$usuario = Usuario::searchByCodigoUser($mov['UsuarioID']);
							$esEntrada = $mov['Tipo'] === 'ENTRADA';
						?>
						<tr>
							<td><span class="glyphicon glyphicon-arrow-<?php echo $esEntrada ? 'down' : 'up'; ?>" style="color: <?php echo $esEntrada ? '#3c763d' : '#a94442'; ?>;"></span></td>
							<td><?php echo h($mov['Fecha']); ?> <?php echo h($mov['Hora']); ?></td>
							<td><?php echo h($usuario->getNombre().' '.$usuario->getApellido()); ?></td>
							<td><?php echo (int)$mov['Lineas']; ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } ?>
		</div>
	</div>

	<div class="row" style="margin-top:20px;">
		<h3>Proyectos</h3>
		<?php foreach ($proyectosActivos as $proyecto) { ?>
			<span class="label label-default" style="font-size:100%; margin-right:6px;"><?php echo h($proyecto->getDescripcion()); ?></span>
		<?php } ?>
	</div>
</div>
