<?php
ob_start();

require('../fpdf/fpdf.php');
require_once('../Model/Material.php');
require_once('../Model/Usuario.php');
require_once('../Model/RegistroEntradas.php');
require_once('../Model/MaterialRegistroEntradas.php');
require_once('../Model/InformeEntradaPorMaterialFecha.php');
require_once('../connection.php');

session_start();


class InformeEntradaPorMaterialFechaPDF extends FPDF
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
    $this->Cell(40,10,'REPORTE DE ENTRADA POR MATERIAL',0,1,'C');

    
    // Salto de línea
    $this->Ln(14);
    $this->SetFont('Arial','B',12);
    $this->Cell(20, 10, 'Codigo', 0, 0, 'C', 0);
    $this->Cell(60, 10, 'Descripcion', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Unidad', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Cantidad', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Fecha', 0, 0, 'C', 0);
    $this->Cell(20, 10, 'Hora', 0, 1, 'C', 0);
}

// Pie de página
function Footer()
{

    
    // Posición: a 1,5 cm del final
    $this->SetY(250);
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

$materialRegistroEntradas = $_SESSION['informeGeneralPorMaterialFecha'];


    
$pdf = new InformeEntradaPorMaterialFechaPDF();
$pdf->SetMargins(10,10,10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

foreach ($materialRegistroEntradas as $entrada){

    $material = Material::searchById($entrada->getMaterialId());
    $registroEntradas = RegistroEntradas::searchEntrada($entrada->getRegistroEntradasId());;

    $pdf->Cell(20, 6, $material->getCodigo(), 0, 0, 'C', 0);
    $pdf->Cell(60, 6, $material->getDescripcion(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $material->getUnidad(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $entrada->getCantidad(), 0, 0, 'C', 0);
    $pdf->Cell(30, 6, $registroEntradas->getFecha(), 0, 0, 'C', 0);
    $pdf->Cell(20, 6, $registroEntradas->getHora(), 0, 1, 'C', 0);

}
ob_end_clean();
$pdf->Output(); 
ob_end_flush();


?>