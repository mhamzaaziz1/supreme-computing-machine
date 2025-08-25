<?php

/**
 * This script scans PHP files in the specified directory and identifies methods
 * that are missing PHPDoc blocks or have incomplete PHPDoc blocks.
 * 
 * Usage:
 * php scripts/find-missing-phpdoc.php [directory]
 * 
 * If [directory] is not provided, it will scan the app directory.
 */

// Set default directory to scan
$directory = isset($argv[1]) ? $argv[1] : __DIR__ . '/../app';

// Initialize counters
$totalFiles = 0;
$totalMethods = 0;
$missingPhpDoc = 0;
$incompletePhpDoc = 0;

echo "Scanning directory: $directory\n";
echo "This may take a while for large codebases...\n\n";

// Recursively scan the directory for PHP files
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($directory)
);

$phpFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getPathname();
    }
}

$totalFiles = count($phpFiles);
echo "Found $totalFiles PHP files to scan.\n\n";

// Process each PHP file
foreach ($phpFiles as $file) {
    $content = file_get_contents($file);
    
    // Skip files that don't contain class definitions
    if (!preg_match('/class\s+\w+/i', $content)) {
        continue;
    }
    
    // Get the tokens from the file
    $tokens = token_get_all($content);
    
    $inClass = false;
    $className = '';
    $methods = [];
    $currentMethod = null;
    $hasPhpDoc = false;
    
    // Analyze the tokens
    foreach ($tokens as $index => $token) {
        // Skip non-array tokens
        if (!is_array($token)) {
            continue;
        }
        
        list($id, $text) = $token;
        
        // Track class definitions
        if ($id === T_CLASS) {
            $inClass = true;
            
            // Get the class name
            for ($i = $index + 1; $i < count($tokens); $i++) {
                if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                    $className = $tokens[$i][1];
                    break;
                }
            }
        }
        
        // Track method definitions
        if ($inClass && $id === T_FUNCTION) {
            $totalMethods++;
            
            // Get the method name
            for ($i = $index + 1; $i < count($tokens); $i++) {
                if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                    $currentMethod = $tokens[$i][1];
                    break;
                }
            }
            
            // Check if the method has a PHPDoc block
            $hasPhpDoc = false;
            $hasParams = false;
            $hasReturn = false;
            
            // Look backward for PHPDoc
            for ($i = $index - 1; $i >= 0; $i--) {
                if (is_array($tokens[$i])) {
                    // Skip whitespace and comments
                    if ($tokens[$i][0] === T_WHITESPACE || $tokens[$i][0] === T_COMMENT) {
                        continue;
                    }
                    
                    // Check for PHPDoc
                    if ($tokens[$i][0] === T_DOC_COMMENT) {
                        $hasPhpDoc = true;
                        
                        // Check for @param and @return tags
                        if (strpos($tokens[$i][1], '@param') !== false) {
                            $hasParams = true;
                        }
                        
                        if (strpos($tokens[$i][1], '@return') !== false) {
                            $hasReturn = true;
                        }
                        
                        break;
                    } else {
                        // If we hit a non-whitespace, non-comment token, there's no PHPDoc
                        break;
                    }
                }
            }
            
            // Record the method information
            if (!$hasPhpDoc) {
                $missingPhpDoc++;
                $methods[] = [
                    'name' => $currentMethod,
                    'status' => 'missing',
                ];
            } elseif (!$hasParams || !$hasReturn) {
                $incompletePhpDoc++;
                $methods[] = [
                    'name' => $currentMethod,
                    'status' => 'incomplete',
                    'missing' => [
                        'params' => !$hasParams,
                        'return' => !$hasReturn,
                    ],
                ];
            }
        }
    }
    
    // Report issues for this file
    if (!empty($methods)) {
        $relativePath = str_replace(__DIR__ . '/../', '', $file);
        echo "File: $relativePath\n";
        echo "Class: $className\n";
        
        foreach ($methods as $method) {
            if ($method['status'] === 'missing') {
                echo "  - Method '{$method['name']}' is missing PHPDoc\n";
            } else {
                echo "  - Method '{$method['name']}' has incomplete PHPDoc:\n";
                if ($method['missing']['params']) {
                    echo "      * Missing @param tags\n";
                }
                if ($method['missing']['return']) {
                    echo "      * Missing @return tag\n";
                }
            }
        }
        
        echo "\n";
    }
}

// Print summary
echo "Summary:\n";
echo "Total PHP files scanned: $totalFiles\n";
echo "Total methods found: $totalMethods\n";
echo "Methods missing PHPDoc: $missingPhpDoc (" . round(($missingPhpDoc / $totalMethods) * 100, 2) . "%)\n";
echo "Methods with incomplete PHPDoc: $incompletePhpDoc (" . round(($incompletePhpDoc / $totalMethods) * 100, 2) . "%)\n";
echo "Methods with proper PHPDoc: " . ($totalMethods - $missingPhpDoc - $incompletePhpDoc) . " (" . 
     round((($totalMethods - $missingPhpDoc - $incompletePhpDoc) / $totalMethods) * 100, 2) . "%)\n";