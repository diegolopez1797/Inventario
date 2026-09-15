<div class="container">
	<h2>Editar Tipo de Ubicación</h2>
	<form action="?controller=TipoUbicacion&&action=update" method="POST">
	<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" id="id" value="<?php echo $tipo->getId(); ?>" >

		<div class="form-group">
			<label for="descripcion">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo h($tipo->getDescripcion()); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>
