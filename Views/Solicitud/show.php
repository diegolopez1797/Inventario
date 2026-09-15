<div class="container">
	<h2>Solicitudes</h2>

	<?php if (Permiso::usuarioPuede('solicitud.crear')) { ?>
	<a class="btn btn-success" href="?controller=Solicitud&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Nueva Solicitud</a>
	<?php } ?>

	<?php if (!empty($pendientesParaAprobar)) { ?>
	<h3>Pendientes de mi aprobacion</h3>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Fecha</th>
					<th>Solicitante</th>
					<th>Proyecto</th>
					<th>Materiales</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($pendientesParaAprobar as $solicitud) {
						$solicitante = Usuario::searchByCodigoUser($solicitud->getUsuarioSolicitaId());
						$proyecto = Proyecto::searchById($solicitud->getProyectoId());
						$detalle = Solicitud::detalle($solicitud->getId());
					?>
					<tr>
						<td><?php echo $solicitud->getId(); ?></td>
						<td><?php echo h($solicitud->getFecha()); ?></td>
						<td><?php echo h($solicitante->getNombre() . ' ' . $solicitante->getApellido()); ?></td>
						<td><?php echo h($proyecto->getDescripcion()); ?></td>
						<td>
							<?php foreach ($detalle as $linea) { ?>
							<?php echo h($linea['material']->getDescripcion()) . ' (' . h($linea['cantidad']) . ' ' . h($linea['material']->getUnidad()) . ')'; ?><br>
							<?php } ?>
						</td>
						<td>
							<a class="btn btn-success btn-xs" href="?controller=Solicitud&&action=aprobar&&id=<?php echo $solicitud->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-ok"> </span> Aprobar</a>
							<a class="btn btn-danger btn-xs" href="?controller=Solicitud&&action=rechazar&&id=<?php echo $solicitud->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-remove"> </span> Rechazar</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
	<?php } ?>

	<?php if (!empty($aprobadasParaEntregar)) { ?>
	<h3>Aprobadas, pendientes de entrega</h3>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Fecha</th>
					<th>Solicitante</th>
					<th>Proyecto</th>
					<th>Materiales</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($aprobadasParaEntregar as $solicitud) {
						$solicitante = Usuario::searchByCodigoUser($solicitud->getUsuarioSolicitaId());
						$proyecto = Proyecto::searchById($solicitud->getProyectoId());
						$detalle = Solicitud::detalle($solicitud->getId());
					?>
					<tr>
						<td><?php echo $solicitud->getId(); ?></td>
						<td><?php echo h($solicitud->getFecha()); ?></td>
						<td><?php echo h($solicitante->getNombre() . ' ' . $solicitante->getApellido()); ?></td>
						<td><?php echo h($proyecto->getDescripcion()); ?></td>
						<td>
							<?php foreach ($detalle as $linea) { ?>
							<?php echo h($linea['material']->getDescripcion()) . ' (' . h($linea['cantidad']) . ' ' . h($linea['material']->getUnidad()) . ')'; ?><br>
							<?php } ?>
						</td>
						<td>
							<a class="btn btn-primary btn-xs" href="?controller=Solicitud&&action=mostrarEntrega&&id=<?php echo $solicitud->getId(); ?>"><span class="glyphicon glyphicon-send"> </span> Entregar</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
	<?php } ?>

	<h3>Mis solicitudes</h3>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Fecha</th>
					<th>Proyecto</th>
					<th>Estado</th>
					<th>Materiales</th>
				</tr>
				<tbody>
					<?php foreach ($misSolicitudes as $solicitud) {
						$proyecto = Proyecto::searchById($solicitud->getProyectoId());
						$detalle = Solicitud::detalle($solicitud->getId());
					?>
					<tr>
						<td><?php echo $solicitud->getId(); ?></td>
						<td><?php echo h($solicitud->getFecha()); ?></td>
						<td><?php echo h($proyecto->getDescripcion()); ?></td>
						<td><?php echo h($solicitud->getEstado()); ?></td>
						<td>
							<?php foreach ($detalle as $linea) { ?>
							<?php echo h($linea['material']->getDescripcion()) . ' (' . h($linea['cantidad']) . ' ' . h($linea['material']->getUnidad()) . ')'; ?><br>
							<?php } ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
