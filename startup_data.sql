-- startup_data.sql
-- Initial data for day zero operations

-- Adding basic employees (2 managers, 2 sales staff)
INSERT INTO People (PersonID, FirstName, LastName, Email, PhoneNumber, StreetAddress, City, State, ZipCode)
VALUES 
(1, 'Sarah', 'Johnson', 'sarah.johnson@company.com', '555-123-4567', '123 Main Street', 'Springfield', 'IL', '62701'),
(2, 'Michael', 'Chen', 'michael.chen@company.com', '555-234-5678', '456 Oak Avenue', 'Springfield', 'IL', '62702'),
(3, 'David', 'Rodriguez', 'david.rodriguez@company.com', '555-345-6789', '789 Elm Street', 'Springfield', 'IL', '62704'),
(4, 'Emily', 'Williams', 'emily.williams@company.com', '555-456-7890', '321 Pine Road', 'Springfield', 'IL', '62703');

INSERT INTO Employees (PersonID, Role, HireDate, TerminationDate, Password)
VALUES 
(1, 'Manager', '2023-01-15', NULL, 'password'), 
(2, 'Manager', '2023-03-10', NULL, 'password'), 
(3, 'Sales', '2023-06-05', NULL, 'password'), 
(4, 'Sales', '2023-08-20', NULL, 'password');

-- Adding initial products inventory
INSERT INTO Products (ProductID, ProductName, Brand, Category, Size, SizeUnit, StockQuantity, StorageRequirement)
VALUES 
(1, 'Ultra HD Smart TV 55"', 'TechVision', 'Electronics', 55, 'cm', 25, 'Dry Storage'),
(2, 'Ultra HD Smart TV 65"', 'TechVision', 'Electronics', 65, 'cm', 15, 'Dry Storage'),
(3, 'Premium Laptop Pro', 'ByteBook', 'Electronics', 15, 'cm', 30, 'Dry Storage'),
(4, 'Wireless Noise-Cancelling Headphones', 'SoundWave', 'Electronics', 20, 'cm', 40, 'Dry Storage'),
(5, 'Classic Denim Jeans', 'UrbanStyle', 'Clothing', 32, 'cm', 50, 'Dry Storage'),
(6, 'Premium Cotton T-Shirt', 'UrbanStyle', 'Clothing', 42, 'cm', 100, 'Dry Storage'),
(7, 'Ergonomic Office Chair', 'ComfortPlus', 'Furniture', 110, 'cm', 20, 'Dry Storage'),
(8, 'Modern Coffee Table', 'HomeLuxe', 'Furniture', 120, 'cm', 15, 'Dry Storage'),
(9, 'Sectional Sofa', 'ComfortPlus', 'Furniture', 250, 'cm', 10, 'Dry Storage'),
(10, 'Organic Granola', 'NatureHarvest', 'Food', 500, 'g', 60, 'Dry Storage'),
(11, 'Premium Coffee Beans', 'BeanMaster', 'Food', 1, 'kg', 75, 'Dry Storage'),
(12, 'Frozen Mixed Berries', 'FreshFarms', 'Food', 2, 'kg', 40, 'Frozen Storage'),
(13, 'Artisanal Cheese Selection', 'DairyDelights', 'Food', 500, 'g', 30, 'Cold Storage');
