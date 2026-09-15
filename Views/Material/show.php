<?php
// Enlaces que preservan los filtros/orden/pagina actuales, cambiando solo lo indicado en $cambios.
$construirEnlace = function($cambios = []) use ($texto, $estado, $orden, $direccion, $pagina) {
	$parametros = array_merge([
		'controller' => 'Material',
		'action' => 'show',
		'texto' => $texto,
		'estado' => $estado,
		'orden' => $orden,
		'direccion' => $direccion,
		'pagina' => $pagina,
	], $cambios);

	$parametros = array_filter($parametros, function($valor){
		return $valor !== '' && $valor !== null;
	});

	return '?' . http_build_query($parametros);
};

$enlaceOrden = function($columna) use ($construirEnlace, $orden, $direccion) {
	$nuevaDireccion = ($orden === $columna && $direccion === 'ASC') ? 'DESC' : 'ASC';
	return $construirEnlace(['orden' => $columna, 'direccion' => $nuevaDireccion, 'pagina' => 1]);
};

$flechaOrden = function($columna) use ($orden, $direccion) {
	if ($orden !== $columna) return '';
	return $direccion === 'ASC' ? ' ▲' : ' ▼';
};
?>
<div class="container">
	<h2>Inventario Actual</h2>

	<form class="form-inline" action="?controller=Material&action=show" method="get">
		<input type="hidden" name="controller" value="Material">
		<input type="hidden" name="action" value="show">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="text" name="texto" class="form-control" placeholder="Buscar por código o descripción" value="<?php echo h($texto); ?>">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="estado" class="form-control">
					<option value="" <?php echo $estado === '' ? 'selected' : ''; ?>>Todos los estados</option>
					<option value="existencia" <?php echo $estado === 'existencia' ? 'selected' : ''; ?>>Con existencia</option>
					<option value="normal" <?php echo $estado === 'normal' ? 'selected' : ''; ?>>Normal</option>
					<option value="critico" <?php echo $estado === 'critico' ? 'selected' : ''; ?>>Crítico</option>
					<option value="agotado" <?php echo $estado === 'agotado' ? 'selected' : ''; ?>>Sin existencia</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Consultar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-default" href="?controller=Material&action=show">Limpiar filtros</a>
			</div>
		</div>
		<?php if (Permiso::usuarioPuede('catalogo.gestionar')) { ?>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=Material&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Material</a>
			</div>
		</div>
		<?php } ?>
	</form>

	<p><strong><?php echo (int) $totalMateriales; ?></strong> material<?php echo $totalMateriales == 1 ? '' : 'es'; ?> encontrado<?php echo $totalMateriales == 1 ? '' : 's'; ?><?php echo ($texto !== '' || $estado !== '') ? ' con los filtros aplicados' : ''; ?>.</p>

	<?php if (empty($listaMaterial)) { ?>
		<div class="alert alert-info">No se encontraron materiales con los filtros aplicados.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th><a href="<?php echo $enlaceOrden('Codigo'); ?>">Código<?php echo $flechaOrden('Codigo'); ?></a></th>
					<th><a href="<?php echo $enlaceOrden('Descripcion'); ?>">Material<?php echo $flechaOrden('Descripcion'); ?></a></th>
					<th>Unidad</th>
					<th><a href="<?php echo $enlaceOrden('Saldo'); ?>">Saldo<?php echo $flechaOrden('Saldo'); ?></a></th>
					<th><a href="<?php echo $enlaceOrden('Min_Almacen'); ?>">Mínimo<?php echo $flechaOrden('Min_Almacen'); ?></a></th>
					<th><a href="<?php echo $enlaceOrden('Estado'); ?>">Estado<?php echo $flechaOrden('Estado'); ?></a></th>
					<?php if (Permiso::usuarioPuede('costo.ver')) { ?>
					<th>Costo Promedio</th>
					<th>Valor</th>
					<?php } ?>
					<th>Kardex</th>
					<?php if (Permiso::usuarioPuede('catalogo.gestionar')) { ?>
					<th>Acciones</th>
					<?php } ?>
				</tr>
				<tbody>
					<?php foreach ($listaMaterial as $material) {
						$infoEstado = Material::estadoInventario($material);
					?>
					<tr>
						<td><?php echo h($material->getCodigo()); ?></td>
						<td><?php echo h($material->getDescripcion()); ?></td>
						<td><?php echo !empty($material->getUnidad()) ? h($material->getUnidad()) : ''; ?></td>
						<td><?php echo (int) $material->getSaldo(); ?></td>
						<td><?php echo (int) $material->getMinAlmacen(); ?></td>
						<td><span class="label label-<?php echo $infoEstado['clase']; ?>"><?php echo h($infoEstado['etiqueta']); ?></span></td>
						<?php if (Permiso::usuarioPuede('costo.ver')) {
							$costo = $material->getCostoPromedio();
						?>
						<td><?php echo $costo !== null ? '$ ' . number_format($costo, 2) : 'Sin costo registrado'; ?></td>
						<td><?php echo $costo !== null ? '$ ' . number_format($costo * $material->getSaldo(), 2) : 'Sin costo'; ?></td>
						<?php } ?>
						<td><a class="btn btn-info btn-xs" href="?controller=Kardex&action=show"><span class="glyphicon glyphicon-list-alt"> </span> Kardex</a></td>
						<?php if (Permiso::usuarioPuede('catalogo.gestionar')) { ?>
						<td>
							<a id="boton-editar" class="btn btn-warning btn-xs" href="?controller=Material&&action=updateshow&&id=<?php echo $material->getId() ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a>
							<a id="boton-eliminar" class="btn btn-danger btn-xs" href="?controller=Material&&action=delete&&id=<?php echo $material->getId() ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a>
						</td>
						<?php } ?>
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
