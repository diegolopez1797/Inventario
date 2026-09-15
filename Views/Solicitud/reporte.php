<?php
$construirEnlace = function($cambios = []) use ($estado, $proyectoId, $usuarioSolicitaId, $fechaInicial, $fechaFinal, $pagina) {
	$parametros = array_merge([
		'controller' => 'Solicitud',
		'action' => 'reporte',
		'estado' => $estado,
		'proyectoId' => $proyectoId,
		'usuarioSolicitaId' => $usuarioSolicitaId,
		'fechaInicial' => $fechaInicial,
		'fechaFinal' => $fechaFinal,
		'pagina' => $pagina,
	], $cambios);

	$parametros = array_filter($parametros, function($valor){
		return $valor !== '' && $valor !== null;
	});

	return '?' . http_build_query($parametros);
};

$etiquetasEstado = [
	'PENDIENTE' => 'default',
	'APROBADA' => 'info',
	'RECHAZADA' => 'danger',
	'ENTREGADA' => 'success',
];
?>
<div class="container">
	<h2>Informe de Solicitudes</h2>

	<form class="form-inline" action="?controller=Solicitud&action=reporte" method="get">
		<input type="hidden" name="controller" value="Solicitud">
		<input type="hidden" name="action" value="reporte">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="estado" class="form-control">
					<option value="">Todo estado</option>
					<option value="PENDIENTE" <?php echo $estado === 'PENDIENTE' ? 'selected' : ''; ?>>Pendiente</option>
					<option value="APROBADA" <?php echo $estado === 'APROBADA' ? 'selected' : ''; ?>>Aprobada</option>
					<option value="RECHAZADA" <?php echo $estado === 'RECHAZADA' ? 'selected' : ''; ?>>Rechazada</option>
					<option value="ENTREGADA" <?php echo $estado === 'ENTREGADA' ? 'selected' : ''; ?>>Entregada</option>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="proyectoId" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="">Todos los proyectos</option>
					<?php foreach ($listaProyecto as $proyectoOpcion) { ?>
					<option value="<?php echo $proyectoOpcion->getId(); ?>" <?php echo (string) $proyectoId === (string) $proyectoOpcion->getId() ? 'selected' : ''; ?>><?php echo h($proyectoOpcion->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="usuarioSolicitaId" class="form-control">
					<option value="">Todos los solicitantes</option>
					<?php foreach ($listaUsuario as $usuarioOpcion) { ?>
					<option value="<?php echo $usuarioOpcion->getId(); ?>" <?php echo (string) $usuarioSolicitaId === (string) $usuarioOpcion->getId() ? 'selected' : ''; ?>><?php echo h($usuarioOpcion->getNombre() . ' ' . $usuarioOpcion->getApellido()); ?></option>
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
				<a class="btn btn-default" href="?controller=Solicitud&action=reporte">Limpiar filtros</a>
			</div>
		</div>
	</form>

	<p>
		<strong><?php echo (int) $totalSolicitudes; ?></strong> solicitud<?php echo $totalSolicitudes == 1 ? '' : 'es'; ?>
		<?php if (!empty($conteoPorEstado)) { ?>
			(<?php
			$partes = [];
			foreach ($conteoPorEstado as $est => $cant) {
				$partes[] = h($est) . ': ' . (int) $cant;
			}
			echo implode(' | ', $partes);
			?>)
		<?php } ?>
	</p>

	<?php if (empty($listaSolicitud)) { ?>
		<div class="alert alert-info">No se encontraron solicitudes con los filtros aplicados.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Fecha</th>
					<th>Solicitante</th>
					<th>Proyecto</th>
					<th>Estado</th>
					<th>Aprobador</th>
					<th>Fecha aprobación</th>
					<th>Salida asociada</th>
				</tr>
				<tbody>
					<?php foreach ($listaSolicitud as $solicitud) {
						$solicitante = Usuario::searchByCodigoUser($solicitud->getUsuarioSolicitaId());
						$proyecto = Proyecto::searchById($solicitud->getProyectoId());
						$aprobador = $solicitud->getUsuarioApruebaId() !== null ? Usuario::searchByCodigoUser($solicitud->getUsuarioApruebaId()) : null;
						$colorEstado = isset($etiquetasEstado[$solicitud->getEstado()]) ? $etiquetasEstado[$solicitud->getEstado()] : 'default';
					?>
					<tr>
						<td><?php echo h($solicitud->getFecha()); ?> <?php echo h($solicitud->getHora()); ?></td>
						<td><?php echo h($solicitante->getNombre() . ' ' . $solicitante->getApellido()); ?></td>
						<td><?php echo h($proyecto->getDescripcion()); ?></td>
						<td><span class="label label-<?php echo $colorEstado; ?>"><?php echo h($solicitud->getEstado()); ?></span></td>
						<td><?php echo $aprobador !== null ? h($aprobador->getNombre() . ' ' . $aprobador->getApellido()) : 'N/D'; ?></td>
						<td><?php echo $solicitud->getFechaAprobacion() !== null ? h($solicitud->getFechaAprobacion()) : 'N/D'; ?></td>
						<td>
							<?php if ($solicitud->getRegistroSalidasId() !== null) {
								$registroSalidaAsociado = RegistroSalidas::searchSalida($solicitud->getRegistroSalidasId());
							?>
							<a class="btn btn-warning btn-xs" href="?controller=InformeSalida&action=detalle&id=<?php echo $solicitud->getRegistroSalidasId(); ?>&usuario=<?php echo $registroSalidaAsociado->getUsuario(); ?>"><span class="glyphicon glyphicon-eye-open"> </span> Ver salida</a>
							<?php } else { echo 'N/D'; } ?>
						</td>
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
