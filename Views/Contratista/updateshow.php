<div class="container">
	<h2>Editar Area</h2>
	<form action="?controller=Contratista&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $contratista->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $contratista->getDescripcion(); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>