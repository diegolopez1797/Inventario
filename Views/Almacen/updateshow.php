<div class="container">
	<h2>Editar Almacén</h2>
	<form action="?controller=Almacen&&action=update" method="POST">
	<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" id="id" value="<?php echo $almacen->getId(); ?>" >

		<div class="form-group">
			<label for="descripcion">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo h($almacen->getDescripcion()); ?>" required>
		</div>

		<div class="form-group">
			<label for="tipo">Tipo</label>
			<input type="text" name="tipo" id="tipo" class="form-control" value="<?php echo h($almacen->getTipo()); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>
	</form>
</div>
