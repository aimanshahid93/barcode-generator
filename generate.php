<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set memory limit
ini_set('memory_limit', '1024M'); // Increase as necessary

// Log initial memory usage
echo "Initial memory usage: " . memory_get_usage() . " bytes\n";

// Include Composer's autoload
require 'C:\xampp\htdocs\VDO Tech\vendor\autoload.php'; // Ensure this path is correct

// Test input for debugging
$data = '123456789012'; // EAN-13 example input
$barcodeType = 'ean13'; // Test type

// Validate input
$validTypes = ['code128', 'ean13'];
if (empty($data) || !in_array($barcodeType, $validTypes)) {
    die('Invalid input. Please provide data and select a valid barcode type.');
}

// Log memory usage before barcode generation
echo "Memory usage before barcode generation: " . memory_get_usage() . " bytes\n";

// Generate barcode
$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
try {
    $barcodeImagePath = 'barcode.png'; // Save path for the barcode image
    file_put_contents($barcodeImagePath, $generator->getBarcode($data, $barcodeType));
    
    echo "Barcode generated successfully! <br>";
    echo "<img src='$barcodeImagePath' alt='Generated Barcode'>";

    // Log memory usage after barcode generation
    echo "Memory usage after barcode generation: " . memory_get_usage() . " bytes\n";

    // Database connection
    try {
        // Use 'root' and an empty password for default XAMPP installation
        $pdo = new PDO('mysql:host=localhost;dbname=barcode_generator', 'root', ''); 
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get user IP address
        $userIp = $_SERVER['REMOTE_ADDR'];

        // Insert data into the database
        $stmt = $pdo->prepare("INSERT INTO barcode_logs (user_input, barcode_type, ip_address) VALUES (:user_input, :barcode_type, :ip_address)");
        $stmt->execute([
            ':user_input' => $data,
            ':barcode_type' => $barcodeType,
            ':ip_address' => $userIp
        ]);

        echo "Data logged successfully!<br>";
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

} catch (Picqer\Barcode\Exceptions\UnknownTypeException $e) {
    die('Error generating barcode: ' . $e->getMessage());
} catch (Exception $e) {
    die('An error occurred: ' . $e->getMessage());
}

// Final memory usage
echo "Final memory usage: " . memory_get_usage() . " bytes\n";
?>
