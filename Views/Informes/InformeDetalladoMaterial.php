<div class="container">
	<h2>Informe</h2>
	
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>No salida</th>
					<th>Material</th>
					<th>Proyecto</th>
					<th>Etapa</th>
					<th>Manzana</th>
					<th>Casa</th>
					<th>Destino</th>
					<th>Cantidad</th>

				</tr>
				<tbody>

					<?php if (!empty($informeDetalladoMaterial)) { ?>



						<?php foreach ($informeDetalladoMaterial as $informe) {

						$material = Material::searchById($informe->getMaterialId());
						$proyecto = Proyecto::searchById($informe->getProyectoId());
						$etapa = Area::searchById($informe->getAreaId());
						$manzana = Manzana::searchById($informe->getManzanaId());
						$casa = Casa::searchById($informe->getCasaId());
						$destino = Destino::searchById($informe->getDestinoId());
						


					?>
					<tr>
						<td><?php echo $informe->getRegistroSalidasId(); ?></td>
						<td><?php echo $material->getDescripcion(); ?></td>
						<td><?php echo $proyecto->getDescripcion(); ?></td>
						<td><?php echo $etapa->getDescripcion(); ?></td>
						<td><?php echo $manzana->getDescripcion(); ?></td>
						<td><?php echo $casa->getDescripcion(); ?></td>
						<td><?php echo $destino->getDescripcion(); ?></td>
						<td><?php echo $informe->getCantidad(); ?></td>
					</tr>
					<?php } ?>

						
					
					<?php }else{ ?>

						<?php //echo "<script>alert('¡ No hay resultados !')</script>"; ?>


					<?php } ?>

					
				</tbody>
			</thead>
		</table>
	</div>
	
</div>