<div class="container">
	<h2>Informe Salida / Por material y proyecto</h2>
	<form class="form-inline" action="?controller=InformeDetallado&action=searchDescripcion" method="post">

		<div class="form-group row">
			<div class="col-xs-4">
	      		<select name="idMaterial" id="idMaterial" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getId(); ?>" data-subtext="<?php echo $material->getDescripcion(); ?>"><?php echo $material->getCodigo()." - "; ?></option>
				<?php }?>          
				</select>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
	      		<select name="idProyecto" id="idProyecto" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaProyectoCompleta as $proyecto) { ?>
				<option value="<?php echo $proyecto->getId(); ?>" data-subtext="<?php echo $proyecto->getDescripcion(); ?>"><?php echo $proyecto->getId()." - "; ?></option>
				<?php }?>          
				</select>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" id="cantidad" name="cantidad" type="text" placeholder="Cant. Max. por casa">
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" name="btnConsultar" class="btn btn-success"><span class="glyphicon glyphicon-folder-open"> </span> Consultar</button>
			</div>
		</div>
	</form>

	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Descripcion Material</th>
					<th>Proyecto</th>
					<th>Manzana</th>
					<th>Casa</th>
					<th>Cantidad</th>
					<th>Acciones</th>

				</tr>
				<tbody>

					<?php if (!empty($_SESSION['informeDetallado'])) { ?>



						<?php foreach ($_SESSION['informeDetallado'] as $informe) {

						$material = Material::searchById($informe->getMaterialId());
						$manzana = Manzana::searchById($informe->getManzanaId());
						$casa = Casa::searchById($informe->getCasaId());
						$proyecto = Proyecto::searchById($informe->getProyectoId());


					?>
					<tr>
						<td><?php echo $material->getDescripcion(); ?></td>
						<td><?php echo $proyecto->getDescripcion(); ?></td>
						<td><?php echo $manzana->getDescripcion(); ?></td>
						<td><?php echo $casa->getDescripcion(); ?></td>
						<td><?php echo $informe->getCantidad(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=InformeDetallado&action=detalladoMaterial&proyecto=<?php echo $informe->getProyectoId()?>&manzana=<?php echo $informe->getManzanaId()?>&casa=<?php echo $informe->getCasaId()?>&material=<?php echo $informe->getMaterialId()?>"><span class="glyphicon glyphicon-eye-open"> </span> Detalle</a></td>
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