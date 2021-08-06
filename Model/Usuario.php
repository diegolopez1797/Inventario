<?php 
/**
* 
*/
class Usuario
{
	private $Id;
	private $Identificacion;
	private $Nombre;
	private $Apellido;
	private $Clave;
	private $RolId;

	
	function __construct($Id, $Identificacion, $Nombre, $Apellido, $Clave, $RolId)
	{
		$this->setId($Id);
		$this->setIdentificacion($Identificacion);
		$this->setNombre($Nombre);
		$this->setApellido($Apellido);
		$this->setClave($Clave);
		$this->setRolId($RolId);			
	}

	public function getId(){
		return $this->Id;
	}

	public function setId($Id){
		$this->Id = $Id;
	}

	public function getIdentificacion(){
		return $this->Identificacion;
	}

	public function setIdentificacion($Identificacion){
		$this->Identificacion = $Identificacion;
	}

	public function getNombre(){
		return $this->Nombre;
	}

	public function setNombre($Nombre){
		$this->Nombre = $Nombre;
	}

	public function getApellido(){
		return $this->Apellido;
	}

	public function setApellido($Apellido){
		$this->Apellido = $Apellido;
	}

	public function getClave(){
		return $this->Clave;
	}

	public function setClave($Clave){
		$this->Clave = $Clave;
	}

	public function getRolId(){
		return $this->RolId;
	}

	public function setRolId($RolId){
		$this->RolId = $RolId;
	}


	//------Busqueda de usuarios por IDENTIFICACION------------------

	public static function searchByIdUser($Identificacion){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM usuario WHERE Identificacion=:Identificacion');
		$select->bindValue('Identificacion',$Identificacion);
		$select->execute();

		$Usuario = $select->fetch();


		$listaUsuario = new Usuario($Usuario['ID'],$Usuario['Identificacion'],$Usuario['Nombre'],$Usuario['Apellido'],$Usuario['Clave'],$Usuario['RolID']);
	
		return $listaUsuario;


	}

//-------- BUSQUEDA POR ID--------------------------------------------------------------------
	public static function searchByCodigoUser($codigo){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM usuario WHERE ID=:Codigo');
		$select->bindValue('Codigo',$codigo);
		$select->execute();

		$Usuario = $select->fetch();


		$listaUsuario = new Usuario($Usuario['ID'],$Usuario['Identificacion'],$Usuario['Nombre'],$Usuario['Apellido'],$Usuario['Clave'],$Usuario['RolID']);
	
		return $listaUsuario;


	}

//-----------------------------------------------------------------------------------------------------------

	public static function verificarUsuario($Identificacion, $Clave){

		$db=Db::getConnect();
		$select=$db->prepare('SELECT * FROM usuario WHERE Clave=:Clave and Identificacion=:Identificacion');
		$select->bindValue('Identificacion',$Identificacion);
		$select->bindValue('Clave',$Clave);
		$select->execute();

		if ($select->rowCount()) {
			return true;
		}else{
			return false;
		}

		
	}

//----------------------LISTA TODOS LOS USUARIOS EXISTENTES EN SISTEMA---------------------------------

	public static function all(){
		$db = Db::getConnect();
		$listaUsuario = [];

		$select = $db->query('SELECT * FROM usuario order by ID');

		foreach($select->fetchAll() as $Usuario){
			$listaUsuario[] = new Usuario($Usuario['ID'],$Usuario['Identificacion'],$Usuario['Nombre'],$Usuario['Apellido'],$Usuario['Clave'],$Usuario['RolID']);
		}
		
		return $listaUsuario;
	}

//----------------------------------------------------------------------------------------------
	public static function save($usuario){
		
		$db=Db::getConnect();
		$insert=$db->prepare('INSERT INTO usuario VALUES (null,:Identificacion,:Nombre,:Apellido,:Clave,:RolID)');
		$insert->bindValue('Identificacion',$usuario->getIdentificacion());
		$insert->bindValue('Nombre',$usuario->getNombre());
		$insert->bindValue('Apellido',$usuario->getApellido());
		$insert->bindValue('Clave',$usuario->getClave());
		$insert->bindValue('RolID',$usuario->getRolId());
		$insert->execute();

		return $insert;

	}

//---------------------------------------------------------------------------------------------

	public static function delete($id){
		$db=Db::getConnect();
		$delete=$db->prepare('DELETE FROM usuario WHERE ID=:ID');
		$delete->bindValue('ID',$id);
		$delete->execute();		

		return $delete;
	}

//---------------------------------------------------------------------------------------

	public static function update($usuario){
		$db=Db::getConnect();
		$update=$db->prepare('UPDATE usuario SET Identificacion=:Identificacion, Nombre=:Nombre, Apellido=:Apellido, Clave=:Clave, RolID=:RolID WHERE ID=:ID');
		$update->bindValue('Identificacion', $usuario->getIdentificacion());
		$update->bindValue('Nombre', $usuario->getNombre());
		$update->bindValue('Apellido',$usuario->getApellido());
		$update->bindValue('Clave',$usuario->getClave());
		$update->bindValue('RolID',$usuario->getRolId());
		$update->bindValue('ID',$usuario->getId());
		$update->execute();
	}


}

?>