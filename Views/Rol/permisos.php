<div class="container">
	<h2>Permisos del Rol: <?php echo h($rol->getDescripcion()); ?></h2>
	<form action="?controller=Rol&&action=guardarPermisos" method="POST">
	<?php echo Csrf::field(); ?>
		<input type="hidden" name="id" value="<?php echo $rol->getId(); ?>">

		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Asignar</th>
						<th>Codigo</th>
						<th>Descripcion</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($listaPermiso as $permiso) {?>
					<tr>
						<td>
							<input type="checkbox" name="permisos[]" value="<?php echo $permiso->getId(); ?>" <?php echo in_array($permiso->getCodigo(), $codigosAsignados) ? 'checked' : ''; ?>>
						</td>
						<td><?php echo h($permiso->getCodigo()); ?></td>
						<td><?php echo h($permiso->getDescripcion()); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<button type="submit" class="btn btn-primary">Guardar Permisos</button>
		<a class="btn btn-default" href="?controller=Rol&&action=show">Cancelar</a>
	</form>
</div>
