<?php
ob_start();

require('../fpdf/fpdf.php');
//require_once('../Model/Material.php');
require_once('../connection.php');
require_once('../Model/InformeSalida.php');
require_once('../Model/Usuario.php');
require_once('../Model/Contratista.php');

session_start();

class InformeSalidaPDF extends FPDF
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
    $this->Cell(40,10,'INFORME GENERAL DE SALIDA',0,1,'C');

    $this->SetFont('Arial','I',10);
    $this->Cell(0,5,'Fecha: '.date('Y-m-d').'  /  Hora: '.date('H:i:s').'',0,1,'C');
    // Salto de línea
    $this->Ln(8);
    $this->SetFont('Arial','B',12);
    $this->Cell(30, 10, 'Salida No', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Fecha', 0, 0, 'C', 0);
    $this->Cell(30, 10, 'Hora', 0, 0, 'C', 0);
    $this->Cell(50, 10, 'Usuario', 0, 0, 'C', 0);
    $this->Cell(50, 10, 'Contratista', 0, 1, 'C', 0);
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


$informeGeneral = $_SESSION['informeSalidaGeneral'];

   
$pdf = new InformeSalidaPDF();
$pdf->SetMargins(10,10,10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

foreach ($informeGeneral as $material){

    $usuario = Usuario::searchByCodigoUser($material->getUsuario());
    $contratista = Contratista::searchById($material->getContratista());

    $pdf->Cell(30, 10, $material->getId(), 0, 0, 'C', 0);
    $pdf->Cell(30, 10, $material->getFecha(), 0, 0, 'C', 0);
    $pdf->Cell(30, 10, $material->getHora(), 0, 0, 'C', 0);
    $pdf->Cell(50, 10, $usuario->getNombre().' '.$usuario->getApellido(), 0, 0, 'C', 0);
    $pdf->Cell(50, 10, $contratista->getDescripcion(), 0, 1, 'C', 0);

}
ob_end_clean();
$pdf->Output(); 
ob_end_flush();


?>