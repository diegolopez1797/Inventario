<?php 
/**
* 
*/
class Contratista
{
	private $Id;
	private $Descripcion;

	
	function __construct($Id, $Descripcion)
	{
		$this->setId($Id);
		$this->setDescripcion($Descripcion);
		
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


	public static function all(){
		$db = Db::getConnect();
		$listaContratista = [];
		$select = $db->query('SELECT * FROM contratista order by ID');

		foreach($select->fetchAll() as $contratista){
			$listaContratista[] = new Contratista($contratista['ID'],$contratista['Descripcion']);
		}
		
		return $listaContratista;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM contratista WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$contratista = $select->fetch();

		$listaContratista = new Contratista($contratista['ID'],$contratista['Descripcion']);
		
		return $listaContratista;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM contratista WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$contratista = $select->fetch();

		$listaContratista = new Contratista($contratista['ID'],$contratista['Descripcion']);
		
		return $listaContratista;

	}

	public static function save($contratista){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO contratista VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$contratista->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($contratista){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE contratista SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $contratista->getDescripcion());
		$update->bindValue('ID',$contratista->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM contratista WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();

		return $delete;		
	}
}

?>