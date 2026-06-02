# 🗄️ SEU UNIBUZZ — Database Reference

**Database Name:** `seu_businesszone_database`  
**Engine:** InnoDB | **Charset:** utf8mb4 | **Server:** MariaDB 10.4+

> The full SQL dump is available in the `demo database/` folder.

---

## 📋 Table Overview

| # | Table | Description |
|---|-------|-------------|
| 1 | `admin` | Admin accounts with login credentials |
| 2 | `user_table` | All registered users — admins, buyers, and sellers |
| 3 | `buyer` | Buyer profiles linked to a user and a cart |
| 4 | `seller` | Seller profiles with university email, department, semester, and rating |
| 5 | `cart` | Shopping cart belonging to each buyer |
| 6 | `add_to_cart` | Individual products added into a cart |
| 7 | `product_category` | Product categories (Electronics, Books, Clothing, Food, Stationery) |
| 8 | `product` | Product listings created by sellers |
| 9 | `payment` | Payment records linked to orders |
| 10 | `order` | Orders placed by buyers |
| 11 | `product_orders` | Line items — which products belong to which order |
| 12 | `business_idea` | Business ideas submitted by sellers with a funding goal |
| 13 | `idea_fund` | Funding contributions made by buyers toward ideas |
| 14 | `transaction` | Transaction records linking a fund contribution to an idea |
| 15 | `global_chat` | Community chat messages from all users |

---

## 🔗 Table Relationships

```
user_table     ──→  admin, buyer, seller
buyer          ──→  user_table, cart
seller         ──→  user_table
cart           ──→  buyer
add_to_cart    ──→  cart, product
product        ──→  seller, product_category
order          ──→  buyer, payment
product_orders ──→  product, order
payment        ──→  order
business_idea  ──→  seller
idea_fund      ──→  buyer
transaction    ──→  idea_fund, business_idea
global_chat    ──→  user_table
```

---

## 🏗️ Full CREATE TABLE Queries

```sql
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- -------------------------------------------------------
-- 1. admin
-- -------------------------------------------------------
CREATE TABLE `admin` (
  `Admin_Id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `Admin_Name`     VARCHAR(100) NOT NULL,
  `Admin_mail`     VARCHAR(150) NOT NULL,
  `Admin_Password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`Admin_Id`),
  UNIQUE KEY `Admin_mail` (`Admin_mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 2. user_table
