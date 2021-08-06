<?php 
/**
* 
*/


class InformeSalida
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

//---------------------------------------------------------------------------------

	public static function all(){
			$db = Db::getConnect();
			$informeSalida = [];

			$select = $db->query('SELECT * FROM registro_salidas order by ID DESC LIMIT 0, 100');

			foreach($select->fetchAll() as $salida){
				$informeSalida[] = new InformeSalida($salida['ID'],$salida['Fecha'],$salida['Hora'],$salida['UsuarioID'],$salida['ContratistaID']);
			}
			
			return $informeSalida;

	}

//-------------------------------------------------------------------------------

	public static function searchById($id){
		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM registro_salidas WHERE ID=:ID');
		$select->bindValue('ID',$id);
		$select->execute();

		$salida = $select->fetch();

		$informeSalida = new InformeSalida($salida['ID'],$salida['Fecha'],$salida['Hora'],$salida['UsuarioID'],$salida['ContratistaID']);

		return $informeSalida;

	}

//----------------------------------------------------------------------------------------------------

	
	
}

?>