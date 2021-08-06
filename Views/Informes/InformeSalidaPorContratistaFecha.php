<div class="container">
	<h2>Informe Salida / Por contratista y fecha</h2>
	<form class="form-inline" action="?controller=InformeSalidaPorContratistaFecha&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<select name="idContratista" id="idContratista" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaContratistaCompleta as $contratista) { ?>
				<option value="<?php echo $contratista->getId(); ?>" data-subtext="<?php echo $contratista->getDescripcion(); ?>"><?php echo $contratista->getId()." - "; ?></option>
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
				<a class="btn btn-success" href="?controller=InformeSalidaPorContratistaFecha&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-danger" href="?controller=InformeSalidaPorContratistaFecha&action=eliminar"><span class="glyphicon glyphicon-trash"> </span> Eliminar Seleccion</a>
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
					<?php foreach ($_SESSION['informeGeneralSalidaPorContratistaFecha'] as $salida) {

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