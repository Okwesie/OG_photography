-- Create users table
CREATE TABLE users_og (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('customer', 'admin', 'photographer') DEFAULT 'customer',
    profile_picture VARCHAR(255),
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create user_profiles table
CREATE TABLE user_profiles_og (
    profile_id INT PRIMARY KEY,
    user_id INT UNIQUE,
    phone_number VARCHAR(20),
    address TEXT,
    bio TEXT,
    social_media_links JSON CHECK (JSON_VALID(social_media_links)),
    FOREIGN KEY (user_id) REFERENCES users_og(user_id) ON DELETE CASCADE
);

-- Create gallery_categories table
CREATE TABLE gallery_categories_og (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name ENUM('nature', 'architecture', 'event', 'model', 'portrait', 'landscape') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create galleries table
CREATE TABLE galleries_og (
    gallery_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    photographer_id INT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES gallery_categories_og(category_id),
    FOREIGN KEY (photographer_id) REFERENCES users_og(user_id) ON DELETE SET NULL
);

-- Create photos table
CREATE TABLE photos_og (
    photo_id INT PRIMARY KEY AUTO_INCREMENT,
    gallery_id INT,
    file_path VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    description TEXT,
    is_purchasable TINYINT(1) DEFAULT 0,
    digital_price DECIMAL(10,2),
    print_price DECIMAL(10,2),
    FOREIGN KEY (gallery_id) REFERENCES galleries_og(gallery_id) ON DELETE SET NULL
);

-- Create services table
CREATE TABLE services_og (
    service_id INT PRIMARY KEY AUTO_INCREMENT,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    base_price DECIMAL(10,2) NOT NULL,
    category ENUM('event', 'portrait', 'commercial', 'wedding') NOT NULL,
    duration_hours INT
);

-- Create bookings table
CREATE TABLE bookings_og (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    photographer_id INT,
    service_id INT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255),
    additional_requirements TEXT,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    total_price DECIMAL(10,2) NOT NULL,
    payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    FOREIGN KEY (user_id) REFERENCES users_og(user_id) ON DELETE SET NULL,
    FOREIGN KEY (photographer_id) REFERENCES users_og(user_id) ON DELETE SET NULL,
    FOREIGN KEY (service_id) REFERENCES services_og(service_id)
);

-- Create reviews table
CREATE TABLE reviews_og (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT,
    user_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings_og(booking_id),
    FOREIGN KEY (user_id) REFERENCES users_og(user_id)
);

-- Create assignments table
CREATE TABLE assignments_og (
    assignment_id INT PRIMARY KEY AUTO_INCREMENT,
    photographer_id INT NOT NULL,
    assignment_title VARCHAR(100) NOT NULL,
    description TEXT,
    due_date DATE NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'overdue') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (photographer_id) REFERENCES users_og(user_id) ON DELETE CASCADE
);

-- Create orders table
CREATE TABLE orders_og (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('credit_card', 'paypal', 'bank_transfer') NOT NULL,
    status ENUM('pending', 'paid', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users_og(user_id) ON DELETE SET NULL
);

-- Create order_items table
CREATE TABLE order_items_og (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT,
    photo_id INT,
    quantity INT NOT NULL DEFAULT 1,
    item_price DECIMAL(10,2) NOT NULL,
    print_size VARCHAR(50),
    FOREIGN KEY (order_id) REFERENCES orders_og(order_id) ON DELETE CASCADE,
    FOREIGN KEY (photo_id) REFERENCES photos_og(photo_id)
);

-- Create user_activity_log table
CREATE TABLE user_activity_log_og (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type ENUM('login', 'booking', 'photo_view', 'photo_purchase', 'profile_update') NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (user_id) REFERENCES users_og(user_id) ON DELETE SET NULL
);

-- Create login_attempts table
CREATE TABLE login_attempts (
    ip_address VARCHAR(45) PRIMARY KEY,
    attempts INT DEFAULT 0,
    last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);