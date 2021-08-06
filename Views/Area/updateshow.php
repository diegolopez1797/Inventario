<div class="container">
	<h2>Editar Area</h2>
	<form action="?controller=Area&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $area->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $area->getDescripcion(); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>