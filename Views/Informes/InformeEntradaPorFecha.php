<div class="container">
	<h2>Informe Entrada / Por fecha</h2>
	<form class="form-inline" action="?controller=InformeEntradaPorFecha&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<p>Desde: </p>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaInicial" min="2021-04-01" max="2050-12-31" class="form-control"/>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<p>Hasta: </p>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="date" name="fechaFinal" min="2021-04-01" max="2050-12-31" class="form-control"/>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" class="btn btn-primary" ><span class="glyphicon glyphicon-search"> </span> Buscar</button>
			</div>
		</div class="form-group row">
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeEntradaPorFecha&action=generarPDF"><span class="glyphicon glyphicon-file"> </span> Generar PDF</a>
			</div>
		</div class="form-group row">
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-danger" href="?controller=InformeEntradaPorFecha&action=eliminar"><span class="glyphicon glyphicon-trash"> </span> Eliminar Seleccion</a>
			</div>
		</div class="form-group row">
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Entrada No</th>
					<th>Fecha</th>
					<th>Hora</th>
					<th>Usuario</th>
					<th>Acciones</th>
				</tr>
				<tbody>
					<?php foreach ($_SESSION['informeEntradaPorFechaGeneral'] as $entrada) {

						$usuario = Usuario::searchByCodigoUser($entrada->getUsuario());

					?>
					<tr>
						<td><?php echo $entrada->getId(); ?></td>
						<td><?php echo $entrada->getFecha(); ?></td>
						<td><?php echo $entrada->getHora(); ?></td>
						<td><?php echo $usuario->getNombre().' '.$usuario->getApellido(); ?></td>
						<td><a id="boton-editar" class="btn btn-warning" href="?controller=InformeEntrada&action=detalle&id=<?php echo $entrada->getId()?>&usuario=<?php echo $entrada->getUsuario()?>"><span class="glyphicon glyphicon-eye-open"> </span> Ver</a></td>
					</tr>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>