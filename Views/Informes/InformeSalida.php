<div class="container">
	<h2>Informe Salida / No. salida</h2>
	<form class="form-inline" action="?controller=InformeSalida&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" id="codigo" name="codigo" type="text" placeholder="Salida No">
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-search"> </span> Buscar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeSalida&action=generarSalidaPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-danger" href="?controller=InformeSalida&action=eliminar"><span class="glyphicon glyphicon-trash"> </span> Eliminar Seleccion</a>
			</div>
		</div>
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Salida No</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Usuario</th>
					<th>Contratista</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($_SESSION['informeSalidaGeneral'] as $salida) {

						$usuario = Usuario::searchByCodigoUser($salida->getUsuario());
						$contratista = Contratista::searchById($salida->getContratista());

					?>
					<tr>
						<td><?php echo $salida->getId(); ?></td>
						<td><?php echo $salida->getFecha(); ?></td>
						<td><?php echo $salida->getHora(); ?></td>
						<td><?php echo $usuario->getNombre().' '.$usuario->getApellido(); ?></td>
						<td><?php echo $contratista->getDescripcion(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=InformeSalida&action=detalle&id=<?php echo $salida->getId()?>&usuario=<?php echo $salida->getUsuario()?>"><span class="glyphicon glyphicon-eye-open"> </span> Ver</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>