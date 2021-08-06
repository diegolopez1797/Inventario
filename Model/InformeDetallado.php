<?php 
/**
* 
*/
class InformeDetallado
{
	private $Id;
	private $MaterialId;
	private $RegistroSalidasId;
	private $Cantidad;
	private $casaId;
	private $manzanaId;
	private $destinoId;
	private $areaId;
	private $proyectoId;

	
	function __construct($Id, $MaterialId, $RegistroSalidasId, $Cantidad, $casaId, $manzanaId, $destinoId, $areaId, $proyectoId)
	{
		$this->setID($Id);
		$this->setMaterialId($MaterialId);
		$this->setRegistroSalidasId($RegistroSalidasId);
		$this->setCantidad($Cantidad);	
		$this->setCasaId($casaId);
		$this->setManzanaId($manzanaId);
		$this->setDestinoId($destinoId);
		$this->setAreaId($areaId);
		$this->setProyectoId($proyectoId);
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

	public function getProyectoId(){
		return $this->proyectoId;
	}

	public function setProyectoId($proyectoId){
		$this->proyectoId = $proyectoId;
	}


	//------------------------------------------------------------------------------------

	public static function searchByMaterial($idMaterial, $idProyecto){
		$db = Db::getConnect();

				$InformeDetallado = [];

				$select = $db->prepare('SELECT material_registro_salidas.ID, material_registro_salidas.MaterialID, material_registro_salidas.Registro_SalidasID, material_registro_salidas.Cantidad, material_registro_salidas.CasaID, material_registro_salidas.ManzanaID, material_registro_salidas.DestinoID, material_registro_salidas.AreaID, registro_salidas.ProyectoID FROM material_registro_salidas INNER JOIN registro_salidas ON material_registro_salidas.Registro_SalidasID = registro_salidas.ID AND registro_salidas.ProyectoID=:ProyectoID AND material_registro_salidas.MaterialID=:MaterialID');
				$select->bindValue('ProyectoID',$idProyecto);
				$select->bindValue('MaterialID',$idMaterial);
				$select->execute();

				foreach($select->fetchAll() as $entrada){
					$InformeDetallado[] = new InformeDetallado($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_SalidasID'],$entrada['Cantidad'],$entrada['CasaID'],$entrada['ManzanaID'],$entrada['DestinoID'],$entrada['AreaID'],$entrada['ProyectoID']);

				}
		
		return $InformeDetallado;
	}

	//------------------------------------------------------------------------------------

	public static function searchByMaterialDetalle($idProyecto, $idManzana, $idCasa, $idMaterial){
		$db = Db::getConnect();

				$InformeMaterialDetallado = [];

				$select = $db->prepare('SELECT material_registro_salidas.ID, material_registro_salidas.MaterialID, material_registro_salidas.Registro_SalidasID, material_registro_salidas.Cantidad, material_registro_salidas.CasaID, material_registro_salidas.ManzanaID, material_registro_salidas.DestinoID, material_registro_salidas.AreaID, registro_salidas.ProyectoID FROM material_registro_salidas INNER JOIN registro_salidas ON material_registro_salidas.Registro_SalidasID = registro_salidas.ID AND registro_salidas.ProyectoID=:ProyectoID AND material_registro_salidas.ManzanaID=:ManzanaID AND material_registro_salidas.CasaID=:CasaID AND material_registro_salidas.MaterialID=:MaterialID');
				$select->bindValue('ProyectoID',$idProyecto);
				$select->bindValue('ManzanaID',$idManzana);
				$select->bindValue('CasaID',$idCasa);
				$select->bindValue('MaterialID',$idMaterial);
				$select->execute();

				foreach($select->fetchAll() as $entrada){
					$InformeMaterialDetallado[] = new InformeDetallado($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_SalidasID'],$entrada['Cantidad'],$entrada['CasaID'],$entrada['ManzanaID'],$entrada['DestinoID'],$entrada['AreaID'],$entrada['ProyectoID']);

				}
		
		return $InformeMaterialDetallado;
	}

	public static function searchByCasa($idProyecto, $idManzana, $idCasa){
		$db = Db::getConnect();

				$InformeDetallado = [];

				$select = $db->prepare('SELECT material_registro_salidas.ID, material_registro_salidas.MaterialID, material_registro_salidas.Registro_SalidasID, material_registro_salidas.Cantidad, material_registro_salidas.CasaID, material_registro_salidas.ManzanaID, material_registro_salidas.DestinoID, material_registro_salidas.AreaID, registro_salidas.ProyectoID FROM material_registro_salidas INNER JOIN registro_salidas ON material_registro_salidas.Registro_SalidasID = registro_salidas.ID AND registro_salidas.ProyectoID=:ProyectoID AND material_registro_salidas.ManzanaID=:ManzanaID AND material_registro_salidas.CasaID=:CasaID');
				$select->bindValue('ProyectoID',$idProyecto);
				$select->bindValue('ManzanaID',$idManzana);
				$select->bindValue('CasaID',$idCasa);
				$select->execute();

				foreach($select->fetchAll() as $entrada){
					$InformeDetallado[] = new InformeDetallado($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_SalidasID'],$entrada['Cantidad'],$entrada['CasaID'],$entrada['ManzanaID'],$entrada['DestinoID'],$entrada['AreaID'],$entrada['ProyectoID']);

				}
		
		return $InformeDetallado;
	}
}

?>