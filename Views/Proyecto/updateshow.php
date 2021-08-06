<div class="container">
	<h2>Editar Proyecto</h2>
	<form action="?controller=Proyecto&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $proyecto->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $proyecto->getDescripcion(); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>