-- -------------------------------------------------------
CREATE TABLE `user_table` (
  `User_Id`    INT(11)      NOT NULL AUTO_INCREMENT,
  `First_Name` VARCHAR(100) NOT NULL,
  `Last_Name`  VARCHAR(100) NOT NULL,
  `Email`      VARCHAR(150) NOT NULL,
  `Mobile`     VARCHAR(20)  DEFAULT NULL,
  `Password`   VARCHAR(255) NOT NULL,
  `Admin_Id`   INT(11)      DEFAULT NULL,
  `Buyer_Id`   INT(11)      DEFAULT NULL,
  `Seller_Id`  INT(11)      DEFAULT NULL,
  PRIMARY KEY (`User_Id`),
  UNIQUE KEY `Email` (`Email`),
  KEY `Admin_Id` (`Admin_Id`),
  KEY `Buyer_Id` (`Buyer_Id`),
  KEY `Seller_Id` (`Seller_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 3. buyer
-- -------------------------------------------------------
CREATE TABLE `buyer` (
  `Buyer_Id` INT(11) NOT NULL AUTO_INCREMENT,
  `User_Id`  INT(11) NOT NULL,
  `Cart_Id`  INT(11) DEFAULT NULL,
  PRIMARY KEY (`Buyer_Id`),
  KEY `User_Id` (`User_Id`),
  KEY `Cart_Id` (`Cart_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 4. seller
-- -------------------------------------------------------
CREATE TABLE `seller` (
  `Seller_Id`        INT(11)      NOT NULL AUTO_INCREMENT,
  `User_Id`          INT(11)      NOT NULL,
  `University_Email` VARCHAR(150) NOT NULL,
  `Rating`           DECIMAL(3,2) DEFAULT 0.00,
  `Department`       VARCHAR(100) DEFAULT NULL,
  `Semester`         VARCHAR(20)  DEFAULT NULL,
  `Student_Id`       VARCHAR(50)  DEFAULT NULL,
  PRIMARY KEY (`Seller_Id`),
  UNIQUE KEY `University_Email` (`University_Email`),
  KEY `User_Id` (`User_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 5. cart
-- -------------------------------------------------------
CREATE TABLE `cart` (
  `Cart_Id`  INT(11) NOT NULL AUTO_INCREMENT,
  `Quantity` INT(11) NOT NULL DEFAULT 0,
  `Buyer_Id` INT(11) NOT NULL,
  PRIMARY KEY (`Cart_Id`),
  KEY `Buyer_Id` (`Buyer_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 6. add_to_cart
-- -------------------------------------------------------
CREATE TABLE `add_to_cart` (
  `Product_add_id` INT(11) NOT NULL AUTO_INCREMENT,
  `Cart_Id`        INT(11) NOT NULL,
  `Product_Id`     INT(11) NOT NULL,
  `Quantity`       INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Product_add_id`),
  KEY `Cart_Id` (`Cart_Id`),
  KEY `Product_Id` (`Product_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 7. product_category
-- -------------------------------------------------------
CREATE TABLE `product_category` (
  `Product_Category_Id` INT(11)      NOT NULL AUTO_INCREMENT,
  `Category_Name`       VARCHAR(100) NOT NULL,
  PRIMARY KEY (`Product_Category_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 8. product
-- -------------------------------------------------------
CREATE TABLE `product` (
  `Product_Id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `Seller_Id`           INT(11)       NOT NULL,
  `Product_Name`        VARCHAR(150)  NOT NULL,
  `Product_Type`        VARCHAR(100)  DEFAULT NULL,
  `Price`               DECIMAL(10,2) NOT NULL,
  `Stock_Quantity`      INT(11)       NOT NULL DEFAULT 0,
  `Product_Image`       VARCHAR(255)  DEFAULT NULL,
  `Product_Category_Id` INT(11)       DEFAULT NULL,
  PRIMARY KEY (`Product_Id`),
  KEY `Seller_Id` (`Seller_Id`),
  KEY `Product_Category_Id` (`Product_Category_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 9. payment
-- -------------------------------------------------------
CREATE TABLE `payment` (
  `Payment_id`     INT(11)       NOT NULL AUTO_INCREMENT,
  `Order_Id`       INT(11)       NOT NULL,
  `Payment_Method` VARCHAR(50)   NOT NULL,
  `Payment_Status` VARCHAR(50)   NOT NULL DEFAULT 'Pending',
  `Amount`         DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`Payment_id`),
  KEY `Order_Id` (`Order_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 10. order
-- -------------------------------------------------------
CREATE TABLE `order` (
  `Order_Id`     INT(11)       NOT NULL AUTO_INCREMENT,
  `Buyer_Id`     INT(11)       NOT NULL,
  `Order_Date`   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status`       VARCHAR(50)   NOT NULL DEFAULT 'Pending',
  `Total_amount` DECIMAL(10,2) NOT NULL,
  `Payment_id`   INT(11)       DEFAULT NULL,
  PRIMARY KEY (`Order_Id`),
  KEY `Buyer_Id` (`Buyer_Id`),
  KEY `Payment_id` (`Payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 11. product_orders
-- -------------------------------------------------------
CREATE TABLE `product_orders` (
  `Product_Order_Id` INT(11) NOT NULL AUTO_INCREMENT,
  `Product_Id`       INT(11) NOT NULL,
  `Order_Id`         INT(11) NOT NULL,
  `Quantity`         INT(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Product_Order_Id`),
  KEY `Product_Id` (`Product_Id`),
  KEY `Order_Id` (`Order_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 12. business_idea
-- -------------------------------------------------------
CREATE TABLE `business_idea` (
  `Idea_Id`      INT(11)       NOT NULL AUTO_INCREMENT,
  `Seller_Id`    INT(11)       NOT NULL,
  `Idea`         VARCHAR(255)  NOT NULL,
  `Description`  TEXT          DEFAULT NULL,
  `Funding_Goal` DECIMAL(12,2) DEFAULT NULL,
  `Status`       VARCHAR(50)   DEFAULT 'Pending',
  PRIMARY KEY (`Idea_Id`),
  KEY `Seller_Id` (`Seller_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 13. idea_fund
-- -------------------------------------------------------
CREATE TABLE `idea_fund` (
  `Fund_id`        INT(11)       NOT NULL AUTO_INCREMENT,
  `Idea_Id`        INT(11)       NOT NULL,
  `Payment_Method` VARCHAR(50)   NOT NULL,
  `Payment_Status` VARCHAR(50)   NOT NULL DEFAULT 'Pending',
  `Amount`         DECIMAL(12,2) NOT NULL,
  `Buyer_Id`       INT(11)       NOT NULL,
  PRIMARY KEY (`Fund_id`),
  KEY `Buyer_Id` (`Buyer_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 14. transaction
-- -------------------------------------------------------
CREATE TABLE `transaction` (
  `Transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
  `Fund_id`        INT(11) NOT NULL,
  `Idea_Id`        INT(11) NOT NULL,
  PRIMARY KEY (`Transaction_id`),
  KEY `Fund_id` (`Fund_id`),
  KEY `Idea_Id` (`Idea_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- 15. global_chat
-- -------------------------------------------------------
CREATE TABLE `global_chat` (
  `Message_id`   INT(11)  NOT NULL AUTO_INCREMENT,
  `User_Id`      INT(11)  NOT NULL,
  `Message_Date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Message`      TEXT     NOT NULL,
  PRIMARY KEY (`Message_id`),
  KEY `User_Id` (`User_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -------------------------------------------------------
-- Foreign Key Constraints
-- -------------------------------------------------------

ALTER TABLE `user_table`
  ADD CONSTRAINT `user_table_ibfk_1` FOREIGN KEY (`Admin_Id`)  REFERENCES `admin`  (`Admin_Id`)  ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_table_ibfk_2` FOREIGN KEY (`Buyer_Id`)  REFERENCES `buyer`  (`Buyer_Id`)  ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `user_table_ibfk_3` FOREIGN KEY (`Seller_Id`) REFERENCES `seller` (`Seller_Id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `buyer`
  ADD CONSTRAINT `buyer_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user_table` (`User_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `buyer_ibfk_2` FOREIGN KEY (`Cart_Id`) REFERENCES `cart`       (`Cart_Id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `seller`
  ADD CONSTRAINT `seller_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user_table` (`User_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`Buyer_Id`) REFERENCES `buyer` (`Buyer_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `add_to_cart`
  ADD CONSTRAINT `add_to_cart_ibfk_1` FOREIGN KEY (`Cart_Id`)    REFERENCES `cart`    (`Cart_Id`)    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `add_to_cart_ibfk_2` FOREIGN KEY (`Product_Id`) REFERENCES `product` (`Product_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Seller_Id`)           REFERENCES `seller`           (`Seller_Id`)           ON DELETE CASCADE   ON UPDATE CASCADE,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`Product_Category_Id`) REFERENCES `product_category` (`Product_Category_Id`) ON DELETE SET NULL  ON UPDATE CASCADE;

ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Order_Id`) REFERENCES `order` (`Order_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`Buyer_Id`)   REFERENCES `buyer`   (`Buyer_Id`)   ON DELETE CASCADE  ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`Payment_id`) REFERENCES `payment` (`Payment_id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `product_orders`
  ADD CONSTRAINT `product_orders_ibfk_1` FOREIGN KEY (`Product_Id`) REFERENCES `product` (`Product_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_orders_ibfk_2` FOREIGN KEY (`Order_Id`)   REFERENCES `order`   (`Order_Id`)   ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `business_idea`
  ADD CONSTRAINT `business_idea_ibfk_1` FOREIGN KEY (`Seller_Id`) REFERENCES `seller` (`Seller_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `idea_fund`
  ADD CONSTRAINT `idea_fund_ibfk_1` FOREIGN KEY (`Buyer_Id`) REFERENCES `buyer` (`Buyer_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`Fund_id`)  REFERENCES `idea_fund`     (`Fund_id`)  ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`Idea_Id`)  REFERENCES `business_idea` (`Idea_Id`)  ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `global_chat`
  ADD CONSTRAINT `global_chat_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user_table` (`User_Id`) ON DELETE CASCADE ON UPDATE CASCADE;
```
