<?php
$construirEnlace = function($cambios = []) use ($tipo, $materialId, $usuarioId, $fechaInicial, $fechaFinal, $pagina) {
	$parametros = array_merge([
		'controller' => 'AjusteInventario',
		'action' => 'show',
		'tipo' => $tipo,
		'materialId' => $materialId,
		'usuarioId' => $usuarioId,
		'fechaInicial' => $fechaInicial,
		'fechaFinal' => $fechaFinal,
		'pagina' => $pagina,
	], $cambios);

	$parametros = array_filter($parametros, function($valor){
		return $valor !== '' && $valor !== null;
	});

	return '?' . http_build_query($parametros);
};
?>
<div class="container">
	<h2>Ajustes de Inventario</h2>

	<div class="container">
		<a class="btn btn-success" href="?controller=AjusteInventario&action=register&tipo=APERTURA"><span class="glyphicon glyphicon-plus-sign"> </span> Apertura</a>
		<a class="btn btn-primary" href="?controller=AjusteInventario&action=register&tipo=CONTEO"><span class="glyphicon glyphicon-list-alt"> </span> Conteo</a>
		<a class="btn btn-warning" href="?controller=AjusteInventario&action=register&tipo=PERDIDA"><span class="glyphicon glyphicon-remove-circle"> </span> Pérdida</a>
		<a class="btn btn-danger" href="?controller=AjusteInventario&action=register&tipo=DANO"><span class="glyphicon glyphicon-fire"> </span> Daño</a>
	</div>

	<form class="form-inline" action="?controller=AjusteInventario&action=show" method="get">
		<input type="hidden" name="controller" value="AjusteInventario">
		<input type="hidden" name="action" value="show">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="tipo" class="form-control">
					<option value="">Todo tipo</option>
					<option value="APERTURA" <?php echo $tipo === 'APERTURA' ? 'selected' : ''; ?>>Apertura</option>
					<option value="CONTEO" <?php echo $tipo === 'CONTEO' ? 'selected' : ''; ?>>Conteo</option>
					<option value="PERDIDA" <?php echo $tipo === 'PERDIDA' ? 'selected' : ''; ?>>Pérdida</option>
					<option value="DANO" <?php echo $tipo === 'DANO' ? 'selected' : ''; ?>>Daño</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="materialId" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="">Todos los materiales</option>
					<?php foreach ($listaMaterialCompleta as $material) { ?>
					<option value="<?php echo $material->getId(); ?>" data-subtext="<?php echo h($material->getDescripcion()); ?>" <?php echo (string) $materialId === (string) $material->getId() ? 'selected' : ''; ?>><?php echo $material->getCodigo()." - "; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="usuarioId" class="form-control">
					<option value="">Todos los usuarios</option>
					<?php foreach ($listaUsuario as $usuarioOpcion) { ?>
					<option value="<?php echo $usuarioOpcion->getId(); ?>" <?php echo (string) $usuarioId === (string) $usuarioOpcion->getId() ? 'selected' : ''; ?>><?php echo h($usuarioOpcion->getNombre() . ' ' . $usuarioOpcion->getApellido()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaInicial" class="form-control" value="<?php echo h($fechaInicial); ?>" placeholder="Desde">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaFinal" class="form-control" value="<?php echo h($fechaFinal); ?>" placeholder="Hasta">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Filtrar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-default" href="?controller=AjusteInventario&action=show">Limpiar filtros</a>
			</div>
		</div>
	</form>

	<p><strong><?php echo (int) $totalAjustes; ?></strong> ajuste<?php echo $totalAjustes == 1 ? '' : 's'; ?> <?php echo ($tipo !== '' || $materialId !== '' || $usuarioId !== '' || $fechaInicial !== '' || $fechaFinal !== '') ? 'con los filtros aplicados' : 'en total'; ?>.</p>

	<?php if (empty($listaAjuste)) { ?>
		<div class="alert alert-info">No se encontraron ajustes con los filtros aplicados.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Fecha</th>
					<th>Material</th>
					<th>Tipo</th>
					<th>Cant. Anterior</th>
					<th>Cant. Nueva</th>
					<th>Ajuste</th>
					<th>Costo Unitario</th>
					<th>Usuario</th>
					<th>Motivo</th>
				</tr>
				<tbody>
					<?php foreach ($listaAjuste as $ajuste) {
						$material = Material::searchById($ajuste->getMaterialId());
						$usuario = Usuario::searchByCodigoUser($ajuste->getUsuarioId());
						$esPositivo = $ajuste->getCantidadAjuste() > 0;
					?>
					<tr>
						<td><?php echo h($ajuste->getFecha()); ?> <?php echo h($ajuste->getHora()); ?></td>
						<td><?php echo h($material->getDescripcion()); ?></td>
						<td><span class="label label-<?php echo $ajuste->getTipo() === 'APERTURA' ? 'info' : ($esPositivo ? 'success' : 'danger'); ?>"><?php echo h($ajuste->getTipo()); ?></span></td>
						<td><?php echo (int) $ajuste->getCantidadAnterior(); ?></td>
						<td><?php echo (int) $ajuste->getCantidadNueva(); ?></td>
						<td><?php echo $ajuste->getCantidadAjuste() > 0 ? '+' : ''; ?><?php echo (int) $ajuste->getCantidadAjuste(); ?></td>
						<td><?php echo $ajuste->getCostoUnitario() !== null ? h($ajuste->getCostoUnitario()) : '-'; ?></td>
						<td><?php echo h($usuario->getNombre() . ' ' . $usuario->getApellido()); ?></td>
						<td><?php echo h($ajuste->getMotivo()); ?></td>
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
