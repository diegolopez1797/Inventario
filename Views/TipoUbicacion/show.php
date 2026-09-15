<div class="container">
	<h2>Tipos de Ubicación</h2>
	<p><em>Catálogo sugerido para el campo "Tipo" al crear ubicaciones (Torre, Piso, Apartamento...). Es solo una lista de apoyo: al escribir un tipo que coincide con uno de esta lista, siempre se guarda con esta misma redacción. La opción "Otro" en los formularios de ubicación sigue aceptando texto libre.</em></p>

	<a class="btn btn-success" href="?controller=TipoUbicacion&&action=register"><span class="glyphicon glyphicon-plus-sign"> </span> Crear Tipo</a>

	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($listaTipoUbicacion as $tipo) {?>
					<tr>
						<td><?php echo h($tipo->getCodigo()); ?></td>
						<td><?php echo h($tipo->getDescripcion()); ?></td>
						<td>
							<a class="btn btn-warning" href="?controller=TipoUbicacion&&action=updateshow&&id=<?php echo $tipo->getId(); ?>"><span class="glyphicon glyphicon-wrench"> </span> Editar</a>
							<a class="btn btn-danger" href="?controller=TipoUbicacion&&action=desactivar&&id=<?php echo $tipo->getId(); ?>&&csrf_token=<?php echo Csrf::token(); ?>" onclick="return confirm('¿Desactivar este tipo? Dejará de sugerirse en formularios nuevos.');"><span class="glyphicon glyphicon-trash"> </span> Desactivar</a>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>
