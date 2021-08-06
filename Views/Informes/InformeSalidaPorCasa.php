<div class="container">
	<h2>Informe Salida / Por casa</h2>
	<form class="form-inline" action="?controller=InformeSalidaPorCasa&action=search" method="post">
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
				<select name="idManzana" id="idManzana" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaManzanaCompleta as $manzana) { ?>
				<option value="<?php echo $manzana->getId(); ?>" data-subtext="<?php echo $manzana->getDescripcion(); ?>"><?php echo $manzana->getId()." - "; ?></option>
				<?php }?>          
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="idCasa" id="idCasa" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaCasaCompleta as $casa) { ?>
				<option value="<?php echo $casa->getId(); ?>" data-subtext="<?php echo $casa->getDescripcion(); ?>"><?php echo $casa->getId()." - "; ?></option>
				<?php }?>          
				</select>
			</div>
		</div>
		
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-search"> </span> Buscar</button>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeSalidaPorCasa&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-danger" href="?controller=InformeSalidaPorCasa&action=eliminar"><span class="glyphicon glyphicon-trash"> </span> Eliminar Seleccion</a>
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
				<?php if (isset($_SESSION['informeGeneralSalidaPorCasa'])) { ?>
						# code...
					
					<?php $i = 0?>
					<?php foreach ($_SESSION['informeGeneralSalidaPorCasa'] as $salida) {

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
				<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>