<?php
$construirEnlace = function($cambios = []) use ($umbralDias, $pagina) {
	$parametros = array_merge([
		'controller' => 'Material',
		'action' => 'sinMovimiento',
		'umbral' => $umbralDias,
		'pagina' => $pagina,
	], $cambios);

	$parametros = array_filter($parametros, function($valor){
		return $valor !== '' && $valor !== null;
	});

	return '?' . http_build_query($parametros);
};
?>
<div class="container">
	<h2>Materiales sin Movimiento</h2>

	<form class="form-inline" action="?controller=Material&action=sinMovimiento" method="get">
		<input type="hidden" name="controller" value="Material">
		<input type="hidden" name="action" value="sinMovimiento">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="umbral" class="form-control">
					<option value="30" <?php echo $umbralDias === 30 ? 'selected' : ''; ?>>Más de 30 días sin movimiento</option>
					<option value="60" <?php echo $umbralDias === 60 ? 'selected' : ''; ?>>Más de 60 días sin movimiento</option>
					<option value="90" <?php echo $umbralDias === 90 ? 'selected' : ''; ?>>Más de 90 días sin movimiento</option>
					<option value="180" <?php echo $umbralDias === 180 ? 'selected' : ''; ?>>Más de 180 días sin movimiento</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Consultar</button>
			</div>
		</div>
	</form>

	<p><strong><?php echo (int) $totalMateriales; ?></strong> material<?php echo $totalMateriales == 1 ? '' : 'es'; ?> con más de <?php echo (int) $umbralDias; ?> días sin movimiento (o sin ninguno registrado).</p>

	<?php if (empty($listaMaterial)) { ?>
		<div class="alert alert-info">No hay materiales sin movimiento con este umbral.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Código</th>
					<th>Material</th>
					<th>Unidad</th>
					<th>Saldo actual</th>
					<th>Último movimiento</th>
					<th>Días sin movimiento</th>
				</tr>
				<tbody>
					<?php foreach ($listaMaterial as $fila) {
						$material = $fila['material'];
					?>
					<tr>
						<td><?php echo h($material->getCodigo()); ?></td>
						<td><?php echo h($material->getDescripcion()); ?></td>
						<td><?php echo !empty($material->getUnidad()) ? h($material->getUnidad()) : ''; ?></td>
						<td><?php echo (int) $material->getSaldo(); ?></td>
						<td><?php echo $fila['ultimoMovimiento'] !== null ? h($fila['ultimoMovimiento']) : 'Sin movimientos registrados'; ?></td>
						<td><?php echo $fila['dias'] !== null ? (int) $fila['dias'] : 'N/A'; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
	<?php } ?>
	<?php if ($totalPaginas > 1) { ?>
	<nav>
		<ul class="pagination">
			<li<?php echo $pagina <= 1 ? ' class="disabled"' : ''; ?>>
				<?php if ($pagina > 1) { ?>
				<a href="<?php echo $construirEnlace(['pagina' => $pagina - 1]); ?>">&laquo; Anterior</a>
				<?php } else { ?>
				<span>&laquo; Anterior</span>
				<?php } ?>
			</li>
			<li class="disabled"><span>Página <?php echo (int) $pagina; ?> de <?php echo (int) $totalPaginas; ?></span></li>
			<li<?php echo $pagina >= $totalPaginas ? ' class="disabled"' : ''; ?>>
				<?php if ($pagina < $totalPaginas) { ?>
				<a href="<?php echo $construirEnlace(['pagina' => $pagina + 1]); ?>">Siguiente &raquo;</a>
				<?php } else { ?>
				<span>Siguiente &raquo;</span>
				<?php } ?>
			</li>
		</ul>
	</nav>
	<?php } ?>
</div>
