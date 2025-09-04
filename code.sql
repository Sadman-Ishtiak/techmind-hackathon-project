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


CREATE TABLE user (
id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
name VARCHAR(255) NOT NULL,
email VARCHAR NOT NULL,
password VARCHAR NOT NULL);

CREATE TABLE stores (
id INTEGER PRIMARY KEY NOT NULL,
owner INTEGER NOT NULL,
Name VARCHAR NOT NULL,
FOREIGN KEY(owner) REFERENCES user(id));

CREATE TABLE products (
id INTEGER PRIMARY KEY NOT NULL,
seller VARCHAR NOT NULL,
name INTEGER NOT NULL,
price INTEGER NOT NULL,
Description TEXT NOT NULL,
category TEXT NOT NULL,
subcategory TEXT NOT NULL,
subsubcategory TEXT NOT NULL,
stock INTEGER NOT NULL,
review FLOAT(2,2),
FOREIGN KEY(seller) REFERENCES stores(Name),
FOREIGN KEY(category) REFERENCES categories(name),
FOREIGN KEY(subcategory) REFERENCES subcategories(name),
FOREIGN KEY(subsubcategory) REFERENCES subsubcategories(name));

CREATE TABLE categories (
name TEXT PRIMARY KEY NOT NULL UNIQUE);

CREATE TABLE subcategories (
categories TEXT NOT NULL,
name TEXT PRIMARY KEY NOT NULL,
FOREIGN KEY(categories) REFERENCES categories(name));

CREATE TABLE subsubcategories (
subcategory TEXT NOT NULL,
name TEXT PRIMARY KEY NOT NULL,
FOREIGN KEY(subcategory) REFERENCES subcategories(name));

CREATE TABLE Auction (
id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
Seller INTEGER NOT NULL,
minimum INTEGER NOT NULL DEFAULT 0,
current_holder INTEGER,
current_price INTEGER NOT NULL,
ends_at DATETIME NOT NULL,
product TEXT NOT NULL,
category TEXT NOT NULL,
subcategory TEXT NOT NULL,
subsubcategory TEXT NOT NULL,
description TEXT NOT NULL,
FOREIGN KEY(Seller) REFERENCES user(id),
FOREIGN KEY(current_holder) REFERENCES user(id),
FOREIGN KEY(category) REFERENCES categories(name),
FOREIGN KEY(subcategory) REFERENCES subcategories(name),
FOREIGN KEY(subsubcategory) REFERENCES subsubcategories(name));

CREATE TABLE transactions (
id INTEGER PRIMARY KEY NOT NULL,
store INTEGER NOT NULL,
product INTEGER NOT NULL,
buyer INTEGER NOT NULL,
cost INTEGER NOT NULL,
number INTEGER NOT NULL,
total INTEGER NOT NULL,
time DATETIME NOT NULL,
FOREIGN KEY(store) REFERENCES stores(id),
FOREIGN KEY(product) REFERENCES products(id),
FOREIGN KEY(buyer) REFERENCES user(id));

CREATE TABLE reviews (
id INTEGER PRIMARY KEY NOT NULL,
buyer INTEGER NOT NULL,
product INTEGER NOT NULL,
rate_of_ten INTEGER NOT NULL,
review TEXT,
FOREIGN KEY(buyer) REFERENCES transactions(buyer),
FOREIGN KEY(product) REFERENCES products(id));

CREATE TABLE auction_images (
id INTEGER PRIMARY KEY NOT NULL,
aution_id INTEGER NOT NULL,
image_id INTEGER NOT NULL,
FOREIGN KEY(aution_id) REFERENCES Auction(id),
FOREIGN KEY(image_id) REFERENCES images(id));

CREATE TABLE images (
id INTEGER PRIMARY KEY NOT NULL,
image BLOB NOT NULL);

CREATE TABLE review_images (
id INTEGER PRIMARY KEY NOT NULL,
review_id INTEGER NOT NULL,
inage_id INTEGER NOT NULL,
FOREIGN KEY(review_id) REFERENCES reviews(id),
FOREIGN KEY(inage_id) REFERENCES images(id));

CREATE TABLE product_images (
id INTEGER PRIMARY KEY NOT NULL,
product_id INTEGER NOT NULL,
image_id INTEGER NOT NULL,
FOREIGN KEY(product_id) REFERENCES products(id),
FOREIGN KEY(image_id) REFERENCES images(id));