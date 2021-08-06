<div class="container">
	<h2>Gestion Material</h2>
	<form class="form-inline" action="?controller=Material&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="codigo" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getCodigo(); ?>" data-subtext="<?php echo $material->getDescripcion(); ?>"><?php echo $material->getCodigo()." - "; ?></option>
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
				<a class="btn btn-success" href="?controller=Material&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Material</a>
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
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaMaterial as $material) {?>
					<tr>
						<td><?php echo $material->getCodigo(); ?></td>
						<td><?php echo $material->getDescripcion(); ?></td>
						<td><?php echo $material->getUnidad(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=Material&&action=updateshow&&id=<?php echo $material->getId() ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a></td>
						<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=Material&&action=delete&&id=<?php echo $material->getId() ?>"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>