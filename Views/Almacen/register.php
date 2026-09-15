<div class="container">
	<h2>Crear Almacén</h2>
	<form action="?controller=Almacen&&action=save" method="POST">
	<?php echo Csrf::field(); ?>

		<div class="form-group">
			<label for="descripcion">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="Ej. Almacén Principal, Bodega Torre 2" required>
		</div>

		<div class="form-group">
			<label for="tipo">Tipo</label>
			<input type="text" name="tipo" id="tipo" class="form-control" placeholder="principal, temporal, frente de obra..." required>
		</div>

		<button type="submit" class="btn btn-primary">Guardar</button>
	</form>
</div>
