<?php
ob_start();

require('../fpdf/fpdf.php');
//require_once('../Model/Material.php');
require_once('../connection.php');
require_once('../Model/InformeEntrada.php');
require_once('../Model/Usuario.php');

session_start();

class InformeEntradaPDF extends FPDF
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
    $this->Cell(40,10,'INFORME GENERAL DE ENTRADA',0,1,'C');

    $this->SetFont('Arial','I',10);
    $this->Cell(0,5,'Fecha: '.date('Y-m-d').'  /  Hora: '.date('H:i:s').'',0,1,'C');
    // Salto de línea
    $this->Ln(8);
    $this->SetFont('Arial','B',12);
    $this->Cell(40, 10, 'Entrada No', 0, 0, 'C', 0);
    $this->Cell(48, 10, 'Fecha', 0, 0, 'C', 0);
    $this->Cell(48, 10, 'Hora', 0, 0, 'C', 0);
    $this->Cell(56, 10, 'Usuario', 0, 1, 'C', 0);
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


$informeGeneral = $_SESSION['informeEntradaGeneral'];

   
$pdf = new InformeEntradaPDF();
$pdf->SetMargins(10,10,10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

foreach ($informeGeneral as $material){

    $usuario = Usuario::searchByCodigoUser($material->getUsuario());

    $pdf->Cell(40, 10, $material->getId(), 0, 0, 'C', 0);
    $pdf->Cell(48, 10, $material->getFecha(), 0, 0, 'C', 0);
    $pdf->Cell(48, 10, $material->getHora(), 0, 0, 'C', 0);
    $pdf->Cell(56, 10, $usuario->getNombre().' '.$usuario->getApellido(), 0, 1, 'C', 0);

}
ob_end_clean();
$pdf->Output(); 
ob_end_flush();


?>