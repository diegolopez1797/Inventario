<div class="container">
	<h2>Editar Proveedor</h2>
	<form action="?controller=Proveedor&&action=update" method="POST">
	<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" id="id" value="<?php echo $proveedor->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo h($proveedor->getDescripcion()); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>
