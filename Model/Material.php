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

	
	function __construct($Id, $Codigo, $Descripcion, $Unidad, $Saldo, $MinAlmacen, $MaxCasa)
	{
		$this->setId($Id);
		$this->setCodigo($Codigo);
		$this->setDescripcion($Descripcion);
		$this->setUnidad($Unidad);
		$this->setSaldo($Saldo);
		$this->setMinAlmacen($MinAlmacen);
		$this->setMaxCasa($MaxCasa);		
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


	public static function ingresoMaterial($id, $nuevoSaldo){
		$db=Db::getConnect();

		$update=$db->prepare('UPDATE material SET Saldo=:Saldo WHERE ID=:ID');
		$update->bindValue('Saldo',$nuevoSaldo);
		$update->bindValue('ID',$id);
		$update->execute();	
	}

	public static function save($material){
		$db=Db::getConnect();
		
		$insert=$db->prepare('INSERT INTO material VALUES (null,:Codigo,:Descripcion,:Unidad,null,:Min_Almacen,:Max_Casa)');
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
			$listaMaterial[] = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa']);
		}
		return $listaMaterial;
	}

	public static function searchByCodigo($codigo){
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE Codigo=:Codigo');
		$select->bindValue('Codigo',$codigo);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa']);
		
		return $listaMaterial;

	}

	public static function searchById($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa']);
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