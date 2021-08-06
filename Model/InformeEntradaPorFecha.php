<?php 
/**
* 
*/


class InformeEntradaPorFecha
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $Usuario;

	
	function __construct($Id, $Fecha, $Hora, $Usuario)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setUsuario($Usuario);	
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

//-----------------------------------------------------------------------------------------------------

	public static function all(){
		$db = Db::getConnect();
		$informeEntrada = [];

		$select = $db->query('SELECT * FROM registro_entradas order by ID DESC');

		foreach($select->fetchAll() as $entrada){
			$informeEntrada[] = new InformeEntradaPorFecha($entrada['ID'],$entrada['Fecha'],$entrada['Hora'],$entrada['UsuarioID']);
		}
		
		return $informeEntrada;

	}

//------------------------------------------------------------------------------------------------------

	public static function searchByFecha($fechaInicial, $fechaFinal){
		$db = Db::getConnect();
		$informeEntrada = [];

		$select=$db->prepare('SELECT * FROM registro_entradas WHERE Fecha>=:fechaInicial and Fecha<=:fechaFinal');
		$select->bindValue('fechaInicial',$fechaInicial);
		$select->bindValue('fechaFinal',$fechaFinal);
		$select->execute();

		foreach($select->fetchAll() as $entrada){
			$informeEntrada[] = new InformeEntradaPorFecha($entrada['ID'],$entrada['Fecha'],$entrada['Hora'],$entrada['UsuarioID']);
		}
		
		return $informeEntrada;

	}


//----------------------------------------------------------------------------------------------------

	
	
}

?>