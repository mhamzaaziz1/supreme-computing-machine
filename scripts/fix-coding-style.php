<?php

/**
 * This script demonstrates how to use PHP-CS-Fixer to fix coding style issues
 * in a specific file or directory.
 * 
 * Usage:
 * php scripts/fix-coding-style.php [path]
 * 
 * If [path] is not provided, it will fix all files configured in .php-cs-fixer.php
 * If [path] is provided, it will only fix the specified file or directory
 */

// Check if PHP-CS-Fixer is installed
$phpCsFixerPath = __DIR__ . '/../vendor/bin/php-cs-fixer';
if (!file_exists($phpCsFixerPath)) {
    echo "PHP-CS-Fixer not found. Please install it using:\n";
    echo "composer require --dev friendsofphp/php-cs-fixer\n";
    exit(1);
}

// Get the path argument if provided
$path = isset($argv[1]) ? $argv[1] : null;

// Build the command
$command = $phpCsFixerPath . ' fix';
if ($path) {
    $command .= ' ' . escapeshellarg($path);
}
$command .= ' --config=' . escapeshellarg(__DIR__ . '/../.php-cs-fixer.php');
$command .= ' --verbose';

// Display the command
echo "Running: $command\n";

// Execute the command
passthru($command, $returnCode);

// Check the result
if ($returnCode === 0) {
    echo "\nSuccess! Code style has been fixed.\n";
} else {
    echo "\nError: PHP-CS-Fixer returned code $returnCode\n";
    exit($returnCode);
}

// If a specific file was fixed, show a diff of the changes
if ($path && file_exists($path) && !is_dir($path)) {
    echo "\nChanges made to $path:\n";
    passthru("git diff $path");
}

echo "\nDone!\n";