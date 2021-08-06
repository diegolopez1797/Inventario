<?php 
/**
* 
*/
class MaterialRegistroEntradas
{
	private $Id;
	private $MaterialId;
	private $RegistroEntradasId;
	private $Cantidad;

	
	function __construct($Id, $MaterialId, $RegistroEntradasId, $Cantidad)
	{
		$this->setID($Id);
		$this->setMaterialId($MaterialId);
		$this->setRegistroEntradasId($RegistroEntradasId);
		$this->setCantidad($Cantidad);	
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getMaterialId(){
		return $this->MaterialId;
	}

	public function setMaterialId($MaterialId){
		$this->MaterialId = $MaterialId;
	}

	public function getRegistroEntradasId(){
		return $this->RegistroEntradasId;
	}

	public function setRegistroEntradasId($RegistroEntradasId){
		$this->RegistroEntradasId = $RegistroEntradasId;
	}

	public function getCantidad(){
		return $this->Cantidad;
	}

	public function setCantidad($Cantidad){
		$this->Cantidad = $Cantidad;
	}



	public static function save($materialRegistroEntradas){
		$db=Db::getConnect();
		
		$insert=$db->prepare('INSERT INTO material_registro_entradas VALUES (null,:materialId,:registroEntradasId,:Cantidad)');

		$insert->bindValue('materialId',$materialRegistroEntradas->getMaterialId());
		$insert->bindValue('registroEntradasId',$materialRegistroEntradas->getRegistroEntradasId());
		$insert->bindValue('Cantidad',$materialRegistroEntradas->getCantidad());
		$insert->execute();

		return $insert;
	}

	public static function searchMaterialRegistroEntradas($idEntrada){
		$db = Db::getConnect();

		$materialRegistroEntradas = [];

		$select = $db->prepare('SELECT * FROM material_registro_entradas WHERE Registro_EntradasID=:idEntrada order by ID');
		$select->bindValue('idEntrada',$idEntrada);
		$select->execute();

		foreach($select->fetchAll() as $entrada){
			$materialRegistroEntradas[] = new MaterialRegistroEntradas($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_EntradasID'],$entrada['Cantidad']);

		}
		
		return $materialRegistroEntradas;

	}
	
	
}

?>