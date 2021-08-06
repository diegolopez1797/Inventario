<?php 
/**
* 
*/


class RegistroSalidas
{
	private $Id;
	private $Fecha;
	private $Hora;
	private $Usuario;
	private $contratistaId;
	private $proyectoId;

	
	function __construct($Id, $Fecha, $Hora, $Usuario, $contratistaId, $proyectoId)
	{
		$this->setId($Id);
		$this->setFecha($Fecha);
		$this->setHora($Hora);
		$this->setUsuario($Usuario);
		$this->setContratista($contratistaId);
		$this->setProyecto($proyectoId);	
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

	public function getProyecto(){
		return $this->proyectoId;
	}

	public function setProyecto($proyectoId){
		$this->proyectoId = $proyectoId;
	}



	public static function save($registroSalidas1){
		$db = Db::getConnect();
		
		$insert=$db->prepare('INSERT INTO registro_salidas VALUES (null,:fechaActual,:Hora,:UsuarioID,:ContratistaID,:ProyectoID)');
		$insert->bindValue('fechaActual',$registroSalidas1->getFecha());
		$insert->bindValue('Hora',$registroSalidas1->getHora());
		$insert->bindValue('UsuarioID',$registroSalidas1->getUsuario());
		$insert->bindValue('ContratistaID',$registroSalidas1->getContratista());
		$insert->bindValue('ProyectoID',$registroSalidas1->getProyecto());
		$insert->execute();

		return $insert;
	}


	public static function searchUltimoId(){
		$db = Db::getConnect();

		$registroSalidas = [];

		$select = $db->prepare('SELECT * FROM registro_salidas order by ID');
		$select->execute();
		
		foreach($select->fetchAll() as $salidas){
			$registroSalidas[] = new RegistroSalidas($salidas['ID'],$salidas['Fecha'],$salidas['Hora'],$salidas['UsuarioID'],$salidas['ContratistaID'],$salidas['ProyectoID']);
		}
		return $registroSalidas;

	}

	public static function searchSalida($Id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT * FROM registro_salidas WHERE ID=:ID');
		$select->bindValue('ID',$Id);
		$select->execute();

		$salida = $select->fetch();

		$registroSalidas = new RegistroSalidas($salida['ID'],$salida['Fecha'],$salida['Hora'],$salida['UsuarioID'],$salida['ContratistaID'],$salida['ProyectoID']);
		
		return $registroSalidas;

	}

	
	
}

?>