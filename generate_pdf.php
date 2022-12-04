<?php

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

use Fpdf\Fpdf;

// Get the number of PDF documents to generate from the form
$numPdfs = (int)$_GET['num_pdfs'];

// Validate the number of PDF documents
if (empty($numPdfs) || $numPdfs < 1 || $numPdfs > 15) {
    // Set the number of PDF documents to 1 if the user did not select a valid number
    $numPdfs = 1;
}

// Check if the number of PDF documents is 1
if ($numPdfs == 1) {
    
    // Set up the PDF document
    $pdf = new Fpdf();
    
    // Add the user's string to the PDF
    addUserStringToPdf($pdf);

    // Output the PDF document to the browser
    $pdf->Output('random_doc.pdf', 'D');

} else {
    // Create a zip archive to store the PDF documents
    $zip = new ZipArchive();
    $zipFilename = 'random_documents.zip';
    $zip->open($zipFilename, ZipArchive::CREATE);

    // Generate the PDF documents
    for ($i = 1; $i <= $numPdfs; $i++) {
        
        // Set up the PDF document
        $pdf = new Fpdf();

        // Add the user's string to the PDF
        addUserStringToPdf($pdf);

        // Output the PDF document to a variable
        ob_start();
        $pdf->Output();
        $pdfContent = ob_get_contents();
        ob_end_clean();

        // Add the PDF to the zip archive
        $zip->addFromString("random_doc_$i.pdf", $pdfContent);
    }

    // Close the zip archive
    $zip->close();

    // Send the zip file to the browser
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="'. $zipFilename. '"');
    header('Content-Length: ' . filesize($zipFilename));
    readfile($zipFilename);

    // Delete the zip file
    unlink($zipFilename);
}

/**
 * Adds a user string to a PDF document.
 *
 * @param Fpdf $pdf The PDF document.
 */
function addUserStringToPdf($pdf)
{
    // Generate a random string
    $userString = bin2hex(random_bytes(16));
    
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 16);
    $pdf->Cell(40, 10, $userString);
}
