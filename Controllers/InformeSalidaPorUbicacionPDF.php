<?php
ob_start();

require('../fpdf/fpdf.php');
require_once('../connection.php');

session_start();

class InformeSalidaPorUbicacionPDF extends FPDF
{
function Header()
{
    $this->Image(__DIR__ . '/../fpdf/tutorial/logoInf.png',12,12,50);
    $this->SetFont('Arial','B',15);
    $this->Cell(80);
    $this->Cell(40,10,'SALIDAS POR UBICACION',0,1,'C');

    $this->SetFont('Arial','I',10);
    $proyectoNombre = isset($_SESSION['informeSalidaPorUbicacionProyecto']) ? $_SESSION['informeSalidaPorUbicacionProyecto']->getDescripcion() : '';
    $this->Cell(0,5,'Proyecto: '.$proyectoNombre,0,1,'C');
    $this->Cell(0,5,'Fecha: '.date('Y-m-d').'  /  Hora: '.date('H:i:s').'',0,1,'C');
    $this->Ln(8);
    $this->SetFont('Arial','B',10);
    $this->Cell(60, 10, 'Ubicacion', 1, 0, 'C', 0);
    $this->Cell(60, 10, 'Material', 1, 0, 'C', 0);
    $this->Cell(35, 10, 'Cantidad total', 1, 0, 'C', 0);
    $this->Cell(35, 10, 'No. documentos', 1, 1, 'C', 0);
}

function Footer()
{
    $this->SetY(-20);
    $this->SetFont('Arial','I',10);
    $linea = "___________________________________";
    $this->Cell(100,10, $linea, 0, 1, 'C', 0);
    $this->Cell(100,0, 'Genera: '.$_SESSION['usuario']->getNombre().' '.$_SESSION['usuario']->getApellido().'', 0, 1, 'C', 0);
    $this->Cell(0,15,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
}
}

$grupos = isset($_SESSION['informeSalidaPorUbicacionGeneral']) ? $_SESSION['informeSalidaPorUbicacionGeneral'] : [];

$pdf = new InformeSalidaPorUbicacionPDF();
$pdf->SetMargins(10,10,10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',9);

foreach ($grupos as $grupo){
    $pdf->Cell(60, 10, $grupo['UbicacionRuta'], 1, 0, 'L', 0);
    $pdf->Cell(60, 10, $grupo['MaterialCodigo'].' - '.$grupo['MaterialDescripcion'], 1, 0, 'L', 0);
    $pdf->Cell(35, 10, $grupo['CantidadTotal'], 1, 0, 'C', 0);
    $pdf->Cell(35, 10, $grupo['TotalDocumentos'], 1, 1, 'C', 0);
}
ob_end_clean();
$pdf->Output();
ob_end_flush();

?>
