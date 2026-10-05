-- =========================================================
-- Project Database: Customer Complaint Tracking System
-- NOTE: re-importing this file wipes and rebuilds the database.
-- =========================================================

DROP DATABASE IF EXISTS complaint_tracker;
CREATE DATABASE complaint_tracker;
USE complaint_tracker;

-- Customers (log in with email)
CREATE TABLE customers (
    customer_id     INT AUTO_INCREMENT PRIMARY KEY,
    first_name      VARCHAR(50)  NOT NULL,
    last_name       VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    street_address  VARCHAR(100) NOT NULL,
    city            VARCHAR(50)  NOT NULL,
    state           CHAR(2)      NOT NULL,
    zip_code        VARCHAR(10)  NOT NULL,
    phone           VARCHAR(20)  NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Employees: Technicians and Administrators (log in with user ID)
CREATE TABLE employees (
    employee_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id         VARCHAR(30)  NOT NULL UNIQUE,
    first_name      VARCHAR(50)  NOT NULL,
    last_name       VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    phone_ext       VARCHAR(10),
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('Administrator','Technician') NOT NULL
);

CREATE TABLE products (
    product_id      INT AUTO_INCREMENT PRIMARY KEY,
    product_name    VARCHAR(100) NOT NULL,
    description     VARCHAR(255),
    price           DECIMAL(10,2) NOT NULL
);

CREATE TABLE complaint_types (
    complaint_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name         VARCHAR(50) NOT NULL,
    description       VARCHAR(255)
);

-- Complaints tie customers, products, types, and the assigned technician together
CREATE TABLE complaints (
    complaint_id      INT AUTO_INCREMENT PRIMARY KEY,
    customer_id       INT NOT NULL,
    product_id        INT NOT NULL,
    complaint_type_id INT NOT NULL,
    employee_id       INT,
    description       VARCHAR(1000) NOT NULL,
    image_path        VARCHAR(255),
    status            ENUM('Open','Closed') NOT NULL DEFAULT 'Open',
    technician_notes  TEXT,
    resolution_date   DATE,
    resolution_notes  TEXT,
    date_submitted    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (complaint_type_id) REFERENCES complaint_types(complaint_type_id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

CREATE USER 'db_user'@'localhost' IDENTIFIED BY 'db_password'; GRANT ALL PRIVILEGES ON complaint_tracker.* TO 'db_user'@'localhost'; FLUSH PRIVILEGES;

-- =========================================================
-- Seed Data
-- =========================================================

-- Products/Services (5 required)
INSERT INTO products (product_name, description, price) VALUES
('Widget A', 'Standard widget for general use', 19.99),
('Widget B', 'Heavy-duty widget for industrial use', 34.99),
('Widget Pro Subscription', 'Monthly service plan for widget maintenance', 9.99),
('Widget Installation Service', 'On-site installation service', 49.99),
('Extended Warranty Plan', '2-year extended warranty coverage', 24.99);

-- Complaint Types (3 required)
INSERT INTO complaint_types (type_name, description) VALUES
('Product Defect', 'Item is damaged, broken, or not functioning as expected'),
('Warranty', 'Issue related to warranty coverage or claims'),
('Billing', 'Issue related to charges, refunds, or payments');

-- Starter staff accounts (change these passwords after first login)
--   admin / Admin123!    tech1 / Tech12345!
INSERT INTO employees (user_id, first_name, last_name, email, phone_ext, password_hash, role) VALUES
('admin', 'Alex', 'Admin', 'admin@example.com', '100', '$2b$10$fL6C9D/4tzRdW5iLT5S6t.AFRl1wg.J0eTNUOdtIf2NrmxBh42c6m', 'Administrator'),
('tech1', 'Terry', 'Tech', 'tech1@example.com', '101', '$2b$10$dGSk3vqvU/W/Gl4GA0MEuO82cS8NvQVFlOTOLMSQ9eDhv34t8aBUe', 'Technician');
