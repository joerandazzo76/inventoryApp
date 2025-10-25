-- sql/schema.sql (Microsoft SQL Server)

IF OBJECT_ID('dbo.items','U') IS NOT NULL DROP TABLE dbo.items;
IF OBJECT_ID('dbo.bins','U') IS NOT NULL DROP TABLE dbo.bins;
GO

CREATE TABLE dbo.bins (
  id INT IDENTITY(1,1) PRIMARY KEY,
  bin_number INT NOT NULL UNIQUE,
  category NVARCHAR(100) NULL,
  notes NVARCHAR(400) NULL
);
GO

CREATE TABLE dbo.items (
  id INT IDENTITY(1,1) PRIMARY KEY,
  title NVARCHAR(200) NULL,
  description NVARCHAR(1000) NULL,
  vendor NVARCHAR(200) NULL,
  price DECIMAL(10,2) NULL,
  product_id NVARCHAR(100) NULL,
  vendor_url NVARCHAR(500) NULL,
  quantity INT NOT NULL DEFAULT(1),
  bin_id INT NULL FOREIGN KEY REFERENCES dbo.bins(id) ON DELETE SET NULL,
  image_path NVARCHAR(500) NULL,
  created_at DATETIME2 NOT NULL DEFAULT SYSUTCDATETIME()
);
GO

-- Seed some bins & categories (optional)
INSERT INTO dbo.bins (bin_number, category, notes) VALUES
(1, 'Drone Parts', NULL),
(2, 'Gun Parts', NULL),
(3, '3D Printing', NULL),
(4, 'Arduino', NULL),
(5, 'Raspberry Pi', NULL);
GO
