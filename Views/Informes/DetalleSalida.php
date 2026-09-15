<div class="container">
	<h3 align="center">Informe Detallado de Salida No: <?php echo $id?></h3>
	<?php
	$registroSalidas = RegistroSalidas::searchSalida($salida->getRegistroSalidasId());
	$contratista = Contratista::searchById($registroSalidas->getContratista());
	$proyecto = Proyecto::searchById($registroSalidas->getProyecto());
	?>
	<h5 align="center">Fecha: <?php echo $registroSalidas->getFecha()?> / Hora: <?php echo $registroSalidas->getHora()?></h5>
	<h5 align="center">Realizada por: <?php echo h($usuario->getNombre().' '.$usuario->getApellido()); ?> / Entregado a: <?php echo h($contratista->getDescripcion()); ?></h5>
	<h5 align="center">Proyecto: <?php echo h($proyecto->getDescripcion()); ?></h5>
	<div class="form-group row">
		<div class="col-xs-4">
			<a class="btn btn-success" href="?controller=InformeSalida&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Unidad</th>
					<th>Cantidad</th>
					<th>Destino</th>
					<th>Actividad</th>
					<th>Ubicación</th>
				</tr>
				<tbody>
					<?php $i = 0?>
					<?php foreach ($materialRegistroSalidas as $salida) {

						$destino = $salida->getDestinoId() !== null ? Destino::searchById($salida->getDestinoId()) : null;
						$rubro = $salida->getRubroId() !== null ? Rubro::searchById($salida->getRubroId()) : null;
						$ubicacionRuta = $salida->getUbicacionId() !== null ? Ubicacion::ruta($salida->getUbicacionId()) : null;


					?>
					<tr>
						<td><?php echo $material[$i]->getCodigo(); ?></td>
						<td><?php echo h($material[$i]->getDescripcion()); ?></td>
						<td><?php echo h($material[$i]->getUnidad()); ?></td>
						<td><?php echo $salida->getCantidad(); ?></td>
						<td><?php echo $destino !== null ? h($destino->getDescripcion()) : 'N/D'; ?></td>
						<td><?php echo $rubro !== null ? h($rubro->getDescripcion()) : 'N/D'; ?></td>
						<td><?php echo $ubicacionRuta !== null ? h($ubicacionRuta) : 'N/D'; ?></td>

					</tr>
					<?php $i=$i+1; ?>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>