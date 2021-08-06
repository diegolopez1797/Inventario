<div class="container">
	<h2>Informe Salida / Por material y fecha</h2>
	<form class="form-inline" action="?controller=InformeSalidaPorMaterialFecha&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="id" id="id" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getId(); ?>" data-subtext="<?php echo $material->getDescripcion(); ?>"><?php echo $material->getCodigo()." - "; ?></option>
				<?php }?>          
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<p>Desde: </p>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaInicial" min="2021-03-00" max="2022-05-25" class="form-control"/>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<p>Hasta: </p>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaFinal" min="2021-03-00" max="2022-05-25" class="form-control"/>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-search"> </span> Buscar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeSalidaPorMaterialFecha&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-danger" href="?controller=InformeSalidaPorMaterialFecha&action=eliminar"><span class="glyphicon glyphicon-trash"> </span> Eliminar Seleccion</a>
			</div>
		</div>
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Unidad</th>
					<th>Cantidad</th>
					<th>Contratista</th>
					<th>Proyecto</th>
					<th>Etapa</th>
					<th>Manzana</th>
					<th>Casa</th>
					<th>Fecha</th>
					<th>Hora</th>
					
				</tr>
				<tbody>
					<?php $i = 0?>
					<?php foreach ($_SESSION['informeGeneralSalidaPorMaterialFecha'] as $salida) {

						$material = Material::searchById($salida->getMaterialId());
						$manzana = Manzana::searchById($salida->getManzanaId());
						$area = Area::searchById($salida->getAreaId());
						$casa = Casa::searchById($salida->getCasaId());
						$registroSalidas = RegistroSalidas::searchSalida($salida->getRegistroSalidasId());
						$contratista = Contratista::searchById($registroSalidas->getContratista());
						$proyecto = Proyecto::searchById($registroSalidas->getProyecto());


					?>
					<tr>
						<td><?php echo $material->getCodigo(); ?></td>
						<td><?php echo $material->getDescripcion(); ?></td>
						<td><?php echo $material->getUnidad(); ?></td>
						<td><?php echo $salida->getCantidad(); ?></td>
						<td><?php echo $contratista->getDescripcion(); ?></td>
						<td><?php echo $proyecto->getDescripcion(); ?></td>
						<td><?php echo $area->getDescripcion(); ?></td>
						<td><?php echo $manzana->getDescripcion(); ?></td>
						<td><?php echo $casa->getDescripcion(); ?></td>
						<td><?php echo $registroSalidas->getFecha(); ?></td>
						<td><?php echo $registroSalidas->getHora(); ?></td>
						
					</tr>
					<?php $i=$i+1; ?>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>