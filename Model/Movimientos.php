<?php
/**
* Informe Movimientos (Fase 2.1 / Fase 3 seccion 6): une Entradas y Salidas de material en un
* solo listado filtrable. No existe una tabla que ya las combine, asi que se ejecutan 2 consultas
* independientes (Entrada y Salida no comparten columnas: Proveedor solo existe en Entrada;
* Contratista/Proyecto/Ubicacion/Rubro solo existen en Salida) y se combinan en PHP - mismo patron
* ya usado en Kardex::porMaterial() para mezclar Entrada/Salida/Ajuste.
*
* buscar() siempre devuelve el conjunto COMPLETO que cumple los filtros, sin LIMIT de SQL: la
* correccion de la auditoria de Fase 3 establecio que paginar cada lado por separado (ej. "10
* Entradas + 10 Salidas") puede excluir de una pagina movimientos que si le corresponden
* cronologicamente. El Controller es quien aplica array_slice() sobre el resultado ya combinado
* y ordenado - el mismo patron ya usado para los 6 informes de movimiento paginados esta sesion.
*/
class Movimientos
{
	// Entrada no tiene ContratistaID/ProyectoID/UbicacionID/RubroID; Salida no tiene ProveedorID.
	// Se seleccionan como NULL explicito (no ausentes) para que ambos lados del array_merge
	// tengan las mismas claves - mismo criterio que Kardex::porMaterial().
	private static function consultarEntradas($filtros){
		$db = Db::getConnect();

		$where = ['1=1'];
		$params = [];

		if (!empty($filtros['fechaInicial'])) {
			$where[] = 're.Fecha >= :fechaInicial';
			$params['fechaInicial'] = $filtros['fechaInicial'];
		}
		if (!empty($filtros['fechaFinal'])) {
			$where[] = 're.Fecha <= :fechaFinal';
			$params['fechaFinal'] = $filtros['fechaFinal'];
		}
		if (!empty($filtros['materialId'])) {
			$where[] = 'm.ID = :materialId';
			$params['materialId'] = $filtros['materialId'];
		}
		if (!empty($filtros['documento'])) {
			$where[] = 're.ID = :documento';
			$params['documento'] = $filtros['documento'];
		}
		if (!empty($filtros['usuarioId'])) {
			$where[] = 're.UsuarioID = :usuarioId';
			$params['usuarioId'] = $filtros['usuarioId'];
		}
		if (!empty($filtros['proveedorId'])) {
			$where[] = 're.ProveedorID = :proveedorId';
			$params['proveedorId'] = $filtros['proveedorId'];
		}
		if (!empty($filtros['destinoId'])) {
			$where[] = 'mre.DestinoID = :destinoId';
			$params['destinoId'] = $filtros['destinoId'];
		}

		$sql = "
			SELECT
				re.ID AS DocumentoID, re.Fecha, re.Hora, re.UsuarioID,
				re.ProveedorID, NULL AS ContratistaID, NULL AS ProyectoID, NULL AS UbicacionID, NULL AS RubroID,
				m.ID AS MaterialID, m.Codigo AS MaterialCodigo, m.Descripcion AS MaterialDescripcion, m.Unidad AS MaterialUnidad,
				mre.Cantidad, mre.DestinoID, mre.CostoUnitario,
				'ENTRADA' AS Tipo
			FROM material_registro_entradas mre
			INNER JOIN registro_entradas re ON mre.Registro_EntradasID = re.ID
			INNER JOIN material m ON m.ID = mre.MaterialID
			WHERE " . implode(' AND ', $where);

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		return $select->fetchAll(PDO::FETCH_ASSOC);
	}

