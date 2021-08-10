<?php 
/**
* 
*/
class InformeMaterialPorCompra
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

	//----------------------------------------------------------------------
	public static function all(){
		$db = Db::getConnect();
		$listaMaterial = [];

		$select = $db->query('SELECT ID, Codigo, Descripcion, Unidad, Saldo FROM material WHERE Saldo < Min_Almacen');

		foreach($select->fetchAll() as $material){
			$listaMaterial[] = new InformeMaterialPorExistencia($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo']);
		}
		return $listaMaterial;
	}
	//-------------------------------------------------------------------------

	public static function searchByCodigo($codigo){
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE Codigo=:Codigo');
		$select->bindValue('Codigo',$codigo);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new InformeMaterialPorExistencia($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo']);
		
		return $listaMaterial;

	}

	
}

?>