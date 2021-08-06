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

	
	function __construct($Id, $Codigo, $Descripcion, $Unidad, $Saldo)
	{
		$this->setId($Id);
		$this->setCodigo($Codigo);
		$this->setDescripcion($Descripcion);
		$this->setUnidad($Unidad);
		$this->setSaldo($Saldo);		
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


	public static function ingresoMaterial($id, $nuevoSaldo){
		$db=Db::getConnect();

		$update=$db->prepare('UPDATE material SET Saldo=:Saldo WHERE ID=:ID');
		$update->bindValue('Saldo',$nuevoSaldo);
		$update->bindValue('ID',$id);
		$update->execute();	
	}

	public static function save($material){
		$db=Db::getConnect();
		
		$insert=$db->prepare('INSERT INTO material VALUES (null,:Codigo,:Descripcion,:Unidad,null)');
		$insert->bindValue('Codigo',$material->getCodigo());
		$insert->bindValue('Descripcion',$material->getDescripcion());
		$insert->bindValue('Unidad',$material->getUnidad());
		$insert->execute();

		return $insert;
	}

	public static function all(){
		$db = Db::getConnect();
		$listaMaterial = [];

		$select = $db->query('SELECT * FROM material order by ID');

		foreach($select->fetchAll() as $material){
			$listaMaterial[] = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo']);
		}
		return $listaMaterial;
	}

	public static function searchByCodigo($codigo){
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE Codigo=:Codigo');
		$select->bindValue('Codigo',$codigo);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo']);
		
		return $listaMaterial;

	}

	public static function searchById($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo']);
		//var_dump($alumno);
		//die();
		return $listaMaterial;


	}

	public static function update($material){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE material SET Codigo=:Codigo, Descripcion=:Descripcion, Unidad=:Unidad, Saldo=:Saldo WHERE ID=:ID');
		$update->bindValue('Codigo', $material->getCodigo());
		$update->bindValue('Descripcion', $material->getDescripcion());
		$update->bindValue('Unidad',$material->getUnidad());
		$update->bindValue('Saldo',$material->getSaldo());
		$update->bindValue('ID',$material->getId());
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