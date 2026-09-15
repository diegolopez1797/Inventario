<?php
/**
* Almacen fisico (principal, temporal, frente de obra...). El saldo por material
* vive en material_almacen, no aqui - esta clase solo gestiona el catalogo de almacenes.
*/
class Almacen
{
	private $Id;
	private $Descripcion;
	private $Tipo;


	function __construct($Id, $Descripcion, $Tipo)
	{
		$this->setId($Id);
		$this->setDescripcion($Descripcion);
		$this->setTipo($Tipo);
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getDescripcion(){
		return $this->Descripcion;
	}

	public function setDescripcion($Descripcion){
		$this->Descripcion = $Descripcion;
	}

	public function getTipo(){
		return $this->Tipo;
	}

	public function setTipo($Tipo){
		$this->Tipo = $Tipo;
	}


	public static function all(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query('SELECT * FROM almacen order by ID');

		foreach($select->fetchAll() as $almacen){
			$lista[] = new Almacen($almacen['ID'],$almacen['Descripcion'],$almacen['Tipo']);
		}

		return $lista;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM almacen WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$almacen = $select->fetch();

		return new Almacen($almacen['ID'],$almacen['Descripcion'],$almacen['Tipo']);
	}

	public static function save($almacen){
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO almacen (ID, Descripcion, Tipo) VALUES (null,:Descripcion,:Tipo)');
		$insert->bindValue('Descripcion',$almacen->getDescripcion());
		$insert->bindValue('Tipo',$almacen->getTipo());
		$insert->execute();

		return $insert;
	}

	public static function update($almacen){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE almacen SET Descripcion=:Descripcion, Tipo=:Tipo WHERE ID=:ID');
		$update->bindValue('Descripcion', $almacen->getDescripcion());
		$update->bindValue('Tipo', $almacen->getTipo());
		$update->bindValue('ID',$almacen->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE FROM almacen WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();

		return $delete;
	}

	// El almacen "principal" es, por ahora, el unico que la operacion real usa
	// (entradas y salidas siguen trabajando sobre material.Saldo). Se identifica
	// por Tipo, no por ID fijo, para no depender de que siempre sea el ID=1.
	public static function principal(){
		$db = Db::getConnect();
		$row = $db->query("SELECT * FROM almacen WHERE Tipo = 'principal' ORDER BY ID LIMIT 1")->fetch();

		return new Almacen($row['ID'], $row['Descripcion'], $row['Tipo']);
	}

	// Mientras el sistema opere con un solo almacen, material_almacen se mantiene como
	// espejo exacto de material.Saldo en el almacen principal - asi el dato ya esta
	// listo el dia que se conecte un segundo almacen real, sin otra reconciliacion.
	public static function sincronizarSaldoPrincipal($materialId, $nuevoSaldo){
		$db = Db::getConnect();
		$principal = self::principal();

		$update = $db->prepare('INSERT INTO material_almacen (MaterialID, AlmacenID, Saldo) VALUES (:MaterialID, :AlmacenID, :SaldoInsert) ON DUPLICATE KEY UPDATE Saldo = :SaldoUpdate');
		$update->bindValue('MaterialID', $materialId);
		$update->bindValue('AlmacenID', $principal->getId());
		$update->bindValue('SaldoInsert', $nuevoSaldo);
		$update->bindValue('SaldoUpdate', $nuevoSaldo);
		$update->execute();
	}

	// Saldo de un material en un almacen especifico. Si nunca se le abrio saldo
	// en ese almacen, no existe la fila y el saldo es 0.
	public static function saldoDeMaterial($materialId, $almacenId){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT Saldo FROM material_almacen WHERE MaterialID=:MaterialID AND AlmacenID=:AlmacenID');
		$select->bindValue('MaterialID', $materialId);
		$select->bindValue('AlmacenID', $almacenId);
		$select->execute();

		$saldo = $select->fetchColumn();

		return $saldo === false ? 0 : (int)$saldo;
	}

	// Saldo de un material desglosado por cada almacen donde tenga existencia (aunque sea 0).
	public static function saldosPorMaterial($materialId){
		$db = Db::getConnect();
		$lista = [];

		$select = $db->prepare('
			SELECT a.ID, a.Descripcion, a.Tipo, ma.Saldo
			FROM material_almacen ma
			INNER JOIN almacen a ON ma.AlmacenID = a.ID
			WHERE ma.MaterialID = :MaterialID
			ORDER BY a.Descripcion
		');
		$select->bindValue('MaterialID', $materialId);
		$select->execute();

		foreach ($select->fetchAll() as $row) {
			$lista[] = ['almacen' => new Almacen($row['ID'], $row['Descripcion'], $row['Tipo']), 'saldo' => (int)$row['Saldo']];
		}

		return $lista;
	}
}

?>
