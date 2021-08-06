<?php
ob_start();

require('../fpdf/fpdf.php');

require_once('../Model/Material.php');
require_once('../Model/Usuario.php');
require_once('../Model/Contratista.php');
require_once('../Model/Proyecto.php');
require_once('../Model/Manzana.php');
require_once('../Model/Casa.php');
require_once('../Model/Area.php');
require_once('../Model/Destino.php');
require_once('../Model/RegistroSalidas.php');
require_once('../Model/MaterialRegistroSalidas.php');
require_once('../connection.php');

session_start();


$registroSalidas = RegistroSalidas::searchSalida($_SESSION['idSalida']);

$MaterialRegistroSalidas = MaterialRegistroSalidas::searchMaterialRegistroSalidas($registroSalidas->getId());

class SalidaMaterialPDF extends FPDF
{
// Cabecera de página
function Header()
{

    $registroSalidas = RegistroSalidas::searchSalida($_SESSION['idSalida']);
    $proyecto = Proyecto::searchById($registroSalidas->getProyecto());
    // Logo
    $this->Image('../fpdf/tutorial/logo.png',10,5,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(120,10,'REPORTE DE SALIDA DE MATERIAL',0,1,'C');

    $this->SetFont('Arial','I',10);
    $this->Cell(0,4, 'Salida No: '.$registroSalidas->getId().'', 0, 1, 'C');
    $this->Cell(0,4,'Fecha: '.$registroSalidas->getFecha().'  /  Hora: '.$registroSalidas->getHora().'',0,1,'C');
    $this->Cell(0,4, 'Proyecto: '.$proyecto->getDescripcion().'', 0, 1, 'C');
    // Salto de línea
    $this->Ln(5);
    $this->SetFont('Arial','B',12);
    $this->Cell(30, 10, 'Codigo', 0, 0, 'C', 0);
    $this->Cell(60, 10, 'Descripcion', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Unidad', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Cantidad', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Destino', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Casa', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Manzana', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Etapa', 0, 1, 'C', 0);

}

// Pie de página
function Footer()
{

    $registroSalidas = RegistroSalidas::searchSalida($_SESSION['idSalida']);
    $usuario = Usuario::searchByCodigoUser($registroSalidas->getUsuario());
    $contratista = Contratista::searchById($registroSalidas->getContratista());

    //$MaterialRegistroSalidas = MaterialRegistroSalidas::searchMaterialRegistroSalidas($registroSalidas->getId());
    // Posición: a 1,5 cm del final
    $this->SetY(185);
    // Arial italic 8
    $this->SetFont('Arial','I',10);
    // Número de página
    $linea = "___________________________________";
    $this->Cell(100,10, $linea, 0, 0, 'C', 0);
    $this->Cell(220,10, $linea, 0, 1, 'C', 0);
    $this->Cell(100,0, 'Entrega: '.$usuario->getNombre().' '.$usuario->getApellido().'', 0, 0, 'C', 0);
    $this->Cell(220,0, 'Recibe: '.$contratista->getDescripcion().'', 0, 1, 'C', 0);
    //$this->SetFont('Arial','I',10);
    $this->Cell(0,15,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
}
}




    
$pdf = new SalidaMaterialPDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

foreach ($MaterialRegistroSalidas as $salidas){

    $material = Material::searchById($salidas->getMaterialId());
    $manzana = Manzana::searchById($salidas->getManzanaId());
    $casa = Casa::searchById($salidas->getCasaId());
    $area = Area::searchById($salidas->getAreaId());
    $destino = Destino::searchById($salidas->getDestinoId());

    $pdf->Cell(30, 6, $material->getCodigo(), 0, 0, 'C', 0);
    $pdf->Cell(60, 6, $material->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $material->getUnidad(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $salidas->getCantidad(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $destino->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $casa->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $manzana->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $area->getDescripcion(), 0, 1, 'C', 0);
    

}
ob_end_clean();
$pdf->Output(); 
ob_end_flush();


?>