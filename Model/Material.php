<?php 
/**
* 
*/
class Material
{
	private $Id;
	private $Codigo;
	private $Descripcion;
	private $Unidad;
	private $Saldo;
	private $MinAlmacen;
	private $MaxCasa;
	private $CostoPromedio;


	function __construct($Id, $Codigo, $Descripcion, $Unidad, $Saldo, $MinAlmacen, $MaxCasa, $CostoPromedio = null)
	{
		$this->setId($Id);
		$this->setCodigo($Codigo);
		$this->setDescripcion($Descripcion);
		$this->setUnidad($Unidad);
		$this->setSaldo($Saldo);
		$this->setMinAlmacen($MinAlmacen);
		$this->setMaxCasa($MaxCasa);
		$this->setCostoPromedio($CostoPromedio);
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getCodigo(){
		return $this->Codigo;
	}

	public function setCodigo($Codigo){
		$this->Codigo = $Codigo;
	}

	public function getDescripcion(){
		return $this->Descripcion;
	}

	public function setDescripcion($Descripcion){
		$this->Descripcion = $Descripcion;
	}

	public function getUnidad(){
		return $this->Unidad;
	}

	public function setUnidad($Unidad){
		$this->Unidad = $Unidad;
	}

	public function getSaldo(){
		return $this->Saldo;
	}

	public function setSaldo($Saldo){
		$this->Saldo = $Saldo;
	}

	public function getMinAlmacen(){
		return $this->MinAlmacen;
	}

	public function setMinAlmacen($MinAlmacen){
		$this->MinAlmacen = $MinAlmacen;
	}

	public function getMaxCasa(){
		return $this->MaxCasa;
	}

	public function setMaxCasa($MaxCasa){
		$this->MaxCasa = $MaxCasa;
	}

	public function getCostoPromedio(){
		return $this->CostoPromedio;
	}

	public function setCostoPromedio($CostoPromedio){
		$this->CostoPromedio = $CostoPromedio;
	}


	public static function ingresoMaterial($id, $nuevoSaldo){
		$db=Db::getConnect();

		$update=$db->prepare('UPDATE material SET Saldo=:Saldo WHERE ID=:ID');
		$update->bindValue('Saldo',$nuevoSaldo);
		$update->bindValue('ID',$id);
		$update->execute();

		Almacen::sincronizarSaldoPrincipal($id, $nuevoSaldo);
	}

	// Actualiza el costo promedio ponderado del material. Nunca se calcula aqui (la formula
	// vive en RegistroEntradas::registrar, que es quien conoce saldo/costo anterior y de la
	// entrada nueva) - este metodo solo persiste el valor ya calculado.
	public static function actualizarCosto($id, $nuevoCostoPromedio){
		$db=Db::getConnect();

		$update=$db->prepare('UPDATE material SET CostoPromedio=:CostoPromedio WHERE ID=:ID');
		$update->bindValue('CostoPromedio',$nuevoCostoPromedio);
		$update->bindValue('ID',$id);
		$update->execute();
	}

	public static function save($material){
		$db=Db::getConnect();

		// CostoPromedio nace en null: un material recien creado no tiene historial de costo
		// hasta su primera entrada con costo capturado.
		$insert=$db->prepare('INSERT INTO material VALUES (null,:Codigo,:Descripcion,:Unidad,null,:Min_Almacen,:Max_Casa,null)');
		$insert->bindValue('Codigo',$material->getCodigo());
		$insert->bindValue('Descripcion',$material->getDescripcion());
		$insert->bindValue('Unidad',$material->getUnidad());
		$insert->bindValue('Min_Almacen',$material->getMinAlmacen());
		$insert->bindValue('Max_Casa',$material->getMaxCasa());
		$insert->execute();

		return $insert;
	}

	public static function all(){
		$db = Db::getConnect();
		$listaMaterial = [];

		$select = $db->query('SELECT * FROM material order by ID');

		foreach($select->fetchAll() as $material){
			$listaMaterial[] = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa'],$material['CostoPromedio']);
		}
		return $listaMaterial;
	}

	// Columnas de ordenamiento permitidas para paginado() - whitelist obligatoria, el valor de
	// $orden que llega de la URL nunca se concatena directamente en el SQL (Fase 4.1, seccion 7).
	private static function columnasOrdenPermitidas(){
		return [
			'Codigo' => 'Codigo',
			'Descripcion' => 'Descripcion',
			'Saldo' => 'Saldo',
			'Min_Almacen' => 'Min_Almacen',
			// Ranking de severidad: agotado primero, luego critico, luego normal.
			'Estado' => 'CASE WHEN Saldo = 0 THEN 0 WHEN Min_Almacen > 0 AND Saldo <= Min_Almacen THEN 1 ELSE 2 END',
		];
	}

	// Estado de inventario de un material ya cargado, para Inventario Actual (Fase 4.1). Reutiliza
	// exactamente el mismo umbral que Dashboard::materialesCriticos() (Min_Almacen>0 AND Saldo<=Min_Almacen)
	// en vez de inventar una nueva regla. No existe en el sistema una distincion valida entre "Bajo" y
	// "Critico" (ningun informe ni el Dashboard la definen) - por eso este metodo devuelve 3 estados, no 4.
	public static function estadoInventario($material){
		if ($material->getSaldo() == 0) {
			return ['codigo' => 'agotado', 'etiqueta' => 'Sin existencia', 'clase' => 'default'];
		}
		if ($material->getMinAlmacen() > 0 && $material->getSaldo() <= $material->getMinAlmacen()) {
			return ['codigo' => 'critico', 'etiqueta' => 'Crítico', 'clase' => 'danger'];
		}
		return ['codigo' => 'normal', 'etiqueta' => 'Normal', 'clase' => 'success'];
	}

	// Condicion SQL para el filtro de estado de Inventario Actual. Devuelve null si el estado no
	// aplica ningun filtro (opcion "todos"). Mismos umbrales que estadoInventario(), para que el
	// filtro y la columna mostrada nunca puedan contradecirse entre si.
	private static function condicionEstado($estado){
		switch ($estado) {
			case 'existencia':
				return 'Saldo > 0';
			case 'normal':
				return 'Saldo > 0 AND (Min_Almacen = 0 OR Saldo > Min_Almacen)';
			case 'critico':
				return 'Saldo > 0 AND Min_Almacen > 0 AND Saldo <= Min_Almacen';
			case 'agotado':
				return 'Saldo = 0';
			default:
				return null;
		}
	}

	// Total de materiales que cumplen el filtro (para calcular cuantas paginas hay). Separado de
	// paginado() para no repetir el COUNT en cada fila ni traer datos que no hacen falta para ese numero.
	public static function contarTotal($texto = null, $estado = null){
		$db = Db::getConnect();

		$where = [];
		$params = [];

		if (!empty($texto)) {
			$where[] = '(Codigo LIKE :texto OR Descripcion LIKE :texto)';
			$params['texto'] = '%' . $texto . '%';
		}

		$condicionEstado = self::condicionEstado($estado);
		if ($condicionEstado !== null) {
			$where[] = $condicionEstado;
		}

		$sql = 'SELECT COUNT(*) FROM material';
		if (!empty($where)) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->execute();

		return (int) $select->fetchColumn();
	}

	// Una pagina de materiales para Gestion Material / Inventario Actual (mismo listado, dos usos -
	// Fase 3 seccion 3). A diferencia de all() (que sigue trayendo el catalogo completo, porque
	// tambien alimenta selectores que necesitan verlo entero), esta consulta si limita, filtra y
	// ordena en la base de datos - la pantalla deja de traer y renderizar los 296 registros completos
	// en cada carga. $orden solo acepta valores de columnasOrdenPermitidas(); cualquier otro valor cae
	// al orden por defecto (Codigo) en vez de romper la consulta o exponerse a inyeccion SQL.
	public static function paginado($pagina, $tamanoPagina, $texto = null, $estado = null, $orden = 'Codigo', $direccion = 'ASC'){
		$db = Db::getConnect();
		$listaMaterial = [];

		$pagina = max(1, (int) $pagina);
		$offset = ($pagina - 1) * $tamanoPagina;

		$columnasOrden = self::columnasOrdenPermitidas();
		$columnaOrden = isset($columnasOrden[$orden]) ? $columnasOrden[$orden] : $columnasOrden['Codigo'];
		$direccion = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';

		$where = [];
		$params = [];

		if (!empty($texto)) {
			$where[] = '(Codigo LIKE :texto OR Descripcion LIKE :texto)';
			$params['texto'] = '%' . $texto . '%';
		}

		$condicionEstado = self::condicionEstado($estado);
		if ($condicionEstado !== null) {
			$where[] = $condicionEstado;
		}

		$sql = 'SELECT * FROM material';
		if (!empty($where)) {
			$sql .= ' WHERE ' . implode(' AND ', $where);
		}
		$sql .= ' ORDER BY ' . $columnaOrden . ' ' . $direccion . ' LIMIT :offset, :tamano';

		$select = $db->prepare($sql);
		foreach ($params as $nombre => $valor) {
			$select->bindValue($nombre, $valor);
		}
		$select->bindValue('offset', $offset, PDO::PARAM_INT);
		$select->bindValue('tamano', (int) $tamanoPagina, PDO::PARAM_INT);
		$select->execute();

		foreach($select->fetchAll() as $material){
			$listaMaterial[] = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa'],$material['CostoPromedio']);
		}
		return $listaMaterial;
	}

	// Materiales sin movimiento (Fase 2.1 seccion 6-7 #9 / Fase 3 seccion 14): "ultimo movimiento"
	// es el maximo de MAX(Fecha) en Entrada, Salida y Ajuste - nunca cuenta una fila de auditoria
	// (crear/editar el catalogo no es movimiento fisico). Si ninguna de las 3 tablas tiene una
	// fila para el material, se devuelve 'ultimoMovimiento' => null - la Vista debe mostrar
	// "Sin movimientos registrados", nunca una fecha inventada (material no tiene columna de
	// fecha de creacion). Un material sin movimiento nunca siempre califica, sin importar el
	// umbral - es el caso mas extremo de inmovilizado, no un caso aparte.
	public static function sinMovimiento($umbralDias){
		$db = Db::getConnect();

		$sql = "
			SELECT m.*, e.UltimaEntrada, s.UltimaSalida, a.UltimoAjuste
			FROM material m
			LEFT JOIN (
				SELECT mre.MaterialID, MAX(re.Fecha) AS UltimaEntrada
				FROM material_registro_entradas mre
				INNER JOIN registro_entradas re ON mre.Registro_EntradasID = re.ID
				GROUP BY mre.MaterialID
			) e ON e.MaterialID = m.ID
			LEFT JOIN (
				SELECT mrs.MaterialID, MAX(rs.Fecha) AS UltimaSalida
				FROM material_registro_salidas mrs
				INNER JOIN registro_salidas rs ON mrs.Registro_SalidasID = rs.ID
				GROUP BY mrs.MaterialID
			) s ON s.MaterialID = m.ID
			LEFT JOIN (
				SELECT MaterialID, MAX(Fecha) AS UltimoAjuste
				FROM ajuste_inventario
				GROUP BY MaterialID
			) a ON a.MaterialID = m.ID
			ORDER BY m.Codigo
		";

		$hoy = date('Y-m-d');
		$resultado = [];

		foreach ($db->query($sql)->fetchAll(PDO::FETCH_ASSOC) as $fila) {
			$fechas = array_filter([$fila['UltimaEntrada'], $fila['UltimaSalida'], $fila['UltimoAjuste']], function($f){ return $f !== null; });
			$ultimoMovimiento = !empty($fechas) ? max($fechas) : null;
			$dias = $ultimoMovimiento !== null ? (int) round((strtotime($hoy) - strtotime($ultimoMovimiento)) / 86400) : null;

			if ($ultimoMovimiento === null || $dias >= $umbralDias) {
				$resultado[] = [
					'material' => new Material($fila['ID'], $fila['Codigo'], $fila['Descripcion'], $fila['Unidad'], $fila['Saldo'], $fila['Min_Almacen'], $fila['Max_Casa'], $fila['CostoPromedio']),
					'ultimoMovimiento' => $ultimoMovimiento,
					'dias' => $dias,
				];
			}
		}

		return $resultado;
	}

	public static function searchByCodigo($codigo){
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE Codigo=:Codigo');
		$select->bindValue('Codigo',$codigo);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa'],$material['CostoPromedio']);

		return $listaMaterial;

	}

	public static function searchById($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa'],$material['CostoPromedio']);
		//var_dump($alumno);
		//die();
		return $listaMaterial;


	}

	public static function update($material){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE material SET Codigo=:Codigo, Descripcion=:Descripcion, Unidad=:Unidad, Saldo=:Saldo, Max_Casa=:Max_Casa, Min_Almacen=:Min_Almacen WHERE ID=:ID');
		$update->bindValue('Codigo', $material->getCodigo());
		$update->bindValue('Descripcion', $material->getDescripcion());
		$update->bindValue('Unidad',$material->getUnidad());
		$update->bindValue('Saldo',$material->getSaldo());
		$update->bindValue('ID',$material->getId());
		$update->bindValue('Max_Casa',$material->getMaxCasa());
		$update->bindValue('Min_Almacen',$material->getMinAlmacen());
		$update->execute();

		Almacen::sincronizarSaldoPrincipal($material->getId(), $material->getSaldo());
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM material WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();	

		return $delete;	
	}
}


?>