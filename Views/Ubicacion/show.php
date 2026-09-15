<div class="container">
	<h2>Ubicaciones por Proyecto</h2>
	<p>
		<a class="btn btn-default btn-sm" href="?controller=TipoUbicacion&&action=show"><span class="glyphicon glyphicon-list"> </span> Tipos de ubicación</a>
	</p>

	<?php
	// Renderiza un nodo y, si tiene hijos, un toggle colapsable (cerrado por defecto) para
	// revelarlos. $indice ya trae TODO el arbol del proyecto (una sola consulta, ver
	// Ubicacion::indicePorPadre) - nunca se vuelve a consultar la base por nodo.
	function renderUbicacionNodo($nodo, $indice) {
		$hijos = isset($indice[(int)$nodo->getId()]) ? $indice[(int)$nodo->getId()] : [];
		$tieneHijos = !empty($hijos);
		$idColapso = 'ubic-hijos-' . (int)$nodo->getId();

		echo '<li' . ($nodo->getActivo() ? '' : ' style="opacity:0.55;"') . '>';

		if ($tieneHijos) {
			echo '<a role="button" data-toggle="collapse" href="#' . $idColapso . '" aria-expanded="false" aria-controls="' . $idColapso . '">';
			echo '<span class="glyphicon glyphicon-triangle-right"></span> ';
			echo '</a> ';
		} else {
			echo '<span class="glyphicon glyphicon-minus" style="color:#ccc;"></span> ';
		}

		echo '<strong>' . h($nodo->getNombre()) . '</strong> <small>(' . h($nodo->getTipo()) . ')</small>';
		echo ($nodo->getActivo() ? '' : ' <em>(inactivo)</em>');

		echo ' &nbsp; ';
		if ($nodo->getActivo()) {
			echo '<a class="btn btn-success btn-xs" href="?controller=Ubicacion&&action=register&&proyecto=' . (int)$nodo->getProyectoId() . '&&padre=' . (int)$nodo->getId() . '">+ Agregar hijo</a> ';
			echo '<a class="btn btn-info btn-xs" href="?controller=Ubicacion&&action=generarMasivoShow&&proyecto=' . (int)$nodo->getProyectoId() . '&&padre=' . (int)$nodo->getId() . '">+ Generar estructura masiva</a> ';
		}
		echo '<a class="btn btn-warning btn-xs" href="?controller=Ubicacion&&action=updateshow&&id=' . (int)$nodo->getId() . '"><span class="glyphicon glyphicon-wrench"> </span> Editar</a> ';
		echo '<a class="btn btn-danger btn-xs" href="?controller=Ubicacion&&action=delete&&id=' . (int)$nodo->getId() . '&&csrf_token=' . Csrf::token() . '"><span class="glyphicon glyphicon-trash"> </span> Eliminar</a>';
		if ($nodo->getActivo()) {
			echo ' <a class="btn btn-default btn-xs" href="?controller=Ubicacion&&action=desactivar&&id=' . (int)$nodo->getId() . '&&csrf_token=' . Csrf::token() . '" onclick="return confirm(\'¿Desactivar esta ubicación? Dejará de ofrecerse en selectores nuevos pero conserva su historial.\');">Desactivar</a>';
		}

		if ($tieneHijos) {
			echo '<ul id="' . $idColapso . '" class="collapse ubicacion-rama">';
			foreach ($hijos as $hijo) {
				renderUbicacionNodo($hijo, $indice);
			}
			echo '</ul>';
		}

		echo '</li>';
	}
	?>

	<style>
		.ubicacion-arbol, .ubicacion-arbol ul { list-style: none; margin: 0; padding-left: 0; }
		.ubicacion-arbol ul.ubicacion-rama { padding-left: 24px; margin-top: 6px; }
		.ubicacion-arbol li { margin-bottom: 8px; }
	</style>

	<div class="panel-group" id="panelProyectosUbicacion" role="tablist" aria-multiselectable="true">
		<?php foreach ($listaProyecto as $proyecto) {
			$indice = Ubicacion::indicePorPadre($proyecto->getId());
			$raices = isset($indice['raiz']) ? $indice['raiz'] : [];
			$totalNodos = 0;
			foreach ($indice as $clave => $grupo) { $totalNodos += count($grupo); }
			$idPanel = 'ubic-proyecto-' . (int)$proyecto->getId();
		?>
		<div class="panel panel-default">
			<div class="panel-heading" role="tab">
				<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#panelProyectosUbicacion" href="#<?php echo $idPanel; ?>" aria-expanded="false">
						<?php echo h($proyecto->getDescripcion()); ?>
						<span class="badge"><?php echo $totalNodos; ?> ubicaci<?php echo $totalNodos == 1 ? 'ón' : 'ones'; ?></span>
					</a>
				</h4>
			</div>
			<div id="<?php echo $idPanel; ?>" class="panel-collapse collapse" role="tabpanel">
				<div class="panel-body">
					<p>
						<a class="btn btn-success btn-sm" href="?controller=Ubicacion&&action=register&&proyecto=<?php echo (int)$proyecto->getId(); ?>"><span class="glyphicon glyphicon-plus-sign"> </span> Agregar nivel raíz</a>
						<a class="btn btn-info btn-sm" href="?controller=Ubicacion&&action=generarMasivoShow&&proyecto=<?php echo (int)$proyecto->getId(); ?>"><span class="glyphicon glyphicon-plus-sign"> </span> Generar estructura masiva</a>
					</p>
					<?php if (empty($raices)) { ?>
					<p><em>Este proyecto todavía no tiene ubicaciones configuradas.</em></p>
					<?php } else { ?>
					<ul class="ubicacion-arbol">
						<?php foreach ($raices as $raiz) { renderUbicacionNodo($raiz, $indice); } ?>
					</ul>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
