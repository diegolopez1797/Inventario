<?php
$construirEnlace = function($cambios = []) use ($filtros, $orden, $direccion, $pagina) {
	$parametros = array_merge([
		'controller' => 'Movimientos',
		'action' => 'show',
		'tipo' => $filtros['tipo'],
		'fechaInicial' => $filtros['fechaInicial'],
		'fechaFinal' => $filtros['fechaFinal'],
		'materialId' => $filtros['materialId'],
		'documento' => $filtros['documento'],
		'contratistaId' => $filtros['contratistaId'],
		'proveedorId' => $filtros['proveedorId'],
		'proyectoId' => $filtros['proyectoId'],
		'usuarioId' => $filtros['usuarioId'],
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

$listaPagina = array_slice($_SESSION['movimientosGeneral'], ($pagina - 1) * MovimientosController::TAMANO_PAGINA, MovimientosController::TAMANO_PAGINA);
?>
<div class="container">
	<h2>Movimientos</h2>

	<form class="form-inline" action="?controller=Movimientos&action=show" method="get">
		<input type="hidden" name="controller" value="Movimientos">
		<input type="hidden" name="action" value="show">

		<div class="form-group row">
			<div class="col-xs-4">
				<select name="tipo" class="form-control">
					<option value="" <?php echo $filtros['tipo'] === '' ? 'selected' : ''; ?>>Entradas y salidas</option>
					<option value="entrada" <?php echo $filtros['tipo'] === 'entrada' ? 'selected' : ''; ?>>Solo entradas</option>
					<option value="salida" <?php echo $filtros['tipo'] === 'salida' ? 'selected' : ''; ?>>Solo salidas</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaInicial" class="form-control" value="<?php echo h($filtros['fechaInicial']); ?>" placeholder="Desde">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaFinal" class="form-control" value="<?php echo h($filtros['fechaFinal']); ?>" placeholder="Hasta">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" name="documento" class="form-control" value="<?php echo h($filtros['documento']); ?>" placeholder="No. de documento">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="materialId" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="">Todos los materiales</option>
					<?php foreach ($listaMaterialCompleta as $material) { ?>
					<option value="<?php echo $material->getId(); ?>" data-subtext="<?php echo h($material->getDescripcion()); ?>" <?php echo (string) $filtros['materialId'] === (string) $material->getId() ? 'selected' : ''; ?>><?php echo $material->getCodigo()." - "; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="contratistaId" class="form-control">
					<option value="">Todos los contratistas</option>
					<?php foreach ($listaContratista as $contratista) { ?>
					<option value="<?php echo $contratista->getId(); ?>" <?php echo (string) $filtros['contratistaId'] === (string) $contratista->getId() ? 'selected' : ''; ?>><?php echo h($contratista->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="proveedorId" class="form-control">
					<option value="">Todos los proveedores</option>
					<?php foreach ($listaProveedor as $proveedor) { ?>
					<option value="<?php echo $proveedor->getId(); ?>" <?php echo (string) $filtros['proveedorId'] === (string) $proveedor->getId() ? 'selected' : ''; ?>><?php echo h($proveedor->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="proyectoId" class="form-control">
					<option value="">Todos los proyectos</option>
					<?php foreach ($listaProyecto as $proyecto) { ?>
					<option value="<?php echo $proyecto->getId(); ?>" <?php echo (string) $filtros['proyectoId'] === (string) $proyecto->getId() ? 'selected' : ''; ?>><?php echo h($proyecto->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="usuarioId" class="form-control">
					<option value="">Todos los usuarios</option>
					<?php foreach ($listaUsuario as $usuario) { ?>
					<option value="<?php echo $usuario->getId(); ?>" <?php echo (string) $filtros['usuarioId'] === (string) $usuario->getId() ? 'selected' : ''; ?>><?php echo h($usuario->getNombre() . ' ' . $usuario->getApellido()); ?></option>
					<?php } ?>
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
				<a class="btn btn-default" href="?controller=Movimientos&action=show">Limpiar filtros</a>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=Movimientos&action=generarPDF"><span class="glyphicon glyphicon-file"> </span> Generar PDF</a>
			</div>
		</div>
	</form>

	<p><strong><?php echo count($_SESSION['movimientosGeneral']); ?></strong> movimiento<?php echo count($_SESSION['movimientosGeneral']) == 1 ? '' : 's'; ?> encontrado<?php echo count($_SESSION['movimientosGeneral']) == 1 ? '' : 's'; ?>.</p>

	<?php if (empty($listaPagina)) { ?>
		<div class="alert alert-info">No se encontraron movimientos con los filtros aplicados.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th><a href="<?php echo $enlaceOrden('documento'); ?>">Documento<?php echo $flechaOrden('documento'); ?></a></th>
					<th>Tipo</th>
					<th><a href="<?php echo $enlaceOrden('fecha'); ?>">Fecha / Hora<?php echo $flechaOrden('fecha'); ?></a></th>
					<th>Material</th>
					<th>Cantidad</th>
					<th>Usuario</th>
					<th>Contratista / Proveedor</th>
					<th>Proyecto / Ubicación</th>
					<th>Destino</th>
					<th>Actividad</th>
					<th>Ver</th>
				</tr>
				<tbody>
					<?php foreach ($listaPagina as $mov) {
						$esEntrada = $mov['Tipo'] === 'ENTRADA';
						$usuarioMov = Usuario::searchByCodigoUser($mov['UsuarioID']);
						$destino = !empty($mov['DestinoID']) ? Destino::searchById($mov['DestinoID']) : null;
						$rubro = !empty($mov['RubroID']) ? Rubro::searchById($mov['RubroID']) : null;
						$contratistaProveedor = null;
						if ($esEntrada && !empty($mov['ProveedorID'])) {
							$contratistaProveedor = Proveedor::searchById($mov['ProveedorID'])->getDescripcion();
						} elseif (!$esEntrada && !empty($mov['ContratistaID'])) {
							$contratistaProveedor = Contratista::searchById($mov['ContratistaID'])->getDescripcion();
						}
						$proyectoUbicacion = null;
						if (!$esEntrada) {
							$partes = [];
							if (!empty($mov['ProyectoID'])) $partes[] = Proyecto::searchById($mov['ProyectoID'])->getDescripcion();
							if (!empty($mov['UbicacionID'])) $partes[] = Ubicacion::ruta($mov['UbicacionID']);
							$proyectoUbicacion = !empty($partes) ? implode(' / ', $partes) : null;
						}
						$controllerDetalle = $esEntrada ? 'InformeEntrada' : 'InformeSalida';
					?>
					<tr>
						<td><?php echo $esEntrada ? 'Entrada' : 'Salida'; ?> #<?php echo $mov['DocumentoID']; ?></td>
						<td><span class="label label-<?php echo $esEntrada ? 'success' : 'danger'; ?>"><?php echo $esEntrada ? 'Entrada' : 'Salida'; ?></span></td>
						<td><?php echo h($mov['Fecha']); ?> <?php echo h($mov['Hora']); ?></td>
						<td><?php echo h($mov['MaterialCodigo']); ?> - <?php echo h($mov['MaterialDescripcion']); ?></td>
						<td><?php echo (int) $mov['Cantidad']; ?></td>
						<td><?php echo h($usuarioMov->getNombre() . ' ' . $usuarioMov->getApellido()); ?></td>
						<td><?php echo $contratistaProveedor !== null ? h($contratistaProveedor) : 'N/D'; ?></td>
						<td><?php echo $proyectoUbicacion !== null ? h($proyectoUbicacion) : 'N/D'; ?></td>
						<td><?php echo $destino !== null ? h($destino->getDescripcion()) : 'N/D'; ?></td>
						<td><?php echo $rubro !== null ? h($rubro->getDescripcion()) : 'N/D'; ?></td>
						<td><a class="btn btn-warning btn-xs" href="?controller=<?php echo $controllerDetalle; ?>&action=detalle&id=<?php echo $mov['DocumentoID']; ?>&usuario=<?php echo $mov['UsuarioID']; ?>"><span class="glyphicon glyphicon-eye-open"> </span> Ver</a></td>
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
