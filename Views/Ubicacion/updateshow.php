<div class="container">
	<h2>Editar Ubicación</h2>

	<form action="?controller=Ubicacion&&action=update" method="POST">
		<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" value="<?php echo (int)$ubicacion->getId(); ?>">

		<div class="form-group">
			<label for="tipo">Tipo de nivel</label>
			<select name="tipo" id="tipo" class="form-control" onchange="document.getElementById('tipo_otro_wrap').style.display = (this.value === '__otro__') ? 'block' : 'none';" required>
				<option value="">Seleccione un tipo...</option>
				<?php $coincide = false; foreach ($listaTipoUbicacion as $tipoCatalogo) { ?>
				<option value="<?php echo h($tipoCatalogo->getDescripcion()); ?>" <?php if (mb_strtolower($tipoCatalogo->getDescripcion()) === mb_strtolower($ubicacion->getTipo())) { echo 'selected'; $coincide = true; } ?>><?php echo h($tipoCatalogo->getDescripcion()); ?></option>
				<?php } ?>
				<option value="__otro__" <?php if (!$coincide) echo 'selected'; ?>>Otro (escribir)...</option>
			</select>
		</div>

		<div class="form-group" id="tipo_otro_wrap" style="<?php echo $coincide ? 'display:none;' : ''; ?>">
			<label for="tipo_otro">Escriba el tipo</label>
			<input type="text" name="tipo_otro" id="tipo_otro" class="form-control" value="<?php echo $coincide ? '' : h($ubicacion->getTipo()); ?>">
		</div>

		<div class="form-group">
			<label for="nombre">Nombre</label>
			<input type="text" name="nombre" id="nombre" class="form-control" value="<?php echo h($ubicacion->getNombre()); ?>" required>
		</div>

		<div class="form-group">
			<label for="padre_id">Nivel superior</label>
			<?php if ($tieneMovimientos) { ?>
				<p><em>Esta ubicación tiene movimientos históricos asociados (salidas o solicitudes), por lo que su nivel superior no se puede cambiar.</em></p>
				<p><?php echo $ubicacion->getPadreId() ? h(Ubicacion::searchById($ubicacion->getPadreId())->getNombre()) : 'Ninguno (nivel raíz)'; ?></p>
			<?php } else { ?>
				<select name="padre_id" id="padre_id" class="form-control">
					<option value="">Ninguno (nivel raíz del proyecto)</option>
					<?php foreach ($listaPadresElegibles as $candidato) { ?>
					<option value="<?php echo (int)$candidato->getId(); ?>" <?php if ((int)$candidato->getId() === (int)$ubicacion->getPadreId()) echo 'selected'; ?>>
						<?php echo str_repeat('— ', (int)$candidato->getNivel()) . h($candidato->getTipo()) . ': ' . h($candidato->getNombre()); ?>
					</option>
					<?php } ?>
				</select>
				<p><small>Solo se listan ubicaciones del mismo proyecto que no crearían un ciclo en el árbol.</small></p>
			<?php } ?>
		</div>

		<button type="submit" class="btn btn-primary">Actualizar</button>
	</form>
</div>
