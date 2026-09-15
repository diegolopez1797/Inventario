<div class="container">
	<h2>Agregar Ubicación</h2>

	<form action="?controller=Ubicacion&&action=save" method="POST">
		<?php echo Csrf::field(); ?>
		<input type="hidden" name="proyecto_id" value="<?php echo (int)$_GET['proyecto']; ?>">
		<input type="hidden" name="padre_id" value="<?php echo isset($_GET['padre']) ? (int)$_GET['padre'] : ''; ?>">

		<div class="form-group">
			<label>Proyecto</label>
			<p><?php echo h(Proyecto::searchById($_GET['proyecto'])->getDescripcion()); ?></p>
		</div>

		<div class="form-group">
			<label>Nivel superior</label>
			<?php if (isset($_GET['padre'])) { $padre = Ubicacion::searchById($_GET['padre']); ?>
			<p><?php echo h($padre->getTipo()) . ': ' . h($padre->getNombre()); ?></p>
			<?php } else { ?>
			<p>Ninguno (nivel raíz del proyecto)</p>
			<?php } ?>
		</div>

		<div class="form-group">
			<label for="tipo">Tipo de nivel</label>
			<select name="tipo" id="tipo" class="form-control" onchange="document.getElementById('tipo_otro_wrap').style.display = (this.value === '__otro__') ? 'block' : 'none';" required>
				<option value="">Seleccione un tipo...</option>
				<?php foreach ($listaTipoUbicacion as $tipoCatalogo) { ?>
				<option value="<?php echo h($tipoCatalogo->getDescripcion()); ?>"><?php echo h($tipoCatalogo->getDescripcion()); ?></option>
				<?php } ?>
				<option value="__otro__">Otro (escribir)...</option>
			</select>
		</div>

		<div class="form-group" id="tipo_otro_wrap" style="display:none;">
			<label for="tipo_otro">Escriba el tipo</label>
			<input type="text" name="tipo_otro" id="tipo_otro" class="form-control" placeholder="etapa, manzana, casa, torre, piso, unidad...">
		</div>

		<div class="form-group">
			<label for="nombre">Nombre</label>
			<input type="text" name="nombre" id="nombre" class="form-control" required>
		</div>

		<button type="submit" class="btn btn-primary">Guardar</button>
	</form>
</div>
