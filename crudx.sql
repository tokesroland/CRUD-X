-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Feb 04. 12:48
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `crudx`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `categories`
--

CREATE TABLE `categories` (
  `ID` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `categories`
--

INSERT INTO `categories` (`ID`, `category_name`) VALUES
(1, 'Kábelek és csatlakozók'),
(2, 'Számítógép kiegészítők'),
(3, 'Mobil kiegészítők'),
(4, 'Gaming tartozékok'),
(5, 'Háztartási és lifestyle elektronika'),
(6, 'Adattárolók'),
(7, 'Hálózati eszközök'),
(8, 'Irodai kiegészítők'),
(9, 'Audio eszközök'),
(10, 'Video eszközök'),
(11, 'Okoseszközök kiegészítői'),
(12, 'Laptop kiegészítők'),
(13, 'Töltők és adapterek'),
(14, 'Monitor tartozékok'),
(15, 'Fotós kiegészítők'),
(16, 'Tablet kiegészítők'),
(17, 'Okosotthon eszközök'),
(18, 'Biztonságtechnika'),
(19, 'Ergonómiai kiegészítők'),
(20, 'Egyéb elektronikai termékek'),
(21, 'Laptop/PC');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `inventory`
--

CREATE TABLE `inventory` (
  `ID` int(11) NOT NULL,
  `product_ID` int(11) NOT NULL,
  `warehouse_ID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `min_quantity` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `inventory`
--

INSERT INTO `inventory` (`ID`, `product_ID`, `warehouse_ID`, `quantity`, `min_quantity`, `created_at`, `updated_at`) VALUES
(2, 41, 22, 41, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(4, 41, 24, 7, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(5, 42, 21, 54, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(6, 42, 22, 29, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(7, 42, 23, 122, 6, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(8, 42, 24, 66, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(9, 43, 21, 27, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(10, 43, 22, 51, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(11, 43, 23, 2, 1, '2026-01-21 10:56:53', '2026-02-04 12:20:13'),
(12, 43, 24, 34, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(13, 44, 21, 99, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(14, 44, 22, 48, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(15, 44, 23, 70, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(16, 44, 24, 5, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(21, 46, 21, 68, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(22, 46, 22, 132, 7, '2026-01-21 10:56:53', '2026-02-04 10:42:13'),
(23, 46, 23, 33, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(24, 46, 24, 15, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(25, 47, 21, 56, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(26, 47, 22, 0, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(27, 47, 23, 73, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(28, 47, 24, 128, 7, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(29, 48, 21, 28, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(30, 48, 22, 96, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(31, 48, 23, 111, 6, '2026-01-21 10:56:53', '2026-02-04 12:41:49'),
(32, 48, 24, 34, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(33, 49, 21, 147, 8, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(34, 49, 22, 62, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(35, 49, 23, 19, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(36, 49, 24, 87, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(37, 50, 21, 74, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(38, 50, 22, 121, 6, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(39, 50, 23, 53, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(40, 50, 24, 12, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(41, 51, 21, 33, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(42, 51, 22, 84, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(43, 51, 23, 37, 8, '2026-01-21 10:56:53', '2026-02-04 12:29:46'),
(44, 51, 24, 45, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(45, 52, 21, 50, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(46, 52, 22, 122, 7, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(47, 52, 23, 5, 1, '2026-01-21 10:56:53', '2026-02-04 12:03:53'),
(48, 52, 24, 73, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(49, 53, 21, 116, 6, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(50, 53, 22, 0, 100, '2026-01-21 10:56:53', '2026-02-04 10:42:13'),
(51, 53, 23, 87, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(52, 53, 24, 129, 7, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(53, 54, 21, 95, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(54, 54, 22, 58, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(55, 54, 23, 11, 1, '2026-01-21 10:56:53', '2026-02-04 12:20:13'),
(56, 54, 24, 101, 6, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(57, 55, 21, 149, 9, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(58, 55, 22, 77, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(59, 55, 23, 30, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(60, 55, 24, 18, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(61, 56, 21, 66, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(62, 56, 22, 108, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(63, 56, 23, 120, 7, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(64, 56, 24, 49, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(65, 57, 21, 24, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(66, 57, 22, 93, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(67, 57, 23, 14, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(68, 57, 24, 135, 7, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(69, 58, 21, 83, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(70, 58, 22, 58, 3, '2026-01-21 10:56:53', '2026-02-04 12:00:38'),
(71, 58, 23, 130, 6, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(72, 58, 24, 10, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(73, 59, 21, 52, 3, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(74, 59, 22, 145, 8, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(75, 59, 23, 46, 2, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(76, 59, 24, 10, 1, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(77, 60, 21, 71, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(78, 60, 22, 38, 2, '2026-01-21 10:56:53', '2026-02-04 12:00:38'),
(79, 60, 23, 75, 4, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(80, 60, 24, 138, 6, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(81, 51, 23, 100, 40, '2026-01-21 10:56:53', '2026-02-04 12:01:41'),
(82, 41, 21, 120, 10, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(83, 42, 21, 50, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(84, 42, 22, 41, 5, '2026-01-21 10:56:53', '2026-02-03 21:01:14'),
(86, 41, 21, 120, 10, '2026-01-21 10:57:06', '2026-02-03 21:01:14'),
(87, 42, 21, 50, 5, '2026-01-21 10:57:06', '2026-02-03 21:01:14'),
(88, 42, 22, 41, 5, '2026-01-21 10:57:06', '2026-02-03 21:01:14'),
(89, 43, 23, 0, 2, '2026-01-21 10:57:06', '2026-02-04 12:20:13'),
(90, 45, 23, 100, 10, '2026-01-21 11:04:38', '2026-02-03 21:01:14'),
(91, 64, 21, 120, NULL, '2026-01-21 11:58:12', '2026-02-03 21:01:14'),
(92, 65, 21, 50, NULL, '2026-01-21 11:58:12', '2026-02-03 21:01:14'),
(93, 65, 22, 30, NULL, '2026-01-21 11:58:12', '2026-02-03 21:01:14'),
(94, 66, 23, 0, 100, '2026-01-21 11:58:12', '2026-02-04 12:29:46'),
(95, 43, 25, 100, NULL, '2026-01-21 12:15:02', '2026-02-03 21:01:14'),
(96, 59, 26, 1, NULL, '2026-01-22 20:06:34', '2026-02-03 21:01:14'),
(97, 45, 26, 100, NULL, '2026-01-22 20:06:34', '2026-02-03 21:01:14'),
(98, 58, 27, 10, NULL, '2026-01-22 20:08:52', '2026-02-03 21:01:14'),
(99, 52, 31, 11, NULL, '2026-02-03 10:24:48', '2026-02-04 12:09:54'),
(100, 51, 31, 11, NULL, '2026-02-03 10:24:48', '2026-02-04 12:31:42'),
(101, 52, 28, 20, NULL, '2026-02-03 10:59:23', '2026-02-03 21:01:14'),
(102, 65, 28, 10, NULL, '2026-02-03 10:59:59', '2026-02-03 21:01:14'),
(103, 74, 31, 88, NULL, '2026-02-03 20:22:34', '2026-02-03 21:08:39'),
(104, 74, 27, 12, NULL, '2026-02-03 21:08:39', NULL),
(105, 46, 31, 10, NULL, '2026-02-04 10:42:13', NULL),
(106, 53, 31, 52, NULL, '2026-02-04 10:42:13', NULL),
(107, 52, 29, 1, NULL, '2026-02-04 12:01:52', NULL),
(108, 51, 29, 100, NULL, '2026-02-04 12:01:52', NULL),
(109, 58, 31, 10, NULL, '2026-02-04 12:09:56', NULL),
(110, 60, 31, 1, NULL, '2026-02-04 12:09:56', NULL),
(111, 43, 31, 10, NULL, '2026-02-04 12:23:35', NULL),
(112, 54, 31, 1, NULL, '2026-02-04 12:23:35', NULL),
(113, 66, 31, 10, NULL, '2026-02-04 12:31:42', NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE `products` (
  `ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `item_number` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category_ID` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `products`
--

INSERT INTO `products` (`ID`, `name`, `item_number`, `description`, `category_ID`, `active`, `created_at`, `updated_at`) VALUES
(41, 'USB-C Kábel 1m', '100001', 'Gyors töltésre alkalmas USB-C kábel.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(42, 'Laptop Hátizsák 15.6\"', '100002', 'Vízlepergető bevonattal, több rekesszel.', 2, 0, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(43, 'Logitech Bluetooth Egér', '100003', 'Vezeték nélküli, 1600 DPI érzékenységgel.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(44, 'Mechanikus Billentyűzet', '100004', 'Kék switch, RGB világítás.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(45, 'iPhone 15 Tok', '100005', 'Szilikon védőtok fekete színben.', 3, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(46, 'Notebook Állvány', '100006', 'Állítható magasságú alumínium állvány.', 2, 0, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(47, 'HDMI Kábel 2m', '100007', '4K felbontás támogatás, aranyozott csatlakozó.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(48, 'USB Pendrive 64GB', '100008', 'Gyors adatátvitel, kulcstartóra rögzíthető.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(49, 'Gaming Headset', '100009', '7.1 virtuális hangzás, zajszűrő mikrofonnal.', 4, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(50, 'Webkamera Full HD', '100010', '1080p felbontás, autofókusz funkció.', 4, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(51, 'Prémium Egérpad', '100011', 'Csúszásmentes felület gamer használatra.', 4, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(52, 'Type-C Töltőfej 30W', '100012', 'Gyors töltés támogatás USB Power Delivery-vel.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(53, 'Asztali Ventillátor USB', '100013', 'Csendes működés, 3 sebességfokozat.', 5, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(54, 'Okosóra Szíj 22mm', '100014', 'Szilikon szíj különböző órákhoz.', 3, 0, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(55, 'Powerbank 20 000mAh', '100015', 'Gyors töltés, LED töltöttség kijelző.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(56, 'Bluetooth Hangszóró', '100016', 'Vízálló IPX5 minősítés, erős basszus.', 5, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(57, 'Kijelzőtisztító Spray', '100017', 'Antisztatikus tisztítószer mikroszálas kendővel.', 3, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(58, 'USB-C Docking Station', '100018', 'HDMI, USB3, LAN és SD kártya olvasóval.', 2, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(59, 'Vezeték nélküli Töltő', '100019', '10W gyorstöltés Qi szabvány szerint.', 1, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(60, 'Fotós Állvány Mini', '100020', 'Kompakt tripod, 360° fejjel.', 5, 1, '2025-12-05 18:38:27', '2026-02-03 20:52:44'),
(64, 'Trapista sajt', '1768993094', 'CSV Importált', 1, 1, '2026-01-21 11:58:12', '2026-02-03 20:52:44'),
(65, 'Iphone 10 Töltő', '1768993095', 'CSV Importált', 1, 1, '2026-01-21 11:58:12', '2026-02-03 20:52:44'),
(66, '43', '1768993097', 'CSV Importált', 1, 0, '2026-01-21 11:58:12', '2026-02-03 20:52:44'),
(67, 'Várj egy kicsit', '2147483647', 'Várj, generálok egyetttt, na mit irjak meg ide, NA,  na jojo jolesz, termek mentese, nem befejezes, nyomj egy tabot roland. Igen? pont. termék mentése, nyomd meg a gombot roland, hahahahahahhaahah néázd add ide az egeret', 12, 1, '2026-01-22 20:36:39', '2026-02-03 20:52:44'),
(68, 'ASUS Vivobook AX', '345346664', 'Várj, generálok egyetttt, na mit irjak meg ide, NA, aaana jojo jolesz, termek mentese, nem befejezes, nyomj egy tabot roland. Igen? pont. termék mentése, nyomd meg a gombot roland, hahahahahahahhaahah néázd add ide az egeret', 21, 0, '2026-02-03 10:22:59', '2026-02-03 20:52:44'),
(70, 'ASUS Vivobook 3 A', '177011271', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio corporis eaque eveniet, quam sed ab vero expedita, odit, doloremque voluptatum aliquid nobis quo ratione aperiam incidunt animi quae! Facere, obcaecati.', 21, 1, '2026-02-03 10:58:28', '2026-02-03 20:52:44'),
(74, 'Hikvision DS-2CD5526G0-IZHS(2.8-12mm)(B) FULLHD Ultra plus oliva olaj', '1770146457', 'Fejhallgató Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsum earum cupiditate repudiandae illo eum corporis consequuntur facere similique, quos explicabo voluptates, quisquam error exercitationem. Quisquam libero excepturi voluptatum architecto? Voluptates.', 20, 1, '2026-02-03 20:20:57', '2026-02-03 20:52:44'),
(81, '3e24ewf', '2342342', '23423424', 19, 1, '2026-02-03 20:39:13', '2026-02-03 20:52:44'),
(82, 'RokesToland bodypillow', '1531434343', 'Gamer', 7, 1, '2026-02-03 20:39:27', '2026-02-03 20:52:44');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `transports`
--

CREATE TABLE `transports` (
  `ID` int(11) NOT NULL,
  `batch_id` varchar(50) DEFAULT NULL,
  `product_ID` int(11) NOT NULL,
  `warehouse_ID` int(11) NOT NULL,
  `type` enum('import','export') NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `user_ID` int(11) NOT NULL,
  `description` text NOT NULL,
  `arriveIn` date DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','completed','canceled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `transports`
--

INSERT INTO `transports` (`ID`, `batch_id`, `product_ID`, `warehouse_ID`, `type`, `date`, `user_ID`, `description`, `arriveIn`, `quantity`, `status`) VALUES
(1, 'TR-1770202838-7D32', 58, 22, 'export', '2026-02-04 12:00:38', 5, 'Kiszállítás cél: Raktár #31. (B épület AFS-242)', '2026-02-05', 10, 'completed'),
(2, 'TR-1770202838-7D32', 58, 31, 'import', '2026-02-04 12:00:38', 5, 'Beérkezés forrás: Raktár #22. (B épület AFS-242)', '2026-02-05', 10, 'completed'),
(3, 'TR-1770202838-7D32', 60, 22, 'export', '2026-02-04 12:00:38', 5, 'Kiszállítás cél: Raktár #31. (B épület AFS-242)', '2026-02-05', 1, 'completed'),
(4, 'TR-1770202838-7D32', 60, 31, 'import', '2026-02-04 12:00:38', 5, 'Beérkezés forrás: Raktár #22. (B épület AFS-242)', '2026-02-05', 1, 'completed'),
(5, 'TR-1770202901-1163', 52, 23, 'export', '2026-02-04 12:01:41', 5, 'Kiszállítás cél: Raktár #29. (weww)', '2026-02-04', 1, 'completed'),
(6, 'TR-1770202901-1163', 52, 29, 'import', '2026-02-04 12:01:41', 5, 'Beérkezés forrás: Raktár #23. (weww)', '2026-02-04', 1, 'completed'),
(7, 'TR-1770202901-1163', 51, 23, 'export', '2026-02-04 12:01:41', 5, 'Kiszállítás cél: Raktár #29. (weww)', '2026-02-04', 100, 'completed'),
(8, 'TR-1770202901-1163', 51, 29, 'import', '2026-02-04 12:01:41', 5, 'Beérkezés forrás: Raktár #23. (weww)', '2026-02-04', 100, 'completed'),
(9, 'TR-1770203033-E82B', 52, 23, 'export', '2026-02-04 12:03:53', 5, 'Kiszállítás cél: Raktár #31. (B épület AFS-2422)', '2026-02-05', 1, 'completed'),
(10, 'TR-1770203033-E82B', 52, 31, 'import', '2026-02-04 12:03:53', 5, 'Beérkezés forrás: Raktár #23. (B épület AFS-2422)', '2026-02-05', 1, 'completed'),
(11, 'TR-1770204013-C370', 43, 23, 'export', '2026-02-04 12:20:13', 5, 'Kiszállítás cél: Raktár #31. (B épület AFS-242)', '2026-02-04', 10, 'completed'),
(12, 'TR-1770204013-C370', 43, 31, 'import', '2026-02-04 12:20:13', 5, 'Beérkezés forrás: Raktár #23. (B épület AFS-242)', '2026-02-04', 10, 'completed'),
(13, 'TR-1770204013-C370', 54, 23, 'export', '2026-02-04 12:20:13', 5, 'Kiszállítás cél: Raktár #31. (B épület AFS-242)', '2026-02-04', 1, 'completed'),
(14, 'TR-1770204013-C370', 54, 31, 'import', '2026-02-04 12:20:13', 5, 'Beérkezés forrás: Raktár #23. (B épület AFS-242)', '2026-02-04', 1, 'completed'),
(15, 'TR-1770204586-93AC', 51, 23, 'export', '2026-02-04 12:29:46', 5, 'Kiszállítás cél: Raktár #31. (weww)', '2026-02-04', 10, 'completed'),
(16, 'TR-1770204586-93AC', 51, 31, 'import', '2026-02-04 12:29:46', 5, 'Beérkezés forrás: Raktár #23. (weww)', '2026-02-04', 10, 'completed'),
(17, 'TR-1770204586-93AC', 66, 23, 'export', '2026-02-04 12:29:46', 5, 'Kiszállítás cél: Raktár #31. (weww)', '2026-02-04', 10, 'completed'),
(18, 'TR-1770204586-93AC', 66, 31, 'import', '2026-02-04 12:29:46', 5, 'Beérkezés forrás: Raktár #23. (weww)', '2026-02-04', 10, 'completed'),
(19, 'TR-1770205309-24F4', 48, 23, 'export', '2026-02-04 12:41:49', 5, 'Kiszállítás cél: Raktár #27. (weww)', '2026-02-26', 1, 'completed'),
(20, 'TR-1770205309-24F4', 48, 27, 'import', '2026-02-04 12:41:49', 5, 'Beérkezés forrás: Raktár #23. (weww)', '2026-02-26', 1, 'pending');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','owner') NOT NULL,
  `warehouse_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `login_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`ID`, `username`, `active`, `password`, `role`, `warehouse_id`, `created_at`, `login_at`) VALUES
(5, '2', 1, '$2y$10$HJdkInDHH2a0ai1lOuc4fuVoDn9.1K8kCuTIEYYoVStriK2b0rD/y', 'owner', NULL, '2026-01-21 15:02:35', '2026-02-04 12:22:04'),
(11, '1', 1, '$2y$10$80/EqxHnQnBCPFMe3qLwp.YP0PlSmMky2K4auOBUE7vHyBcAEO98a', 'admin', NULL, '2026-01-21 15:08:42', '2026-02-03 21:39:43'),
(12, 'Tőkés Roland', 1, '$2y$10$1LLVsSxLFWUnChal0TUg1OsDZi3uhjv3d0H1YYjrdBih2S5OYHPjy', 'owner', NULL, '2026-01-22 13:40:39', '2026-02-03 19:20:41'),
(13, 'Minta Pista', 1, '$2y$10$7uSpwJScnN1dWajysVu1DecFE02odcrfRYnYWTVKfuKZEklFRMPOK', 'user', 31, '2026-02-03 10:09:14', '2026-02-03 10:24:59');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_error`
--

CREATE TABLE `user_error` (
  `errID` int(11) NOT NULL,
  `user_ID` int(11) DEFAULT NULL,
  `input_value` varchar(255) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('incomplete','complete') NOT NULL DEFAULT 'incomplete',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_warehouse_access`
--

CREATE TABLE `user_warehouse_access` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `user_warehouse_access`
--

INSERT INTO `user_warehouse_access` (`id`, `user_id`, `warehouse_id`) VALUES
(1, 13, 31);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `warehouses`
--

CREATE TABLE `warehouses` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `max_quantity` int(20) NOT NULL,
  `type` enum('warehouse','store') NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `warehouses`
--

INSERT INTO `warehouses` (`ID`, `name`, `address`, `max_quantity`, `type`, `active`) VALUES
(21, 'Központi Raktár', 'Budapest, Fő utca 12.', 20000, 'warehouse', 1),
(22, 'Északi Logisztikai Központ', 'Győr, Ipari park 5.', 50000, 'warehouse', 1),
(23, 'Déli Regionális Raktár', 'Pécs, Logisztikai út 8.', 50000, 'warehouse', 1),
(24, 'Keleti Elosztó', 'Debrecen, Külső-Böszörményi út 44.', 94566, 'warehouse', 1),
(25, 'Nyugati Raktárbázis', 'Szombathely, Bevásárló köz 2.', 54000, 'warehouse', 1),
(26, 'Mini Depó 1', 'Székesfehérvár, Seregélyesi út 19.', 40000, 'warehouse', 1),
(27, 'Mini Depó 2', 'Kecskemét, Vágóhíd utca 7.', 5000, 'warehouse', 1),
(28, 'High-Tech Raktár', 'Budapest, Üllői út 215.', 5660, 'warehouse', 1),
(29, 'Készletközpont 1', 'Miskolc, Szentpéteri kapu 103.', 60000, 'warehouse', 1),
(30, 'Készletközpont 2', 'Szeged, Dorozsmai út 33.', 40000, 'warehouse', 1),
(31, 'Keleti CRUD Üzlet', '1214 Mars u. 16.', 6800, 'store', 1);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`ID`);

--
-- A tábla indexei `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `warehouse_ID` (`warehouse_ID`),
  ADD KEY `product_ID` (`product_ID`);

--
-- A tábla indexei `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `item_number` (`item_number`),
  ADD KEY `category_ID` (`category_ID`);

--
-- A tábla indexei `transports`
--
ALTER TABLE `transports`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `warehouse_ID` (`warehouse_ID`),
  ADD KEY `product_ID` (`product_ID`),
  ADD KEY `user_ID` (`user_ID`),
  ADD KEY `idx_transports_arrive` (`type`,`arriveIn`,`warehouse_ID`,`product_ID`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_user_warehouse` (`warehouse_id`);

--
-- A tábla indexei `user_error`
--
ALTER TABLE `user_error`
  ADD PRIMARY KEY (`errID`),
  ADD KEY `fk_user_error_user` (`user_ID`);

--
-- A tábla indexei `user_warehouse_access`
--
ALTER TABLE `user_warehouse_access`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_access` (`user_id`,`warehouse_id`),
  ADD KEY `warehouse_id` (`warehouse_id`);

--
-- A tábla indexei `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`ID`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `categories`
--
ALTER TABLE `categories`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT a táblához `inventory`
--
ALTER TABLE `inventory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT a táblához `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT a táblához `transports`
--
ALTER TABLE `transports`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT a táblához `user_error`
--
ALTER TABLE `user_error`
  MODIFY `errID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `user_warehouse_access`
--
ALTER TABLE `user_warehouse_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_ID`) REFERENCES `products` (`ID`),
  ADD CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`warehouse_ID`) REFERENCES `warehouses` (`ID`);

--
-- Megkötések a táblához `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_ID`) REFERENCES `categories` (`ID`);

--
-- Megkötések a táblához `transports`
--
ALTER TABLE `transports`
  ADD CONSTRAINT `fk_transports_warehouse` FOREIGN KEY (`warehouse_ID`) REFERENCES `warehouses` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transports_ibfk_1` FOREIGN KEY (`product_ID`) REFERENCES `products` (`ID`),
  ADD CONSTRAINT `transports_ibfk_2` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`);

--
-- Megkötések a táblához `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `user_error`
--
ALTER TABLE `user_error`
  ADD CONSTRAINT `fk_user_error_user` FOREIGN KEY (`user_ID`) REFERENCES `users` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Megkötések a táblához `user_warehouse_access`
--
ALTER TABLE `user_warehouse_access`
  ADD CONSTRAINT `user_warehouse_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_warehouse_access_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
