<?php 
/**
* 
*/
class Rol
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


	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM rol WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$rol = $select->fetch();


		$listaRol = new Rol($rol['ID'],$rol['Descripcion']);
		
		return $listaRol;

	}
}

?>