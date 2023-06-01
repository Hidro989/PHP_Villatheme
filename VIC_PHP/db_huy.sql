-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th5 31, 2023 lúc 10:12 AM
-- Phiên bản máy phục vụ: 10.4.27-MariaDB
-- Phiên bản PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `villathemedb`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `CategoryID` int(11) NOT NULL,
  `Type` text NOT NULL,
  `Name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`CategoryID`, `Type`, `Name`) VALUES
(1, 'category', 'Plugins'),
(2, 'tag', ' autoptimize'),
(3, 'tag', ' clear autoptimize cache automatically'),
(4, 'tag', ' woocommerce'),
(5, 'tag', 'wordpress'),
(6, 'category', ' woocommerce coupons'),
(7, 'tag', 'wordpress plugin');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categorydetail`
--

CREATE TABLE `categorydetail` (
  `ProductID` int(11) NOT NULL,
  `CategoryID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categorydetail`
--

INSERT INTO `categorydetail` (`ProductID`, `CategoryID`) VALUES
(10, 6),
(10, 5),
(11, 1),
(11, 2),
(11, 4),
(8, 6),
(8, 4),
(9, 1),
(9, 6),
(9, 3),
(9, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `SKU` char(8) NOT NULL,
  `Title` text NOT NULL,
  `Price` float NOT NULL,
  `SalePrice` float NOT NULL,
  `FeaturedImage` text NOT NULL,
  `Gallery` text NOT NULL,
  `Description` text NOT NULL,
  `CreatedDate` date NOT NULL DEFAULT current_timestamp(),
  `ModifiedDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`ProductID`, `SKU`, `Title`, `Price`, `SalePrice`, `FeaturedImage`, `Gallery`, `Description`, `CreatedDate`, `ModifiedDate`) VALUES
(8, 'SKU4', 'WooCommerce Thank You Page Customizer – Increase Customer Retention Rate – Boost Sales', 22, 0, './assets/images/product8/image/OnTapASP.jpg', './assets/images/product8/gallery/PHP_Ex.jpg', '', '2023-05-30', NULL),
(9, 'SKU3', 'Sales Countdown Timer for WooCommerce and WordPress', 87, 0, './assets/images/product9/image/PHP_Ex.jpg', './assets/images/product9/gallery/OnTapASP.jpg', '', '2023-05-31', NULL),
(10, 'SKU4', 'WordPress EU Cookies Bar – General Data Protection Regulation Compliance', 12, 0, './assets/images/product10/image/jsform.jpg', './assets/images/product10/gallery/Logo4S.png', '', '2023-05-31', NULL),
(11, 'SKU5', 'WordPress Lucky Wheel – Lucky Wheel Spin and Win', 32, 0, './assets/images/product11/image/Logo4S.png', './assets/images/product11/gallery/OnTapASP.jpg', '', '2023-05-31', NULL);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Chỉ mục cho bảng `categorydetail`
--
ALTER TABLE `categorydetail`
  ADD KEY `FK_Product` (`ProductID`),
  ADD KEY `FK_Category` (`CategoryID`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `category`
--
ALTER TABLE `category`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `categorydetail`
--
ALTER TABLE `categorydetail`
  ADD CONSTRAINT `FK_Category` FOREIGN KEY (`CategoryID`) REFERENCES `category` (`CategoryID`),
  ADD CONSTRAINT `FK_Product` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
