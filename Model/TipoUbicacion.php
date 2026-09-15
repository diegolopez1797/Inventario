<?php
/**
* Catalogo de tipos SUGERIDOS de ubicacion (Torre, Piso, Casa...). No es una FK dura
* sobre ubicacion.Tipo - existe para que el usuario elija de una lista consistente en
* vez de escribir texto libre cada vez, y para que un Administrador pueda agregar tipos
* nuevos sin tocar codigo. La opcion "Otro" en los formularios sigue aceptando texto libre.
*/
class TipoUbicacion
{
	private $Id;
	private $Codigo;
	private $Descripcion;
	private $Activo;


	function __construct($Id, $Codigo, $Descripcion, $Activo)
	{
		$this->setId($Id);
		$this->setCodigo($Codigo);
		$this->setDescripcion($Descripcion);
		$this->setActivo($Activo);
	}

	public function getId(){ return $this->Id; }
	public function setId($Id){ $this->Id = $Id; }

	public function getCodigo(){ return $this->Codigo; }
	public function setCodigo($Codigo){ $this->Codigo = $Codigo; }

	public function getDescripcion(){ return $this->Descripcion; }
	public function setDescripcion($Descripcion){ $this->Descripcion = $Descripcion; }

	public function getActivo(){ return $this->Activo; }
	public function setActivo($Activo){ $this->Activo = $Activo; }


	public static function all(){
		$db = Db::getConnect();
		$lista = [];
		$select = $db->query("SELECT * FROM tipo_ubicacion WHERE Activo = 1 ORDER BY Descripcion");

		foreach ($select->fetchAll() as $fila) {
			$lista[] = new TipoUbicacion($fila['ID'], $fila['Codigo'], $fila['Descripcion'], $fila['Activo']);
		}

		return $lista;
	}

	public static function searchById($id){
		$db = Db::getConnect();
		$select = $db->prepare("SELECT * FROM tipo_ubicacion WHERE ID=:ID");
		$select->bindValue('ID', $id);
		$select->execute();
		$fila = $select->fetch();

		return new TipoUbicacion($fila['ID'], $fila['Codigo'], $fila['Descripcion'], $fila['Activo']);
	}

	public static function save($tipo){
		$db = Db::getConnect();
		$insert = $db->prepare("INSERT INTO tipo_ubicacion (ID, Codigo, Descripcion, Activo) VALUES (null, :Codigo, :Descripcion, 1)");
		$insert->bindValue('Codigo', strtoupper($tipo->getDescripcion()));
		$insert->bindValue('Descripcion', $tipo->getDescripcion());
		$insert->execute();

		return $insert;
	}

	public static function update($tipo){
		$db = Db::getConnect();
		$update = $db->prepare("UPDATE tipo_ubicacion SET Descripcion=:Descripcion WHERE ID=:ID");
		$update->bindValue('Descripcion', $tipo->getDescripcion());
		$update->bindValue('ID', $tipo->getId());
		$update->execute();
	}

	public static function desactivar($id){
		$db = Db::getConnect();
		$update = $db->prepare("UPDATE tipo_ubicacion SET Activo=0 WHERE ID=:ID");
		$update->bindValue('ID', $id);
		$update->execute();
	}

	// Normaliza un texto de tipo ingresado por el usuario contra el catalogo (comparacion
	// insensible a mayusculas/acentos simples). Si hay coincidencia, devuelve la Descripcion
	// canonica del catalogo (para que "apartamento"/"APARTAMENTO"/"Apartamento" siempre se
	// guarden igual); si no hay coincidencia, devuelve el texto ingresado tal cual (caso "Otro").
	public static function normalizar($tipoIngresado){
		$tipoIngresado = trim($tipoIngresado);
		if ($tipoIngresado === '') {
			return $tipoIngresado;
		}

		foreach (self::all() as $tipoCatalogo) {
			if (mb_strtolower($tipoCatalogo->getDescripcion()) === mb_strtolower($tipoIngresado)) {
				return $tipoCatalogo->getDescripcion();
			}
		}

		return $tipoIngresado;
	}
}

?>
