<?php
require('fpdf/fpdf.php');
include('conexion.php');

class PDF extends FPDF {
    // Pie de página
    function Footer() {
        $this->SetY(-40); // 40 mm desde el final
        $this->SetFont('Arial', '', 9);
        $text = utf8_decode(
            "BCP: MIGUEL ANGEL AMPUERO HUARAYA\n" .
            "CTA. AHORROS - SOLES : CTA: 19193539117054\n" .
            "\tCCI: 002-19119353911705451\n" .
            "INTERBANK: MIGUEL ANGEL AMPUERO HUARAYA\n" .
            "CTA. AHORROS - SOLES : CTA: 8963412209925\n" .
            "\tCCI: 003-89601341220992549"
        );
        $this->MultiCell(0, 5, $text, 0, 'C');
    }
}

$id = $_GET['id'];
$sql = "SELECT * FROM contratos WHERE id = $id";
$resultado = $conexion->query($sql);
$contrato = $resultado->fetch_assoc();

$pdf = new PDF();
$pdf->AddPage();

$pageWidth = $pdf->GetPageWidth();
$logoWidth = 100;
$logoX = ($pageWidth - $logoWidth) / 2;

// Logo centrado en Y=10
$pdf->Image('../img/logo_contrato.png', $logoX, 10, $logoWidth);

// Número de contrato arriba a la derecha en Y=15
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetXY($pageWidth - 60, 15);
$pdf->Cell(50, 10, 'Contrato N-' . str_pad($contrato['id'], 4, '0', STR_PAD_LEFT), 0, 1, 'R');

// Datos del emisor debajo del logo, Y=45
$pdf->SetXY(10, 45);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pageWidth - 18, 5, utf8_decode("DE: MIGUEL ANGEL AMPUERO HUARAYA"), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell($pageWidth - 18, 5, utf8_decode("Correo: jmimpresionessac@hotmail.com
Oficina: Av. Panam. Norte Km. 13.5 C.C. Fiori 2do Piso Int. 238 Urb. Fiori (Frente al ex Terminal de Fiori - S.M.P. - Lima)
Taller: Av. Panam. Norte Km. 13.5 Int. 256A C.C. Fiori 2do Piso (Frente al ex Terminal de Fiori) S.M.P. - Lima."), 0, 'C');
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell($pageWidth - 18, 5, utf8_decode("BOLETAS DE VENTA - GUÍAS DE REMISIÓN - FACTURA - TARJETAS - VOLANTES - FOLDERS - ETC."), 0, 1, 'C');

// Posición fija para iniciar datos del contrato justo debajo del bloque anterior, por ejemplo Y=85
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(10, 80);
$pdf->Cell(40, 10, 'Contribuyente:');
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 80);
$pdf->Cell(0, 10, utf8_decode($contrato['nombre_empresa']), 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(10, 90);
$pdf->Cell(40, 10, 'RUC:');
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 90);
$pdf->Cell(0, 10, $contrato['ruc'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(10, 100);
$pdf->Cell(40, 10, 'Celular:');
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 100);
$pdf->Cell(0, 10, $contrato['telefono'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetXY(10, 110);
$pdf->Cell(40, 10, 'Fecha:');
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(50, 110);
$pdf->Cell(0, 10, $contrato['fecha'], 0, 1);

// Cabecera con fondo negro y texto blanco
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetXY(10, 130);
$pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(110, 10, 'Descripcion', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Importe', 1, 1, 'C', true);

// Filas normales sin relleno y texto negro
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetXY(10, 140);
$pdf->Cell(30, 10, number_format($contrato['cantidad'], 2), 1, 0, 'C');
$pdf->Cell(110, 10, utf8_decode($contrato['descripcion']), 1, 0);
$pdf->Cell(50, 10, 'S/ ' . number_format($contrato['importe_unitario'], 2), 1, 1, 'R');

// Totales, a la derecha
$posX = $pageWidth - 80;
$pdf->SetXY($posX, 155);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Total', 0, 0, 'R');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, 'S/ ' . number_format($contrato['total'], 2), 1, 1, 'R');

$pdf->SetXY($posX, 165);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'A Cuenta', 0, 0, 'R');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, 'S/ ' . number_format($contrato['a_cuenta'], 2), 1, 1, 'R');

$pdf->SetXY($posX, 175);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Saldo', 0, 0, 'R');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 10, 'S/ ' . number_format($contrato['saldo'], 2), 1, 1, 'R');

// Estado un poco más abajo
$pdf->SetXY(10, 190);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Estado:');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode($contrato['estado']), 0, 1);

// Firma
$pdf->SetXY(40, 220);
$pdf->Cell(60, 0, '', 'B');
$pdf->SetXY(40, 225);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(60, 10, 'Firma del Contribuyente', 0, 1, 'C');

$pdf->Output();
?>