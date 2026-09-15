<div class="container">
	<h2>Kardex de Material</h2>

	<form class="form-inline" action="?controller=Kardex&action=buscar" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="idMaterial" id="idMaterial" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getId(); ?>" data-subtext="<?php echo h($material->getDescripcion()); ?>"><?php echo $material->getCodigo()." - "; ?></option>
				<?php }?>
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Consultar</button>
			</div>
		</div>
	</form>

	<?php if (isset($_SESSION['kardexMaterial'])) {
		$material = $_SESSION['kardexMaterial'];

		$construirEnlace = function($cambios = []) use ($fechaInicial, $fechaFinal, $ubicacionId, $pagina) {
			$parametros = array_merge([
				'controller' => 'Kardex',
				'action' => 'show',
				'fechaInicial' => $fechaInicial,
				'fechaFinal' => $fechaFinal,
				'ubicacionId' => $ubicacionId,
				'pagina' => $pagina,
			], $cambios);

			$parametros = array_filter($parametros, function($valor){
				return $valor !== '' && $valor !== null;
			});

			return '?' . http_build_query($parametros);
		};
	?>
		<h3><?php echo h($material->getDescripcion()); ?> — Saldo actual: <?php echo $material->getSaldo(); ?> <?php echo h($material->getUnidad()); ?></h3>

		<div class="alert alert-warning">El saldo de cada línea se reconstruye hacia atrás a partir del saldo actual del material. Si alguna vez se corrigió el saldo por fuera de una Entrada, Salida o Ajuste, el saldo mostrado en movimientos anteriores a esa corrección podría no ser exacto.</div>

		<form class="form-inline" action="?controller=Kardex&action=show" method="get">
			<input type="hidden" name="controller" value="Kardex">
			<input type="hidden" name="action" value="show">
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
					<select name="ubicacionId" class="selectpicker" data-show-subtext="false" data-live-search="true">
						<option value="">Todas las ubicaciones (solo aplica a Salidas)</option>
						<?php foreach ($listaUbicacion as $ubicacion) { ?>
						<option value="<?php echo $ubicacion['id']; ?>" <?php echo (string) $ubicacionId === (string) $ubicacion['id'] ? 'selected' : ''; ?>><?php echo h($ubicacion['ruta']); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xs-4">
					<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Filtrar</button>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xs-4">
					<a class="btn btn-default" href="?controller=Kardex&action=show">Limpiar filtros</a>
				</div>
			</div>
		</form>

		<p><strong><?php echo (int) $totalMovimientos; ?></strong> movimiento<?php echo $totalMovimientos == 1 ? '' : 's'; ?> <?php echo ($fechaInicial !== '' || $fechaFinal !== '' || $ubicacionId !== '') ? 'con los filtros aplicados' : 'en total'; ?>.</p>

		<?php if (empty($movimientosPagina)) { ?>
			<div class="alert alert-info">No se encontraron movimientos con los filtros aplicados.</div>
		<?php } else { ?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Documento</th>
						<th>Fecha</th>
						<th>Tipo</th>
						<th>Usuario</th>
						<th>Cantidad</th>
						<th>Saldo después</th>
						<?php if (Permiso::usuarioPuede('costo.ver')) { ?>
						<th>Costo Unitario</th>
						<?php } ?>
						<th>Motivo</th>
						<th>Ubicación</th>
					</tr>
					<tbody>
						<?php foreach ($movimientosPagina as $mov) {
							$usuario = Usuario::searchByCodigoUser($mov['UsuarioID']);

							$etiquetas = [
								'ENTRADA' => ['Entrada', 'success'],
								'SALIDA' => ['Salida', 'danger'],
								'APERTURA' => ['Ajuste (Apertura)', 'info'],
								'CONTEO' => ['Ajuste (Conteo)', $mov['EfectoNeto'] >= 0 ? 'success' : 'danger'],
								'PERDIDA' => ['Ajuste (Pérdida)', 'danger'],
								'DANO' => ['Ajuste (Daño)', 'danger'],
							];
							$etiqueta = $etiquetas[$mov['Tipo']][0];
							$colorEtiqueta = $etiquetas[$mov['Tipo']][1];
							$signo = $mov['EfectoNeto'] > 0 ? '+' : ($mov['EfectoNeto'] < 0 ? '-' : '');

							// Documento: solo Entrada/Salida tienen una pantalla de detalle existente
							// hoy (reutilizada, no se crea ninguna nueva) - un Ajuste se muestra como
							// texto, sin enlace, hasta que exista ese bloque.
							if ($mov['Tipo'] === 'ENTRADA') {
								$documentoTexto = 'Entrada #' . $mov['RegistroID'];
								$documentoUrl = '?controller=InformeEntrada&action=detalle&id=' . $mov['RegistroID'] . '&usuario=' . $mov['UsuarioID'];
							} elseif ($mov['Tipo'] === 'SALIDA') {
								$documentoTexto = 'Salida #' . $mov['RegistroID'];
								$documentoUrl = '?controller=InformeSalida&action=detalle&id=' . $mov['RegistroID'] . '&usuario=' . $mov['UsuarioID'];
							} else {
								$documentoTexto = 'Ajuste #' . $mov['RegistroID'];
								$documentoUrl = null;
							}
						?>
						<tr>
							<td><?php if ($documentoUrl !== null) { ?><a href="<?php echo $documentoUrl; ?>"><?php echo h($documentoTexto); ?></a><?php } else { echo h($documentoTexto); } ?></td>
							<td><?php echo h($mov['Fecha']); ?> <?php echo h($mov['Hora']); ?></td>
							<td><span class="label label-<?php echo $colorEtiqueta; ?>"><?php echo h($etiqueta); ?></span></td>
							<td><?php echo h($usuario->getNombre().' '.$usuario->getApellido()); ?></td>
							<td><?php echo $signo; ?><?php echo abs((int)$mov['EfectoNeto']); ?></td>
							<td><strong><?php echo (int)$mov['SaldoDespues']; ?></strong></td>
							<?php if (Permiso::usuarioPuede('costo.ver')) { ?>
							<td><?php echo $mov['CostoUnitario'] !== null ? '$ ' . number_format($mov['CostoUnitario'], 2) : 'Sin costo'; ?></td>
							<?php } ?>
							<td><?php echo isset($mov['Motivo']) ? h($mov['Motivo']) : ''; ?></td>
							<td><?php echo !empty($mov['UbicacionID']) ? h(Ubicacion::rutaConProyecto($mov['UbicacionID'])) : ''; ?></td>
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
	<?php } ?>
</div>
