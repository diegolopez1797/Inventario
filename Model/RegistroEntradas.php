<?php 
/**
* 
*/


class RegistroEntradas
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



	public static function save($registroEntradas1){
		$db = Db::getConnect();
		
		$insert=$db->prepare('INSERT INTO registro_entradas VALUES (null,:fechaActual,:hora,:usuario)');
		$insert->bindValue('fechaActual',$registroEntradas1->getFecha());
		$insert->bindValue('hora',$registroEntradas1->getHora());
		$insert->bindValue('usuario',$registroEntradas1->getUsuario());
		$insert->execute();

		return $insert;
	}


	public static function searchUltimoId(){
		$db = Db::getConnect();

		$registroEntradas = [];

		$select = $db->prepare('SELECT * FROM registro_entradas order by ID');
		$select->execute();
		
		foreach($select->fetchAll() as $entradas){
			$registroEntradas[] = new RegistroEntradas($entradas['ID'],$entradas['Fecha'],$entradas['Hora'],$entradas['UsuarioID']);
		}
		return $registroEntradas;

	}

	public static function searchEntrada($Id){
		$db = Db::getConnect();

		$select = $db->prepare('SELECT * FROM registro_entradas WHERE ID=:ID');
		$select->bindValue('ID',$Id);
		$select->execute();

		$entrada = $select->fetch();

		$registroEntradas = new RegistroEntradas($entrada['ID'],$entrada['Fecha'],$entrada['Hora'],$entrada['UsuarioID']);
		
		return $registroEntradas;

	}

	
	
}

?>
