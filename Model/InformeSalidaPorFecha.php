<?php 
/**
* 
*/


class InformeSalidaPorFecha
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $Usuario;
	private $contratistaId;

	
	function __construct($Id, $Fecha, $Hora, $Usuario, $contratistaId)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setUsuario($Usuario);
		$this->setContratista($contratistaId);	
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getFecha(){
		return $this->Fecha;
	}

	public function setFecha($Fecha){
		$this->Fecha = $Fecha;
	}

	public function getHora(){
		return $this->Hora;
	}

	public function setHora($Hora){
		$this->Hora = $Hora;
	}

	public function getUsuario(){
		return $this->Usuario;
	}

	public function setUsuario($Usuario){
		$this->Usuario = $Usuario;
	}

	public function getContratista(){
		return $this->contratistaId;
	}

	public function setContratista($contratistaId){
		$this->contratistaId = $contratistaId;
	}


	public static function all(){
		$db = Db::getConnect();
		$informeSalida = [];

		$select = $db->query('SELECT * FROM registro_salidas order by ID DESC');

		foreach($select->fetchAll() as $salidas){
			$informeSalida[] = new InformeSalidaPorFecha($salidas['ID'],$salidas['Fecha'],$salidas['Hora'],$salidas['UsuarioID'],$salidas['ContratistaID']);
		}
		
		return $informeSalida;

	}

//------------------------------------------------------------------------------------------------------

	public static function searchByFecha($fechaInicial, $fechaFinal){
		$db = Db::getConnect();
		$informeSalida = [];

		$select=$db->prepare('SELECT * FROM registro_salidas WHERE Fecha>=:fechaInicial and Fecha<=:fechaFinal');
		$select->bindValue('fechaInicial',$fechaInicial);
		$select->bindValue('fechaFinal',$fechaFinal);
		$select->execute();

		foreach($select->fetchAll() as $salidas){
			$informeSalida[] = new InformeSalidaPorFecha($salidas['ID'],$salidas['Fecha'],$salidas['Hora'],$salidas['UsuarioID'],$salidas['ContratistaID']);
		}
		
		return $informeSalida;

	}

	public static function searchByContratistaFecha($fechaInicial, $fechaFinal, $idContratista){
		$db = Db::getConnect();
		$informeSalida = [];

		$select=$db->prepare('SELECT * FROM registro_salidas WHERE Fecha>=:fechaInicial and Fecha<=:fechaFinal and ContratistaID=:idContratista');
		$select->bindValue('fechaInicial',$fechaInicial);
		$select->bindValue('fechaFinal',$fechaFinal);
		$select->bindValue('idContratista',$idContratista);
		$select->execute();

		foreach($select->fetchAll() as $salidas){
			$informeSalida[] = new InformeSalidaPorFecha($salidas['ID'],$salidas['Fecha'],$salidas['Hora'],$salidas['UsuarioID'],$salidas['ContratistaID']);
		}
		
		return $informeSalida;

	}

	public static function searchByContratista($idContratista){
		$db = Db::getConnect();
		$informeSalida = [];

		$select=$db->prepare('SELECT * FROM registro_salidas WHERE ContratistaID=:idContratista');
		$select->bindValue('idContratista',$idContratista);
		$select->execute();

		foreach($select->fetchAll() as $salidas){
			$informeSalida[] = new InformeSalidaPorFecha($salidas['ID'],$salidas['Fecha'],$salidas['Hora'],$salidas['UsuarioID'],$salidas['ContratistaID']);
		}
		
		return $informeSalida;

	}
	
	
}

?>