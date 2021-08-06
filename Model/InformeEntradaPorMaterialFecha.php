<?php 
/**
* 
*/
class InformeEntradaPorMaterialFecha
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




	public static function all(){
		$db = Db::getConnect();
		$materialRegistroEntradas = [];

		$select = $db->query('SELECT * FROM material_registro_entradas order by ID DESC LIMIT 0, 50');

		foreach($select->fetchAll() as $entrada){
			$materialRegistroEntradas[] = new InformeEntradaPorMaterialFecha($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_EntradasID'],$entrada['Cantidad']);

		}
		
		return $materialRegistroEntradas;
	}


	public static function searchMaterialRegistroEntradas($idMaterial){
		$db = Db::getConnect();

		$materialRegistroEntradas = [];

		$select = $db->prepare('SELECT * FROM material_registro_entradas WHERE MaterialID=:idMaterial order by ID');
		$select->bindValue('idMaterial',$idMaterial);
		$select->execute();

		foreach($select->fetchAll() as $entrada){
			$materialRegistroEntradas[] = new InformeEntradaPorMaterialFecha($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_EntradasID'],$entrada['Cantidad']);

		}
		
		return $materialRegistroEntradas;

	}
	
	
}

?>