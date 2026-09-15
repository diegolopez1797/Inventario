<div class="container">
	<h2>Agregar Destinatario</h2>
	<form action="?controller=NotificacionDestinatario&action=save" method="POST">
		<?php echo Csrf::field(); ?>
		<div class="form-group">
			<label>Nombre</label>
			<input type="text" name="nombre" class="form-control" maxlength="60" required>
		</div>
		<div class="form-group">
			<label>Correo</label>
			<input type="email" name="correo" class="form-control" maxlength="150" required>
		</div>
		<button type="submit" class="btn btn-primary">Guardar</button>
		<a href="?controller=NotificacionDestinatario&action=show" class="btn btn-default">Cancelar</a>
	</form>
</div>
