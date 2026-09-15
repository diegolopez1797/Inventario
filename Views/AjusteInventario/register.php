<div class="container">
	<h2>Nuevo Ajuste — <?php echo h($tipo); ?></h2>

	<?php if ($material === null) { ?>

		<p>Seleccione el material sobre el cual va a registrar el ajuste.</p>
		<form class="form-inline" action="?controller=AjusteInventario&action=register" method="get">
			<input type="hidden" name="controller" value="AjusteInventario">
			<input type="hidden" name="action" value="register">
			<input type="hidden" name="tipo" value="<?php echo h($tipo); ?>">
			<div class="form-group">
				<select name="material" class="selectpicker form-control" data-show-subtext="true" data-live-search="true" required>
					<option value="">-- Elija un material --</option>
					<?php foreach ($listaMaterialCompleta as $m) { ?>
					<option value="<?php echo $m->getId(); ?>" data-subtext="Saldo: <?php echo (int) $m->getSaldo(); ?> <?php echo h($m->getUnidad()); ?>"><?php echo h($m->getCodigo() . ' - ' . $m->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
			<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-arrow-right"> </span> Continuar</button>
		</form>

	<?php } else { ?>

		<h3><?php echo h($material->getCodigo() . ' - ' . $material->getDescripcion()); ?></h3>
		<p><strong>Saldo actual del sistema:</strong> <?php echo (int) $material->getSaldo(); ?> <?php echo h($material->getUnidad()); ?></p>

		<?php if ($tipo === 'APERTURA') { ?>
			<div class="alert alert-info">
				Una apertura <strong>no modifica el saldo</strong>. Solo documenta el saldo actual como punto de partida trazado en el Kardex.
				Si este material ya tiene entradas, salidas u otros ajustes registrados, verifíquelo en su Kardex antes de continuar — la apertura seguirá documentando el saldo actual sin alterarlo.
			</div>
		<?php } elseif ($tipo === 'CONTEO') { ?>
			<div class="alert alert-info">Ingrese la cantidad física encontrada. El sistema calculará automáticamente la diferencia contra el saldo actual (puede ser positiva o negativa).</div>
		<?php } elseif ($tipo === 'PERDIDA') { ?>
			<div class="alert alert-warning">Ingrese la cantidad perdida. Se restará del saldo actual.</div>
		<?php } elseif ($tipo === 'DANO') { ?>
			<div class="alert alert-danger">Ingrese la cantidad dañada. Se restará del saldo actual.</div>
		<?php } ?>

		<form action="?controller=AjusteInventario&&action=save" method="POST">
			<?php echo Csrf::field(); ?>
			<input type="hidden" name="material" value="<?php echo $material->getId(); ?>">
			<input type="hidden" name="tipo" value="<?php echo h($tipo); ?>">

			<?php if ($tipo === 'APERTURA') { ?>
				<input type="hidden" name="valor" value="0">
				<div class="form-group">
					<label>Costo unitario inicial (opcional)</label>
					<input type="number" step="0.01" min="0" name="costoUnitario" class="form-control" placeholder="Dejar en blanco si no se conoce" value="<?php echo $material->getCostoPromedio() !== null ? h($material->getCostoPromedio()) : ''; ?>">
				</div>
			<?php } elseif ($tipo === 'CONTEO') { ?>
				<div class="form-group">
					<label>Cantidad física encontrada</label>
					<input type="number" min="0" name="valor" class="form-control" required>
				</div>
			<?php } elseif ($tipo === 'PERDIDA') { ?>
				<div class="form-group">
					<label>Cantidad perdida</label>
					<input type="number" min="0" max="<?php echo (int) $material->getSaldo(); ?>" name="valor" class="form-control" required>
				</div>
			<?php } elseif ($tipo === 'DANO') { ?>
				<div class="form-group">
					<label>Cantidad dañada</label>
					<input type="number" min="0" max="<?php echo (int) $material->getSaldo(); ?>" name="valor" class="form-control" required>
				</div>
			<?php } ?>

			<div class="form-group">
				<label>Motivo</label>
				<textarea name="motivo" class="form-control" rows="2" required placeholder="Explique el motivo de este ajuste"></textarea>
			</div>

			<br>
			<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"> </span> Guardar Ajuste</button>
			<a class="btn btn-default" href="?controller=AjusteInventario&action=show">Cancelar</a>
		</form>

	<?php } ?>
</div>
