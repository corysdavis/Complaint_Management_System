-- Project Database: Customer Complaint Tracking System

CREATE DATABASE IF NOT EXISTS complaint_tracker;
USE complaint_tracker;

-- Table: customers
CREATE TABLE customers (
    customer_id     INT AUTO_INCREMENT PRIMARY KEY,
    first_name      VARCHAR(50)  NOT NULL,
    last_name       VARCHAR(50)  NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    phone           VARCHAR(20),
    password_hash   VARCHAR(255) NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: employees (handle/assign complaints)
CREATE TABLE employees (
    employee_id     INT AUTO_INCREMENT PRIMARY KEY,
    first_name      VARCHAR(50) NOT NULL,
    last_name       VARCHAR(50) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    role            VARCHAR(50) NOT NULL
);

-- Table: products
CREATE TABLE products (
    product_id      INT AUTO_INCREMENT PRIMARY KEY,
    product_name    VARCHAR(100) NOT NULL,
    description     VARCHAR(255),
    price           DECIMAL(10,2) NOT NULL
);

-- Table: complaint_types
CREATE TABLE complaint_types (
    complaint_type_id INT AUTO_INCREMENT PRIMARY KEY,
    type_name         VARCHAR(50) NOT NULL,
    description       VARCHAR(255)
);

-- Table: complaints
CREATE TABLE complaints (
    complaint_id      INT AUTO_INCREMENT PRIMARY KEY,
    customer_id       INT NOT NULL,
    product_id        INT NOT NULL,
    complaint_type_id INT NOT NULL,
    employee_id       INT,
    description       VARCHAR(1000) NOT NULL,
    status            ENUM('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
    date_submitted    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (complaint_type_id) REFERENCES complaint_types(complaint_type_id),
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id)
);

-- data (sample)

-- Products/Services (5 required)
INSERT INTO products (product_name, description, price) VALUES
('Widget #1', 'Standard widget for general use', 19.99),
('Widget #2', 'Heavy-duty widget for industrial use', 34.99),
('Pro Subscription', 'Monthly service plan for widget maintenance', 9.99),
('Installation Service', 'On-site installation service', 49.99),
('Extended Warranty Plan', '2-year extended warranty coverage', 24.99);

-- types of complaints
INSERT INTO complaint_types (type_name, description) VALUES
('Product Defect', 'Item is damaged, broken, or not functioning as expected'),
('Warranty', 'Issue related to warranty coverage or claims'),
('Billing', 'Issue related to charges, refunds, or payments');
