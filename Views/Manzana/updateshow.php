<div class="container">
	<h2>Editar Casa</h2>
	<form action="?controller=Manzana&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $manzana->getId(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $manzana->getDescripcion(); ?>" required>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>