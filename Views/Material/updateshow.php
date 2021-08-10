<div class="container">
	<h2>Editar Material</h2>
	<form action="?controller=Material&&action=update" method="POST">
		<input type="hidden" name="id" id="id" value="<?php echo $material->getId(); ?>" >
		<input type="hidden" name="codigo" id="codigo" value="<?php echo $material->getCodigo(); ?>" >

		<div class="form-group">
			<label for="text">Descripcion</label>
			<input type="text" name="descripcion" id="descripcion" class="form-control" value="<?php echo $material->getDescripcion(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Unidad</label>
			<input type="text" name="unidad" id="unidad" class="form-control" value="<?php echo $material->getUnidad(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Min Almacen</label>
			<input type="text" name="min" id="min" class="form-control" value="<?php echo $material->getMinAlmacen(); ?>" required>
		</div>

		<div class="form-group">
			<label for="text">Max Casa</label>
			<input type="text" name="max" id="max" class="form-control" value="<?php echo $material->getMaxCasa(); ?>" required>
		</div>

		<input type="hidden" name="saldo" id="saldo" value="<?php echo $material->getSaldo(); ?>" >

		<button type="submit" class="btn btn-primary">Actualizar</button>

	</form>
</div>