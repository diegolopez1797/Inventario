<div class="container">
	<h2>Salida Material</h2>
	<form class="form-inline" action="?controller=RegistroSalidas&&action=searchMaterial" method="post">
		<div class="form-group row">
			<div class="col-xs-4">
				<input type="number" class="form-control" id="codigo" name="codigo" type="text" placeholder="Busqueda por Codigo">
				<?php /* 

				PARA BUSCADOR INTELIGENTE ---------------------------------------------

				<select name="codigo" class="selectpicker" data-show-subtext="true" data-live-search="true">
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
				<select name="idContratista" class="form-control">
											<?php if (isset($_SESSION['seleccionContratista'])) { ?>
											<option selected="" value="<?php echo $_SESSION['seleccionContratista']->getId(); ?>"><?php echo $_SESSION['seleccionContratista']->getDescripcion(); ?></option>										
											<?php }else{ ?>
											<option value="0" selected="">Elija Contratista...</option>
											<?php } ?>
												
											<?php foreach ($_SESSION['listaContratista'] as $contratista){ ?>
																			<?php	if (isset($_SESSION['seleccionContratista'])) { ?>
			<?php		if ($_SESSION['seleccionContratista']->getId() != $contratista->getId()) {?>
											<option value="<?php echo $contratista->getId(); ?>"><?php echo $contratista->getDescripcion(); ?></option> 
												<?php } ?>
												<?php }else{ ?>
											<option value="<?php echo $contratista->getId(); ?>"><?php echo $contratista->getDescripcion(); ?></option>
												<?php } ?>
											<?php } ?>
											
				</select>
			</div>
		</div>

		<div class="form-group row">
			<div class="col-xs-4">
				<select name="idProyecto" class="form-control">
											<?php if (isset($_SESSION['seleccionProyecto'])) { ?>
											<option selected="" value="<?php echo $_SESSION['seleccionProyecto']->getId(); ?>"><?php echo $_SESSION['seleccionProyecto']->getDescripcion(); ?></option>										
											<?php }else{ ?>
											<option value="0" selected="">Elija Proyecto...</option>
											<?php } ?>
												
											<?php foreach ($_SESSION['listaProyecto'] as $proyecto){ ?>
																			<?php	if (isset($_SESSION['seleccionProyecto'])) { ?>
			<?php		if ($_SESSION['seleccionProyecto']->getId() != $proyecto->getId()) {?>
											<option value="<?php echo $proyecto->getId(); ?>"><?php echo $proyecto->getDescripcion(); ?></option> 
												<?php } ?>
												<?php }else{ ?>
											<option value="<?php echo $proyecto->getId(); ?>"><?php echo $proyecto->getDescripcion(); ?></option>
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
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Unidad</th>
					<th>Cantidad</th>
					<th>Destino</th>
					<th>Casa</th>
					<th>Manzana</th>
					<th>Etapa</th>
					<th>Acciones</th>

				</tr>
				</tr>
				<tbody>
					<?php if (isset($_SESSION['listaCantidadSalida'])) {

								$listaCantidadSalida = $_SESSION['listaCantidadSalida'];

							} 

					?>
					<?php if (isset($_SESSION['listaMaterialSalida'])) { 

						  $listaMaterialSalida = $_SESSION['listaMaterialSalida'];
						  
						  $listaSeleccionCasa = $_SESSION['listaSeleccionCasa'];
						  $seleccionCasa = $_SESSION['seleccionCasa'];

						  $listaSeleccionManzana = $_SESSION['listaSeleccionManzana'];
						  $seleccionManzana = $_SESSION['seleccionManzana'];

						  $listaSeleccionArea = $_SESSION['listaSeleccionArea'];
						  $seleccionArea = $_SESSION['seleccionArea'];

						  $listaSeleccionDestino = $_SESSION['listaSeleccionDestino'];
						  $seleccionDestino = $_SESSION['seleccionDestino'];



					?>
						<?php $i = 0; ?>
						<?php foreach ($listaMaterialSalida as $material) {?>

						<tr>
							<td><?php echo $material->getCodigo(); ?></td>
							<td><?php echo $material->getDescripcion(); ?></td>
							<td><?php echo $material->getUnidad(); ?></td>
							<td>
								<div class="col-xs-4">
									<input style="width : 80px" type="number" class="form-control" name="listaCantidadSalida[<?php echo $i ?>]" type="text" value="<?php echo  $listaCantidadSalida[$i]?>" placeholder="">
								</div>
							</td>
							<td>	
								<select style="width : 100px" name="listaSeleccionDestino[<?php echo $i ?>]" class="form-control">
										<?php if ($seleccionDestino[$i] != null) { ?>

										<option selected="" value="<?php echo $seleccionDestino[$i]->getId(); ?>"><?php echo $seleccionDestino[$i]->getDescripcion(); ?></option>	

										<?php }else{ ?>
										<option selected="" value="0">...</option>
										<?php } ?>
											
										<?php foreach ($_SESSION['listaDestino'] as $destino){ ?>

										<option value="<?php echo $destino->getId(); ?>"><?php echo $destino->getDescripcion(); ?></option>

										<?php } ?>
								</select>
							</td>
							<td>	
								<select style="width : 100px" name="listaSeleccionCasa[<?php echo $i ?>]" class="form-control">
										<?php if ($seleccionCasa[$i] != null) { ?>

										<option selected="" value="<?php echo $seleccionCasa[$i]->getId(); ?>"><?php echo $seleccionCasa[$i]->getDescripcion(); ?></option>	

										<?php }else{ ?>
										<option selected="" value="0">...</option>
										<?php } ?>
											
										<?php foreach ($_SESSION['listaCasa'] as $casa){ ?>

										<option value="<?php echo $casa->getId(); ?>"><?php echo $casa->getDescripcion(); ?></option>

										<?php } ?>
								</select>
							</td>
							<td>
								<select style="width : 100px" name="listaSeleccionManzana[<?php echo $i ?>]" class="form-control">
										<?php if ($seleccionManzana[$i] != null) { ?>

										<option selected="" value="<?php echo $seleccionManzana[$i]->getId(); ?>"><?php echo $seleccionManzana[$i]->getDescripcion(); ?></option>	

										<?php }else{ ?>
										<option selected="" value="0">...</option>
										<?php } ?>
											
										<?php foreach ($_SESSION['listaManzana'] as $manzana){ ?>

										<option value="<?php echo $manzana->getId(); ?>"><?php echo $manzana->getDescripcion(); ?></option>

										<?php } ?>
								</select>
							</td>
							<td>
								<select style="width : 100px" name="listaSeleccionArea[<?php echo $i ?>]" class="form-control">
										<?php if ($seleccionArea[$i] != null) { ?>

										<option selected="" value="<?php echo $seleccionArea[$i]->getId(); ?>"><?php echo $seleccionArea[$i]->getDescripcion(); ?></option>	

										<?php }else{ ?>
										<option selected="" value="0">...</option>
										<?php } ?>
											
										<?php foreach ($_SESSION['listaArea'] as $area){ ?>

										<option value="<?php echo $area->getId(); ?>"><?php echo $area->getDescripcion(); ?></option>

										<?php } ?>
								</select>
							</td>
							<td></td>
							<td><a id="boton-eliminar" class="btn btn-danger" href="?controller=RegistroSalidas&&action=quitarMaterial&&id=<?php echo $i ?>"><span class="glyphicon glyphicon-erase"> </span> Borrar</a></td>
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