<div class="container">
	<h2>Editar Destinatario</h2>
	<form action="?controller=NotificacionDestinatario&action=update" method="POST">
		<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" value="<?php echo $destinatario->getId(); ?>">
		<div class="form-group">
			<label>Nombre</label>
			<input type="text" name="nombre" class="form-control" maxlength="60" value="<?php echo h($destinatario->getNombre()); ?>" required>
		</div>
		<div class="form-group">
			<label>Correo</label>
			<input type="email" name="correo" class="form-control" maxlength="150" value="<?php echo h($destinatario->getCorreo()); ?>" required>
		</div>
		<button type="submit" class="btn btn-primary">Guardar</button>
		<a href="?controller=NotificacionDestinatario&action=show" class="btn btn-default">Cancelar</a>
	</form>
</div>
