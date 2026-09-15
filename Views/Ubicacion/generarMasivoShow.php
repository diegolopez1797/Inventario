<div class="container">
	<h2>Generar Estructura Masiva</h2>
	<p><em>Esta herramienta crea muchas ubicaciones de una sola vez (ej. 20 pisos x 10 apartamentos = 220 unidades). Antes de crear nada se mostrará una vista previa con los totales; nada se guarda hasta que la confirme en el siguiente paso.</em></p>

	<div class="form-group">
		<label>Proyecto</label>
		<p><?php echo h($proyecto->getDescripcion()); ?></p>
	</div>

	<div class="form-group">
		<label>Punto de partida</label>
		<p><?php echo $padre ? h($padre->getTipo()) . ': ' . h($padre->getNombre()) : 'Raíz del proyecto (sin nivel superior)'; ?></p>
	</div>

	<form action="?controller=Ubicacion&&action=generarMasivoPreview" method="POST">
		<?php echo Csrf::field(); ?>
		<input type="hidden" name="proyecto_id" value="<?php echo (int)$proyecto->getId(); ?>">
		<input type="hidden" name="padre_id" value="<?php echo $padre ? (int)$padre->getId() : ''; ?>">

		<fieldset>
			<legend>Contenedor nuevo (opcional)</legend>
			<p><small>Si primero va a crear un nuevo nivel contenedor (ej. una Torre) y luego generar niveles en cascada debajo de él, indique su nombre y tipo aquí. Si va a generar directamente bajo el punto de partida de arriba, deje esto en blanco.</small></p>

			<div class="form-group">
				<label for="contenedor_nombre">Nombre del contenedor nuevo</label>
				<input type="text" name="contenedor_nombre" id="contenedor_nombre" class="form-control" placeholder="Ej: Torre A">
			</div>

			<div class="form-group">
				<label for="contenedor_tipo">Tipo del contenedor nuevo</label>
				<select name="contenedor_tipo" id="contenedor_tipo" class="form-control" onchange="document.getElementById('contenedor_tipo_otro_wrap').style.display = (this.value === '__otro__') ? 'block' : 'none';">
					<option value="">Seleccione un tipo...</option>
					<?php foreach ($listaTipoUbicacion as $tipoCatalogo) { ?>
					<option value="<?php echo h($tipoCatalogo->getDescripcion()); ?>"><?php echo h($tipoCatalogo->getDescripcion()); ?></option>
					<?php } ?>
					<option value="__otro__">Otro (escribir)...</option>
				</select>
			</div>
			<div class="form-group" id="contenedor_tipo_otro_wrap" style="display:none;">
				<input type="text" name="contenedor_tipo_otro" class="form-control" placeholder="Tipo de contenedor">
			</div>
		</fieldset>

		<fieldset>
			<legend>Niveles a generar en cascada</legend>
			<p><small>Patrón de nombre: use <code>{N}</code> para el número consecutivo (<code>{N:02}</code> para relleno con ceros, ej. 01, 02...), y <code>{PISO}</code>/<code>{TORRE}</code>/<code>{PADRE}</code> para el código del nivel contenedor inmediatamente superior. Ej: "Piso {N:02}" o "Apto {PISO}{N:02}".</small></p>

			<?php for ($i = 1; $i <= 3; $i++) { ?>
			<div class="well">
				<h4>Nivel <?php echo $i; ?><?php echo $i > 1 ? ' (opcional)' : ''; ?></h4>
				<div class="form-group">
					<label>Tipo</label>
					<select name="nivel<?php echo $i; ?>_tipo" class="form-control" onchange="document.getElementById('nivel<?php echo $i; ?>_tipo_otro_wrap').style.display = (this.value === '__otro__') ? 'block' : 'none';">
						<option value="">Seleccione un tipo...</option>
						<?php foreach ($listaTipoUbicacion as $tipoCatalogo) { ?>
						<option value="<?php echo h($tipoCatalogo->getDescripcion()); ?>"><?php echo h($tipoCatalogo->getDescripcion()); ?></option>
						<?php } ?>
						<option value="__otro__">Otro (escribir)...</option>
					</select>
				</div>
				<div class="form-group" id="nivel<?php echo $i; ?>_tipo_otro_wrap" style="display:none;">
					<input type="text" name="nivel<?php echo $i; ?>_tipo_otro" class="form-control" placeholder="Tipo de nivel">
				</div>
				<div class="form-group">
					<label>Cantidad<?php echo $i > 1 ? ' (por cada elemento del nivel anterior; deje en blanco si no aplica este nivel)' : ''; ?></label>
					<input type="number" min="1" name="nivel<?php echo $i; ?>_cantidad" class="form-control">
				</div>
				<div class="form-group">
					<label>Patrón de nombre</label>
					<input type="text" name="nivel<?php echo $i; ?>_patron" class="form-control" placeholder="Ej: Piso {N:02}">
				</div>
			</div>
			<?php } ?>
		</fieldset>

		<button type="submit" class="btn btn-primary">Ver vista previa</button>
		<button type="button" href="?controller=Ubicacion&&action=show" class="btn btn-default">Cancelar</button>
	</form>
</div>
