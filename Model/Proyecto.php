<?php 
/**
* 
*/
class Proyecto
{
	private $Id;
	private $Descripcion;
	private $ResponsableId;


	function __construct($Id, $Descripcion, $ResponsableId = null)
	{
		$this->setId($Id);
		$this->setDescripcion($Descripcion);
		$this->setResponsableId($ResponsableId);

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

	public function getResponsableId(){
		return $this->ResponsableId;
	}

	public function setResponsableId($ResponsableId){
		$this->ResponsableId = $ResponsableId;
	}


	public static function all(){
		$db = Db::getConnect();
		$listaProyecto = [];
		$select = $db->query('SELECT * FROM proyecto order by ID');

		foreach($select->fetchAll() as $proyecto){
			$listaProyecto[] = new Proyecto($proyecto['ID'],$proyecto['Descripcion'],$proyecto['ResponsableID']);
		}

		return $listaProyecto;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM proyecto WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$proyecto = $select->fetch();

		$listaProyecto = new Proyecto($proyecto['ID'],$proyecto['Descripcion'],$proyecto['ResponsableID']);

		return $listaProyecto;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM proyecto WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$proyecto = $select->fetch();

		$listaProyecto = new Proyecto($proyecto['ID'],$proyecto['Descripcion'],$proyecto['ResponsableID']);

		return $listaProyecto;

	}

	public static function save($proyecto){

		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO proyecto VALUES (null,:Descripcion,:ResponsableID)');
		$insert->bindValue('Descripcion',$proyecto->getDescripcion());
		$insert->bindValue('ResponsableID',$proyecto->getResponsableId(), $proyecto->getResponsableId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->execute();

		return $insert;

	}

	public static function update($proyecto){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE proyecto SET Descripcion=:Descripcion, ResponsableID=:ResponsableID WHERE ID=:ID');
		$update->bindValue('Descripcion', $proyecto->getDescripcion());
		$update->bindValue('ResponsableID', $proyecto->getResponsableId(), $proyecto->getResponsableId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
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