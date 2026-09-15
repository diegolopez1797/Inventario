<?php
/**
* Nivel generico de ubicacion dentro de un proyecto (etapa, manzana, casa, torre, piso,
* unidad, zona, sector, frente...). Se auto-referencia via PadreID para formar un arbol
* cuya profundidad y nomenclatura decide cada proyecto, no el esquema.
*/
class Ubicacion
{
	private $Id;
	private $ProyectoId;
	private $PadreId;
	private $Nombre;
	private $Codigo;
	private $Orden;
	private $Nivel;
	private $Tipo;
	private $Activo;


	function __construct($Id, $ProyectoId, $PadreId, $Nombre, $Tipo, $Activo, $Codigo = null, $Orden = null, $Nivel = 0)
	{
		$this->setId($Id);
		$this->setProyectoId($ProyectoId);
		$this->setPadreId($PadreId);
		$this->setNombre($Nombre);
		$this->setTipo($Tipo);
		$this->setActivo($Activo);
		$this->setCodigo($Codigo);
		$this->setOrden($Orden);
		$this->setNivel($Nivel);
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getProyectoId(){
		return $this->ProyectoId;
	}

	public function setProyectoId($ProyectoId){
		$this->ProyectoId = $ProyectoId;
	}

	public function getPadreId(){
		return $this->PadreId;
	}

	public function setPadreId($PadreId){
		$this->PadreId = $PadreId;
	}

	public function getNombre(){
		return $this->Nombre;
	}

	public function setNombre($Nombre){
		$this->Nombre = $Nombre;
	}

	public function getCodigo(){
		return $this->Codigo;
	}

	public function setCodigo($Codigo){
		$this->Codigo = $Codigo;
	}

	public function getOrden(){
		return $this->Orden;
	}

	public function setOrden($Orden){
		$this->Orden = $Orden;
	}

	public function getNivel(){
		return $this->Nivel;
	}

	public function setNivel($Nivel){
		$this->Nivel = $Nivel;
	}

	public function getTipo(){
		return $this->Tipo;
	}

	public function setTipo($Tipo){
		$this->Tipo = $Tipo;
	}

	public function getActivo(){
		return $this->Activo;
	}

	public function setActivo($Activo){
		$this->Activo = $Activo;
	}

	private static function deFila($fila){
		return new Ubicacion($fila['ID'], $fila['ProyectoID'], $fila['PadreID'], $fila['Nombre'], $fila['Tipo'], $fila['Activo'], $fila['Codigo'], $fila['Orden'], $fila['Nivel']);
	}

	public static function save($ubicacion){
		$db = Db::getConnect();

		$nivel = self::calcularNivel($db, $ubicacion->getProyectoId(), $ubicacion->getPadreId());

		$insert = $db->prepare('INSERT INTO ubicacion (ID, ProyectoID, PadreID, Nombre, Codigo, Orden, Nivel, Tipo, Activo) VALUES (null, :proyectoId, :padreId, :nombre, :codigo, :orden, :nivel, :tipo, :activo)');
		$insert->bindValue('proyectoId', $ubicacion->getProyectoId());
		$insert->bindValue('padreId', $ubicacion->getPadreId(), $ubicacion->getPadreId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('nombre', $ubicacion->getNombre());
		$insert->bindValue('codigo', $ubicacion->getCodigo(), $ubicacion->getCodigo() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$insert->bindValue('orden', $ubicacion->getOrden(), $ubicacion->getOrden() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('nivel', $nivel);
		$insert->bindValue('tipo', $ubicacion->getTipo());
		$insert->bindValue('activo', $ubicacion->getActivo());
		$insert->execute();

		return $insert;
	}

	// Calcula ProyectoID esperado y Nivel a partir del padre real (si existe), y valida
	// que el ProyectoID declarado coincida - unico punto de esta regla, usado por save(),
	// update() (re-parenting) y generarEstructura(). Lanza Exception si hay inconsistencia,
	// nunca confia en el valor de proyecto que venga del formulario/URL sin verificarlo
	// contra el padre real leido de la base de datos.
	private static function calcularNivel($db, $proyectoId, $padreId){
		if ($padreId === null || $padreId === '') {
			return 0;
		}

		$select = $db->prepare('SELECT ProyectoID, Nivel FROM ubicacion WHERE ID=:ID');
		$select->bindValue('ID', $padreId);
		$select->execute();
		$padre = $select->fetch();

		if ($padre === false) {
			throw new Exception('La ubicacion padre indicada no existe.');
		}

		if ((int) $padre['ProyectoID'] !== (int) $proyectoId) {
			throw new Exception('Una ubicacion debe pertenecer al mismo proyecto que su nivel superior.');
		}

		return (int) $padre['Nivel'] + 1;
	}

	public static function update($ubicacion){
		$db = Db::getConnect();

		// El re-parenting valida la misma regla de proyecto y recalcula Nivel; si PadreID
		// no cambio, calcularNivel() sencillamente confirma que sigue siendo consistente.
		$nivel = self::calcularNivel($db, $ubicacion->getProyectoId(), $ubicacion->getPadreId());

		$update = $db->prepare('UPDATE ubicacion SET Nombre=:nombre, Codigo=:codigo, Orden=:orden, Nivel=:nivel, Tipo=:tipo, PadreID=:padreId WHERE ID=:id');
		$update->bindValue('nombre', $ubicacion->getNombre());
		$update->bindValue('codigo', $ubicacion->getCodigo(), $ubicacion->getCodigo() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$update->bindValue('orden', $ubicacion->getOrden(), $ubicacion->getOrden() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$update->bindValue('nivel', $nivel);
		$update->bindValue('tipo', $ubicacion->getTipo());
		$update->bindValue('padreId', $ubicacion->getPadreId(), $ubicacion->getPadreId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$update->bindValue('id', $ubicacion->getId());
		$update->execute();

		// Si el Nivel cambio (por un re-parenting), los descendientes quedan con un Nivel
		// cacheado desactualizado - se recalculan en cascada, sin tocar su Nombre/Tipo/PadreID.
		self::recalcularNivelDescendientes($db, $ubicacion->getId(), $nivel);
	}

	private static function recalcularNivelDescendientes($db, $padreId, $nivelPadre){
		$hijos = $db->prepare('SELECT ID FROM ubicacion WHERE PadreID=:PadreID');
		$hijos->bindValue('PadreID', $padreId);
		$hijos->execute();

		foreach ($hijos->fetchAll(PDO::FETCH_COLUMN) as $hijoId) {
			$nuevoNivel = $nivelPadre + 1;
			$db->prepare('UPDATE ubicacion SET Nivel=:n WHERE ID=:id')->execute(['n' => $nuevoNivel, 'id' => $hijoId]);
			self::recalcularNivelDescendientes($db, $hijoId, $nuevoNivel);
		}
	}

	// Verifica que $nuevoPadreId no sea $ubicacionId ni ninguno de sus propios descendientes
	// (evitaria un ciclo: una ubicacion no puede terminar siendo su propio ancestro).
	public static function generariaCiclo($ubicacionId, $nuevoPadreId){
		if ($nuevoPadreId === null || $nuevoPadreId === '') {
			return false;
		}
		if ((int) $nuevoPadreId === (int) $ubicacionId) {
			return true;
		}

		$db = Db::getConnect();
		$actualId = $nuevoPadreId;
		$visitados = [];
		while ($actualId !== null) {
			if ((int) $actualId === (int) $ubicacionId) {
				return true;
			}
			if (isset($visitados[$actualId])) {
				break; // proteccion defensiva ante datos corruptos preexistentes
			}
			$visitados[$actualId] = true;
			$actualId = $db->query("SELECT PadreID FROM ubicacion WHERE ID=$actualId")->fetchColumn();
			if ($actualId === false) {
				break;
			}
		}

		return false;
	}

	// Una ubicacion con movimientos historicos (salidas o solicitudes) no debe moverse de
	// padre, para no reescribir retroactivamente el significado de un movimiento ya registrado.
	public static function tieneMovimientos($id){
		$db = Db::getConnect();
		$enSalidas = $db->prepare('SELECT COUNT(*) FROM material_registro_salidas WHERE UbicacionID=:id');
		$enSalidas->execute(['id' => $id]);
		if ($enSalidas->fetchColumn() > 0) return true;

		$enSolicitudes = $db->prepare('SELECT COUNT(*) FROM solicitud WHERE UbicacionID=:id');
		$enSolicitudes->execute(['id' => $id]);
		return $enSolicitudes->fetchColumn() > 0;
	}

	public static function delete($id){
		$db = Db::getConnect();

		$delete = $db->prepare('DELETE FROM ubicacion WHERE ID=:id');
		$delete->bindValue('id', $id);
		$delete->execute();

		return $delete;
	}

	// Alternativa no destructiva al delete(): nunca borra, solo deja de ofrecerse en
	// selectores nuevos (hojasConRuta ya filtra Activo=1). Conserva el historial intacto.
	public static function desactivar($id){
		$db = Db::getConnect();
		$update = $db->prepare('UPDATE ubicacion SET Activo=0 WHERE ID=:id');
		$update->bindValue('id', $id);
		$update->execute();
	}

	public static function searchById($id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT * FROM ubicacion WHERE ID=:id');
		$select->bindValue('id', $id);
		$select->execute();

		$row = $select->fetch();

		return self::deFila($row);
	}

	// Nodos raiz (sin padre) de un proyecto.
	public static function raicesPorProyecto($proyectoId){
		$db = Db::getConnect();
		$lista = [];

		$select = $db->prepare('SELECT * FROM ubicacion WHERE ProyectoID=:proyectoId AND PadreID IS NULL ORDER BY Orden IS NULL, Orden, Nombre');
		$select->bindValue('proyectoId', $proyectoId);
		$select->execute();

		foreach ($select->fetchAll() as $row) {
			$lista[] = self::deFila($row);
		}

		return $lista;
	}

	// Todos los nodos de un proyecto (cualquier nivel), ordenados por Nivel/Orden/Nombre.
	// Usado para construir el selector de "nuevo padre" al re-parentar una ubicacion,
	// donde hace falta ver el arbol completo del proyecto, no solo un nivel.
	public static function todosPorProyecto($proyectoId){
		$db = Db::getConnect();
		$lista = [];

		$select = $db->prepare('SELECT * FROM ubicacion WHERE ProyectoID=:proyectoId ORDER BY Nivel, Orden IS NULL, Orden, Nombre');
		$select->bindValue('proyectoId', $proyectoId);
		$select->execute();

		foreach ($select->fetchAll() as $row) {
			$lista[] = self::deFila($row);
		}

		return $lista;
	}

	// Indice PadreID -> hijos directos de TODO el arbol de un proyecto, construido con la
	// UNICA consulta de todosPorProyecto() en vez de una consulta por nodo (usado por la
	// vista de arbol colapsable, que antes llamaba a hijos() recursivamente: para un arbol
	// de 187 nodos eso eran 187 consultas solo para pintar la pantalla). La clave 'raiz'
	// agrupa los nodos sin PadreID; el resto usa el (int) PadreID real.
	public static function indicePorPadre($proyectoId){
		$indice = [];
		foreach (self::todosPorProyecto($proyectoId) as $nodo) {
			$clave = $nodo->getPadreId() === null ? 'raiz' : (int) $nodo->getPadreId();
			$indice[$clave][] = $nodo;
		}
		return $indice;
	}

	// Hijos directos de un nodo (incluye inactivos, para que la gestion pueda verlos y
	// reactivarlos si hiciera falta - solo hojasConRuta(), usada por los selectores de
	// movimientos, excluye inactivos).
	public static function hijos($padreId){
		$db = Db::getConnect();
		$lista = [];

		$select = $db->prepare('SELECT * FROM ubicacion WHERE PadreID=:padreId ORDER BY Orden IS NULL, Orden, Nombre');
		$select->bindValue('padreId', $padreId);
		$select->execute();

		foreach ($select->fetchAll() as $row) {
			$lista[] = self::deFila($row);
		}

		return $lista;
	}

	// Nombres de los hijos directos de un padre (o raices de un proyecto si $padreId es
	// null), en minuscula, para chequeo de duplicados/conflictos antes de generar.
	private static function nombresHijosExistentes($proyectoId, $padreId){
		$db = Db::getConnect();
		if ($padreId === null) {
			$select = $db->prepare('SELECT LOWER(Nombre) FROM ubicacion WHERE ProyectoID=:p AND PadreID IS NULL');
			$select->execute(['p' => $proyectoId]);
		} else {
			$select = $db->prepare('SELECT LOWER(Nombre) FROM ubicacion WHERE PadreID=:padreId');
			$select->execute(['padreId' => $padreId]);
		}

		return $select->fetchAll(PDO::FETCH_COLUMN);
	}

	// Ruta jerarquica de UNA ubicacion, desde la raiz de su proyecto hasta ella misma (ej.
	// "Etapa 1 > M10 > 1"), SIN el nombre del Proyecto - para pantallas que ya lo muestran por
	// separado (columna propia o encabezado de pagina) y no deben repetirlo en la misma fila.
	// Muestra la descripcion real de la ubicacion en vez de solo el nombre de la hoja, que sin
	// contexto de sus padres puede ser ambiguo (ej. "1" o "Apto 01" se repite en varias
	// ubicaciones distintas del mismo proyecto).
	public static function ruta($id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT * FROM ubicacion WHERE ID=:id');
		$select->bindValue('id', $id);
		$select->execute();
		$fila = $select->fetch(PDO::FETCH_ASSOC);
		if ($fila === false) {
			return null;
		}

		$segmentos = [$fila['Nombre']];
		$actual = $fila;
		while ($actual['PadreID'] !== null) {
			$padre = $db->prepare('SELECT * FROM ubicacion WHERE ID=:id');
			$padre->bindValue('id', $actual['PadreID']);
			$padre->execute();
			$filaPadre = $padre->fetch(PDO::FETCH_ASSOC);
			if ($filaPadre === false) {
				break;
			}
			array_unshift($segmentos, $filaPadre['Nombre']);
			$actual = $filaPadre;
		}

		return implode(' > ', $segmentos);
	}

	// Igual que ruta(), pero antepone el nombre del Proyecto - para pantallas (como Kardex) que
	// no muestran el Proyecto en ninguna otra columna, y sin el, la ruta perderia ese contexto.
	public static function rutaConProyecto($id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT ProyectoID FROM ubicacion WHERE ID=:id');
		$select->bindValue('id', $id);
		$select->execute();
		$proyectoId = $select->fetchColumn();
		if ($proyectoId === false) {
			return null;
		}

		$rutaPropia = self::ruta($id);
		if ($rutaPropia === null) {
			return null;
		}

		$proyecto = Proyecto::searchById($proyectoId);
		return $proyecto->getDescripcion() . ' > ' . $rutaPropia;
	}

	// Nodos hoja (sin hijos) ACTIVOS de un proyecto especifico, cada uno con su ruta completa
	// "Proyecto > Etapa > Manzana > Casa". Antes devolvia las de TODOS los proyectos (usado
	// sin filtro por Salidas/Solicitud); ahora exige el proyecto para que esos selectores
	// dejen de mezclar ubicaciones de proyectos distintos.
	public static function hojasConRuta($proyectoId = null){
		$db = Db::getConnect();

		$sql = 'SELECT * FROM ubicacion WHERE Activo = 1' . ($proyectoId !== null ? ' AND ProyectoID = :proyectoId' : '');
		$select = $db->prepare($sql);
		if ($proyectoId !== null) {
			$select->bindValue('proyectoId', $proyectoId);
		}
		$select->execute();

		$todas = [];
		foreach ($select->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$todas[$row['ID']] = $row;
		}

		$tienenHijos = [];
		foreach ($todas as $row) {
			if ($row['PadreID'] !== null) {
				$tienenHijos[$row['PadreID']] = true;
			}
		}

		$resultado = [];
		foreach ($todas as $id => $row) {
			if (isset($tienenHijos[$id])) {
				continue;
			}

			$segmentos = [$row['Nombre']];
			$actual = $row;
			while ($actual['PadreID'] !== null && isset($todas[$actual['PadreID']])) {
				$actual = $todas[$actual['PadreID']];
				array_unshift($segmentos, $actual['Nombre']);
			}

			$proyecto = Proyecto::searchById($row['ProyectoID']);
			array_unshift($segmentos, $proyecto->getDescripcion());

			$resultado[] = ['id' => (int)$id, 'ruta' => implode(' > ', $segmentos)];
		}

		usort($resultado, function($a, $b){ return strcmp($a['ruta'], $b['ruta']); });

		return $resultado;
	}

	// ---------------------------------------------------------------------------------
	// GENERACION MASIVA
	// ---------------------------------------------------------------------------------

	// Resuelve un patron de numeracion tipo "{PISO}{N:02}" para el n-esimo elemento de una
	// generacion, usando el codigo del contenedor (piso/torre) cuando el patron lo referencia.
	public static function resolverPatron($patron, $n, $codigoContenedor = null){
		$resultado = preg_replace_callback('/\{N(?::0(\d+))?\}/', function($m) use ($n) {
			if (isset($m[1])) {
				return str_pad((string) $n, (int) $m[1], '0', STR_PAD_LEFT);
			}
			return (string) $n;
		}, $patron);

		$resultado = str_replace(['{PISO}', '{TORRE}', '{PADRE}'], (string) $codigoContenedor, $resultado);

		return $resultado;
	}

	// Calcula, SIN insertar nada, exactamente lo que generarEstructura() crearia: totales,
	// muestra de las primeras/ultimas etiquetas por nivel, y conflictos de nombre contra lo
	// que ya existe. Es la base de la vista previa obligatoria antes de confirmar.
	public static function previsualizarEstructura($proyectoId, $padreId, $nombreContenedorNuevo, $niveles){
		$resumen = ['totalPorNivel' => [], 'totalGeneral' => 0, 'muestras' => [], 'conflictos' => []];

		// Nivel 0 "virtual": o el contenedor nuevo (1 elemento, nombre fijo) o el padre ya
		// existente (0 elementos nuevos, solo referencia).
		$contenedores = [];
		if ($nombreContenedorNuevo !== null && $nombreContenedorNuevo !== '') {
			$contenedores = [['nombre' => $nombreContenedorNuevo, 'codigo' => null]];
		} else {
			$nombresExistentes = self::nombresHijosExistentes($proyectoId, $padreId);
			$contenedores = [['nombre' => null, 'codigo' => null]]; // el propio padre existente, sin crear nada nuevo
			$resumen['nombresExistentesEnPadre'] = $nombresExistentes;
		}

		foreach ($niveles as $indiceNivel => $nivel) {
			$cantidad = (int) $nivel['cantidad'];
			$etiquetas = [];
			$nuevosContenedores = [];
			$vistos = [];

			foreach ($contenedores as $contenedor) {
				for ($n = 1; $n <= $cantidad; $n++) {
					$codigoBase = $contenedor['codigo'] ?? '';
					$etiqueta = self::resolverPatron($nivel['patron'], $n, $codigoBase);
					$etiquetas[] = $etiqueta;
					$vistos[mb_strtolower($etiqueta)] = true;
					$nuevosContenedores[] = ['nombre' => $etiqueta, 'codigo' => $etiqueta];
				}
			}

			$totalNivel = count($etiquetas);
			$resumen['totalPorNivel'][] = ['tipo' => $nivel['tipo'], 'cantidad' => $totalNivel];
			$resumen['totalGeneral'] += $totalNivel;
			$resumen['muestras'][] = [
				'tipo' => $nivel['tipo'],
				'primeras' => array_slice($etiquetas, 0, 3),
				'ultimas' => array_slice($etiquetas, -3),
				'total' => $totalNivel,
			];

			// Conflicto solo tiene sentido para el primer nivel generado bajo un padre que
			// YA existe (si el contenedor es nuevo, no puede haber conflicto: no existe todavia).
			if ($indiceNivel === 0 && ($nombreContenedorNuevo === null || $nombreContenedorNuevo === '')) {
				foreach (array_keys($vistos) as $etiquetaMin) {
					if (in_array($etiquetaMin, $resumen['nombresExistentesEnPadre'] ?? [])) {
						$resumen['conflictos'][] = $etiquetaMin;
					}
				}
			}

			$contenedores = $nuevosContenedores;
		}

		return $resumen;
	}

	// Genera una estructura completa (contenedor opcional + hasta 2 niveles en cascada) en
	// UNA sola transaccion. Si cualquier insercion falla (ej. un nombre duplicado ya
	// existente que la previsualizacion no pudo ver por una carrera de datos), se revierte
	// TODO - nunca deja una generacion parcial.
	public static function generarEstructura($proyectoId, $padreId, $nombreContenedorNuevo, $tipoContenedorNuevo, $niveles, $usuarioId){
		if (empty($niveles)) {
			throw new Exception('Debe especificar al menos un nivel a generar.');
		}
		foreach ($niveles as $nivel) {
			if ((int) $nivel['cantidad'] <= 0) {
				throw new Exception('La cantidad a generar debe ser mayor a cero.');
			}
		}

		$totalAGenerar = 0;
		$acumulador = 1;
		foreach ($niveles as $nivel) {
			$acumulador *= (int) $nivel['cantidad'];
			$totalAGenerar += $acumulador;
		}
		if ($totalAGenerar > 2000) {
			throw new Exception('No se pueden generar mas de 2000 ubicaciones en una sola operacion (se solicitaron ' . $totalAGenerar . ').');
		}

		$db = Db::getConnect();

		try {
			$db->beginTransaction();

			$padreEfectivo = $padreId;

			if ($nombreContenedorNuevo !== null && $nombreContenedorNuevo !== '') {
				$tipoNormalizado = TipoUbicacion::normalizar($tipoContenedorNuevo);
				$nivelCalculado = self::calcularNivel($db, $proyectoId, $padreId);
				$insertContenedor = $db->prepare('INSERT INTO ubicacion (ID, ProyectoID, PadreID, Nombre, Codigo, Orden, Nivel, Tipo, Activo) VALUES (null, :p, :padre, :nombre, :codigo, 1, :nivel, :tipo, 1)');
				$insertContenedor->execute([
					'p' => $proyectoId,
					'padre' => $padreId,
					'nombre' => $nombreContenedorNuevo,
					'codigo' => $nombreContenedorNuevo,
					'nivel' => $nivelCalculado,
					'tipo' => $tipoNormalizado,
				]);
				$padreEfectivo = $db->lastInsertId();
			}

			$insert = $db->prepare('INSERT INTO ubicacion (ID, ProyectoID, PadreID, Nombre, Codigo, Orden, Nivel, Tipo, Activo) VALUES (null, :p, :padre, :nombre, :codigo, :orden, :nivel, :tipo, 1)');

			$contenedoresNivelActual = [['padreId' => $padreEfectivo]];
			$totalCreado = 0;

			foreach ($niveles as $nivel) {
				$cantidad = (int) $nivel['cantidad'];
				$tipoNormalizado = TipoUbicacion::normalizar($nivel['tipo']);
				$siguienteNivelContenedores = [];

				foreach ($contenedoresNivelActual as $contenedor) {
					$padreIdActual = $contenedor['padreId'];
					$nivelPadre = self::calcularNivel($db, $proyectoId, $padreIdActual);
					$codigoPadre = $contenedor['codigo'] ?? '';

					for ($n = 1; $n <= $cantidad; $n++) {
						$etiqueta = self::resolverPatron($nivel['patron'], $n, $codigoPadre);

						$insert->execute([
							'p' => $proyectoId,
							'padre' => $padreIdActual,
							'nombre' => $etiqueta,
							'codigo' => $etiqueta,
							'orden' => $n,
							'nivel' => $nivelPadre,
							'tipo' => $tipoNormalizado,
						]);
						$nuevoId = $db->lastInsertId();
						$totalCreado++;

						$siguienteNivelContenedores[] = ['padreId' => $nuevoId, 'codigo' => $etiqueta];
					}
				}

				$contenedoresNivelActual = $siguienteNivelContenedores;
			}

			Auditoria::registrar('Ubicacion', $padreEfectivo, 'GENERAR_MASIVO', $usuarioId, null, [
				'ProyectoID' => $proyectoId,
				'PadreID' => $padreId,
				'ContenedorNuevo' => $nombreContenedorNuevo,
				'Niveles' => $niveles,
				'TotalGenerado' => $totalCreado,
			]);

			$db->commit();

			return ['padreId' => $padreEfectivo, 'totalGenerado' => $totalCreado];

		} catch (Exception $e) {
			$db->rollBack();
			throw $e;
		}
	}
}

?>
