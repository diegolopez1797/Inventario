<?php
$construirEnlace = function($cambios = []) use ($usuarioId, $entidad, $accion, $fechaInicial, $fechaFinal, $pagina) {
	$parametros = array_merge([
		'controller' => 'Auditoria',
		'action' => 'show',
		'usuarioId' => $usuarioId,
		'entidad' => $entidad,
		'accion' => $accion,
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
	<h2>Auditoría</h2>

	<form class="form-inline" action="?controller=Auditoria&action=show" method="get">
		<input type="hidden" name="controller" value="Auditoria">
		<input type="hidden" name="action" value="show">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="usuarioId" class="form-control">
					<option value="">Todos los usuarios</option>
					<?php foreach ($listaUsuario as $usuario) { ?>
					<option value="<?php echo $usuario->getId(); ?>" <?php echo (string) $usuarioId === (string) $usuario->getId() ? 'selected' : ''; ?>><?php echo h($usuario->getNombre() . ' ' . $usuario->getApellido()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="entidad" class="form-control">
					<option value="">Toda entidad</option>
					<?php foreach ($listaEntidad as $valorEntidad) { ?>
					<option value="<?php echo h($valorEntidad); ?>" <?php echo $entidad === $valorEntidad ? 'selected' : ''; ?>><?php echo h($valorEntidad); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="accion" class="form-control">
					<option value="">Toda acción</option>
					<?php foreach ($listaAccion as $valorAccion) { ?>
					<option value="<?php echo h($valorAccion); ?>" <?php echo $accion === $valorAccion ? 'selected' : ''; ?>><?php echo h($valorAccion); ?></option>
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
				<a class="btn btn-default" href="?controller=Auditoria&action=show">Limpiar filtros</a>
			</div>
		</div>
	</form>

	<p><strong><?php echo (int) $totalEventos; ?></strong> evento<?php echo $totalEventos == 1 ? '' : 's'; ?> <?php echo ($usuarioId !== '' || $entidad !== '' || $accion !== '' || $fechaInicial !== '' || $fechaFinal !== '') ? 'con los filtros aplicados' : 'en total'; ?>.</p>

	<?php if (empty($listaAuditoria)) { ?>
		<div class="alert alert-info">No se encontraron eventos con los filtros aplicados.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Fecha</th>
					<th>Usuario</th>
					<th>Entidad</th>
					<th>ID</th>
					<th>Acción</th>
					<th>Antes</th>
					<th>Después</th>
				</tr>
				<tbody>
					<?php foreach ($listaAuditoria as $evento) {

						$usuarioEvento = Usuario::searchByCodigoUser($evento->getUsuarioId());

					?>
					<tr>
						<td><?php echo h($evento->getFecha()); ?></td>
						<td><?php echo h($usuarioEvento->getNombre().' '.$usuarioEvento->getApellido()); ?></td>
						<td><?php echo h($evento->getEntidad()); ?></td>
						<td><?php echo h($evento->getEntidadId()); ?></td>
						<td><?php echo h($evento->getAccion()); ?></td>
						<td><small><?php echo h($evento->getDatosAntes()); ?></small></td>
						<td><small><?php echo h($evento->getDatosDespues()); ?></small></td>
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
