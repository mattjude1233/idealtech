<?php
/**
 * Permissions Module Installation Script
 * 
 * Run this script once to add the permissions module to your admin_tabs
 * Only run this if the permissions entries don't already exist
 */

// Uncomment the lines below to run the installation

/*
// Database connection (adjust as needed)
$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'hr_idealtech';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if permissions entries already exist
    $check = $pdo->prepare("SELECT COUNT(*) FROM admin_tabs WHERE keyword IN ('tab_permissions', 'manage_permissions')");
    $check->execute();
    $count = $check->fetchColumn();
    
    if ($count > 0) {
        echo "Permissions entries already exist in admin_tabs!\n";
        exit;
    }
    
    // Get the next available ID
    $maxId = $pdo->query("SELECT MAX(id) FROM admin_tabs")->fetchColumn();
    $nextId = $maxId + 1;
    
    // Insert permissions entries
    $sql = "INSERT INTO admin_tabs (id, keyword, name, link, grouping, level, special_user, exclude_user, icon, position, type, status) VALUES 
            (?, 'tab_permissions', 'Permissions Management', 'permissions', 1, 'admin', '', '', 'fas fa-shield-alt', 11, 1, 1),
            (?, 'manage_permissions', 'Manage System Permissions', '', 0, 'admin', '', '', '', 0, 2, 1)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nextId, $nextId + 1]);
    
    echo "Permissions module installed successfully!\n";
    echo "Added entries with IDs: $nextId and " . ($nextId + 1) . "\n";
    echo "You can now access the Permissions Management module.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
*/

echo "Installation script ready. Uncomment the code block and run to install.\n";
?>