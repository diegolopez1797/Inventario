<div class="container">
	<h2>Entrada Material</h2>
	<form class="form-inline" action="?controller=RegistroEntradas&&action=searchMaterial" method="post">
		<?php echo Csrf::field(); ?>
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" id="codigo" name="codigo" type="text" placeholder="Busqueda por Codigo">
				<?php /*

				PARA BUSCADOR INTELIGENTE -------------------------------------

				<select name="codigo" id="codigo" class="selectpicker" data-show-subtext="true" data-live-search="true">
				<?php foreach ($listaMaterialCompleta as $material) { ?>
				<option value="<?php echo $material->getCodigo(); ?>" data-subtext="<?php echo h($material->getDescripcion()); ?>"><?php echo $material->getCodigo()." - "; ?></option>
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
				<select name="idProveedor" class="form-control">
						<?php if (isset($_SESSION['seleccionProveedorEntrada'])) { ?>
						<option selected="" value="<?php echo $_SESSION['seleccionProveedorEntrada']->getId(); ?>"><?php echo h($_SESSION['seleccionProveedorEntrada']->getDescripcion()); ?></option>
						<?php }else{ ?>
						<option value="0" selected="">Elija Proveedor...</option>
						<?php } ?>

						<?php foreach ($_SESSION['listaProveedor'] as $proveedor){ ?>
													<?php if (isset($_SESSION['seleccionProveedorEntrada'])) { ?>
					<?php		if ($_SESSION['seleccionProveedorEntrada']->getId() != $proveedor->getId()) {?>
						<option value="<?php echo $proveedor->getId(); ?>"><?php echo h($proveedor->getDescripcion()); ?></option>
							<?php } ?>
							<?php }else{ ?>
						<option value="<?php echo $proveedor->getId(); ?>"><?php echo h($proveedor->getDescripcion()); ?></option>
							<?php } ?>
						<?php } ?>
				</select>
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
					<th>Destino</th>
					<?php if (Permiso::usuarioPuede('costo.ver')) { ?>
					<th>Costo Unitario</th>
					<?php } ?>
					<th>Acciones</th>
					
				<tbody>
					<?php if (isset($_SESSION['listaCantidad'])) {

								$listaCantidad = $_SESSION['listaCantidad'];

								$listaSeleccionDestino = $_SESSION['listaSeleccionDestinoEntrada'];
								$seleccionDestino = $_SESSION['seleccionDestinoEntrada'];

								$listaCostoUnitario = isset($_SESSION['listaCostoUnitario']) ? $_SESSION['listaCostoUnitario'] : [];

							}

					?>
					<?php if (isset($_SESSION['listaMaterial'])) { 

						  $listaMaterial = $_SESSION['listaMaterial'];

					?>
						<?php $i = 0; ?>
						<?php foreach ($listaMaterial as $material) {?>

						<tr>
							<td><?php echo $material->getCodigo(); ?></td>
							<td><?php echo h($material->getDescripcion()); ?></td>
							<td><?php echo h($material->getUnidad()); ?></td>
							<td>
								<div class="col-xs-4">
									<input type="number" class="form-control" name="listaCantidad[<?php echo $i ?>]" type="text" value="<?php echo  $listaCantidad[$i]?>" placeholder="cantidad">
								</div>
							</td>

							<td>	
								<select style="width : 100px" name="listaSeleccionDestinoEntrada[<?php echo $i ?>]" class="form-control">
										<?php if ($seleccionDestino[$i] != null) { ?>

										<option selected="" value="<?php echo $seleccionDestino[$i]->getId(); ?>"><?php echo h($seleccionDestino[$i]->getDescripcion()); ?></option>	

										<?php }else{ ?>
										<option selected="" value="0">...</option>
										<?php } ?>
											
										<?php foreach ($_SESSION['listaDestinoEntrada'] as $destino){ ?>

										<option value="<?php echo $destino->getId(); ?>"><?php echo h($destino->getDescripcion()); ?></option>

										<?php } ?>
								</select>
							</td>
							<?php if (Permiso::usuarioPuede('costo.ver')) { ?>
							<td>
								<div class="col-xs-4">
									<input style="width : 100px" type="number" step="0.01" min="0" class="form-control" name="listaCostoUnitario[<?php echo $i ?>]" value="<?php echo isset($listaCostoUnitario[$i]) ? h($listaCostoUnitario[$i]) : ''; ?>" placeholder="opcional">
								</div>
							</td>
							<?php } ?>
							<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=RegistroEntradas&&action=quitarMaterial&&id=<?php echo $i ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-erase"> </span> Borrar</a></td>
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