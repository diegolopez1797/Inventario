<div class="container">
	<h2>Editar Actividad</h2>
	<form action="?controller=Rubro&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $rubro->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $rubro->getDescripcion(); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>