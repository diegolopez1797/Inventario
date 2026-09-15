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
	private $Destino;
	private $CostoUnitario;


	function __construct($Id, $MaterialId, $RegistroEntradasId, $Cantidad, $Destino, $CostoUnitario = null)
	{
		$this->setID($Id);
		$this->setMaterialId($MaterialId);
		$this->setRegistroEntradasId($RegistroEntradasId);
		$this->setCantidad($Cantidad);
		$this->setDestino($Destino);
		$this->setCostoUnitario($CostoUnitario);
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

	public function getDestino(){
		return $this->Destino;
	}

	public function setDestino($Destino){
		$this->Destino = $Destino;
	}

	public function getCostoUnitario(){
		return $this->CostoUnitario;
	}

	public function setCostoUnitario($CostoUnitario){
		$this->CostoUnitario = $CostoUnitario;
	}

	public static function save($materialRegistroEntradas){
		$db=Db::getConnect();

		$insert=$db->prepare('INSERT INTO material_registro_entradas VALUES (null,:materialId,:registroEntradasId,:Cantidad,:Destino,:Costo)');

		$insert->bindValue('materialId',$materialRegistroEntradas->getMaterialId());
		$insert->bindValue('registroEntradasId',$materialRegistroEntradas->getRegistroEntradasId());
		$insert->bindValue('Cantidad',$materialRegistroEntradas->getCantidad());
		$insert->bindValue('Destino',$materialRegistroEntradas->getDestino());
		$insert->bindValue('Costo',$materialRegistroEntradas->getCostoUnitario(), $materialRegistroEntradas->getCostoUnitario() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
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
			$materialRegistroEntradas[] = new MaterialRegistroEntradas($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_EntradasID'],$entrada['Cantidad'],$entrada['Destino'],$entrada['CostoUnitario']);

		}

		return $materialRegistroEntradas;

	}
	
	
}

?>