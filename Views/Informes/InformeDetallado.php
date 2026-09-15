<?php
$construirEnlace = function($cambios = []) use ($proyectoId, $ubicacionId, $materialId, $rubroId, $fechaInicial, $fechaFinal) {
	$parametros = array_merge([
		'controller' => 'InformeDetallado',
		'action' => 'show',
		'proyectoId' => $proyectoId,
		'ubicacionId' => $ubicacionId,
		'materialId' => $materialId,
		'rubroId' => $rubroId,
		'fechaInicial' => $fechaInicial,
		'fechaFinal' => $fechaFinal,
	], $cambios);

	$parametros = array_filter($parametros, function($valor){
		return $valor !== '' && $valor !== null;
	});

	return '?' . http_build_query($parametros);
};
?>
<div class="container">
	<h2>Salidas por Ubicación</h2>

	<form class="form-inline" action="?controller=InformeDetallado&action=show" method="get">
		<input type="hidden" name="controller" value="InformeDetallado">
		<input type="hidden" name="action" value="show">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="proyectoId" class="selectpicker" data-show-subtext="true" data-live-search="true">
					<option value="">Elija un proyecto...</option>
					<?php foreach ($listaProyectoCompleta as $proyecto) { ?>
					<option value="<?php echo $proyecto->getId(); ?>" <?php echo (string) $proyectoId === (string) $proyecto->getId() ? 'selected' : ''; ?>><?php echo h($proyecto->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<?php if (!empty($proyectoId)) { ?>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="ubicacionId" class="selectpicker" data-show-subtext="false" data-live-search="true">
					<option value="">Todas las ubicaciones del proyecto</option>
					<?php foreach ($listaUbicacionProyecto as $ubicacion) { ?>
					<option value="<?php echo $ubicacion['id']; ?>" <?php echo (string) $ubicacionId === (string) $ubicacion['id'] ? 'selected' : ''; ?>><?php echo h($ubicacion['ruta']); ?></option>
					<?php } ?>
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
				<select name="rubroId" class="form-control">
					<option value="">Toda actividad</option>
					<?php foreach ($listaRubro as $rubro) { ?>
					<option value="<?php echo $rubro->getId(); ?>" <?php echo (string) $rubroId === (string) $rubro->getId() ? 'selected' : ''; ?>><?php echo h($rubro->getDescripcion()); ?></option>
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
		<?php } ?>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"> </span> Consultar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-default" href="?controller=InformeDetallado&action=show">Limpiar filtros</a>
			</div>
		</div>
		<?php if (!empty($proyectoId)) { ?>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeDetallado&action=generarPDF"><span class="glyphicon glyphicon-file"> </span> Generar PDF</a>
			</div>
		</div>
		<?php } ?>
	</form>

	<?php if (empty($proyectoId)) { ?>
		<div class="alert alert-info">Elija un proyecto para consultar sus salidas por ubicación.</div>
	<?php } elseif (empty($grupos)) { ?>
		<div class="alert alert-info">No se encontraron salidas con los filtros aplicados.</div>
	<?php } else { ?>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Ubicación</th>
					<th>Material</th>
					<th>Unidad</th>
					<th>Cantidad total</th>
					<th>No. de documentos</th>
					<th>Ver</th>
				</tr>
				<tbody>
					<?php foreach ($grupos as $grupo) { ?>
					<tr>
						<td><?php echo h($grupo['UbicacionRuta']); ?></td>
						<td><?php echo h($grupo['MaterialCodigo']); ?> - <?php echo h($grupo['MaterialDescripcion']); ?></td>
						<td><?php echo !empty($grupo['MaterialUnidad']) ? h($grupo['MaterialUnidad']) : ''; ?></td>
						<td><?php echo (int) $grupo['CantidadTotal']; ?></td>
						<td><?php echo (int) $grupo['TotalDocumentos']; ?></td>
						<td><a class="btn btn-warning btn-xs" href="?controller=InformeDetallado&action=detalle&proyecto=<?php echo $proyectoId; ?>&ubicacion=<?php echo $grupo['UbicacionID']; ?>&material=<?php echo $grupo['MaterialID']; ?>"><span class="glyphicon glyphicon-eye-open"> </span> Detalle</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
	<?php } ?>
</div>
