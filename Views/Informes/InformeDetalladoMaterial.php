<div class="container">
	<h2>Detalle de salidas</h2>
	<h5>Proyecto: <?php echo h($proyecto->getDescripcion()); ?> / Ubicación: <?php echo h($ubicacionRuta); ?></h5>
	<h5>Material: <?php echo h($material->getCodigo()); ?> - <?php echo h($material->getDescripcion()); ?></h5>

	<?php if (empty($lineas)) { ?>
		<div class="alert alert-info">No se encontraron líneas para esta combinación.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Documento</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Usuario</th>
					<th>Contratista</th>
					<th>Cantidad</th>
					<th>Destino</th>
					<th>Actividad</th>
					<th>Ver</th>
				</tr>
				<tbody>
					<?php foreach ($lineas as $linea) {
						$usuario = Usuario::searchByCodigoUser($linea['UsuarioID']);
						$contratista = !empty($linea['ContratistaID']) ? Contratista::searchById($linea['ContratistaID']) : null;
						$destino = !empty($linea['DestinoID']) ? Destino::searchById($linea['DestinoID']) : null;
						$rubro = !empty($linea['RubroID']) ? Rubro::searchById($linea['RubroID']) : null;
					?>
					<tr>
						<td>Salida #<?php echo $linea['DocumentoID']; ?></td>
						<td><?php echo h($linea['Fecha']); ?></td>
						<td><?php echo h($linea['Hora']); ?></td>
						<td><?php echo h($usuario->getNombre().' '.$usuario->getApellido()); ?></td>
						<td><?php echo $contratista !== null ? h($contratista->getDescripcion()) : 'N/D'; ?></td>
						<td><?php echo (int) $linea['Cantidad']; ?></td>
						<td><?php echo $destino !== null ? h($destino->getDescripcion()) : 'N/D'; ?></td>
						<td><?php echo $rubro !== null ? h($rubro->getDescripcion()) : 'N/D'; ?></td>
						<td><a class="btn btn-warning btn-xs" href="?controller=InformeSalida&action=detalle&id=<?php echo $linea['DocumentoID']; ?>&usuario=<?php echo $linea['UsuarioID']; ?>"><span class="glyphicon glyphicon-eye-open"> </span> Ver documento</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
	<?php } ?>
</div>
