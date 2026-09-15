<div class="container">
	<h2>Vista Previa de Generación Masiva</h2>
	<p><em>Todavía no se ha creado nada. Revise los totales y ejemplos abajo; solo se escribirá en la base de datos si confirma al final de esta página.</em></p>

	<div class="form-group">
		<label>Proyecto</label>
		<p><?php echo h($proyecto->getDescripcion()); ?></p>
	</div>

	<div class="form-group">
		<label>Punto de partida</label>
		<p><?php echo $padre ? h($padre->getTipo()) . ': ' . h($padre->getNombre()) : 'Raíz del proyecto (sin nivel superior)'; ?></p>
	</div>

	<?php if ($nombreContenedorNuevo !== '') { ?>
	<div class="form-group">
		<label>Contenedor nuevo</label>
		<p><?php echo h($tipoContenedorIngresado) . ': ' . h($nombreContenedorNuevo); ?></p>
	</div>
	<?php } ?>

	<?php if (!empty($resumen['conflictos'])) { ?>
	<div class="alert alert-danger">
		<strong>¡ Atención !</strong> Los siguientes nombres ya existen en este mismo nivel y provocarían un conflicto: <?php echo h(implode(', ', $resumen['conflictos'])); ?>. Ajuste el patrón de nombres o el punto de partida antes de continuar.
	</div>
	<?php } ?>

	<div class="table-responsive">
	<table class="table table-hover">
		<thead>
			<tr><th>Nivel</th><th>Cantidad generada</th></tr>
		</thead>
		<tbody>
			<?php foreach ($resumen['totalPorNivel'] as $fila) { ?>
			<tr><td><?php echo h($fila['tipo']); ?></td><td><?php echo (int)$fila['cantidad']; ?></td></tr>
			<?php } ?>
			<tr class="active"><td><strong>Total</strong></td><td><strong><?php echo (int)$resumen['totalGeneral']; ?></strong></td></tr>
		</tbody>
	</table>
	</div>

	<h4>Ejemplos por nivel</h4>
	<?php foreach ($resumen['muestras'] as $muestra) { ?>
		<p>
			<strong><?php echo h($muestra['tipo']); ?></strong> (<?php echo (int)$muestra['total']; ?> en total):
			primeros: <?php echo h(implode(', ', $muestra['primeras'])); ?><?php if ($muestra['total'] > 6) { ?>, ...<?php } ?>,
			últimos: <?php echo h(implode(', ', $muestra['ultimas'])); ?>
		</p>
	<?php } ?>

	<form action="?controller=Ubicacion&&action=generarMasivoConfirmar" method="POST">
		<?php echo Csrf::field(); ?>
		<?php foreach ($_POST as $campo => $valor) {
			if ($campo === 'csrf_token' || is_array($valor)) continue;
			echo '<input type="hidden" name="' . h($campo) . '" value="' . h($valor) . '">' . "\n";
		} ?>

		<button type="submit" class="btn btn-primary" <?php echo !empty($resumen['conflictos']) ? 'disabled' : ''; ?>>Confirmar y generar <?php echo (int)$resumen['totalGeneral']; ?> ubicaciones</button>
		<a class="btn btn-default" href="?controller=Ubicacion&&action=generarMasivoShow&&proyecto=<?php echo (int)$proyecto->getId(); ?><?php echo $padre ? '&&padre=' . (int)$padre->getId() : ''; ?>">Volver a editar</a>
	</form>
</div>
