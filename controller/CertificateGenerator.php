<?php
require_once __DIR__ . '/../fpdf/fpdf.php'; // Adjust the path if necessary

class CertificateGenerator {
    public static function generateCertificate($userId, $userName, $quizName) {
        // Create a new PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add content to the certificate
        $pdf->Cell(0, 10, 'Certificate of Completion', 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "This certifies that", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(0, 10, strtoupper($userName), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, "has successfully completed the formation:", 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, $quizName, 0, 1, 'C');

        // Save the PDF to a file
        $fileName = __DIR__ . '/../certificates/certificate_user_' . $userId . '.pdf';
        $pdf->Output('F', $fileName); // Save the file locally

        return $fileName;
    }
}