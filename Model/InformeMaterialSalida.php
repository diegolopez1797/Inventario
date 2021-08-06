<?php 
/**
* 
*/
class InformeMaterialSalida
{
	private $Id;
	private $MaterialId;
	private $RegistroSalidasId;
	private $Cantidad;
	private $casaId;
	private $manzanaId;
	private $destinoId;
	private $areaId;

	
	function __construct($Id, $MaterialId, $RegistroSalidasId, $Cantidad, $casaId, $manzanaId, $destinoId, $areaId)
	{
		$this->setID($Id);
		$this->setMaterialId($MaterialId);
		$this->setRegistroSalidasId($RegistroSalidasId);
		$this->setCantidad($Cantidad);	
		$this->setCasaId($casaId);
		$this->setManzanaId($manzanaId);
		$this->setDestinoId($destinoId);
		$this->setAreaId($areaId);
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

	public function getRegistroSalidasId(){
		return $this->RegistroSalidasId;
	}

	public function setRegistroSalidasId($RegistroSalidasId){
		$this->RegistroSalidasId = $RegistroSalidasId;
	}

	public function getCantidad(){
		return $this->Cantidad;
	}

	public function setCantidad($Cantidad){
		$this->Cantidad = $Cantidad;
	}

	public function getCasaId(){
		return $this->casaId;
	}

	public function setCasaId($casaId){
		$this->casaId = $casaId;
	}
	public function getManzanaId(){
		return $this->manzanaId;
	}

	public function setManzanaId($manzanaId){
		$this->manzanaId = $manzanaId;
	}

	public function getDestinoId(){
		return $this->destinoId;
	}

	public function setDestinoId($destinoId){
		$this->destinoId = $destinoId;
	}
	
	public function getAreaId(){
		return $this->areaId;
	}

	public function setAreaId($areaId){
		$this->areaId = $areaId;
	}
//--------------------------------------------------------------------------------------------------------

	public static function searchMaterialRegistroSalidas($idEntrada){
		$db = Db::getConnect();

		$materialRegistroEntradas = [];

		$select = $db->prepare('SELECT * FROM material_registro_salidas WHERE Registro_SalidasID=:idEntrada order by ID');
		$select->bindValue('idEntrada',$idEntrada);
		$select->execute();

		foreach($select->fetchAll() as $entrada){
			$materialRegistroEntradas[] = new MaterialRegistroSalidas($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_SalidasID'],$entrada['Cantidad'],$entrada['CasaID'],$entrada['ManzanaID'],$entrada['DestinoID'],$entrada['AreaID']);

		}
		
		return $materialRegistroEntradas;

	}

//--------------------------------------------------------------------------------------------------------
	
	
}

?>