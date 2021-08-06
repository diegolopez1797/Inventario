<div class="container">
	<h2>Editar Material</h2>
	<form action="?controller=Material&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $material->getId(); ?>" >
		<div class="form-group">
			<label for="text">Codigo</label>
			<input type="number" name="codigo" id="codigo" class="form-control" value="<?php echo $material->getCodigo(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $material->getDescripcion(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Unidad</label>
			<input type="text" name="unidad" id="unidad" class="form-control" value="<?php echo $material->getUnidad(); ?>" required>
		</div>

		<input type="hidden" name="saldo" id="saldo" value="<?php echo $material->getSaldo(); ?>" >

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>