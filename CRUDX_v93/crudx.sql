-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Jan 22. 20:45
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
(20, 'Egyéb elektronikai termékek');

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
(2, 41, 22, 41, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(4, 41, 24, 7, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(5, 42, 21, 54, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(6, 42, 22, 18, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(7, 42, 23, 133, 6, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(8, 42, 24, 66, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(9, 43, 21, 27, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(10, 43, 22, 51, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(11, 43, 23, 12, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(12, 43, 24, 34, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(13, 44, 21, 99, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(14, 44, 22, 48, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(15, 44, 23, 70, 5, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(16, 44, 24, 5, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(21, 46, 21, 68, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(22, 46, 22, 142, 7, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(23, 46, 23, 33, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(24, 46, 24, 15, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(25, 47, 21, 56, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(26, 47, 22, 0, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(27, 47, 23, 73, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(28, 47, 24, 128, 7, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(29, 48, 21, 28, 3, '2026-01-21 10:56:53', '2026-01-22 20:03:36'),
(30, 48, 22, 96, 5, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(31, 48, 23, 112, 6, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(32, 48, 24, 34, 2, '2026-01-21 10:56:53', '2026-01-22 20:03:36'),
(33, 49, 21, 147, 8, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(34, 49, 22, 62, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(35, 49, 23, 19, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(36, 49, 24, 87, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(37, 50, 21, 74, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(38, 50, 22, 121, 6, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(39, 50, 23, 53, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(40, 50, 24, 12, 1, '2026-01-21 10:56:53', '2026-01-21 12:01:26'),
(41, 51, 21, 33, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(42, 51, 22, 85, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(43, 51, 23, 147, 8, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(44, 51, 24, 45, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(45, 52, 21, 50, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(46, 52, 22, 132, 7, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(47, 52, 23, 7, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(48, 52, 24, 73, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(49, 53, 21, 116, 6, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(50, 53, 22, 42, 100, '2026-01-21 10:56:53', '2026-01-21 13:27:09'),
(51, 53, 23, 97, 5, '2026-01-21 10:56:53', '2026-01-21 11:37:06'),
(52, 53, 24, 129, 7, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(53, 54, 21, 95, 5, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(54, 54, 22, 58, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(55, 54, 23, 12, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(56, 54, 24, 101, 6, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(57, 55, 21, 149, 9, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(58, 55, 22, 77, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(59, 55, 23, 30, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(60, 55, 24, 18, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(61, 56, 21, 66, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(62, 56, 22, 108, 5, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(63, 56, 23, 120, 7, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(64, 56, 24, 49, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(65, 57, 21, 24, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(66, 57, 22, 93, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(67, 57, 23, 14, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(68, 57, 24, 135, 7, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(69, 58, 21, 83, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(70, 58, 22, 68, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(71, 58, 23, 130, 6, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(72, 58, 24, 10, 1, '2026-01-21 10:56:53', '2026-01-22 20:08:52'),
(73, 59, 21, 52, 3, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(74, 59, 22, 145, 8, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(75, 59, 23, 46, 2, '2026-01-21 10:56:53', '2026-01-22 20:06:34'),
(76, 59, 24, 10, 1, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(77, 60, 21, 71, 5, '2026-01-21 10:56:53', '2026-01-22 20:03:36'),
(78, 60, 22, 39, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(79, 60, 23, 75, 4, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(80, 60, 24, 138, 6, '2026-01-21 10:56:53', '2026-01-22 20:03:36'),
(81, 51, 23, 200, 40, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(82, 41, 21, 120, 10, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(83, 42, 21, 50, 5, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(84, 42, 22, 30, 5, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(85, 43, 23, 0, 2, '2026-01-21 10:56:53', '2026-01-21 10:56:53'),
(86, 41, 21, 120, 10, '2026-01-21 10:57:06', '2026-01-21 10:57:06'),
(87, 42, 21, 50, 5, '2026-01-21 10:57:06', '2026-01-21 10:57:06'),
(88, 42, 22, 30, 5, '2026-01-21 10:57:06', '2026-01-21 10:57:06'),
(89, 43, 23, 0, 2, '2026-01-21 10:57:06', '2026-01-21 10:57:06'),
(90, 45, 23, 100, 10, '2026-01-21 11:04:38', '2026-01-22 20:06:34'),
(91, 64, 21, 120, NULL, '2026-01-21 11:58:12', NULL),
(92, 65, 21, 50, NULL, '2026-01-21 11:58:12', NULL),
(93, 65, 22, 30, NULL, '2026-01-21 11:58:12', NULL),
(94, 66, 23, 0, NULL, '2026-01-21 11:58:12', NULL),
(95, 43, 25, 100, NULL, '2026-01-21 12:15:02', NULL),
(96, 59, 26, 1, NULL, '2026-01-22 20:06:34', NULL),
(97, 45, 26, 100, NULL, '2026-01-22 20:06:34', NULL),
(98, 58, 27, 10, NULL, '2026-01-22 20:08:52', NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE `products` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `item_number` int(100) NOT NULL,
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
(41, 'USB-C Kábel 1m', 100001, 'Gyors töltésre alkalmas USB-C kábel.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(42, 'Laptop Hátizsák 15.6\"', 100002, 'Vízlepergető bevonattal, több rekesszel.', 2, 0, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(43, 'Bluetooth Egér', 100003, 'Vezeték nélküli, 1600 DPI érzékenységgel.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(44, 'Mechanikus Billentyűzet', 100004, 'Kék switch, RGB világítás.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(45, 'iPhone 14 Tok', 100005, 'Szilikon védőtok fekete színben.', 3, 0, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(46, 'Notebook Állvány', 100006, 'Állítható magasságú alumínium állvány.', 2, 0, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(47, 'HDMI Kábel 2m', 100007, '4K felbontás támogatás, aranyozott csatlakozó.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(48, 'USB Pendrive 64GB', 100008, 'Gyors adatátvitel, kulcstartóra rögzíthető.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(49, 'Gaming Headset', 100009, '7.1 virtuális hangzás, zajszűrő mikrofonnal.', 4, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(50, 'Webkamera Full HD', 100010, '1080p felbontás, autofókusz funkció.', 4, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(51, 'Prémium Egérpad', 100011, 'Csúszásmentes felület gamer használatra.', 4, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(52, 'Type-C Töltőfej 30W', 100012, 'Gyors töltés támogatás USB Power Delivery-vel.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(53, 'Asztali Ventillátor USB', 100013, 'Csendes működés, 3 sebességfokozat.', 5, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(54, 'Okosóra Szíj 22mm', 100014, 'Szilikon szíj különböző órákhoz.', 3, 0, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(55, 'Powerbank 20 000mAh', 100015, 'Gyors töltés, LED töltöttség kijelző.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(56, 'Bluetooth Hangszóró', 100016, 'Vízálló IPX5 minősítés, erős basszus.', 5, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(57, 'Kijelzőtisztító Spray', 100017, 'Antisztatikus tisztítószer mikroszálas kendővel.', 3, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(58, 'USB-C Docking Station', 100018, 'HDMI, USB3, LAN és SD kártya olvasóval.', 2, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(59, 'Vezeték nélküli Töltő', 100019, '10W gyorstöltés Qi szabvány szerint.', 1, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(60, 'Fotós Állvány Mini', 100020, 'Kompakt tripod, 360° fejjel.', 5, 1, '2025-12-05 18:38:27', '2025-12-05 18:38:27'),
(64, 'Trapista sajt', 1768993094, 'CSV Importált', 1, 1, '2026-01-21 11:58:12', '2026-01-22 20:24:21'),
(65, '42', 1768993095, 'CSV Importált', 1, 1, '2026-01-21 11:58:12', '2026-01-21 11:58:12'),
(66, '43', 1768993097, 'CSV Importált', 1, 1, '2026-01-21 11:58:12', '2026-01-21 11:58:12'),
(67, 'Várj egy kicsit', 2147483647, 'Várj, generálok egyetttt, na mit irjak meg ide, NA,  na jojo jolesz, termek mentese, nem befejezes, nyomj egy tabot roland. Igen? pont. termék mentése, nyomd meg a gombot roland, hahahahahahhaahah néázd add ide az egeret', 12, 1, '2026-01-22 20:36:39', '2026-01-22 20:36:39');

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
  `arriveIn` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `transports`
--

INSERT INTO `transports` (`ID`, `batch_id`, `product_ID`, `warehouse_ID`, `type`, `date`, `user_ID`, `description`, `arriveIn`) VALUES
(1, 'TR-1769108616-95A6', 60, 21, 'export', '2026-01-22 20:03:36', 12, 'Kiszállítás cél: Raktár #24. (Ford transit Rendszám: FKI-211 B épület)', '2026-01-23'),
(2, 'TR-1769108616-95A6', 60, 24, 'import', '2026-01-22 20:03:36', 12, 'Beérkezés forrás: Raktár #21. (Ford transit Rendszám: FKI-211 B épület)', '2026-01-23'),
(3, 'TR-1769108616-95A6', 48, 21, 'export', '2026-01-22 20:03:36', 12, 'Kiszállítás cél: Raktár #24. (Ford transit Rendszám: FKI-211 B épület)', '2026-01-23'),
(4, 'TR-1769108616-95A6', 48, 24, 'import', '2026-01-22 20:03:36', 12, 'Beérkezés forrás: Raktár #21. (Ford transit Rendszám: FKI-211 B épület)', '2026-01-23'),
(5, 'TR-1769108794-88FE', 59, 23, 'export', '2026-01-22 20:06:34', 12, 'Kiszállítás cél: Raktár #26. (Nagy János - SJF-834)', '2026-01-24'),
(6, 'TR-1769108794-88FE', 59, 26, 'import', '2026-01-22 20:06:34', 12, 'Beérkezés forrás: Raktár #23. (Nagy János - SJF-834)', '2026-01-24'),
(7, 'TR-1769108794-88FE', 45, 23, 'export', '2026-01-22 20:06:34', 12, 'Kiszállítás cél: Raktár #26. (Nagy János - SJF-834)', '2026-01-24'),
(8, 'TR-1769108794-88FE', 45, 26, 'import', '2026-01-22 20:06:34', 12, 'Beérkezés forrás: Raktár #23. (Nagy János - SJF-834)', '2026-01-24'),
(9, 'TR-1769108932-2160', 58, 24, 'export', '2026-01-22 20:08:52', 12, 'Kiszállítás cél: Raktár #27. (Ford transit Rendszám: ZZA-211 B épület)', '2222-02-22'),
(10, 'TR-1769108932-2160', 58, 27, 'import', '2026-01-22 20:08:52', 12, 'Beérkezés forrás: Raktár #24. (Ford transit Rendszám: ZZA-211 B épület)', '2222-02-22');

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
  `created_at` datetime NOT NULL,
  `login_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`ID`, `username`, `active`, `password`, `role`, `created_at`, `login_at`) VALUES
(5, 'rol', 1, '$2y$10$j9qb4sLStP9kTu8J9gporue3Qp.YWYK5.0.sHVKcBhzBAJC8EFwZa', 'admin', '2026-01-21 15:02:35', '2026-01-22 13:43:04'),
(11, '1', 1, '$2y$10$/2axDqJKNMX9DCt5uSJTleD3PY0s2BP2DU6eMMzKonXDKThXirzHK', 'user', '2026-01-21 15:08:42', '2026-01-22 20:22:32'),
(12, 'Tőkés Roland', 1, '$2y$10$1LLVsSxLFWUnChal0TUg1OsDZi3uhjv3d0H1YYjrdBih2S5OYHPjy', 'owner', '2026-01-22 13:40:39', '2026-01-22 20:23:10');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `warehouses`
--

CREATE TABLE `warehouses` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `max_quantity` int(20) NOT NULL,
  `type` enum('warehouse','store') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `warehouses`
--

INSERT INTO `warehouses` (`ID`, `name`, `address`, `max_quantity`, `type`) VALUES
(21, 'Központi Raktár', 'Budapest, Fő utca 12.', 20000, 'warehouse'),
(22, 'Északi Logisztikai Központ', 'Győr, Ipari park 5.', 50000, 'warehouse'),
(23, 'Déli Regionális Raktár', 'Pécs, Logisztikai út 8.', 50000, 'warehouse'),
(24, 'Keleti Elosztó', 'Debrecen, Külső-Böszörményi út 44.', 100000, 'warehouse'),
(25, 'Nyugati Raktárbázis', 'Szombathely, Bevásárló köz 2.', 54000, 'warehouse'),
(26, 'Mini Depó 1', 'Székesfehérvár, Seregélyesi út 19.', 40000, 'warehouse'),
(27, 'Mini Depó 2', 'Kecskemét, Vágóhíd utca 7.', 5000, 'warehouse'),
(28, 'High-Tech Raktár', 'Budapest, Üllői út 215.', 5660, 'warehouse'),
(29, 'Készletközpont 1', 'Miskolc, Szentpéteri kapu 103.', 60000, 'warehouse'),
(30, 'Készletközpont 2', 'Szeged, Dorozsmai út 33.', 40000, 'warehouse');

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
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT a táblához `inventory`
--
ALTER TABLE `inventory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT a táblához `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT a táblához `transports`
--
ALTER TABLE `transports`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT a táblához `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
