<?php
/**
* Historial cronologico de movimientos de un material, con saldo corriente.
* No hay fotos de saldo por fecha en el sistema actual, asi que el saldo de cada linea
* se reconstruye HACIA ATRAS a partir del saldo actual del material, deshaciendo el
* EfectoNeto de cada movimiento (Entrada=+Cantidad, Salida=-Cantidad, Ajuste=CantidadAjuste
* con su propio signo). Limitacion conocida: si el saldo de un material fue corregido
* alguna vez por edicion manual del catalogo (fuera de Entrada/Salida/Ajuste), el saldo
* reconstruido antes de esa correccion no sera exacto.
*/
class Kardex
{
	public static function porMaterial($materialId){
		$db = Db::getConnect();

		$entradas = $db->prepare("
			SELECT re.Fecha, re.Hora, mre.Cantidad, 'ENTRADA' AS Tipo, re.ID AS RegistroID, re.UsuarioID, NULL AS Motivo, NULL AS UbicacionID, mre.CostoUnitario
			FROM material_registro_entradas mre
			INNER JOIN registro_entradas re ON mre.Registro_EntradasID = re.ID
			WHERE mre.MaterialID = :MaterialID
		");
		$entradas->bindValue('MaterialID', $materialId);
		$entradas->execute();

		$salidas = $db->prepare("
			SELECT rs.Fecha, rs.Hora, mrs.Cantidad, 'SALIDA' AS Tipo, rs.ID AS RegistroID, rs.UsuarioID, NULL AS Motivo, mrs.UbicacionID AS UbicacionID, mrs.CostoUnitario
			FROM material_registro_salidas mrs
			INNER JOIN registro_salidas rs ON mrs.Registro_SalidasID = rs.ID
			WHERE mrs.MaterialID = :MaterialID
		");
		$salidas->bindValue('MaterialID', $materialId);
		$salidas->execute();

		$ajustes = $db->prepare("
			SELECT Fecha, Hora, CantidadAjuste, Tipo, ID AS RegistroID, UsuarioID, Motivo, NULL AS UbicacionID, CostoUnitario
			FROM ajuste_inventario
			WHERE MaterialID = :MaterialID
		");
		$ajustes->bindValue('MaterialID', $materialId);
		$ajustes->execute();

		$movimientos = array_merge($entradas->fetchAll(PDO::FETCH_ASSOC), $salidas->fetchAll(PDO::FETCH_ASSOC), $ajustes->fetchAll(PDO::FETCH_ASSOC));

		// Normaliza cada movimiento a un EfectoNeto unico (positivo = aumento de saldo,
		// negativo = disminucion), para que la reconstruccion hacia atras no dependa de
		// un if/else binario por tipo. Entrada/Salida ya traian 'Cantidad' sin signo;
		// Ajuste ya trae 'CantidadAjuste' con su propio signo (se expone tambien como
		// 'Cantidad' con signo, para que la vista la muestre igual que los demas).
		foreach ($movimientos as &$mov) {
			if ($mov['Tipo'] === 'ENTRADA') {
				$mov['EfectoNeto'] = (int) $mov['Cantidad'];
			} elseif ($mov['Tipo'] === 'SALIDA') {
				$mov['EfectoNeto'] = -1 * (int) $mov['Cantidad'];
			} else {
				$mov['EfectoNeto'] = (int) $mov['CantidadAjuste'];
				$mov['Cantidad'] = $mov['CantidadAjuste'];
			}
		}
		unset($mov);

		// Desempate secundario por RegistroID cuando Fecha+Hora coinciden exactamente
		// (antes solo dependia del orden de array_merge, que no era un criterio real).
		usort($movimientos, function($a, $b){
			$cmp = strcmp($a['Fecha'].' '.$a['Hora'], $b['Fecha'].' '.$b['Hora']);
			return $cmp !== 0 ? $cmp : ($a['RegistroID'] <=> $b['RegistroID']);
		});

		$material = Material::searchById($materialId);
		$saldoCorriente = $material->getSaldo();

		for ($i = count($movimientos) - 1; $i >= 0; $i--) {
			$movimientos[$i]['SaldoDespues'] = $saldoCorriente;
			$saldoCorriente -= $movimientos[$i]['EfectoNeto'];
		}

		return $movimientos;
	}

	// Filtra un conjunto de movimientos YA devuelto por porMaterial() (con SaldoDespues ya
	// calculado) por fecha y/o Ubicacion, para presentacion. CRITICO: nunca se aplica antes de
	// la reconstruccion ni modifica sus entradas - la auditoria de la Fase 3 establecio que
	// filtrar las 3 consultas de origen por fecha rompe el anclaje del saldo en material.Saldo
	// (una linea filtrada como "la mas reciente" heredaria el saldo de HOY, no el real de esa
	// fecha, si hubo movimientos posteriores excluidos por el filtro).
	public static function filtrar($movimientos, $fechaInicial = null, $fechaFinal = null, $ubicacionId = null){
		return array_values(array_filter($movimientos, function($mov) use ($fechaInicial, $fechaFinal, $ubicacionId){
			if (!empty($fechaInicial) && $mov['Fecha'] < $fechaInicial) return false;
			if (!empty($fechaFinal) && $mov['Fecha'] > $fechaFinal) return false;
			if (!empty($ubicacionId) && (empty($mov['UbicacionID']) || (int) $mov['UbicacionID'] !== (int) $ubicacionId)) return false;
			return true;
		}));
	}
}

?>
