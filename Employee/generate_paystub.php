<?php
require('fpdf/fpdf.php'); // Make sure you have FPDF installed and in the same folder or autoloaded

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(0, 10, "Employee Paystub", 0, 1, 'C');
    $pdf->Ln(10);

    foreach ($_POST as $key => $value) {
        $label = str_replace('_', ' ', $key);
        $pdf->Cell(60, 10, ucwords($label) . ":", 0, 0);
        $pdf->Cell(60, 10, $value, 0, 1);
    }

    $pdf->Output('D', 'paystub.pdf');
    exit;
}
?>
