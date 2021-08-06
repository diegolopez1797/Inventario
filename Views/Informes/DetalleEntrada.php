<div class="container">
	<h5 align="center">Informe Detallado de Entrada No: <?php echo $id?></h5>
	<h5 align="center">Realizada por: <?php echo $usuario->getNombre().' '.$usuario->getApellido()?></h5>
	<form class="form-inline" action="?controller=InformeGeneral&action=search" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<a class="btn btn-success" href="?controller=InformeEntrada&action=generarPDF"><span class="glyphicon glyphicon-file"></span> Generar PDF</a>
			</div>
		</div class="form-group row">
	</form>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Unidad</th>
					<th>Cantidad</th>
					
				</tr>
				<tbody>
					<?php $i = 0?>
					<?php foreach ($materialRegistroEntradas as $entrada) {?>
					<tr>
						<td><?php echo $material[$i]->getCodigo(); ?></td>
						<td><?php echo $material[$i]->getDescripcion(); ?></td>
						<td><?php echo $material[$i]->getUnidad(); ?></td>
						<td><?php echo $entrada->getCantidad(); ?></td>
						
					</tr>
					<?php $i=$i+1; ?>
					<?php } ?>
				</tbody>
			</thead>
		</table>
	</div>
</div>