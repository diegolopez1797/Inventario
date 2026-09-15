<?php 
/**
* 
*/
class MaterialRegistroSalidas
{
	private $Id;
	private $MaterialId;
	private $RegistroSalidasId;
	private $Cantidad;
	private $casaId;
	private $manzanaId;
	private $destinoId;
	private $areaId;
	private $rubroId;
	private $ubicacionId;
	private $costoUnitario;


	function __construct($Id, $MaterialId, $RegistroSalidasId, $Cantidad, $casaId, $manzanaId, $destinoId, $areaId, $rubroId, $ubicacionId = null, $costoUnitario = null)
	{
		$this->setID($Id);
		$this->setMaterialId($MaterialId);
		$this->setRegistroSalidasId($RegistroSalidasId);
		$this->setCantidad($Cantidad);
		$this->setCasaId($casaId);
		$this->setManzanaId($manzanaId);
		$this->setDestinoId($destinoId);
		$this->setAreaId($areaId);
		$this->setRubroId($rubroId);
		$this->setUbicacionId($ubicacionId);
		$this->setCostoUnitario($costoUnitario);
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

	public function getRubroId(){
		return $this->rubroId;
	}

	public function setRubroId($rubroId){
		$this->rubroId = $rubroId;
	}

	public function getUbicacionId(){
		return $this->ubicacionId;
	}

	public function setUbicacionId($ubicacionId){
		$this->ubicacionId = $ubicacionId;
	}

	public function getCostoUnitario(){
		return $this->costoUnitario;
	}

	public function setCostoUnitario($costoUnitario){
		$this->costoUnitario = $costoUnitario;
	}

	public static function save($materialRegistroSalidas){
		$db=Db::getConnect();

		$insert=$db->prepare('INSERT INTO material_registro_salidas VALUES (null,:materialId,:registroSalidasId,:Cantidad,:Casa,:Manzana,:Destino,:Area,:Rubro,:Ubicacion,:Costo)');

		$insert->bindValue('materialId',$materialRegistroSalidas->getMaterialId());
		$insert->bindValue('registroSalidasId',$materialRegistroSalidas->getRegistroSalidasId());
		$insert->bindValue('Cantidad',$materialRegistroSalidas->getCantidad());
		$insert->bindValue('Casa',$materialRegistroSalidas->getCasaId(), $materialRegistroSalidas->getCasaId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('Manzana',$materialRegistroSalidas->getManzanaId(), $materialRegistroSalidas->getManzanaId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('Destino',$materialRegistroSalidas->getDestinoId(), $materialRegistroSalidas->getDestinoId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('Area',$materialRegistroSalidas->getAreaId(), $materialRegistroSalidas->getAreaId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('Rubro',$materialRegistroSalidas->getRubroId(), $materialRegistroSalidas->getRubroId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('Ubicacion',$materialRegistroSalidas->getUbicacionId(), $materialRegistroSalidas->getUbicacionId() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
		$insert->bindValue('Costo',$materialRegistroSalidas->getCostoUnitario(), $materialRegistroSalidas->getCostoUnitario() === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
		$insert->execute();

		return $insert;
	}

	public static function searchMaterialRegistroSalidas($idEntrada){
		$db = Db::getConnect();

		$materialRegistroEntradas = [];

		$select = $db->prepare('SELECT * FROM material_registro_salidas WHERE Registro_SalidasID=:idEntrada order by ID');
		$select->bindValue('idEntrada',$idEntrada);
		$select->execute();

		foreach($select->fetchAll() as $entrada){
			$materialRegistroEntradas[] = new MaterialRegistroSalidas($entrada['ID'],$entrada['MaterialID'],$entrada['Registro_SalidasID'],$entrada['Cantidad'],$entrada['CasaID'],$entrada['ManzanaID'],$entrada['DestinoID'],$entrada['AreaID'],$entrada['RubroID'],$entrada['UbicacionID'],$entrada['CostoUnitario']);

		}

		return $materialRegistroEntradas;

	}
	
	
}

?>