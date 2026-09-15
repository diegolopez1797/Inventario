<div class="container">
	<h2>Crear Solicitud</h2>
	<form class="form-inline" action="?controller=Solicitud&&action=searchMaterial" method="post">
		<?php echo Csrf::field(); ?>

		<div class="form-group row">
			<div class="col-xs-4">
				<select name="proyecto" class="form-control">
					<option value="0">Elija Proyecto...</option>
					<?php foreach ($listaProyecto as $proyecto) {?>
					<option value="<?php echo $proyecto->getId(); ?>" <?php echo (isset($_SESSION['solicitudProyectoId']) && $_SESSION['solicitudProyectoId'] == $proyecto->getId()) ? 'selected' : ''; ?>><?php echo h($proyecto->getDescripcion()); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<select name="ubicacion" class="form-control">
					<option value="">-- Sin ubicacion especifica --</option>
					<?php foreach ($listaUbicacion as $ubicacion) {?>
					<option value="<?php echo $ubicacion['id']; ?>" <?php echo (isset($_SESSION['solicitudUbicacionId']) && $_SESSION['solicitudUbicacionId'] == $ubicacion['id']) ? 'selected' : ''; ?>><?php echo h($ubicacion['ruta']); ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" name="codigo" placeholder="Codigo del material">
			</div>
			<div class="col-xs-4">
				<input type="number" step="0.01" min="0.01" class="form-control" name="cantidad" placeholder="Cantidad">
			</div>
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-plus"> </span> Agregar Material</button>
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
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php if (isset($_SESSION['solicitudMaterial'])) { ?>
						<?php $i = 0; ?>
						<?php foreach ($_SESSION['solicitudMaterial'] as $material) {?>
						<tr>
							<td><?php echo $material->getCodigo(); ?></td>
							<td><?php echo h($material->getDescripcion()); ?></td>
							<td><?php echo h($material->getUnidad()); ?></td>
							<td><?php echo h($_SESSION['solicitudCantidad'][$i]); ?></td>
							<td><a class="btn btn-danger btn-xs" href="?controller=Solicitud&&action=quitarMaterial&&id=<?php echo $i ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-erase"> </span> Quitar</a></td>
						</tr>
						<?php $i = $i + 1; ?>
						<?php } ?>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>

	<?php if (!empty($_SESSION['solicitudMaterial'])) { ?>
	<form action="?controller=Solicitud&&action=save" method="POST">
		<?php echo Csrf::field(); ?>
		<input type="hidden" name="proyecto" value="<?php echo h($_SESSION['solicitudProyectoId']); ?>">
		<input type="hidden" name="ubicacion" value="<?php echo h($_SESSION['solicitudUbicacionId']); ?>">
		<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"> </span> Guardar Solicitud</button>
	</form>
	<?php } ?>
</div>
