<div class="container">
	<h2>Entregar Solicitud #<?php echo $solicitud->getId(); ?></h2>
	<p><strong>Proyecto:</strong> <?php echo h($proyecto->getDescripcion()); ?></p>

	<form action="?controller=Solicitud&&action=procesarEntrega" method="POST">
		<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" value="<?php echo $solicitud->getId(); ?>">

		<div class="form-group">
			<label>Contratista</label>
			<select name="contratista" class="form-control" required>
				<option value="0">Elija Contratista...</option>
				<?php foreach ($listaContratista as $contratista) { ?>
				<option value="<?php echo $contratista->getId(); ?>"><?php echo h($contratista->getDescripcion()); ?></option>
				<?php } ?>
			</select>
		</div>

		<?php if ($solicitud->getUbicacionId() !== null) { ?>
		<p><strong>Ubicación:</strong> <?php echo h(Ubicacion::searchById($solicitud->getUbicacionId())->getNombre()); ?> (de la solicitud original — se conserva automáticamente, no es necesario volver a elegirla).</p>
		<?php } ?>

		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Material</th>
						<th>Cantidad</th>
						<th>Destino</th>
						<th>Actividad</th>
						<th>Información adicional</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($detalle as $i => $linea) { ?>
					<tr>
						<td><?php echo h($linea['material']->getDescripcion()); ?></td>
						<td><?php echo h($linea['cantidad']); ?> <?php echo h($linea['material']->getUnidad()); ?></td>
						<td>
							<select name="destino[<?php echo $i ?>]" class="form-control">
								<option value="0">...</option>
								<?php foreach ($listaDestino as $destino) { ?>
								<option value="<?php echo $destino->getId(); ?>"><?php echo h($destino->getDescripcion()); ?></option>
								<?php } ?>
							</select>
						</td>
						<td>
							<select name="rubro[<?php echo $i ?>]" class="form-control">
								<option value="0">...</option>
								<?php foreach ($listaRubro as $rubro) { ?>
								<option value="<?php echo $rubro->getId(); ?>"><?php echo h($rubro->getDescripcion()); ?></option>
								<?php } ?>
							</select>
						</td>
						<td>
							<details>
								<summary>Casa / Manzana / Área (opcional)</summary>
								<div class="form-group">
									<label>Casa</label>
									<select name="casa[<?php echo $i ?>]" class="form-control">
										<option value="0">...</option>
										<?php foreach ($listaCasa as $casa) { ?>
										<option value="<?php echo $casa->getId(); ?>"><?php echo h($casa->getDescripcion()); ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label>Manzana</label>
									<select name="manzana[<?php echo $i ?>]" class="form-control">
										<option value="0">...</option>
										<?php foreach ($listaManzana as $manzana) { ?>
										<option value="<?php echo $manzana->getId(); ?>"><?php echo h($manzana->getDescripcion()); ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group">
									<label>Área</label>
									<select name="area[<?php echo $i ?>]" class="form-control">
										<option value="0">...</option>
										<?php foreach ($listaArea as $area) { ?>
										<option value="<?php echo $area->getId(); ?>"><?php echo h($area->getDescripcion()); ?></option>
										<?php } ?>
									</select>
								</div>
							</details>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-send"> </span> Confirmar Entrega</button>
		<a class="btn btn-default" href="?controller=Solicitud&&action=show">Cancelar</a>
	</form>
</div>
