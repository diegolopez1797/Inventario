<div class="container">
	<h2>Informe Material / Completo</h2>
	<form class="form-inline" action="?controller=InformeGeneral&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="codigo" id="codigo" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getCodigo(); ?>" data-subtext="<?php echo $material->getDescripcion(); ?>"><?php echo $material->getCodigo()." - "; ?></option>
				<?php }?>          
				</select>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-th-list"> </span> Listar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeGeneral&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-danger" href="?controller=InformeGeneral&action=eliminar"><span class="glyphicon glyphicon-trash"> </span> Eliminar Seleccion</a>
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
					<th>Saldo</th>
				</tr>
				<tbody>
					<?php foreach ($_SESSION['informeGeneral'] as $material) {?>
					<tr>
						<td><?php echo $material->getCodigo(); ?></td>
						<td><?php echo $material->getDescripcion(); ?></td>
						<td><?php echo $material->getUnidad(); ?></td>
						<td><?php echo $material->getSaldo(); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>