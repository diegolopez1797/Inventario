<div class="container">
	<h3 align="center">Informe Detallado de Salida No: <?php echo $id?></h3>
	<?php
	$registroSalidas = RegistroSalidas::searchSalida($salida->getRegistroSalidasId());
	$contratista = Contratista::searchById($registroSalidas->getContratista());
	$proyecto = Proyecto::searchById($registroSalidas->getProyecto());
	?>
	<h5 align="center">Fecha: <?php echo $registroSalidas->getFecha()?> / Hora: <?php echo $registroSalidas->getHora()?></h5>
	<h5 align="center">Realizada por: <?php echo $usuario->getNombre().' '.$usuario->getApellido()?> / Entregado a: <?php echo $contratista->getDescripcion()?></h5>
	<h5 align="center">Proyecto: <?php echo $proyecto->getDescripcion()?></h5>
	<form class="form-inline" action="?controller=InformeGeneral&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeSalida&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div class="form-group row">
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Unidad</th>
					<th>Cantidad</th>
					<th>Etapa</th>
					<th>Manzana</th>
					<th>Casa</th>
					<th>Destino</th>
				</tr>
				<tbody>
					<?php $i = 0?>
					<?php foreach ($materialRegistroSalidas as $salida) {

						$destino = Destino::searchById($salida->getDestinoId());
						$casa = Casa::searchById($salida->getCasaId());
						$manzana = Manzana::searchById($salida->getManzanaId());
						$area = Area::searchById($salida->getAreaId());
						


					?>
					<tr>
						<td><?php echo $material[$i]->getCodigo(); ?></td>
						<td><?php echo $material[$i]->getDescripcion(); ?></td>
						<td><?php echo $material[$i]->getUnidad(); ?></td>
						<td><?php echo $salida->getCantidad(); ?></td>
						<td><?php echo $area->getDescripcion(); ?></td>
						<td><?php echo $manzana->getDescripcion(); ?></td>
						<td><?php echo $casa->getDescripcion(); ?></td>
						<td><?php echo $destino->getDescripcion(); ?></td>
						
					</tr>
					<?php $i=$i+1; ?>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>