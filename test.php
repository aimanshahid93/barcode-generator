<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Test output
echo "PHP is working!";
// Intentionally cause an error
echo $undefinedVariable; // This should trigger a notice
?>
