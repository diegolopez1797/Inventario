<?php
ob_start();

require('../fpdf/fpdf.php');
require_once('../connection.php');
require_once('../Model/Usuario.php');
require_once('../Model/Contratista.php');
require_once('../Model/Proveedor.php');

session_start();

class MovimientosPDF extends FPDF
{
// Cabecera de página
function Header()
{
    $this->Image(__DIR__ . '/../fpdf/tutorial/logoInf.png',12,12,50);
    $this->SetFont('Arial','B',15);
    $this->Cell(80);
    $this->Cell(40,10,'INFORME DE MOVIMIENTOS',0,1,'C');

    $this->SetFont('Arial','I',10);
    $this->Cell(0,5,'Fecha: '.date('Y-m-d').'  /  Hora: '.date('H:i:s').'',0,1,'C');
    $this->Ln(8);
    $this->SetFont('Arial','B',10);
    $this->Cell(25, 10, 'Documento', 1, 0, 'C', 0);
    $this->Cell(20, 10, 'Tipo', 1, 0, 'C', 0);
    $this->Cell(22, 10, 'Fecha', 1, 0, 'C', 0);
    $this->Cell(45, 10, 'Material', 1, 0, 'C', 0);
    $this->Cell(18, 10, 'Cantidad', 1, 0, 'C', 0);
    $this->Cell(30, 10, 'Usuario', 1, 0, 'C', 0);
    $this->Cell(30, 10, 'Contratista/Proveedor', 1, 1, 'C', 0);
}

// Pie de pagina, posicion relativa al final de la hoja (no un valor fijo) para que no se
// desalinee si el contenido de una pagina es mas corto - mismo ajuste ya identificado como
// pendiente para el resto de informes (Fase 3 seccion 18).
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

$movimientos = isset($_SESSION['movimientosGeneral']) ? $_SESSION['movimientosGeneral'] : [];

$pdf = new MovimientosPDF();
$pdf->SetMargins(10,10,10);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',9);

foreach ($movimientos as $mov){

    $esEntrada = $mov['Tipo'] === 'ENTRADA';
    $usuario = Usuario::searchByCodigoUser($mov['UsuarioID']);

    $contratistaProveedor = '';
    if ($esEntrada && !empty($mov['ProveedorID'])) {
        $contratistaProveedor = Proveedor::searchById($mov['ProveedorID'])->getDescripcion();
    } elseif (!$esEntrada && !empty($mov['ContratistaID'])) {
        $contratistaProveedor = Contratista::searchById($mov['ContratistaID'])->getDescripcion();
    }

    $pdf->Cell(25, 10, ($esEntrada ? 'Entrada #' : 'Salida #').$mov['DocumentoID'], 1, 0, 'C', 0);
    $pdf->Cell(20, 10, $esEntrada ? 'Entrada' : 'Salida', 1, 0, 'C', 0);
    $pdf->Cell(22, 10, $mov['Fecha'], 1, 0, 'C', 0);
    $pdf->Cell(45, 10, $mov['MaterialCodigo'].' - '.$mov['MaterialDescripcion'], 1, 0, 'L', 0);
    $pdf->Cell(18, 10, $mov['Cantidad'], 1, 0, 'C', 0);
    $pdf->Cell(30, 10, $usuario->getNombre().' '.$usuario->getApellido(), 1, 0, 'C', 0);
    $pdf->Cell(30, 10, $contratistaProveedor, 1, 1, 'C', 0);

}
ob_end_clean();
$pdf->Output();
ob_end_flush();

?>
