# Contributing to Ultimate POS

Thank you for considering contributing to Ultimate POS! This document outlines the coding standards and development practices to follow when working on this project.

## Coding Standards

This project follows the [PSR-12 coding standards](https://www.php-fig.org/psr/psr-12/). We use PHP-CS-Fixer to automatically enforce these standards.

### Setting Up PHP-CS-Fixer

1. Install PHP-CS-Fixer globally:
   ```bash
   composer global require friendsofphp/php-cs-fixer
   ```

2. Make sure the global Composer binaries directory is in your system PATH.

3. The project already includes a `.php-cs-fixer.php` configuration file in the root directory.

### Using PHP-CS-Fixer

To check your code for style issues:
```bash
php-cs-fixer fix --dry-run --diff
```

To automatically fix style issues:
```bash
php-cs-fixer fix
```

You can also fix a specific file or directory:
```bash
php-cs-fixer fix app/Http/Controllers/YourController.php
```

### Pre-commit Hook (Recommended)

It's recommended to set up a pre-commit hook to automatically check your code before committing. A pre-commit hook script is provided in the `scripts/git-hooks` directory.

To install the pre-commit hook:

1. Copy the pre-commit hook to your local Git hooks directory:
   ```bash
   # For Unix/Linux/macOS
   cp scripts/git-hooks/pre-commit .git/hooks/
   chmod +x .git/hooks/pre-commit

   # For Windows (PowerShell)
   Copy-Item -Path "scripts\git-hooks\pre-commit" -Destination ".git\hooks\"
   ```

2. Verify that the hook is installed:
   ```bash
   # For Unix/Linux/macOS
   ls -la .git/hooks/pre-commit

   # For Windows (PowerShell)
   Get-Item .git\hooks\pre-commit
   ```

Once installed, the pre-commit hook will automatically run PHP-CS-Fixer on all staged PHP files when you commit changes, ensuring that your code adheres to the PSR-12 coding standards.

## Development Workflow

1. Create a new branch for your feature or bugfix
2. Write code that follows the PSR-12 coding standards
3. Run PHP-CS-Fixer before committing your changes
4. Write tests for your changes when applicable
5. Submit a pull request

## Code Documentation Standards

This project follows PHPDoc standards for code documentation. Proper documentation helps other developers understand your code and improves maintainability.

### PHPDoc Blocks

Every class, method, and function should have a PHPDoc block that describes its purpose and behavior. Here are the guidelines for writing PHPDoc blocks:

#### For Classes

```php
/**
 * Class description goes here.
 * 
 * Additional details about the class can be provided here.
 * 
 * @package App\Namespace
 */
class YourClass
{
    // Class code
}
```

#### For Methods and Functions

```php
/**
 * Method description goes here.
 * 
 * Additional details about the method can be provided here.
 * 
 * @param string $param1 Description of the first parameter
 * @param int $param2 Description of the second parameter
 * @param array $options [Optional] Description of the options array
 * @return mixed Description of the return value
 * @throws \Exception When something goes wrong
 */
public function yourMethod($param1, $param2, array $options = [])
{
    // Method code
}
```

#### For Properties

```php
/**
 * @var string Description of the property
 */
private $property;
```

### Documentation Best Practices

1. **Be Clear and Concise**: Write clear, concise descriptions that explain the purpose and behavior of the code.
2. **Document Parameters**: Document all parameters, including their types and descriptions.
3. **Document Return Values**: Specify the return type and describe what is returned.
4. **Document Exceptions**: List all exceptions that might be thrown by the method.
5. **Use Type Hints**: Use PHP type hints in addition to PHPDoc type annotations.
6. **Keep Documentation Updated**: Update documentation when code changes.

### Documenting Complex Business Logic

Complex business logic should be documented with clear, detailed comments that explain the reasoning behind the implementation. This helps other developers understand why certain decisions were made and how the code works.

#### Guidelines for Documenting Complex Logic:

1. **Explain the "Why"**: Don't just describe what the code does, explain why it does it that way.
2. **Break Down Complex Calculations**: For complex calculations or algorithms, break them down step by step.
3. **Document Business Rules**: Clearly document any business rules or requirements that the code implements.
4. **Use Examples**: Where appropriate, provide examples to illustrate how the logic works.
5. **Reference Sources**: If the logic is based on external sources (e.g., accounting principles, legal requirements), reference them.

#### Example of Well-Documented Complex Logic:

```php
// Calculate the total price with progressive discount
// The discount increases with quantity:
// - First 10 items: no discount
// - Items 11-20: 5% discount
// - Items 21-50: 10% discount
// - Items 51+: 15% discount
$totalPrice = 0;
if ($quantity <= 10) {
    // No discount for small orders
    $totalPrice = $quantity * $unitPrice;
} elseif ($quantity <= 20) {
    // 5% discount for the portion above 10 items
    $totalPrice = (10 * $unitPrice) + (($quantity - 10) * $unitPrice * 0.95);
} elseif ($quantity <= 50) {
    // 5% discount for items 11-20 and 10% discount for items 21-50
    $totalPrice = (10 * $unitPrice) + 
                  (10 * $unitPrice * 0.95) + 
                  (($quantity - 20) * $unitPrice * 0.90);
} else {
    // Progressive discount for all tiers
    $totalPrice = (10 * $unitPrice) + 
                  (10 * $unitPrice * 0.95) + 
                  (30 * $unitPrice * 0.90) + 
                  (($quantity - 50) * $unitPrice * 0.85);
}

// Apply special seasonal discount if applicable
// This is a business requirement specified in REQ-2023-15
if ($isSeasonalPromotion && $totalPrice > 1000) {
    // Additional 3% off for large orders during promotional periods
    $totalPrice *= 0.97;
}
```

### Finding Undocumented Code

To help identify methods that need documentation improvements, you can use the PHPDoc scanning script:

```bash
# Scan the entire app directory
php scripts/find-missing-phpdoc.php

# Scan a specific directory or file
php scripts/find-missing-phpdoc.php app/Http/Controllers
```

This script will:
- Identify methods that are missing PHPDoc blocks entirely
- Find methods with incomplete PHPDoc (missing @param or @return tags)
- Provide a summary of the documentation status
- Help prioritize which methods need documentation improvements

### Example

Here's an example of a well-documented class:

```php
<?php

namespace App\Services;

/**
 * Handles product inventory operations.
 * 
 * This service provides methods for managing product inventory,
 * including stock adjustments, inventory checks, and stock reports.
 * 
 * @package App\Services
 */
class InventoryService
{
    /**
     * Adjusts the stock level for a product.
     * 
     * @param int $productId The ID of the product
     * @param int $quantity The quantity to adjust (positive for increase, negative for decrease)
     * @param string $reason The reason for the adjustment
     * @param int|null $locationId The location ID (null for all locations)
     * @return bool True if the adjustment was successful, false otherwise
     * @throws \App\Exceptions\ProductNotFoundException If the product doesn't exist
     * @throws \App\Exceptions\InsufficientStockException If there's not enough stock to decrease
     */
    public function adjustStock(int $productId, int $quantity, string $reason, ?int $locationId = null): bool
    {
        // Method implementation
    }
}
```

## Additional Resources

- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [PHP-CS-Fixer Documentation](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
- [PHPDoc Reference](https://docs.phpdoc.org/3.0/guide/references/phpdoc/index.html)
- [PHP The Right Way - Documentation](https://phptherightway.com/#documentation)
