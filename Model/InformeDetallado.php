<?php
/**
* Salidas por Ubicación (Fase 2.1 seccion 6-7 #3 / Fase 3 seccion 8). Reemplaza "Por material y
* proyecto" y "Por casa" - los metodos basados en Casa/Manzana (searchByMaterial(),
* searchByMaterialDetalle(), searchByCasa()) se eliminaron junto con esos 2 informes: nada mas
* los llamaba. Ya no se instancian objetos InformeDetallado - ambos metodos devuelven arreglos
* asociativos directamente, consumidos por InformeDetalladoController/las Views del informe nuevo.
*/
class InformeDetallado
{
	// Reemplaza el recorrido Manzana::all() x Casa::all() por un GROUP BY real sobre
	// UbicacionID - el concepto generico que reemplaza a Casa/Manzana/Torre/Etapa en toda la
	// logica de este informe. Casa/Manzana/Area siguen existiendo solo como dato historico
	// visible en otras pantallas, nunca aqui.
	public static function porUbicacion($proyectoId, $filtros = []){
		$db = Db::getConnect();

		$where = ['rs.ProyectoID = :proyectoId'];
		$params = ['proyectoId' => $proyectoId];

		if (!empty($filtros['ubicacionId'])) {
			$where[] = 'mrs.UbicacionID = :ubicacionId';
			$params['ubicacionId'] = $filtros['ubicacionId'];
		}
		if (!empty($filtros['materialId'])) {
			$where[] = 'mrs.MaterialID = :materialId';
			$params['materialId'] = $filtros['materialId'];
		}
		if (!empty($filtros['rubroId'])) {
			$where[] = 'mrs.RubroID = :rubroId';
			$params['rubroId'] = $filtros['rubroId'];
		}
		if (!empty($filtros['fechaInicial'])) {
			$where[] = 'rs.Fecha >= :fechaInicial';
			$params['fechaInicial'] = $filtros['fechaInicial'];
		}
		if (!empty($filtros['fechaFinal'])) {
			$where[] = 'rs.Fecha <= :fechaFinal';
			$params['fechaFinal'] = $filtros['fechaFinal'];
		}

		$sql = "
			SELECT mrs.UbicacionID, mrs.MaterialID,
			       m.Codigo AS MaterialCodigo, m.Descripcion AS MaterialDescripcion, m.Unidad AS MaterialUnidad,
			       SUM(mrs.Cantidad) AS CantidadTotal,
			       COUNT(DISTINCT mrs.Registro_SalidasID) AS TotalDocumentos
			FROM material_registro_salidas mrs
			INNER JOIN registro_salidas rs ON mrs.Registro_SalidasID = rs.ID
			INNER JOIN material m ON m.ID = mrs.MaterialID
			WHERE " . implode(' AND ', $where) . "
			GROUP BY mrs.UbicacionID, mrs.MaterialID
		";

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		return $select->fetchAll(PDO::FETCH_ASSOC);
	}

	// Lineas individuales detras de un grupo (Ubicacion, Material) de porUbicacion() - el
	// drill-down de "ver el detalle" de una fila agrupada. $ubicacionId puede ser null (grupo
	// "Sin ubicación registrada").
	public static function lineasPorUbicacionMaterial($proyectoId, $ubicacionId, $materialId, $filtros = []){
		$db = Db::getConnect();

		$where = ['rs.ProyectoID = :proyectoId', 'mrs.MaterialID = :materialId'];
		$params = ['proyectoId' => $proyectoId, 'materialId' => $materialId];

		if ($ubicacionId !== null) {
			$where[] = 'mrs.UbicacionID = :ubicacionId';
			$params['ubicacionId'] = $ubicacionId;
		} else {
			$where[] = 'mrs.UbicacionID IS NULL';
		}
		if (!empty($filtros['fechaInicial'])) {
			$where[] = 'rs.Fecha >= :fechaInicial';
			$params['fechaInicial'] = $filtros['fechaInicial'];
		}
		if (!empty($filtros['fechaFinal'])) {
			$where[] = 'rs.Fecha <= :fechaFinal';
			$params['fechaFinal'] = $filtros['fechaFinal'];
		}
		if (!empty($filtros['rubroId'])) {
			$where[] = 'mrs.RubroID = :rubroId';
			$params['rubroId'] = $filtros['rubroId'];
		}

		$sql = "
			SELECT rs.ID AS DocumentoID, rs.Fecha, rs.Hora, rs.UsuarioID, rs.ContratistaID,
			       mrs.Cantidad, mrs.DestinoID, mrs.RubroID
			FROM material_registro_salidas mrs
			INNER JOIN registro_salidas rs ON mrs.Registro_SalidasID = rs.ID
			WHERE " . implode(' AND ', $where) . "
			ORDER BY rs.Fecha DESC, rs.Hora DESC
		";

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		return $select->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>
