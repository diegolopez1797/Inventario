<div class="container">
	<h2>Movimientos de <?php echo h($usuario->getNombre().' '.$usuario->getApellido()); ?></h2>

	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Documento</th>
					<th>Tipo</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Material</th>
					<th>Cantidad</th>
					<th>Ver</th>
				</tr>
				<tbody>
					<?php if (empty($movimientos)) { ?>
					<tr><td colspan="7"><em>Este usuario no tiene movimientos registrados.</em></td></tr>
					<?php } ?>
					<?php foreach ($movimientos as $mov) {
						$etiquetas = [
							'ENTRADA' => ['Entrada', 'success', 'InformeEntrada'],
							'SALIDA' => ['Salida', 'danger', 'InformeSalida'],
							'APERTURA' => ['Ajuste (Apertura)', 'info', null],
							'CONTEO' => ['Ajuste (Conteo)', 'info', null],
							'PERDIDA' => ['Ajuste (Pérdida)', 'danger', null],
							'DANO' => ['Ajuste (Daño)', 'danger', null],
						];
						$etiqueta = $etiquetas[$mov['Tipo']][0];
						$colorEtiqueta = $etiquetas[$mov['Tipo']][1];
						$controllerDocumento = $etiquetas[$mov['Tipo']][2];
					?>
					<tr>
						<td><?php echo h($etiqueta); ?> #<?php echo (int) $mov['DocumentoID']; ?></td>
						<td><span class="label label-<?php echo $colorEtiqueta; ?>"><?php echo h($etiqueta); ?></span></td>
						<td><?php echo h($mov['Fecha']); ?></td>
						<td><?php echo h($mov['Hora']); ?></td>
						<td><?php echo h($mov['MaterialCodigo']); ?> - <?php echo h($mov['MaterialDescripcion']); ?></td>
						<td><?php echo (int) $mov['Cantidad']; ?></td>
						<td>
							<?php if ($controllerDocumento !== null) { ?>
							<a class="btn btn-warning btn-xs" href="?controller=<?php echo $controllerDocumento; ?>&action=detalle&id=<?php echo $mov['DocumentoID']; ?>&usuario=<?php echo $usuario->getId(); ?>"><span class="glyphicon glyphicon-eye-open"> </span> Ver</a>
							<?php } ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>

	<a class="btn btn-primary" href="?controller=InformePorUsuario&action=show">Volver</a>
</div>