	private static function consultarSalidas($filtros){
		$db = Db::getConnect();

		$where = ['1=1'];
		$params = [];

		if (!empty($filtros['fechaInicial'])) {
			$where[] = 'rs.Fecha >= :fechaInicial';
			$params['fechaInicial'] = $filtros['fechaInicial'];
		}
		if (!empty($filtros['fechaFinal'])) {
			$where[] = 'rs.Fecha <= :fechaFinal';
			$params['fechaFinal'] = $filtros['fechaFinal'];
		}
		if (!empty($filtros['materialId'])) {
			$where[] = 'm.ID = :materialId';
			$params['materialId'] = $filtros['materialId'];
		}
		if (!empty($filtros['documento'])) {
			$where[] = 'rs.ID = :documento';
			$params['documento'] = $filtros['documento'];
		}
		if (!empty($filtros['usuarioId'])) {
			$where[] = 'rs.UsuarioID = :usuarioId';
			$params['usuarioId'] = $filtros['usuarioId'];
		}
		if (!empty($filtros['contratistaId'])) {
			$where[] = 'rs.ContratistaID = :contratistaId';
			$params['contratistaId'] = $filtros['contratistaId'];
		}
		if (!empty($filtros['proyectoId'])) {
			$where[] = 'rs.ProyectoID = :proyectoId';
			$params['proyectoId'] = $filtros['proyectoId'];
		}
		if (!empty($filtros['ubicacionId'])) {
			$where[] = 'mrs.UbicacionID = :ubicacionId';
			$params['ubicacionId'] = $filtros['ubicacionId'];
		}
		if (!empty($filtros['rubroId'])) {
			$where[] = 'mrs.RubroID = :rubroId';
			$params['rubroId'] = $filtros['rubroId'];
		}
		if (!empty($filtros['destinoId'])) {
			$where[] = 'mrs.DestinoID = :destinoId';
			$params['destinoId'] = $filtros['destinoId'];
		}

		$sql = "
			SELECT
				rs.ID AS DocumentoID, rs.Fecha, rs.Hora, rs.UsuarioID,
				NULL AS ProveedorID, rs.ContratistaID, rs.ProyectoID, mrs.UbicacionID, mrs.RubroID,
				m.ID AS MaterialID, m.Codigo AS MaterialCodigo, m.Descripcion AS MaterialDescripcion, m.Unidad AS MaterialUnidad,
				mrs.Cantidad, mrs.DestinoID, mrs.CostoUnitario,
				'SALIDA' AS Tipo
			FROM material_registro_salidas mrs
			INNER JOIN registro_salidas rs ON mrs.Registro_SalidasID = rs.ID
			INNER JOIN material m ON m.ID = mrs.MaterialID
			WHERE " . implode(' AND ', $where);

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		return $select->fetchAll(PDO::FETCH_ASSOC);
	}

	// Devuelve el listado COMPLETO (sin LIMIT) que cumple los filtros, ya combinado y ordenado.
	// $filtros['tipo'] acepta 'entrada', 'salida' o vacio/'ambos'. Ciertos filtros solo tienen
	// sentido para un tipo (Contratista/Proyecto/Ubicacion/Rubro -> solo Salida; Proveedor -> solo
	// Entrada): si vienen presentes, el otro lado se excluye por completo en vez de ignorarlos,
	// para nunca mezclar un filtro con datos de un tipo que no lo tiene.
	public static function buscar($filtros = [], $orden = 'fecha', $direccion = 'DESC'){
		$tipo = isset($filtros['tipo']) ? $filtros['tipo'] : '';

		$incluirEntradas = $tipo !== 'salida';
		$incluirSalidas = $tipo !== 'entrada';

		if (!empty($filtros['contratistaId']) || !empty($filtros['proyectoId']) || !empty($filtros['ubicacionId']) || !empty($filtros['rubroId'])) {
			$incluirEntradas = false;
		}
		if (!empty($filtros['proveedorId'])) {
			$incluirSalidas = false;
		}

		$movimientos = [];
		if ($incluirEntradas) {
			$movimientos = array_merge($movimientos, self::consultarEntradas($filtros));
		}
		if ($incluirSalidas) {
			$movimientos = array_merge($movimientos, self::consultarSalidas($filtros));
		}

		$direccion = strtoupper($direccion) === 'ASC' ? 'ASC' : 'DESC';
		$claveOrden = $orden === 'documento' ? 'DocumentoID' : null;

		usort($movimientos, function($a, $b) use ($claveOrden, $direccion){
			if ($claveOrden !== null) {
				$cmp = $a[$claveOrden] <=> $b[$claveOrden];
			} else {
				$cmp = strcmp($a['Fecha'] . ' ' . $a['Hora'], $b['Fecha'] . ' ' . $b['Hora']);
				if ($cmp === 0) {
					$cmp = $a['DocumentoID'] <=> $b['DocumentoID'];
				}
			}
			return $direccion === 'ASC' ? $cmp : -$cmp;
		});

		return $movimientos;
	}
}

?>
