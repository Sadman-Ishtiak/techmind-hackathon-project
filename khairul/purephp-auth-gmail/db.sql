DROP DATABASE IF EXISTS Hackathon;
CREATE DATABASE Hackathon;

DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS stores;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS subcategories;
DROP TABLE IF EXISTS subsubcategories;
DROP TABLE IF EXISTS Auction;
DROP TABLE IF EXISTS transactions;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS auction_images;
DROP TABLE IF EXISTS images;
DROP TABLE IF EXISTS review_images;
DROP TABLE IF EXISTS product_images;


-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email_verified TINYINT(1) NOT NULL DEFAULT 0,
    verification_token VARCHAR(64) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stores table
CREATE TABLE IF NOT EXISTS stores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY(owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Categories
CREATE TABLE IF NOT EXISTS categories (
    name VARCHAR(255) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS subcategories (
    name VARCHAR(255) PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    FOREIGN KEY(category_name) REFERENCES categories(name) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS subsubcategories (
    name VARCHAR(255) PRIMARY KEY,
    subcategory_name VARCHAR(255) NOT NULL,
    FOREIGN KEY(subcategory_name) REFERENCES subcategories(name) ON DELETE CASCADE
);

-- Products
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id INT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    category_name VARCHAR(255) NOT NULL,
    subcategory_name VARCHAR(255),
    subsubcategory_name VARCHAR(255),
    stock INT UNSIGNED NOT NULL DEFAULT 0,
    review FLOAT DEFAULT 0,
    FOREIGN KEY(store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY(category_name) REFERENCES categories(name),
    FOREIGN KEY(subcategory_name) REFERENCES subcategories(name),
    FOREIGN KEY(subsubcategory_name) REFERENCES subsubcategories(name)
);

-- Auctions
CREATE TABLE IF NOT EXISTS auctions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id INT UNSIGNED NOT NULL,
    minimum_price DECIMAL(10,2) DEFAULT 0,
    current_holder_id INT UNSIGNED DEFAULT NULL,
    current_price DECIMAL(10,2) DEFAULT 0,
    ends_at DATETIME NOT NULL,
    product_name VARCHAR(255),
    category_name VARCHAR(255),
    subcategory_name VARCHAR(255),
    subsubcategory_name VARCHAR(255),
    description TEXT,
    FOREIGN KEY(seller_id) REFERENCES users(id),
    FOREIGN KEY(current_holder_id) REFERENCES users(id),
    FOREIGN KEY(category_name) REFERENCES categories(name),
    FOREIGN KEY(subcategory_name) REFERENCES subcategories(name),
    FOREIGN KEY(subsubcategory_name) REFERENCES subsubcategories(name)
);

-- Transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    buyer_id INT UNSIGNED NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    time DATETIME NOT NULL,
    FOREIGN KEY(store_id) REFERENCES stores(id),
    FOREIGN KEY(product_id) REFERENCES products(id),
    FOREIGN KEY(buyer_id) REFERENCES users(id)
);

-- Reviews
CREATE TABLE IF NOT EXISTS reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    review TEXT,
    FOREIGN KEY(buyer_id) REFERENCES users(id),
    FOREIGN KEY(product_id) REFERENCES products(id)
);

-- Images
CREATE TABLE IF NOT EXISTS images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image LONGBLOB NOT NULL
);

-- Product images
CREATE TABLE IF NOT EXISTS product_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    image_id INT UNSIGNED NOT NULL,
    FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY(image_id) REFERENCES images(id) ON DELETE CASCADE
);

-- Review images
CREATE TABLE IF NOT EXISTS review_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    review_id INT UNSIGNED NOT NULL,
    image_id INT UNSIGNED NOT NULL,
    FOREIGN KEY(review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY(image_id) REFERENCES images(id) ON DELETE CASCADE
);

-- Auction images
CREATE TABLE IF NOT EXISTS auction_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    auction_id INT UNSIGNED NOT NULL,
    image_id INT UNSIGNED NOT NULL,
    FOREIGN KEY(auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    FOREIGN KEY(image_id) REFERENCES images(id) ON DELETE CASCADE
);
