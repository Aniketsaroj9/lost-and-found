<?php
// Database connection without selecting a database first
$pdo = new PDO("mysql:host=localhost", 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create database if not exists
$pdo->exec("CREATE DATABASE IF NOT EXISTS lost_and_found_db");
$pdo->exec("USE lost_and_found_db");

// Create users table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        student_id VARCHAR(20) UNIQUE,
        phone VARCHAR(15),
        role ENUM('student', 'admin') DEFAULT 'student',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Create categories table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS categories (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

// Create locations table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS locations (
        location_id INT AUTO_INCREMENT PRIMARY KEY,
        building_name VARCHAR(100) NOT NULL,
        room_number VARCHAR(20),
        description TEXT,
        is_active BOOLEAN DEFAULT TRUE
    )
");

// Create items table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS items (
        item_id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        category_id INT,
        location_id INT,
        status ENUM('open', 'matched', 'claimed', 'collected') DEFAULT 'open',
        type ENUM('lost', 'found') NOT NULL,
        reported_by INT NOT NULL,
        reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
        FOREIGN KEY (location_id) REFERENCES locations(location_id) ON DELETE SET NULL,
        FOREIGN KEY (reported_by) REFERENCES users(user_id) ON DELETE CASCADE
    )
");

// Create lost_items table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS lost_items (
        item_id INT PRIMARY KEY,
        date_lost DATE NOT NULL,
        has_identification BOOLEAN DEFAULT FALSE,
        identification_details TEXT,
        reward DECIMAL(10,2),
        FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
    )
");

// Create found_items table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS found_items (
        item_id INT PRIMARY KEY,
        date_found DATE NOT NULL,
        storage_location VARCHAR(100),
        is_identified BOOLEAN DEFAULT FALSE,
        found_by INT NOT NULL,
        FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE,
        FOREIGN KEY (found_by) REFERENCES users(user_id)
    )
");

// Create item_images table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS item_images (
        image_id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_primary BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
    )
");

// Create claims table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS claims (
        claim_id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        claimed_by INT NOT NULL,
        claim_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        admin_notes TEXT,
        resolved_by INT,
        resolved_at TIMESTAMP NULL,
        FOREIGN KEY (item_id) REFERENCES items(item_id),
        FOREIGN KEY (claimed_by) REFERENCES users(user_id),
        FOREIGN KEY (resolved_by) REFERENCES users(user_id)
    )
");

// Create notifications table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS notifications (
        notification_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        related_item_id INT,
        notification_type ENUM('claim_status', 'match_found', 'admin_message', 'other'),
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (related_item_id) REFERENCES items(item_id) ON DELETE SET NULL
    )
");

// Create activity_logs table
$pdo->exec("
    CREATE TABLE IF NOT EXISTS activity_logs (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        action VARCHAR(255) NOT NULL,
        item_id INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE SET NULL
    )
");

// Insert default admin user (username: admin, password: admin123)
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
try {
    $pdo->exec("INSERT IGNORE INTO users (username, password_hash, email, full_name, role) 
                VALUES ('admin', '$hashed_password', 'admin@university.edu', 'Admin User', 'admin')");
    
    // Insert some default categories
    $default_categories = [
        'Electronics' => 'Electronic devices like phones, laptops, tablets',
        'Stationery' => 'Pens, notebooks, and other academic supplies',
        'Clothing' => 'Jackets, hats, scarves, and other wearables',
        'Accessories' => 'Watches, jewelry, glasses',
        'Documents' => 'IDs, passports, certificates',
        'Bags' => 'Backpacks, handbags, wallets',
        'Keys' => 'House keys, car keys, lockers',
        'Other' => 'Miscellaneous items'
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, description) VALUES (?, ?)");
    foreach ($default_categories as $name => $desc) {
        $stmt->execute([$name, $desc]);
    }
    
    // Insert some default locations
    $default_locations = [
        ['Main Building', 'Lobby'],
        ['Library', 'Reading Room'],
        ['Cafeteria', 'Main Area'],
        ['Computer Lab', 'Lab 101'],
        ['Sports Complex', 'Locker Room'],
        ['Auditorium', 'Main Hall'],
        ['Parking Lot', 'Near Entrance']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO locations (building_name, room_number) VALUES (?, ?)");
    foreach ($default_locations as $location) {
        $stmt->execute($location);
    }
    
    echo "Database setup completed successfully!<br>";
    echo "Admin credentials:<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<a href='index.php'>Go to Homepage</a>";
    
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>
