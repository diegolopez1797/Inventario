<div class="container">
	<h2>Entrada Material</h2>
	<form class="form-inline" action="?controller=RegistroEntradas&&action=searchMaterial" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" id="codigo" name="codigo" type="text" placeholder="Busqueda por Codigo">
				<?php /*

				PARA BUSCADOR INTELIGENTE -------------------------------------

				<select name="codigo" id="codigo" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getCodigo(); ?>" data-subtext="<?php echo $material->getDescripcion(); ?>"><?php echo $material->getCodigo()." - "; ?></option>
				<?php }?>          
				</select>
				*/
				?>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
					<button type="submit" name="btn" class="btn btn-primary" ><span class="glyphicon glyphicon-th-list"> </span> Listar</button>
			</div>
		</div>
		<div class="form-group row">
			<div class="col-xs-4">
				<button type="submit" name="btnIngresar" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"> </span> Guardar</button>
			</div>
		</div>
		<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Unidad</th>
					<th>Cantidad</th>
					<th>Acciones</th>
					
				<tbody>
					<?php if (isset($_SESSION['listaCantidad'])) {

								$listaCantidad = $_SESSION['listaCantidad'];

							} 

					?>
					<?php if (isset($_SESSION['listaMaterial'])) { 

						  $listaMaterial = $_SESSION['listaMaterial'];

					?>
						<?php $i = 0; ?>
						<?php foreach ($listaMaterial as $material) {?>

						<tr>
							<td><?php echo $material->getCodigo(); ?></td>
							<td><?php echo $material->getDescripcion(); ?></td>
							<td><?php echo $material->getUnidad(); ?></td>
							<td>
								<div class="col-xs-4">
									<input type="number" class="form-control" name="listaCantidad[<?php echo $i ?>]" type="text" value="<?php echo  $listaCantidad[$i]?>" placeholder="cantidad">
								</div>
							</td>
							<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=RegistroEntradas&&action=quitarMaterial&&id=<?php echo $i ?>"><span class="glyphicon glyphicon-erase"> </span> Borrar</a></td>
						</tr>
						<?php $i = $i + 1; ?>
						<?php } ?>

					<?php } ?>
					
				</tbody>
			</thead>
		</table>
		</div>		
	</form>	
</div>