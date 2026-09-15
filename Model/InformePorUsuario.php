<?php
/**
* Resumen y detalle de movimientos (entradas, salidas y ajustes) agrupados por usuario. El dato
* de "quien hizo cada movimiento" ya existe desde el primer dia (UsuarioID en registro_entradas/
* registro_salidas/ajuste_inventario) - este informe solo lo explota, no agrega ninguna tabla.
*/
class InformePorUsuario
{
	// Totales de entradas/salidas/ajustes por usuario, opcionalmente acotado a un rango de fechas.
	public static function resumen($fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();

		$condicion = '';
		if ($fechaInicial && $fechaFinal) {
			$condicion = " WHERE Fecha >= :fechaInicial AND Fecha <= :fechaFinal";
		}

		$entradasPorUsuario = $db->prepare("SELECT UsuarioID, COUNT(*) AS Total FROM registro_entradas" . $condicion . " GROUP BY UsuarioID");
		$salidasPorUsuario = $db->prepare("SELECT UsuarioID, COUNT(*) AS Total FROM registro_salidas" . $condicion . " GROUP BY UsuarioID");
		$ajustesPorUsuario = $db->prepare("SELECT UsuarioID, COUNT(*) AS Total FROM ajuste_inventario" . $condicion . " GROUP BY UsuarioID");

		if ($condicion) {
			foreach ([$entradasPorUsuario, $salidasPorUsuario, $ajustesPorUsuario] as $consulta) {
				$consulta->bindValue('fechaInicial', $fechaInicial);
				$consulta->bindValue('fechaFinal', $fechaFinal);
			}
		}

		$entradasPorUsuario->execute();
		$salidasPorUsuario->execute();
		$ajustesPorUsuario->execute();

		$resumen = [];
		foreach ($entradasPorUsuario->fetchAll(PDO::FETCH_ASSOC) as $fila) {
			$resumen[$fila['UsuarioID']]['entradas'] = (int)$fila['Total'];
		}
		foreach ($salidasPorUsuario->fetchAll(PDO::FETCH_ASSOC) as $fila) {
			$resumen[$fila['UsuarioID']]['salidas'] = (int)$fila['Total'];
		}
		foreach ($ajustesPorUsuario->fetchAll(PDO::FETCH_ASSOC) as $fila) {
			$resumen[$fila['UsuarioID']]['ajustes'] = (int)$fila['Total'];
		}

		$resultado = [];
		foreach ($resumen as $usuarioId => $totales) {
			$resultado[] = [
				'usuario' => Usuario::searchByCodigoUser($usuarioId),
				'entradas' => isset($totales['entradas']) ? $totales['entradas'] : 0,
				'salidas' => isset($totales['salidas']) ? $totales['salidas'] : 0,
				'ajustes' => isset($totales['ajustes']) ? $totales['ajustes'] : 0,
			];
		}

		return $resultado;
	}

	// Movimientos detallados de UN usuario, a nivel de LINEA (un material por fila) - mezcla
	// Entrada/Salida/Ajuste, mismo patron de consultas separadas + array_merge + usort ya usado
	// en Kardex::porMaterial() y Movimientos::buscar(), aqui acotado por UsuarioID.
	public static function detalleUsuario($usuarioId, $fechaInicial = null, $fechaFinal = null){
		$db = Db::getConnect();
		$conFecha = !empty($fechaInicial) && !empty($fechaFinal);
		$rangoFecha = $conFecha ? ' AND %s.Fecha BETWEEN :fechaInicial AND :fechaFinal' : '';

		$entradas = $db->prepare("
			SELECT re.ID AS DocumentoID, re.Fecha, re.Hora, 'ENTRADA' AS Tipo,
			       m.Codigo AS MaterialCodigo, m.Descripcion AS MaterialDescripcion, mre.Cantidad
			FROM material_registro_entradas mre
			INNER JOIN registro_entradas re ON mre.Registro_EntradasID = re.ID
			INNER JOIN material m ON m.ID = mre.MaterialID
			WHERE re.UsuarioID = :UsuarioID" . sprintf($rangoFecha, 're') . "
		");
		$entradas->bindValue('UsuarioID', $usuarioId);
		if ($conFecha) { $entradas->bindValue('fechaInicial', $fechaInicial); $entradas->bindValue('fechaFinal', $fechaFinal); }
		$entradas->execute();

		$salidas = $db->prepare("
			SELECT rs.ID AS DocumentoID, rs.Fecha, rs.Hora, 'SALIDA' AS Tipo,
			       m.Codigo AS MaterialCodigo, m.Descripcion AS MaterialDescripcion, mrs.Cantidad
			FROM material_registro_salidas mrs
			INNER JOIN registro_salidas rs ON mrs.Registro_SalidasID = rs.ID
			INNER JOIN material m ON m.ID = mrs.MaterialID
			WHERE rs.UsuarioID = :UsuarioID" . sprintf($rangoFecha, 'rs') . "
		");
		$salidas->bindValue('UsuarioID', $usuarioId);
		if ($conFecha) { $salidas->bindValue('fechaInicial', $fechaInicial); $salidas->bindValue('fechaFinal', $fechaFinal); }
		$salidas->execute();

		$ajustes = $db->prepare("
			SELECT ai.ID AS DocumentoID, ai.Fecha, ai.Hora, ai.Tipo AS Tipo,
			       m.Codigo AS MaterialCodigo, m.Descripcion AS MaterialDescripcion, ai.CantidadAjuste AS Cantidad
			FROM ajuste_inventario ai
			INNER JOIN material m ON m.ID = ai.MaterialID
			WHERE ai.UsuarioID = :UsuarioID" . sprintf($rangoFecha, 'ai') . "
		");
		$ajustes->bindValue('UsuarioID', $usuarioId);
		if ($conFecha) { $ajustes->bindValue('fechaInicial', $fechaInicial); $ajustes->bindValue('fechaFinal', $fechaFinal); }
		$ajustes->execute();

		$movimientos = array_merge($entradas->fetchAll(PDO::FETCH_ASSOC), $salidas->fetchAll(PDO::FETCH_ASSOC), $ajustes->fetchAll(PDO::FETCH_ASSOC));

		usort($movimientos, function($a, $b){
			$cmp = strcmp($b['Fecha'].' '.$b['Hora'], $a['Fecha'].' '.$a['Hora']);
			return $cmp !== 0 ? $cmp : ($b['DocumentoID'] <=> $a['DocumentoID']);
		});

		return $movimientos;
	}
}

?>
