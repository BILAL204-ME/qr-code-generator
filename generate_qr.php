<?php
// Check if autoload file exists
$autoloadPath = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Autoload file not found: " . $autoloadPath);
}
require $autoloadPath;

/**
 * Install Dependencies endroid/qr-code (by composer)
 */

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\PdfWriter;

// Debug: Check if Endroid classes are loaded
if (!class_exists(Builder::class)) {
    die("Endroid QR Code classes not loaded. Check composer installation.");
}

// Validate URL 
if (!isset($_GET['url']) || empty($_GET['url'])) {
    die("Error: URL is Required. ");
}

$url = filter_var($_GET['url'], FILTER_VALIDATE_URL);
if (!$url) {
    die("Error: Invalid URL.");
}

// Determine format
$format = isset($_GET['format']) ? $_GET['format'] : 'png';

// Generate the QR code
try {
    if ($format === 'png') {
        $writer = new PngWriter();
        $filename = 'qr_code.png';
        $contentType = 'image/png';
    } elseif ($format === 'pdf') {
        $writer = new PdfWriter();
        $filename = 'qr_code.pdf';
        $contentType = 'application/pdf';
    } else {
        die("Invalid format");
    }

    $result = Builder::create()
        ->writer($writer)
        ->data($url)
        ->build();

    // Serve the file for download
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo $result->getString();
} catch (Exception $e) {
    die("Error generating QR code: " . $e->getMessage());
}
?>
