<?php
ob_start();

require('../fpdf/fpdf.php');
require_once('../Model/Material.php');
require_once('../Model/Usuario.php');
require_once('../Model/RegistroSalidas.php');
require_once('../Model/MaterialRegistroSalidas.php');
require_once('../Model/InformeSalidaPorMaterialFecha.php');
require_once('../Model/Manzana.php');
require_once('../Model/Casa.php');
require_once('../Model/Area.php');
require_once('../Model/Contratista.php');
require_once('../Model/Proyecto.php');
require_once('../connection.php');

session_start();


class InformeSalidaPorMaterialFechaPDF extends FPDF
{
// Cabecera de página
function Header()
{

    // Logo
    $this->Image('../fpdf/tutorial/logo.png',10,5,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(120,10,'REPORTE DE SALIDA POR MATERIAL',0,1,'C');

    
    // Salto de línea
    $this->Ln(14);
    $this->SetFont('Arial','B',12);
    $this->Cell(10, 10, 'Codigo', 0, 0, 'C', 0);
    $this->Cell(60, 10, 'Descripcion', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Unidad', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Cantidad', 0, 0, 'C', 0);
    $this->Cell(35, 10, 'Contratista', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Proyecto', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Etapa', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Manzana', 0, 0, 'C', 0);
    $this->Cell(15, 10, 'Casa', 0, 0, 'C', 0);
    $this->Cell(25, 10, 'Fecha', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Hora', 0, 1, 'C', 0);
}

// Pie de página
function Footer()
{

    
    // Posición: a 1,5 cm del final
    $this->SetY(185);
    // Arial italic 8
    $this->SetFont('Arial','I',10);
    // Número de página
    $linea = "___________________________________";
    $this->Cell(100,10, $linea, 0, 1, 'C', 0);
    $this->Cell(100,0, 'Genera: '.$_SESSION['usuario']->getNombre().' '.$_SESSION['usuario']->getApellido().'', 0, 1, 'C', 0);
    //$this->SetFont('Arial','I',10);
    $this->Cell(0,15,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
}
}

$materialRegistroSalidas = $_SESSION['informeGeneralSalidaPorMaterialFecha'];


    
$pdf = new InformeSalidaPorMaterialFechaPDF('L');
$pdf->SetMargins(10,10,10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

foreach ($materialRegistroSalidas as $salida){

    $material = Material::searchById($salida->getMaterialId());
    $registroSalidas = RegistroSalidas::searchSalida($salida->getRegistroSalidasId());;
    $manzana = Manzana::searchById($salida->getManzanaId());
    $casa = Casa::searchById($salida->getCasaId());
    $area = Area::searchById($salida->getAreaId());
    $contratista = Contratista::searchById($registroSalidas->getContratista());
    $proyecto = Proyecto::searchById($registroSalidas->getProyecto());

    $pdf->Cell(10, 6, $material->getCodigo(), 0, 0, 'C', 0);
    $pdf->Cell(60, 6, $material->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $material->getUnidad(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $salida->getCantidad(), 0, 0, 'C', 0);
    $pdf->Cell(35, 6, $contratista->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $proyecto->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $area->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $manzana->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(15, 6, $casa->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(25, 6, $registroSalidas->getFecha(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $registroSalidas->getHora(), 0, 1, 'C', 0);

}
ob_end_clean();
$pdf->Output(); 
ob_end_flush();


?>