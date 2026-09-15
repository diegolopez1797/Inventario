<?php
/**
* Agrega datos ya existentes (Material, movimientos, proyectos) para la pantalla de inicio.
* No introduce tablas nuevas ni logica de negocio: solo lee y resume.
*/
class Dashboard
{
	// Materiales cuyo saldo esta en o por debajo de su minimo de almacen. Min_Almacen=0
	// significa "sin minimo definido" -> no se considera critico para no llenar la lista de ruido.
	public static function materialesCriticos(){
		$db = Db::getConnect();
		$criticos = [];

		$select = $db->query('SELECT * FROM material WHERE Min_Almacen > 0 AND Saldo <= Min_Almacen ORDER BY Saldo ASC');

		foreach ($select->fetchAll() as $row) {
			$criticos[] = new Material($row['ID'], $row['Codigo'], $row['Descripcion'], $row['Unidad'], $row['Saldo'], $row['Min_Almacen'], $row['Max_Casa'], $row['CostoPromedio']);
		}

		return $criticos;
	}

	// Ultimos movimientos (entradas y salidas mezclados), mas recientes primero.
	public static function movimientosRecientes($limite = 8){
		$db = Db::getConnect();

		$entradas = $db->query("
			SELECT re.ID, re.Fecha, re.Hora, re.UsuarioID, 'ENTRADA' AS Tipo,
			       (SELECT COUNT(*) FROM material_registro_entradas WHERE Registro_EntradasID = re.ID) AS Lineas
			FROM registro_entradas re
			ORDER BY re.Fecha DESC, re.Hora DESC
			LIMIT $limite
		")->fetchAll(PDO::FETCH_ASSOC);

		$salidas = $db->query("
			SELECT rs.ID, rs.Fecha, rs.Hora, rs.UsuarioID, 'SALIDA' AS Tipo,
			       (SELECT COUNT(*) FROM material_registro_salidas WHERE Registro_SalidasID = rs.ID) AS Lineas
			FROM registro_salidas rs
			ORDER BY rs.Fecha DESC, rs.Hora DESC
			LIMIT $limite
		")->fetchAll(PDO::FETCH_ASSOC);

		$movimientos = array_merge($entradas, $salidas);

		usort($movimientos, function($a, $b){
			return strcmp($b['Fecha'].' '.$b['Hora'], $a['Fecha'].' '.$a['Hora']);
		});

		return array_slice($movimientos, 0, $limite);
	}

	public static function proyectosActivos(){
		return Proyecto::all();
	}
}

?>
