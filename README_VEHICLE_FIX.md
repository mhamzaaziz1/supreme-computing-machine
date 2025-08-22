# Fix for "Something went wrong while adding vehicle" Error

## Issue
The error "Something went wrong while adding vehicle" occurs because the database table for storing vehicle information (`customer_vehicles`) has not been created in the database.

## Solution
The solution is to run the database migration that creates the `customer_vehicles` table.

## Instructions

### Option 1: Using the provided script
1. Upload the `run_migration.php` script to your server (it should be in the same directory as your Laravel application)
2. Run the script by navigating to it in your browser or by running it from the command line:
   ```
   php run_migration.php
   ```
3. The script will execute the migration and create the necessary table

### Option 2: Running the migration manually
If you prefer to run the migration manually, follow these steps:
1. Connect to your server via SSH or terminal
2. Navigate to your Laravel application directory
3. Run the following command:
   ```
   php artisan migrate
   ```
4. This will run all pending migrations, including the one for the `customer_vehicles` table

## Verification
After running the migration, try adding a vehicle to a customer again. The error should be resolved, and you should be able to add vehicles successfully.

## Additional Information
The migration creates a table with the following structure:
- `id` - Auto-incrementing primary key
- `contact_id` - Foreign key to the contacts table
- `make` - Vehicle make/manufacturer
- `model` - Vehicle model
- `year` - Vehicle year
- `license_plate` - Vehicle license plate number
- `color` - Vehicle color
- `vin` - Vehicle identification number
- `notes` - Additional notes about the vehicle
- `business_id` - Foreign key to the business table
- `created_by` - Foreign key to the users table
- `created_at` and `updated_at` - Timestamps