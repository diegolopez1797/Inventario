<?php 
/**
* 
*/
class Rubro
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
		$listaRubro = [];
		$select = $db->query('SELECT * FROM rubro order by ID');

		foreach($select->fetchAll() as $rubro){
			$listaRubro[] = new Rubro($rubro['ID'],$rubro['Descripcion']);
		}
		
		return $listaRubro;
	}


	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare('SELECT * FROM rubro WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$rubro = $select->fetch();


		$listaRubro = new Rubro($rubro['ID'],$rubro['Descripcion']);
		
		return $listaRubro;

	}

	public static function searchByIdUpdate($id){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM rubro WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$rubro = $select->fetch();

		$listaRubro = new Rubro($rubro['ID'],$rubro['Descripcion']);
		
		return $listaRubro;

	}

	public static function save($rubro){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO rubro VALUES (null,:Descripcion)');
		$insert->bindValue('Descripcion',$rubro->getDescripcion());
		$insert->execute();

		return $insert;

	}

	public static function update($rubro){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE rubro SET Descripcion=:Descripcion WHERE ID=:ID');
		$update->bindValue('Descripcion', $rubro->getDescripcion());
		$update->bindValue('ID',$rubro->getId());
		$update->execute();
	}

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE  FROM rubro WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();		

		return $delete;
	}
}

?>