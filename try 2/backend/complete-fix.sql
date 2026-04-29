-- Complete Database Fix for MySQL Tablespace Issue
-- Run this in MySQL command line or phpMyAdmin

-- Step 1: Drop the database completely
DROP DATABASE IF EXISTS request_workflow;

-- Step 2: Create fresh database
CREATE DATABASE request_workflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Step 3: Use the database
USE request_workflow;

-- Done! Now run: php artisan migrate && php artisan db:seed
