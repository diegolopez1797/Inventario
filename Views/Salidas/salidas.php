<div class="container">
	<h2>Salida Material</h2>
	<div id="alertaClienteSalidas"></div>
	<form class="form-inline" action="?controller=RegistroSalidas&&action=searchMaterial" method="post">
		<?php echo Csrf::field(); ?>

		<?php if (!isset($_SESSION['seleccionProyecto'])) { ?>

			<!-- ESTADO 1: sin proyecto - unicamente el selector de Proyecto. -->
			<div class="form-group row">
				<div class="col-xs-4">
					<label>Proyecto</label>
					<select name="idProyecto" class="form-control">
						<option value="0" selected="">Seleccionar proyecto...</option>
						<?php foreach ($_SESSION['listaProyecto'] as $proyecto) { ?>
						<option value="<?php echo $proyecto->getId(); ?>"><?php echo h($proyecto->getDescripcion()); ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-xs-4">
					<button type="submit" name="btnSeleccionarProyecto" class="btn btn-primary">Continuar</button>
				</div>
			</div>

		<?php } else { ?>

			<!-- ESTADO 2/3: proyecto ya elegido - el proyecto es el contexto de toda la salida. -->
			<div class="form-group row">
				<div class="col-xs-4">
					<?php if ($proyectoFijo) { ?>
						<p>
							<strong>Proyecto:</strong> <?php echo h($_SESSION['seleccionProyecto']->getDescripcion()); ?>
							<span class="label label-default">fijo para esta salida</span>
						</p>
						<input type="hidden" name="idProyecto" value="<?php echo (int) $_SESSION['seleccionProyecto']->getId(); ?>">
					<?php } else { ?>
						<label>Proyecto</label>
						<select name="idProyecto" class="form-control">
							<option selected="" value="<?php echo $_SESSION['seleccionProyecto']->getId(); ?>"><?php echo h($_SESSION['seleccionProyecto']->getDescripcion()); ?></option>
							<?php foreach ($_SESSION['listaProyecto'] as $proyecto) { if ($_SESSION['seleccionProyecto']->getId() != $proyecto->getId()) { ?>
							<option value="<?php echo $proyecto->getId(); ?>"><?php echo h($proyecto->getDescripcion()); ?></option>
							<?php } } ?>
						</select>
						<button type="submit" name="btnSeleccionarProyecto" class="btn btn-default btn-sm">Actualizar proyecto</button>
					<?php } ?>
				</div>
			</div>
			<?php if ($proyectoFijo) { ?>
			<div class="form-group row">
				<div class="col-xs-4">
					<p><small>Esta salida ya tiene materiales seleccionados, así que el proyecto no se puede cambiar. Si necesita otro proyecto, reinicie la salida.</small></p>
				</div>
			</div>
			<?php } ?>
			<?php if (empty($_SESSION['listaUbicacion'])) { ?>
			<div class="alert alert-warning">
				Este proyecto todavía no tiene ubicaciones activas configuradas. Puede
				<a href="?controller=Ubicacion&&action=register&&proyecto=<?php echo (int)$_SESSION['seleccionProyecto']->getId(); ?>" target="_blank">crear una ubicación ahora</a>
				(se abre en una pestaña nueva; luego recargue esta página para verla en el selector) o continuar sin ubicación.
			</div>
			<?php } ?>

			<div class="form-group row">
				<div class="col-xs-4">
					<input type="number" class="form-control" id="codigo" name="codigo" type="text" placeholder="Busqueda por Codigo">
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
												<option selected="" value="<?php echo $_SESSION['seleccionContratista']->getId(); ?>"><?php echo h($_SESSION['seleccionContratista']->getDescripcion()); ?></option>
												<?php }else{ ?>
												<option value="0" selected="">Elija Contratista...</option>
												<?php } ?>

												<?php foreach ($_SESSION['listaContratista'] as $contratista){ ?>
																				<?php	if (isset($_SESSION['seleccionContratista'])) { ?>
				<?php		if ($_SESSION['seleccionContratista']->getId() != $contratista->getId()) {?>
												<option value="<?php echo $contratista->getId(); ?>"><?php echo h($contratista->getDescripcion()); ?></option>
													<?php } ?>
													<?php }else{ ?>
												<option value="<?php echo $contratista->getId(); ?>"><?php echo h($contratista->getDescripcion()); ?></option>
													<?php } ?>
												<?php } ?>

					</select>
				</div>
			</div>

			<div class="form-group row">
				<div class="col-xs-4">
					<button type="submit" name="btnIngresar" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"> </span> Guardar</button>
					<?php if ($proyectoFijo) { ?>
					<button type="submit" name="btnReiniciar" class="btn btn-default" onclick="return confirm('¿Reiniciar esta salida? Se perderán los materiales ya agregados.');"><span class="glyphicon glyphicon-refresh"> </span> Reiniciar salida</button>
					<?php } ?>
				</div>
			</div>

			<script>
			// Arbol de Ubicacion EXCLUSIVAMENTE del proyecto ya elegido (nunca de otros proyectos
			// ni de todo el sistema) - se recibe ya filtrado desde el Controller, una sola vez por
			// pagina, y lo comparten todas las lineas de material de esta salida. Este bloque solo
			// se emite cuando ya existe un Proyecto en sesion (ver condicion de arriba).
			var arbolUbicacionProyecto = <?php echo $arbolUbicacionJson; ?>;
			var proyectoTieneEstructuraJS = <?php echo $proyectoTieneEstructura ? 'true' : 'false'; ?>;

			function ubicHijos(padreId) {
				return arbolUbicacionProyecto.filter(function (n) {
					return n.activo === 1 && (padreId === null ? n.padreId === null : n.padreId === padreId);
				});
			}

			function ubicNodoPorId(id) {
				for (var i = 0; i < arbolUbicacionProyecto.length; i++) {
					if (arbolUbicacionProyecto[i].id === id) return arbolUbicacionProyecto[i];
				}
				return null;
			}

			function ubicRutaHasta(id) {
				var ruta = [];
				var actual = ubicNodoPorId(id);
				while (actual) {
					ruta.unshift(actual);
					actual = actual.padreId !== null ? ubicNodoPorId(actual.padreId) : null;
				}
				return ruta;
			}

			// Construye la cascada de selectores para UNA linea de material. No asume Tipo alguno:
			// solo usa PadreID (via ubicHijos) para decidir el siguiente nivel, y "0 hijos activos"
			// para decidir cuando un nodo ya es una ubicacion final (hoja).
			function iniciarSelectorUbicacion(contenedorId, inputId, valorInicial) {
				var contenedor = document.getElementById(contenedorId);
				var input = document.getElementById(inputId);

				function limpiarDesde(nivel) {
					while (contenedor.children.length > nivel) {
						contenedor.removeChild(contenedor.lastChild);
					}
				}

				function marcarFinal(nodo) {
					input.value = nodo ? nodo.id : '';
					var resumen = document.createElement('div');
					if (nodo) {
						var nombres = ubicRutaHasta(nodo.id).map(function (n) { return n.nombre; });
						resumen.innerHTML = '<span style="color:#2f6d4f;">&#10003; ' + nombres.join(' &rsaquo; ') + '</span>';
					} else if (arbolUbicacionProyecto.length === 0) {
						resumen.innerHTML = '<em>Sin ubicaciones para este proyecto.</em>';
					}
					contenedor.appendChild(resumen);
				}

				function renderNivel(padreId, nivel) {
					limpiarDesde(nivel);
					input.value = '';
					var hijos = ubicHijos(padreId);
					if (hijos.length === 0) {
						marcarFinal(padreId !== null ? ubicNodoPorId(padreId) : null);
						return;
					}
					var etiqueta = hijos[0].tipo ? (hijos[0].tipo.charAt(0).toUpperCase() + hijos[0].tipo.slice(1)) : ('Nivel ' + (nivel + 1));
					var wrapper = document.createElement('div');
					wrapper.className = 'form-group';
					var label = document.createElement('label');
					label.textContent = etiqueta + ':';
					var select = document.createElement('select');
					select.className = 'form-control';
					select.style.width = '180px';
					var optVacia = document.createElement('option');
					optVacia.value = '';
					optVacia.textContent = '...';
					select.appendChild(optVacia);
					hijos.forEach(function (h) {
						var opt = document.createElement('option');
						opt.value = h.id;
						opt.textContent = h.nombre;
						select.appendChild(opt);
					});
					select.addEventListener('change', function () {
						limpiarDesde(nivel + 1);
						input.value = '';
						if (select.value === '') { return; }
						renderNivel(parseInt(select.value, 10), nivel + 1);
					});
					wrapper.appendChild(label);
					wrapper.appendChild(select);
					contenedor.appendChild(wrapper);
				}

				if (valorInicial) {
					var ruta = ubicRutaHasta(valorInicial);
					var padreActual = null;
					for (var nivel = 0; nivel < ruta.length; nivel++) {
						renderNivel(padreActual, nivel);
						var select = contenedor.children[nivel] ? contenedor.children[nivel].querySelector('select') : null;
						if (!select) { break; }
						select.value = ruta[nivel].id;
						padreActual = ruta[nivel].id;
					}
					renderNivel(padreActual, ruta.length);
				} else {
					renderNivel(null, 0);
				}
			}

			// Alerta de validacion en el cliente, con la misma presentacion (alerta Bootstrap
			// descartable) que los mensajes del servidor (ver flash_html() en Html.php) -
			// evita el alert() nativo, bloqueante y sin estilo, para este caso que se valida
			// antes de enviar el formulario y por lo tanto nunca pasa por el servidor.
			function mostrarAlertaCliente(tipo, mensaje) {
				var contenedor = document.getElementById('alertaClienteSalidas');
				if (!contenedor) { return; }
				var div = document.createElement('div');
				div.className = 'alert alert-' + tipo + ' alert-dismissible';
				div.setAttribute('role', 'alert');
				var boton = document.createElement('button');
				boton.type = 'button';
				boton.className = 'close';
				boton.setAttribute('data-dismiss', 'alert');
				boton.setAttribute('aria-label', 'Cerrar');
				boton.innerHTML = '<span aria-hidden="true">&times;</span>';
				div.appendChild(boton);
				div.appendChild(document.createTextNode(mensaje));
				contenedor.innerHTML = '';
				contenedor.appendChild(div);
				div.scrollIntoView({ behavior: 'smooth', block: 'center' });
			}

			document.addEventListener('DOMContentLoaded', function () {
				var forms = document.querySelectorAll('form');
				for (var i = 0; i < forms.length; i++) {
					forms[i].addEventListener('submit', function (e) {
						// Estas validaciones son exclusivas del envio final (Guardar): "Listar" (agregar
						// material, incluido presionar Enter en el campo de codigo, que el navegador
						// resuelve como el boton por defecto del formulario) y "Reiniciar salida" no
						// deben quedar bloqueados por un campo todavia sin completar en otra linea.
						// e.submitter identifica el boton que realmente disparo el envio.
						if (!e.submitter || e.submitter.name !== 'btnIngresar') { return; }

						// Destino y Rubro/Actividad son obligatorios siempre, sin importar si el
						// proyecto tiene o no estructura de Ubicacion.
						var destinos = document.querySelectorAll('select.destino-obligatorio');
						var rubros = document.querySelectorAll('select.rubro-obligatorio');
						for (var d = 0; d < destinos.length; d++) {
							if (destinos[d].value === '0' || destinos[d].value === '') {
								mostrarAlertaCliente('warning', 'Debe seleccionar un destino y un rubro/actividad para cada material.');
								e.preventDefault();
								return;
							}
						}
						for (var r = 0; r < rubros.length; r++) {
							if (rubros[r].value === '0' || rubros[r].value === '') {
								mostrarAlertaCliente('warning', 'Debe seleccionar un destino y un rubro/actividad para cada material.');
								e.preventDefault();
								return;
							}
						}

						if (!proyectoTieneEstructuraJS) { return; }
						var campos = document.querySelectorAll('input[id^="ubicacionFinal_"]');
						for (var j = 0; j < campos.length; j++) {
							if (campos[j].value === '') {
								mostrarAlertaCliente('warning', 'Este proyecto tiene ubicaciones activas: seleccione una ubicación completa (hasta el nivel final) para cada material.');
								e.preventDefault();
								return;
							}
						}
					});
				}
			});
			</script>

			<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Codigo</th>
						<th>Descripcion</th>
						<th>Unidad</th>
						<th>Cantidad</th>
						<th>Ubicación<?php echo $proyectoTieneEstructura ? ' *' : ''; ?></th>
						<th>Información adicional *</th>
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

							  $listaSeleccionRubro = $_SESSION['listaSeleccionRubro'];
							  $seleccionRubro = $_SESSION['seleccionRubro'];

							  $seleccionUbicacion = isset($_SESSION['seleccionUbicacion']) ? $_SESSION['seleccionUbicacion'] : [];



						?>
							<?php $i = 0; ?>
							<?php foreach ($listaMaterialSalida as $material) {?>

							<tr>
								<td><?php echo $material->getCodigo(); ?></td>
								<td><?php echo h($material->getDescripcion()); ?></td>
								<td><?php echo h($material->getUnidad()); ?></td>
								<td>
									<div class="col-xs-4">
										<input style="width : 80px" type="number" class="form-control" name="listaCantidadSalida[<?php echo $i ?>]" type="text" value="<?php echo (isset($listaCantidadSalida[$i])) ? $listaCantidadSalida[$i] : ''; ?>" placeholder="">
									</div>
								</td>
								<td>
									<?php $valorInicialUbicacion = (isset($seleccionUbicacion[$i]) && $seleccionUbicacion[$i] !== null) ? (int) $seleccionUbicacion[$i]->getId() : null; ?>
									<input type="hidden" name="listaSeleccionUbicacion[<?php echo $i ?>]" id="ubicacionFinal_<?php echo $i ?>" value="<?php echo $valorInicialUbicacion !== null ? $valorInicialUbicacion : ''; ?>">
									<div id="ubicacionContenedor_<?php echo $i ?>" class="ubicacion-jerarquica"></div>
									<script>
										iniciarSelectorUbicacion('ubicacionContenedor_<?php echo $i ?>', 'ubicacionFinal_<?php echo $i ?>', <?php echo $valorInicialUbicacion !== null ? $valorInicialUbicacion : 'null'; ?>);
									</script>
								</td>
								<td>
									<div class="ubicacion-adicional">
										<small><strong>Información adicional</strong></small>
										<div class="form-group">
											<label>Destino *</label>
											<select style="width : 140px" name="listaSeleccionDestino[<?php echo $i ?>]" class="form-control destino-obligatorio">
													<?php if (isset($seleccionDestino[$i]) && $seleccionDestino[$i] != null) { ?>
													<option selected="" value="<?php echo $seleccionDestino[$i]->getId(); ?>"><?php echo h($seleccionDestino[$i]->getDescripcion()); ?></option>
													<?php }else{ ?>
													<option selected="" value="0">Seleccione...</option>
													<?php } ?>
													<?php foreach ($_SESSION['listaDestino'] as $destino){ ?>
													<option value="<?php echo $destino->getId(); ?>"><?php echo h($destino->getDescripcion()); ?></option>
													<?php } ?>
											</select>
										</div>
										<div class="form-group">
											<label>Rubro / Actividad *</label>
											<select style="width : 140px" name="listaSeleccionRubro[<?php echo $i ?>]" class="form-control rubro-obligatorio">
													<?php if (isset($seleccionRubro[$i]) && $seleccionRubro[$i] != null) { ?>
													<option selected="" value="<?php echo $seleccionRubro[$i]->getId(); ?>"><?php echo h($seleccionRubro[$i]->getDescripcion()); ?></option>
													<?php }else{ ?>
													<option selected="" value="0">Seleccione...</option>
													<?php } ?>
													<?php foreach ($_SESSION['listaRubro'] as $rubro){ ?>
													<option value="<?php echo $rubro->getId(); ?>"><?php echo h($rubro->getDescripcion()); ?></option>
													<?php } ?>
											</select>
										</div>
										<?php
										// Casa/Manzana/Area (modelo legado, mostrado en pantalla como "Area"): ya no forman parte del flujo principal
										// de captura (Ubicacion los reemplaza como destino fisico); se conservan como
										// campos ocultos sin seleccionar para no romper el contrato de datos existente
										// (Model/Controller ya los acepta en null) ni las columnas de BD.
										?>
										<input type="hidden" name="listaSeleccionCasa[<?php echo $i ?>]" value="0">
										<input type="hidden" name="listaSeleccionManzana[<?php echo $i ?>]" value="0">
										<input type="hidden" name="listaSeleccionArea[<?php echo $i ?>]" value="0">
									</div>
								</td>
								<td><a id="boton-eliminar" class="btn btn-danger btn-xs" href="?controller=RegistroSalidas&&action=quitarMaterial&&id=<?php echo $i ?>&&csrf_token=<?php echo Csrf::token(); ?>"><span class="glyphicon glyphicon-erase"> </span> Borrar</a></td>
							</tr>
							<?php $i = $i + 1; ?>
							<?php } ?>

						<?php } ?>

					</tbody>
				</thead>
			</table>
			</div>

		<?php } ?>

		</div>
	</form>
</div>
