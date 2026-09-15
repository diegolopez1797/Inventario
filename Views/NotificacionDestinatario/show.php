<div class="container">
	<h2>Destinatarios de Alertas de Inventario</h2>
	<a class="btn btn-success" href="?controller=NotificacionDestinatario&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Agregar Destinatario</a>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Correo</th>
					<th>Estado</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaDestinatario as $destinatario) { ?>
					<tr>
						<td><?php echo h($destinatario->getNombre()); ?></td>
						<td><?php echo h($destinatario->getCorreo()); ?></td>
						<td><span class="label label-<?php echo $destinatario->getActivo() ? 'success' : 'default'; ?>"><?php echo $destinatario->getActivo() ? 'Activo' : 'Inactivo'; ?></span></td>
						<td>
							<a class="btn btn-warning btn-xs" href="?controller=NotificacionDestinatario&action=updateshow&id=<?php echo $destinatario->getId(); ?>">Editar</a>
							<?php if ($destinatario->getActivo()) { ?>
							<a class="btn btn-default btn-xs" href="?controller=NotificacionDestinatario&action=desactivar&id=<?php echo $destinatario->getId(); ?>&csrf_token=<?php echo Csrf::token(); ?>">Desactivar</a>
							<?php } else { ?>
							<a class="btn btn-success btn-xs" href="?controller=NotificacionDestinatario&action=activar&id=<?php echo $destinatario->getId(); ?>&csrf_token=<?php echo Csrf::token(); ?>">Activar</a>
							<?php } ?>
						</td>
					</tr>
					<?php } ?>
					<?php if (empty($listaDestinatario)) { ?>
					<tr><td colspan="4"><em>No hay destinatarios registrados todavía.</em></td></tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
