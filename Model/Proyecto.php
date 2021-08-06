<?php 
/**
* 
*/
class Proyecto
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
		$listaProyecto = [];
		$select = $db->query('SELECT * FROM proyecto order by ID');

		foreach($select->fetchAll() as $proyecto){
			$listaProyecto[] = new Proyecto($proyecto['ID'],$proyecto['Descripcion']);
		}
		
		return $listaProyecto;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM proyecto WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$proyecto = $select->fetch();

		$listaProyecto = new Proyecto($proyecto['ID'],$proyecto['Descripcion']);
		
		return $listaProyecto;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM proyecto WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$proyecto = $select->fetch();

		$listaProyecto = new Proyecto($proyecto['ID'],$proyecto['Descripcion']);
		
		return $listaProyecto;

	}

	public static function save($proyecto){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO proyecto VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$proyecto->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($proyecto){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE proyecto SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $proyecto->getDescripcion());
		$update->bindValue('ID',$proyecto->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM proyecto WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();

		return $delete;		
	}
}

?>