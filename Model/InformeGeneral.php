<?php 
/**
* 
*/
class InformeGeneral
{
	private $Id;
	private $Codigo;
	private $Descripcion;
	private $Unidad;
	private $Saldo;
	private $MinAlmacen;
	private $MaxCasa;

	
	function __construct($Id, $Codigo, $Descripcion, $Unidad, $Saldo, $MinAlmacen, $MaxCasa)
	{
		$this->setId($Id);
		$this->setCodigo($Codigo);
		$this->setDescripcion($Descripcion);
		$this->setUnidad($Unidad);
		$this->setSaldo($Saldo);	
		$this->setMinAlmacen($MinAlmacen);
		$this->setMaxcasa($MaxCasa);	
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

	public function getMinAlmacen(){
		return $this->MinAlmacen;
	}

	public function setMinAlmacen($MinAlmacen){
		$this->MinAlmacen = $MinAlmacen;
	}

	public function getMaxCasa(){
		return $this->MaxCasa;
	}

	public function setMaxcasa($MaxCasa){
		$this->MaxCasa = $MaxCasa;
	}

	//----------------------------------------------------------------------
	public static function all(){
		$db = Db::getConnect();
		$listaMaterial = [];

		$select = $db->query('SELECT * FROM material order by ID');

		foreach($select->fetchAll() as $material){
			$listaMaterial[] = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa']);
		}
		return $listaMaterial;
	}
	//-------------------------------------------------------------------------

	public static function searchById($codigo){
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM material WHERE Codigo=:Codigo');
		$select->bindValue('Codigo',$codigo);
		$select->execute();

		$material = $select->fetch();


		$listaMaterial = new Material($material['ID'],$material['Codigo'],$material['Descripcion'],$material['Unidad'],$material['Saldo'],$material['Min_Almacen'],$material['Max_Casa']);
		
		return $listaMaterial;

	}

	
}

?>