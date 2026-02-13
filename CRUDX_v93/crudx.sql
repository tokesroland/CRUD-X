-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2026. Feb 13. 20:22
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
(21, 'Laptop/PC'),
(22, 'Új Technológia'),
(23, 'Egyéb'),
(30, 'Egyéb elektronika'),
(31, 'Okosotthon'),
(32, 'Kiegészítők');

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
(535, 467, 21, 0, 0, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(536, 468, 22, 78, 0, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(537, 469, 23, 12, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(538, 470, 24, 8, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(539, 471, 25, 120, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(540, 472, 26, 200, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(541, 473, 27, 55, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(542, 474, 28, 32, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(543, 475, 29, 18, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(544, 476, 30, 24, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(545, 477, 31, 40, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(546, 478, 21, 36, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(547, 479, 22, 29, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(548, 480, 23, 15, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(549, 481, 24, 85, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(550, 482, 25, 135, 0, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(551, 483, 26, 7, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(552, 484, 27, 42, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(553, 485, 28, 95, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(554, 486, 29, 60, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(555, 487, 30, 33, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(556, 488, 31, 27, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(557, 489, 21, 22, 30, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(558, 490, 22, 48, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(559, 491, 23, 110, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(560, 492, 24, 64, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(561, 493, 25, 92, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(562, 494, 26, 38, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(563, 495, 27, 19, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(564, 496, 28, 53, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(565, 497, 29, 9, NULL, '2026-02-13 19:14:54', NULL),
(566, 498, 30, 14, NULL, '2026-02-13 19:14:54', NULL),
(567, 499, 31, 6, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(568, 500, 21, 35, NULL, '2026-02-13 19:14:54', '2026-02-13 19:58:34'),
(569, 501, 22, 40, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(570, 502, 23, 31, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(571, 503, 24, 23, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(572, 504, 25, 200, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(573, 505, 26, 80, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(574, 506, 27, 65, NULL, '2026-02-13 19:14:54', '2026-02-13 19:55:39'),
(575, 507, 28, 47, 0, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(576, 508, 29, 22, NULL, '2026-02-13 19:14:54', NULL),
(577, 509, 30, 34, NULL, '2026-02-13 19:14:54', NULL),
(578, 510, 31, 16, NULL, '2026-02-13 19:14:54', NULL),
(579, 511, 21, 11, NULL, '2026-02-13 19:14:54', NULL),
(580, 512, 22, 9, NULL, '2026-02-13 19:14:54', NULL),
(581, 513, 23, 88, 55, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(582, 514, 24, 115, NULL, '2026-02-13 19:14:54', NULL),
(583, 515, 25, 41, NULL, '2026-02-13 19:14:54', NULL),
(584, 516, 26, 12, NULL, '2026-02-13 19:14:54', NULL),
(585, 517, 27, 63, NULL, '2026-02-13 19:14:54', NULL),
(586, 518, 28, 5, NULL, '2026-02-13 19:14:54', NULL),
(587, 519, 29, 17, NULL, '2026-02-13 19:14:54', NULL),
(588, 520, 30, 79, NULL, '2026-02-13 19:14:54', NULL),
(589, 521, 31, 62, NULL, '2026-02-13 19:14:54', NULL),
(590, 522, 21, 43, NULL, '2026-02-13 19:14:54', NULL),
(591, 523, 22, 37, NULL, '2026-02-13 19:14:54', NULL),
(592, 524, 23, 25, NULL, '2026-02-13 19:14:54', NULL),
(593, 525, 24, 33, NULL, '2026-02-13 19:14:54', NULL),
(594, 526, 25, 18, NULL, '2026-02-13 19:14:54', NULL),
(595, 527, 26, 20, NULL, '2026-02-13 19:14:54', NULL),
(596, 528, 27, 112, NULL, '2026-02-13 19:14:54', NULL),
(597, 529, 28, 70, NULL, '2026-02-13 19:14:54', NULL),
(598, 530, 29, 44, NULL, '2026-02-13 19:14:54', NULL),
(599, 531, 30, 58, NULL, '2026-02-13 19:14:54', NULL),
(600, 532, 31, 36, NULL, '2026-02-13 19:14:54', NULL),
(601, 533, 21, 19, NULL, '2026-02-13 19:14:54', '2026-02-13 19:58:34'),
(602, 534, 22, 83, NULL, '2026-02-13 19:14:54', NULL),
(603, 535, 23, 51, NULL, '2026-02-13 19:14:54', NULL),
(604, 536, 24, 97, 0, '2026-02-13 19:14:54', '2026-02-13 19:59:59'),
(605, 537, 25, 48, NULL, '2026-02-13 19:14:54', NULL),
(606, 538, 26, 61, NULL, '2026-02-13 19:14:54', NULL),
(607, 539, 27, 104, NULL, '2026-02-13 19:14:54', NULL),
(608, 540, 28, 14, NULL, '2026-02-13 19:14:54', NULL),
(609, 541, 29, 92, NULL, '2026-02-13 19:14:54', NULL),
(610, 542, 30, 66, NULL, '2026-02-13 19:14:55', NULL),
(611, 543, 31, 19, NULL, '2026-02-13 19:14:55', NULL),
(612, 544, 21, 39, 10, '2026-02-13 19:14:55', '2026-02-13 19:59:59'),
(613, 545, 22, 54, NULL, '2026-02-13 19:14:55', NULL),
(614, 546, 23, 35, NULL, '2026-02-13 19:14:55', NULL),
(615, 547, 24, 10, NULL, '2026-02-13 19:14:55', NULL),
(616, 548, 25, 42, NULL, '2026-02-13 19:14:55', NULL),
(617, 549, 26, 3, NULL, '2026-02-13 19:14:55', NULL),
(618, 550, 27, 13, NULL, '2026-02-13 19:14:55', NULL),
(619, 551, 28, 47, NULL, '2026-02-13 19:14:55', NULL),
(620, 552, 29, 38, NULL, '2026-02-13 19:14:55', NULL),
(621, 553, 30, 16, NULL, '2026-02-13 19:14:55', NULL),
(622, 554, 31, 28, NULL, '2026-02-13 19:14:55', NULL),
(623, 555, 21, 9, NULL, '2026-02-13 19:14:55', '2026-02-13 19:58:34'),
(624, 556, 22, 26, NULL, '2026-02-13 19:14:55', NULL),
(625, 557, 23, 8, NULL, '2026-02-13 19:14:55', NULL),
(626, 558, 24, 11, NULL, '2026-02-13 19:14:55', NULL),
(627, 559, 25, 33, NULL, '2026-02-13 19:14:55', NULL),
(628, 560, 26, 59, NULL, '2026-02-13 19:14:55', NULL),
(629, 561, 27, 17, NULL, '2026-02-13 19:14:55', '2026-02-13 19:15:58'),
(630, 562, 28, 49, NULL, '2026-02-13 19:14:55', NULL),
(631, 563, 29, 130, NULL, '2026-02-13 19:14:55', NULL),
(632, 564, 30, 22, NULL, '2026-02-13 19:14:55', NULL),
(633, 565, 31, 56, NULL, '2026-02-13 19:14:55', NULL),
(634, 566, 21, 0, NULL, '2026-02-13 19:14:55', '2026-02-13 19:58:34'),
(635, 567, 22, 143, 0, '2026-02-13 19:14:55', '2026-02-13 19:59:59'),
(636, 568, 23, 37, NULL, '2026-02-13 19:14:55', NULL),
(637, 569, 24, 44, NULL, '2026-02-13 19:14:55', NULL),
(638, 570, 25, 89, NULL, '2026-02-13 19:14:55', NULL),
(639, 571, 26, 12, NULL, '2026-02-13 19:14:55', NULL),
(640, 572, 27, 71, NULL, '2026-02-13 19:14:55', NULL),
(641, 573, 28, 55, NULL, '2026-02-13 19:14:55', NULL),
(642, 574, 29, 14, NULL, '2026-02-13 19:14:55', NULL),
(643, 575, 30, 31, 200, '2026-02-13 19:14:55', '2026-02-13 19:59:59'),
(644, 576, 31, 41, NULL, '2026-02-13 19:14:55', NULL),
(645, 577, 21, 29, NULL, '2026-02-13 19:14:55', NULL),
(646, 578, 22, 7, NULL, '2026-02-13 19:14:55', NULL),
(647, 579, 23, 34, NULL, '2026-02-13 19:14:55', NULL),
(648, 580, 24, 6, NULL, '2026-02-13 19:14:55', NULL),
(649, 581, 25, 10, NULL, '2026-02-13 19:14:55', NULL),
(650, 582, 26, 68, NULL, '2026-02-13 19:14:55', NULL),
(651, 583, 27, 46, NULL, '2026-02-13 19:14:55', NULL),
(652, 584, 28, 23, NULL, '2026-02-13 19:14:55', NULL),
(653, 585, 29, 32, NULL, '2026-02-13 19:14:55', NULL),
(654, 586, 30, 17, NULL, '2026-02-13 19:14:55', NULL),
(655, 587, 31, 24, NULL, '2026-02-13 19:14:55', NULL),
(656, 588, 21, 9, NULL, '2026-02-13 19:14:55', NULL),
(657, 589, 22, 18, 0, '2026-02-13 19:14:55', '2026-02-13 19:59:59'),
(658, 590, 23, 15, NULL, '2026-02-13 19:14:55', NULL),
(659, 591, 24, 94, NULL, '2026-02-13 19:14:55', NULL),
(660, 592, 25, 73, NULL, '2026-02-13 19:14:55', NULL),
(661, 593, 26, 20, NULL, '2026-02-13 19:14:55', NULL),
(662, 594, 27, 107, NULL, '2026-02-13 19:14:55', NULL),
(663, 595, 28, 26, NULL, '2026-02-13 19:14:55', NULL),
(664, 596, 29, 40, NULL, '2026-02-13 19:14:55', NULL),
(665, 597, 30, 35, NULL, '2026-02-13 19:14:55', NULL),
(666, 598, 31, 77, 0, '2026-02-13 19:14:55', '2026-02-13 19:59:59'),
(667, 599, 21, 0, NULL, '2026-02-13 19:14:55', '2026-02-13 19:58:34'),
(668, 600, 22, 51, NULL, '2026-02-13 19:14:55', NULL),
(669, 467, 22, 30, 20, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(670, 467, 23, 52, 20, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(671, 468, 24, 43, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(672, 468, 26, 65, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(673, 469, 25, 8, NULL, '2026-02-13 19:55:39', NULL),
(674, 469, 28, 5, NULL, '2026-02-13 19:55:39', NULL),
(675, 470, 26, 6, NULL, '2026-02-13 19:55:39', NULL),
(676, 470, 29, 4, NULL, '2026-02-13 19:55:39', NULL),
(677, 471, 27, 85, NULL, '2026-02-13 19:55:39', NULL),
(678, 471, 30, 94, NULL, '2026-02-13 19:55:39', NULL),
(679, 471, 31, 67, NULL, '2026-02-13 19:55:39', NULL),
(680, 472, 22, 150, NULL, '2026-02-13 19:55:39', NULL),
(681, 472, 24, 180, NULL, '2026-02-13 19:55:39', NULL),
(682, 472, 28, 165, NULL, '2026-02-13 19:55:39', NULL),
(683, 473, 23, 62, NULL, '2026-02-13 19:55:39', NULL),
(684, 473, 29, 48, NULL, '2026-02-13 19:55:39', NULL),
(685, 474, 25, 28, NULL, '2026-02-13 19:55:39', NULL),
(686, 474, 30, 36, NULL, '2026-02-13 19:55:39', NULL),
(687, 475, 22, 15, NULL, '2026-02-13 19:55:39', NULL),
(688, 475, 26, 22, NULL, '2026-02-13 19:55:39', NULL),
(689, 476, 24, 31, NULL, '2026-02-13 19:55:39', NULL),
(690, 476, 27, 19, NULL, '2026-02-13 19:55:39', NULL),
(691, 476, 31, 27, NULL, '2026-02-13 19:55:39', NULL),
(692, 477, 23, 35, NULL, '2026-02-13 19:55:39', NULL),
(693, 477, 25, 44, NULL, '2026-02-13 19:55:39', NULL),
(694, 477, 28, 38, NULL, '2026-02-13 19:55:39', NULL),
(695, 478, 24, 42, NULL, '2026-02-13 19:55:39', NULL),
(696, 478, 27, 31, NULL, '2026-02-13 19:55:39', NULL),
(697, 479, 26, 33, NULL, '2026-02-13 19:55:39', NULL),
(698, 479, 29, 25, NULL, '2026-02-13 19:55:39', NULL),
(699, 479, 31, 38, NULL, '2026-02-13 19:55:39', NULL),
(700, 480, 25, 18, NULL, '2026-02-13 19:55:39', NULL),
(701, 480, 28, 22, NULL, '2026-02-13 19:55:39', NULL),
(702, 480, 30, 14, NULL, '2026-02-13 19:55:39', NULL),
(703, 481, 21, 72, NULL, '2026-02-13 19:55:39', NULL),
(704, 481, 27, 93, NULL, '2026-02-13 19:55:39', NULL),
(705, 481, 29, 77, NULL, '2026-02-13 19:55:39', NULL),
(706, 482, 22, 150, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(707, 482, 28, 165, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(708, 482, 30, 142, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(709, 483, 23, 5, NULL, '2026-02-13 19:55:39', NULL),
(710, 483, 29, 9, NULL, '2026-02-13 19:55:39', NULL),
(711, 483, 31, 6, NULL, '2026-02-13 19:55:39', NULL),
(712, 484, 22, 38, NULL, '2026-02-13 19:55:39', NULL),
(713, 484, 25, 46, NULL, '2026-02-13 19:55:39', NULL),
(714, 484, 30, 35, NULL, '2026-02-13 19:55:39', NULL),
(715, 485, 24, 82, NULL, '2026-02-13 19:55:39', NULL),
(716, 485, 26, 104, NULL, '2026-02-13 19:55:39', NULL),
(717, 485, 31, 77, NULL, '2026-02-13 19:55:39', NULL),
(718, 486, 23, 53, NULL, '2026-02-13 19:55:39', NULL),
(719, 486, 27, 68, NULL, '2026-02-13 19:55:39', NULL),
(720, 487, 22, 41, NULL, '2026-02-13 19:55:39', NULL),
(721, 487, 25, 29, NULL, '2026-02-13 19:55:39', NULL),
(722, 487, 28, 37, NULL, '2026-02-13 19:55:39', NULL),
(723, 488, 24, 34, NULL, '2026-02-13 19:55:39', NULL),
(724, 488, 26, 42, NULL, '2026-02-13 19:55:39', NULL),
(725, 488, 29, 31, NULL, '2026-02-13 19:55:39', NULL),
(726, 489, 23, 27, 40, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(727, 489, 27, 19, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(728, 489, 30, 24, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59'),
(729, 490, 25, 53, NULL, '2026-02-13 19:55:39', NULL),
(730, 490, 28, 44, NULL, '2026-02-13 19:55:39', NULL),
(731, 490, 31, 56, NULL, '2026-02-13 19:55:39', NULL),
(732, 491, 21, 35, NULL, '2026-02-13 19:55:39', '2026-02-13 19:58:34'),
(733, 491, 26, 120, NULL, '2026-02-13 19:55:39', NULL),
(734, 491, 29, 102, NULL, '2026-02-13 19:55:39', NULL),
(735, 492, 22, 58, NULL, '2026-02-13 19:55:39', NULL),
(736, 492, 27, 71, NULL, '2026-02-13 19:55:39', NULL),
(737, 492, 30, 67, NULL, '2026-02-13 19:55:39', NULL),
(738, 493, 23, 85, NULL, '2026-02-13 19:55:39', NULL),
(739, 493, 28, 98, NULL, '2026-02-13 19:55:39', NULL),
(740, 493, 31, 79, NULL, '2026-02-13 19:55:39', NULL),
(741, 494, 22, 44, NULL, '2026-02-13 19:55:39', NULL),
(742, 494, 29, 42, NULL, '2026-02-13 19:55:39', NULL),
(743, 494, 31, 35, NULL, '2026-02-13 19:55:39', NULL),
(744, 495, 23, 23, NULL, '2026-02-13 19:55:39', NULL),
(745, 495, 25, 16, NULL, '2026-02-13 19:55:39', NULL),
(746, 495, 30, 21, NULL, '2026-02-13 19:55:39', NULL),
(747, 496, 22, 48, NULL, '2026-02-13 19:55:39', NULL),
(748, 496, 26, 61, NULL, '2026-02-13 19:55:39', NULL),
(749, 496, 29, 55, NULL, '2026-02-13 19:55:39', NULL),
(750, 497, 23, 9, NULL, '2026-02-13 19:55:39', NULL),
(751, 497, 25, 6, NULL, '2026-02-13 19:55:39', NULL),
(752, 497, 28, 4, NULL, '2026-02-13 19:55:39', NULL),
(753, 497, 30, 7, NULL, '2026-02-13 19:55:39', NULL),
(754, 498, 26, 14, NULL, '2026-02-13 19:55:39', NULL),
(755, 498, 22, 12, NULL, '2026-02-13 19:55:39', NULL),
(756, 498, 29, 16, NULL, '2026-02-13 19:55:39', NULL),
(757, 498, 31, 10, NULL, '2026-02-13 19:55:39', NULL),
(758, 499, 24, 4, NULL, '2026-02-13 19:55:39', NULL),
(759, 499, 27, 5, NULL, '2026-02-13 19:55:39', NULL),
(760, 499, 29, 3, NULL, '2026-02-13 19:55:39', NULL),
(761, 500, 23, 68, NULL, '2026-02-13 19:55:39', NULL),
(762, 500, 26, 82, NULL, '2026-02-13 19:55:39', NULL),
(763, 500, 30, 71, NULL, '2026-02-13 19:55:39', NULL),
(764, 501, 25, 35, NULL, '2026-02-13 19:55:39', NULL),
(765, 501, 28, 44, NULL, '2026-02-13 19:55:39', NULL),
(766, 501, 31, 38, NULL, '2026-02-13 19:55:39', NULL),
(767, 502, 26, 27, NULL, '2026-02-13 19:55:39', NULL),
(768, 502, 29, 35, NULL, '2026-02-13 19:55:39', NULL),
(769, 503, 22, 28, NULL, '2026-02-13 19:55:39', NULL),
(770, 503, 27, 20, NULL, '2026-02-13 19:55:39', NULL),
(771, 503, 30, 25, NULL, '2026-02-13 19:55:39', NULL),
(772, 504, 21, 185, NULL, '2026-02-13 19:55:39', NULL),
(773, 504, 28, 220, NULL, '2026-02-13 19:55:39', NULL),
(774, 504, 31, 195, NULL, '2026-02-13 19:55:39', NULL),
(775, 505, 23, 72, NULL, '2026-02-13 19:55:39', NULL),
(776, 505, 29, 88, NULL, '2026-02-13 19:55:39', NULL),
(777, 506, 24, 59, NULL, '2026-02-13 19:55:39', NULL),
(778, 506, 30, 71, NULL, '2026-02-13 19:55:39', NULL),
(779, 507, 22, 47, 0, '2026-02-13 19:55:39', '2026-02-13 19:59:59');

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
(467, 'USB Pendrive 64GB', '1771006496', 'Gyors adatátvitelre alkalmas USB 3.0 pendrive, 64GB tárolókapacitással. Fekete színű, hordozható kialakítás.', 6, 0, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(468, 'USB-C töltő 30W', '1771006497', '30W-os USB-C gyorstöltő, kompatibilis telefonokkal és tabletekkel. Túlfeszültség védelemmel ellátva.', 13, 1, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(469, 'Gamer Laptop 15.6\"', '1771006498', 'Nagy teljesítményű gamer laptop, 16GB RAM, RTX 4060 videókártya, 1TB SSD. RGB háttérvilágítású billentyűzet.', 21, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(470, 'LED FULLHD TV 43\"', '1771006499', '43 hüvelykes LED TV, FULLHD felbontás, beépített hangszórókkal, 3 HDMI port, okos funkciók.', 10, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(471, 'Vezeték nélküli egér', '1771006500', 'Kompakt méretű vezeték nélküli egér, 2.4GHz kapcsolat, csendes kattintás, alkalmas irodai használatra.', 2, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(472, 'HDMI kábel 2m', '1771006501', '2 méter hosszú HDMI kábel, 4K támogatással, aranyozott csatlakozókkal a jobb jelátvitelért.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(473, 'Powerbank 10000mAh', '1771006502', '10000mAh kapacitású powerbank, 2 USB porttal, gyorstöltés támogatás. Fehér színű, kompakt méretű.', 3, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(474, 'Bluetooth hangszóró', '1771006503', 'Hordozható Bluetooth hangszóró, vízálló kivitel, 10 óra üzemidő, beépített mikrofonnal.', 9, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(475, 'Irodai szék hálós', '1771006504', 'Ergonomikus irodai szék hálós háttámlával, állítható magassággal és kartámasszal. Légáteresztő anyag.', 8, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(476, 'Router Wi-Fi 6', '1771006505', 'Wi-Fi 6 router, dual-band kapcsolat, 4 antennával, szülői felügyelettel és vendéghálózattal.', 7, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(477, 'SSD 1TB', '1771006506', '1TB-os SATA SSD, olvasási sebesség 550MB/s, írási sebesség 500MB/s. Ideális rendszerek és játékok számára.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(478, 'Gaming headset', '1771006507', '7.1 virtuális hangzással ellátott gaming headset, zajszűrő mikrofonnal, állítható fejpánttal.', 4, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(479, 'Webkamera Full HD', '1771006508', '1080p felbontású webkamera, beépített mikrofonnal, automatikus fényerő állítással, széles látószögű lencse.', 2, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(480, 'Mechanikus billentyűzet', '1771006509', 'Mechanikus billentyűzet kék kapcsolókkal, RGB háttérvilágítás, kompakt kialakítás, alumínium ház.', 4, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(481, 'HDMI kábel 5m', '1771006510', '5 méter hosszú HDMI kábel, Ethernet csatornával, 3D és 4K támogatás. Fekete színű.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(482, 'USB-C kábel 1m', '1771006511', '1 méter hosszú USB-C kábel, gyorstöltésre alkalmas, adatátviteli sebesség 480Mbps. Szövött anyagú.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(483, 'Monitor 27 4K', '1771006512', '27 hüvelykes 4K monitor, IPS panel, 60Hz frissítés, HDR támogatás, ergonomikus állvánnyal.', 14, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(484, 'Laptop állvány alumínium', '1771006513', 'Állítható magasságú alumínium laptop állvány, hordozható kialakítás, javítja a hűtést és ergonómiát.', 12, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(485, 'Mobiltelefon tok iPhone', '1771006514', 'Szilikon mobiltelefon tok iPhone készülékekhez, precíz kivágásokkal, ütéselnyelő technológiával.', 3, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(486, 'Okosóra szíj 22mm', '1771006515', 'Cserélhető szilikon óraszíj 22mm-es csathoz, több színben kapható, kényelmes viselet.', 3, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(487, 'Asztali ventilátor', '1771006516', 'Kompakt asztali ventilátor, 3 sebességfokozat, állítható dőlésszög, halk működés, USB-s tápellátás.', 5, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(488, 'Kijelzőtisztító készlet', '1771006517', 'Antisztatikus kijelzőtisztító spray + mikroszálas kendő, alkalmas monitorok, telefonok és szemüvegek tisztítására.', 3, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(489, 'Dokkoló állomás USB-C', '1771006518', 'USB-C dokkoló állomás HDMI, VGA, 3x USB 3.0, Ethernet és SD kártya olvasó portokkal. Tápellátással.', 12, 1, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(490, 'Vezeték nélküli töltő', '1771006519', 'Qi szabványú vezeték nélküli töltő, 10W teljesítménnyel, túltöltés védelemmel, csúszásmentes felülettel.', 13, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(491, 'Mini állvány telefonhoz', '1771006520', 'Kompakt mini állvány telefonokhoz és kamerákhoz, állítható lábakkal, gumírozott felülettel a jobb rögzítésért.', 15, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(492, 'Gaming egér RGB', '1771006521', 'RGB világítással ellátott gaming egér, 6 programozható gomb, 6400 DPI érzékenység, kényelmes markolat.', 4, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(493, 'Töltőfej 20W', '1771006522', '20W-os USB-C töltőfej, Power Delivery támogatással, kompatibilis iPhone és Android készülékekkel.', 13, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(494, 'Pendrive 128GB', '1771006523', '128GB kapacitású USB 3.2 pendrive, kompakt kialakítás, fém ház, védelem a fizikai sérülések ellen.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(495, 'Powerbank 20000mAh', '1771006524', '20000mAh kapacitású powerbank, 2 USB és 1 USB-C porttal, gyorstöltés, LED kijelző a töltöttség jelzésére.', 3, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(496, 'Bluetooth fülhallgató', '1771006525', 'Vezeték nélküli Bluetooth fülhallgató, töltőtokkal, 20 óra üzemidő, érintővezérlés, mély basszus.', 9, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(497, 'Gamer laptop 17\"', '1771006526', '17 hüvelykes gamer laptop, 32GB RAM, RTX 4080, 2TB SSD, 240Hz kijelző, mechanikus billentyűzet.', 21, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(498, '4K akció kamera', '1771006527', '4K felbontású akciókamera, vízálló tok, elektronikus képstabilizátor, Wi-Fi kapcsolat, széles látószögű lencse.', 15, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(499, 'NAS 2 bay', '1771006528', '2 fiókos NAS hálózati tároló, RAID támogatás, médiaszerver funkció, alacsony fogyasztás, otthoni és irodai használatra.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(500, 'Okos konnektor', '1771006529', 'Wi-Fi-n keresztül vezérelhető okos konnektor, energiafogyasztás mérés, hangvezérlés támogatás, időzíthető.', 17, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(501, 'LED fény szalag 5m', '1771006530', '5 méter hosszú LED szalag, RGB színek, távvezérlővel, ragasztós hátoldal, alkalmas TV-k és bútorok mögé.', 5, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(502, 'Hálózati switch 8 port', '1771006531', '8 portos gigabit switch, fém ház, Plug-and-Play, energiatakarékos, ideális otthoni és kis irodai hálózatokhoz.', 7, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(503, 'IP kamera beltéri', '1771006532', 'Beltéri IP kamera, 1080p felbontás, éjjellátó, kétirányú hang, mozgásérzékelés, microSD kártya támogatás.', 18, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(504, 'Irodai papír A4 500 lap', '1771006533', 'A4-es irodai papír, 80g/m², 500 lapos csomagolás, alkalmas nyomtatókhoz és fénymásolókhoz.', 8, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(505, 'CD írható 50x', '1771006534', '50 db-os írható CD, 700MB kapacitás, sebesség 52x, alkalmas adatok és zene tárolására.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(506, 'DVD írható 25x', '1771006535', '25 db-os írható DVD, 4.7GB kapacitás, sebesség 16x, alkalmas videók és adatok archiválására.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(507, 'Bluetooth adapter USB', '1771006536', 'Kompakt méretű Bluetooth 5.0 adapter USB-hez, Windows kompatibilis, 20 méteres hatótáv, vezeték nélküli eszközök csatlakoztatásához.', 2, 1, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(508, 'Külső merevlemez 2TB', '1771006537', '2TB kapacitású külső merevlemez, USB 3.0 csatlakozás, alumínium ház, automatikus biztonsági mentés szoftverrel.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(509, 'Mikrofon állvány asztali', '1771006538', 'Állítható asztali mikrofon állvány, stabil talppal, univerzális menettel, alkalmas podcast és streaming felvételekhez.', 9, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(510, 'Monitor kar', '1771006539', 'Monitor kar állítható magassággal és dőléssel, 2 db monitor rögzítésére alkalmas, akár 27 hüvelykes méretig, asztalra szerelhető.', 14, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(511, 'Gamer szék', '1771006540', 'Ergonomikus gamer szék, magas háttámla, állítható kartámasz, dönthető ülőfelület, bőr hatású anyag, piros-fekete szín.', 4, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(512, 'Okos zár', '1771006541', 'Okos zár ajtókhoz, Bluetooth kapcsolat, ujjlenyomat olvasó, PIN kód, távvezérlés okostelefonról, könnyű telepítés.', 17, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(513, 'USB hub 4 port', '1771006542', '4 portos USB 3.0 hub, egyedi kapcsolókkal, kompakt méret, ideális laptopokhoz és asztali gépekhez.', 2, 1, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(514, 'Jack kábel 3.5mm', '1771006543', '3.5mm-es jack kábel, sztereó hangátvitel, 1.5 méter hosszú, aranyozott csatlakozók, alkalmas hangszórók és fejhallgatók csatlakoztatására.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(515, 'Tablet tok 10', '1771006544', 'Univerzális tablet tok 10 hüvelykes készülékekhez, állítható támasztó funkcióval, szilikon anyag, több színben kapható.', 16, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(516, 'Okos kapucsengő', '1771006545', 'Wi-Fi kapcsolattal ellátott okos kapucsengő, 1080p kamera, éjjellátó, kétirányú hang, mozgásérzékelés, alkalmazás értesítések.', 18, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(517, 'UTP kábel Cat6 10m', '1771006546', '10 méter hosszú UTP kábel Cat6 szabvány szerint, 1Gbps sebesség, kék színű, pattintott csatlakozókkal.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(518, 'Projektor vászon', '1771006547', 'Falra szerelhető projektor vászon, 180 cm széles, manuális lehúzható, fekete kerettel, tökéletes vetítési felület.', 8, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(519, 'Szünetmentes táp 600VA', '1771006548', '600VA teljesítményű szünetmentes tápegység, 4 kimenettel, túlfeszültség védelem, automatikus feszültségszabályozás, ideális otthoni irodába.', 20, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(520, 'Vezeték nélküli egér kompakt', '1771006549', 'Kompakt vezeték nélküli egér, nano vevőegységgel, 3 gombos, alkalmas utazáshoz és irodai használatra, több színben.', 2, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(521, 'Earbuds fülhallgató', '1771006550', 'Vezeték nélküli earbuds, töltőtokkal, Bluetooth 5.3, 25 óra üzemidő, IPX4 vízállóság, érintővezérlés.', 9, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(522, 'Gamer fejhallgató RGB', '1771006551', 'Gamer fejhallgató RGB világítással, 7.1 virtuális hang, állítható mikrofon, kényelmes fülpárnák, USB kapcsolat.', 4, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(523, 'Wi-Fi jelismétlő', '1771006552', 'Wi-Fi jelismétlő, dual-band, egyszerű beállítás, LED kijelző, Ethernet porttal, növeli a hálózati lefedettséget.', 7, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(524, 'Külső SSD 500GB', '1771006553', '500GB kapacitású külső SSD, USB-C csatlakozás, olvasási sebesség 1000MB/s, alumínium ház, kompakt méret.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(525, 'Mikrofon állvány gamer', '1771006554', 'Gamer mikrofon állvány, rugalmas karral, asztalra rögzíthető, univerzális menettel, stabil és állítható.', 9, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(526, 'IP kamera kültéri', '1771006555', 'Kültéri IP kamera, 1080p felbontás, éjjellátó 20 méterig, PIR mozgásérzékelő, időjárásálló tok, microSD támogatás.', 18, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(527, 'Dokkoló laptopokhoz', '1771006556', 'Univerzális dokkoló laptopokhoz, HDMI, VGA, USB 3.0, Ethernet portokkal, támogatja a tápellátást és a többmonitoros megjelenítést.', 12, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(528, 'Pendrive 32GB', '1771006557', '32GB kapacitású pendrive, USB 2.0, kompakt méret, kulcstartóval, alkalmas adatok tárolására és átvitelére.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(529, 'Hálózati elosztó 5ös', '1771006558', '5 aljzatos hálózati elosztó, 1.5 méter kábellel, túlfeszültség védelem, gyerekbiztos érintésvédelem, kapcsolóval.', 20, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(530, 'Sűrített levegő spray', '1771006559', '400ml-es sűrített levegő spray, elektronikai eszközök tisztításához, széles csővel, hatékonyan távolítja el a port.', 20, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(531, 'SSD 250GB', '1771006560', '250GB kapacitású SATA SSD, olvasási sebesség 550MB/s, ideális operációs rendszerekhez és gyakran használt programokhoz.', 6, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(532, 'HDMI kábel 10m', '1771006561', '10 méter hosszú HDMI kábel, 4K támogatás, Ethernet funkcióval, aranyozott csatlakozók, vastag szigetelés.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(533, 'Laptop hűtőpad', '1771006562', 'Laptop hűtőpad, 2 db ventilátorral, USB tápellátás, állítható magasság, halk működés, 17 hüvelykes laptopokhoz is.', 12, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(534, 'VGA kábel 1.8m', '1771006563', '1.8 méter hosszú VGA kábel, 15 tűs csatlakozó, alkalmas monitorok és projektorok csatlakoztatásához, fekete színű.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(535, 'Gamer egér vezetékes', '1771006564', 'Vezetékes gaming egér, RGB világítással, 5 programozható gomb, 7200 DPI, szövött kábel, kényelmes markolat.', 4, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(536, 'USB-C töltőkábel 2m', '1771006565', '2 méter hosszú USB-C töltőkábel, gyorstöltésre alkalmas, szövött anyagú, tartós kivitel, kompatibilis telefonokkal és tabletekkel.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:55:05'),
(537, 'DisplayPort kábel 2m', '1771006566', '2 méter hosszú DisplayPort kábel, 4K támogatás, aranyozott csatlakozók, alkalmas monitorok és grafikus kártyák csatlakoztatásához.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(538, 'Okos izzó E27', '1771006567', 'E27 foglalatú okos izzó, 800 lumen, RGB színek, Wi-Fi vezérlés, hangvezérlés támogatás, időzíthető, alkalmazásból vezérelhető.', 17, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(539, 'Hálózati kábel Cat5e 5m', '1771006568', '5 méter hosszú Cat5e hálózati kábel, 100Mbps sebesség, szürke színű, pattintott csatlakozókkal.', 1, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(540, 'Irodai szék műbőr', '1771006569', 'Irodai szék műbőr bevonattal, állítható magassággal, dönthető háttámlával, kényelmes párnázás, görgős talppal.', 8, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(541, 'AAA elem 4db', '1771006570', '4 db AAA elem, tartós alkáli, hosszú élettartam, alkalmas távirányítókhoz, játékokhoz és egyéb eszközökhöz.', 20, 1, '2026-02-13 19:14:54', '2026-02-13 19:14:54'),
(542, 'Fülhallgató vezetékes', '1771006572', 'Vezetékes fülhallgató, 3.5mm jack csatlakozó, mély basszus, beépített mikrofon és hívásfogadó gomb, ergonomikus kialakítás.', 9, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(543, 'Gamer fejhallgató vezeték nélküli', '1771006573', 'Vezeték nélküli gamer fejhallgató, 2.4GHz kapcsolat, 20 óra üzemidő, 7.1 hangzás, állítható mikrofon, RGB világítás.', 4, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(544, 'USB hub 7 port', '1771006574', '7 portos USB 3.0 hub, külső tápellátással, egyedi kapcsolók, ideális több eszköz egyidejű csatlakoztatásához.', 2, 0, '2026-02-13 19:14:55', '2026-02-13 19:55:05'),
(545, 'Jack kábel 5m', '1771006575', '5 méter hosszú jack kábel, 3.5mm csatlakozó, sztereó hangátvitel, alkalmas hangszórók és erősítők összekötésére.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(546, 'Tablet tok 8', '1771006576', 'Univerzális tablet tok 8 hüvelykes készülékekhez, szilikon anyag, állítható támasztó funkció, több színben kapható.', 16, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(547, 'Okos kapucsengő akkumulátoros', '1771006577', 'Akkumulátoros okos kapucsengő, Wi-Fi kapcsolat, 1080p kamera, éjjellátó, mozgásérzékelés, alkalmazás értesítések, egyszerű telepítés.', 18, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(548, 'UTP kábel Cat6 20m', '1771006578', '20 méter hosszú UTP kábel Cat6 szabvány szerint, 1Gbps sebesség, szürke színű, pattintott csatlakozókkal.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(549, 'Projektor vászon elektromos', '1771006579', 'Elektromos projektor vászon, távvezérlővel, 200 cm széles, falra vagy mennyezetre szerelhető, ideális moziélményhez.', 8, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(550, 'Szünetmentes táp 1000VA', '1771006580', '1000VA teljesítményű szünetmentes tápegység, 6 kimenettel, túlfeszültség védelem, automatikus feszültségszabályozás, ideális szerverekhez.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(551, 'Vezeték nélküli egér nagy méretű', '1771006581', 'Nagy méretű vezeték nélküli egér, ergonomikus kialakítás, 5 gombos, alkalmas irodai használatra, hosszú akkumulátor élettartam.', 2, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(552, 'Earbuds sport', '1771006582', 'Vezeték nélküli sport earbuds, fülre akasztható kialakítás, IPX7 vízállóság, Bluetooth 5.2, 20 óra üzemidő, mély basszus.', 9, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(553, 'Gamer fejhallgató PS5', '1771006583', 'Gamer fejhallgató PlayStation 5 konzolokhoz, 3D hangzás, vezeték nélküli kapcsolat, zajszűrő mikrofon, kényelmes fülpárnák.', 4, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(554, 'Wi-Fi jelismétlő külső antennával', '1771006584', 'Wi-Fi jelismétlő 2 külső antennával, dual-band, gigabit Ethernet port, egyszerű beállítás, növeli a hálózati lefedettséget.', 7, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(555, 'Külső SSD 1TB', '1771006585', '1TB kapacitású külső SSD, USB-C csatlakozás, olvasási sebesség 1000MB/s, alumínium ház, kompakt méret, gyors adatátvitel.', 6, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(556, 'Mikrofon állvány stúdió', '1771006586', 'Stúdió minőségű mikrofon állvány, nehéz talppal, állítható magasság, univerzális menettel, alkalmas podcast és zenei felvételekhez.', 9, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(557, 'IP kamera PTZ', '1771006587', 'PTZ IP kamera, 1080p felbontás, 360° forgatható, éjjellátó, mozgáskövetés, kétirányú hang, microSD támogatás, PoE tápellátás.', 18, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(558, 'Dokkoló Thunderbolt', '1771006588', 'Thunderbolt dokkoló, 40Gbps sebesség, HDMI, DisplayPort, USB-C, Ethernet portokkal, támogatja a többmonitoros megjelenítést.', 12, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(559, 'Pendrive 256GB', '1771006589', '256GB kapacitású USB 3.2 pendrive, fém ház, kompakt méret, védelem a fizikai sérülések ellen, gyors adatátvitel.', 6, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(560, 'Hálózati elosztó 8as', '1771006590', '8 aljzatos hálózati elosztó, 2 méter kábellel, túlfeszültség védelem, gyerekbiztos érintésvédelem, kapcsolóval, szerelhető.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(561, 'Sűrített levegő spray 200ml', '1771006591', '200ml-es sűrített levegő spray, elektronikai eszközök tisztításához, széles csővel, hatékonyan távolítja el a port.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(562, 'SSD 500GB', '1771006592', '500GB kapacitású SATA SSD, olvasási sebesség 550MB/s, ideális operációs rendszerekhez és gyakran használt programokhoz.', 6, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(563, 'HDMI kábel 1m', '1771006593', '1 méter hosszú HDMI kábel, 4K támogatás, Ethernet funkcióval, aranyozott csatlakozók, kompakt méret.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(564, 'Laptop hűtőpad RGB', '1771006594', 'Laptop hűtőpad RGB világítással, 3 db ventilátorral, USB tápellátás, állítható magasság, 17 hüvelykes laptopokhoz is.', 12, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(565, 'VGA kábel 5m', '1771006595', '5 méter hosszú VGA kábel, 15 tűs csatlakozó, alkalmas monitorok és projektorok csatlakoztatásához, fekete színű.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(566, 'Gamer egér vezeték nélküli', '1771006596', 'Vezeték nélküli gaming egér, RGB világítással, 6 programozható gomb, 16000 DPI, 70 óra üzemidő, töltőállomással.', 4, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(567, 'USB-C töltőkábel 1m szövött', '1771006597', '1 méter hosszú USB-C töltőkábel, gyorstöltésre alkalmas, szövött anyagú, tartós kivitel, kompatibilis telefonokkal és tabletekkel.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:55:05'),
(568, 'DisplayPort kábel 5m', '1771006598', '5 méter hosszú DisplayPort kábel, 4K támogatás, aranyozott csatlakozók, alkalmas monitorok és grafikus kártyák csatlakoztatásához.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(569, 'Okos izzó GU10', '1771006599', 'GU10 foglalatú okos izzó, 600 lumen, RGB színek, Wi-Fi vezérlés, hangvezérlés támogatás, időzíthető, alkalmazásból vezérelhető.', 17, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(570, 'Hálózati kábel Cat5e 10m', '1771006600', '10 méter hosszú Cat5e hálózati kábel, 100Mbps sebesség, szürke színű, pattintott csatlakozókkal.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(571, 'Irodai szék hálós fekete', '1771006601', 'Ergonomikus irodai szék fekete hálós háttámlával, állítható magassággal és kartámasszal, görgős talppal.', 8, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(572, 'AA elem 8db', '1771006602', '8 db AA elem, tartós alkáli, hosszú élettartam, alkalmas távirányítókhoz, játékokhoz és egyéb eszközökhöz.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(573, 'Fülhallgató vezetékes mikrofonnal', '1771006603', 'Vezetékes fülhallgató beépített mikrofonnal, 3.5mm jack csatlakozó, hívásfogadó gomb, ergonomikus kialakítás.', 9, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(574, 'Gamer fejhallgató Xbox', '1771006604', 'Gamer fejhallgató Xbox konzolokhoz, vezeték nélküli kapcsolat, Windows Sonic hangzás, zajszűrő mikrofon, kényelmes fülpárnák.', 4, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(575, 'USB hub 10 port', '1771006605', '10 portos USB 3.0 hub, külső tápellátással, egyedi kapcsolók, ideális több eszköz egyidejű csatlakoztatásához.', 2, 1, '2026-02-13 19:14:55', '2026-02-13 19:55:05'),
(576, 'Jack kábel 10m', '1771006606', '10 méter hosszú jack kábel, 3.5mm csatlakozó, sztereó hangátvitel, alkalmas hangszórók és erősítők összekötésére.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(577, 'Tablet tok 10.1', '1771006607', 'Univerzális tablet tok 10.1 hüvelykes készülékekhez, szilikon anyag, állítható támasztó funkció, több színben kapható.', 16, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(578, 'Okos kapucsengő vezetékes', '1771006608', 'Vezetékes okos kapucsengő, 1080p kamera, éjjellátó, mozgásérzékelés, alkalmazás értesítések, 24/7 felvétel lehetőség.', 18, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(579, 'UTP kábel Cat6 30m', '1771006609', '30 méter hosszú UTP kábel Cat6 szabvány szerint, 1Gbps sebesség, szürke színű, pattintott csatlakozókkal.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(580, 'Projektor vászon manuális', '1771006610', 'Manuális projektor vászon, 150 cm széles, falra szerelhető, fekete kerettel, tökéletes vetítési felület.', 8, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(581, 'Szünetmentes táp 1500VA', '1771006611', '1500VA teljesítményű szünetmentes tápegység, 8 kimenettel, túlfeszültség védelem, automatikus feszültségszabályozás, ideális szerverekhez.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(582, 'Vezeték nélküli egér színes', '1771006612', 'Vezeték nélküli egér több színben, kompakt méret, 3 gombos, alkalmas irodai használatra és utazáshoz.', 2, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(583, 'Earbuds vezeték nélküli', '1771006613', 'Vezeték nélküli earbuds, töltőtokkal, Bluetooth 5.0, 15 óra üzemidő, IPX5 vízállóság, érintővezérlés.', 9, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(584, 'Gamer fejhallgató PC', '1771006614', 'Gamer fejhallgató PC-hez, 7.1 virtuális hang, USB kapcsolat, állítható mikrofon, RGB világítás, kényelmes fülpárnák.', 4, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(585, 'Wi-Fi jelismétlő beltéri', '1771006615', 'Beltéri Wi-Fi jelismétlő, dual-band, egyszerű beállítás, Ethernet porttal, növeli a hálózati lefedettséget.', 7, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(586, 'Külső SSD 2TB', '1771006616', '2TB kapacitású külső SSD, USB-C csatlakozás, olvasási sebesség 1000MB/s, alumínium ház, kompakt méret, gyors adatátvitel.', 6, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(587, 'Mikrofon állvány asztali gamer', '1771006617', 'Asztali gamer mikrofon állvány, rugalmas karral, RGB világítással, univerzális menettel, stabil és állítható.', 9, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(588, 'IP kamera beltéri PTZ', '1771006618', 'Beltéri PTZ IP kamera, 1080p felbontás, 360° forgatható, éjjellátó, mozgáskövetés, kétirányú hang, microSD támogatás.', 18, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(589, 'Dokkoló USB-C univerzális', '1771006619', 'Univerzális USB-C dokkoló, HDMI, VGA, USB 3.0, Ethernet, SD kártya olvasó portokkal, támogatja a tápellátást.', 12, 0, '2026-02-13 19:14:55', '2026-02-13 19:55:05'),
(590, 'Pendrive 512GB', '1771006620', '512GB kapacitású USB 3.2 pendrive, fém ház, kompakt méret, védelem a fizikai sérülések ellen, gyors adatátvitel.', 6, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(591, 'Hálózati elosztó 3as', '1771006621', '3 aljzatos hálózati elosztó, 1 méter kábellel, túlfeszültség védelem, gyerekbiztos érintésvédelem, kapcsolóval.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(592, 'Sűrített levegő spray 100ml', '1771006622', '100ml-es sűrített levegő spray, elektronikai eszközök tisztításához, széles csővel, hatékonyan távolítja el a port.', 20, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(593, 'SSD 2TB', '1771006623', '2TB kapacitású SATA SSD, olvasási sebesség 550MB/s, ideális nagy mennyiségű adat tárolására és gyakran használt programokhoz.', 6, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(594, 'HDMI kábel 3m', '1771006624', '3 méter hosszú HDMI kábel, 4K támogatás, Ethernet funkcióval, aranyozott csatlakozók, vastag szigetelés.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(595, 'Laptop hűtőpad 14', '1771006625', 'Laptop hűtőpad 14 hüvelykes laptopokhoz, 2 db ventilátorral, USB tápellátás, halk működés, állítható magasság.', 12, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(596, 'VGA kábel 10m', '1771006626', '10 méter hosszú VGA kábel, 15 tűs csatlakozó, alkalmas monitorok és projektorok csatlakoztatásához, fekete színű.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(597, 'Gamer egér RGB vezetékes', '1771006627', 'Vezetékes gaming egér RGB világítással, 6 programozható gomb, 12400 DPI, szövött kábel, kényelmes markolat.', 4, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(598, 'USB-C töltőkábel 3m', '1771006628', '3 méter hosszú USB-C töltőkábel, gyorstöltésre alkalmas, szövött anyagú, tartós kivitel, kompatibilis telefonokkal és tabletekkel.', 1, 0, '2026-02-13 19:14:55', '2026-02-13 19:55:05'),
(599, 'DisplayPort kábel 3m', '1771006629', '3 méter hosszú DisplayPort kábel, 4K támogatás, aranyozott csatlakozók, alkalmas monitorok és grafikus kártyák csatlakoztatásához.', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55'),
(600, 'Okos izzó E14', '1771006630', 'E14 foglalatú okos izzó, 500 lumen, meleg fehér szín, Wi-Fi vezérlés, hangvez', 1, 1, '2026-02-13 19:14:55', '2026-02-13 19:14:55');

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
(91, 'TR-1771006558-E8F0', 561, 27, 'export', '2026-02-13 19:15:58', 15, 'Heti utánpótlás', '2026-02-14', 50, 'completed'),
(92, 'TR-1771006558-E8F0', 561, 31, 'import', '2026-02-13 19:15:58', 15, 'Heti utánpótlás', '2026-02-14', 50, 'pending'),
(93, 'TR-1771006558-E8F0', 495, 27, 'export', '2026-02-13 19:15:58', 15, 'Heti utánpótlás', '2026-02-14', 10, 'completed'),
(94, 'TR-1771006558-E8F0', 495, 31, 'import', '2026-02-13 19:15:58', 15, 'Heti utánpótlás', '2026-02-14', 10, 'pending'),
(95, 'TR-1771009114-ECAE', 491, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 60, 'completed'),
(96, 'TR-1771009114-ECAE', 491, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 60, 'pending'),
(97, 'TR-1771009114-ECAE', 533, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 10, 'completed'),
(98, 'TR-1771009114-ECAE', 533, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 10, 'pending'),
(99, 'TR-1771009114-ECAE', 599, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 42, 'completed'),
(100, 'TR-1771009114-ECAE', 599, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 42, 'pending'),
(101, 'TR-1771009114-ECAE', 500, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 40, 'completed'),
(102, 'TR-1771009114-ECAE', 500, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 40, 'pending'),
(103, 'TR-1771009114-ECAE', 555, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 12, 'completed'),
(104, 'TR-1771009114-ECAE', 555, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 12, 'pending'),
(105, 'TR-1771009114-ECAE', 467, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 45, 'completed'),
(106, 'TR-1771009114-ECAE', 467, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 45, 'pending'),
(107, 'TR-1771009114-ECAE', 566, 21, 'export', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 27, 'completed'),
(108, 'TR-1771009114-ECAE', 566, 31, 'import', '2026-02-13 19:58:34', 15, 'Havi Tech csomag. AHE-341 Rendszám B épület, C dokkoló', '2026-02-13', 27, 'pending'),
(109, 'TR-1753737493-8C9A', 540, 28, 'export', '2025-07-28 23:18:13', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - NOP-012 rendszám', '2025-08-04', 39, 'canceled'),
(110, 'TR-1753737493-8C9A', 540, 22, 'import', '2025-07-28 23:18:13', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - NOP-012 rendszám', '2025-08-04', 39, 'canceled'),
(111, 'TR-1753740983-B04E', 528, 28, 'export', '2025-07-29 00:16:23', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - YZA-567 rendszám', '2025-08-02', 38, 'canceled'),
(112, 'TR-1753740983-B04E', 528, 26, 'import', '2025-07-29 00:16:23', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - YZA-567 rendszám', '2025-08-02', 38, 'canceled'),
(113, 'TR-1753858020-F90C', 472, 26, 'export', '2025-07-30 08:47:00', 16, 'Kiszállítás - Raktárak közötti átmozgatás - G épület - EFG-123 rendszám', '2025-08-03', 10, 'canceled'),
(114, 'TR-1753858020-F90C', 472, 29, 'import', '2025-07-30 08:47:00', 16, 'Beérkezés - Raktárak közötti átmozgatás - G épület - EFG-123 rendszám', '2025-08-03', 10, 'canceled'),
(115, 'TR-1753942572-1C50', 501, 23, 'export', '2025-07-31 08:16:12', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - QRS-345 rendszám', '2025-08-02', 19, 'canceled'),
(116, 'TR-1753942572-1C50', 501, 24, 'import', '2025-07-31 08:16:12', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - QRS-345 rendszám', '2025-08-02', 19, 'canceled'),
(117, 'TR-1754049858-DB3C', 572, 22, 'export', '2025-08-01 14:04:18', 15, 'Kiszállítás - Heti készlet feltöltés - H épület - EFG-123 rendszám', '2025-08-07', 42, 'canceled'),
(118, 'TR-1754049858-DB3C', 572, 23, 'import', '2025-08-01 14:04:18', 15, 'Beérkezés - Heti készlet feltöltés - H épület - EFG-123 rendszám', '2025-08-07', 42, 'canceled'),
(119, 'TR-1754144785-F5F1', 483, 23, 'export', '2025-08-02 16:26:25', 16, 'Kiszállítás - Heti készlet feltöltés - C épület - YZA-567 rendszám', '2025-08-04', 38, 'canceled'),
(120, 'TR-1754144785-F5F1', 483, 27, 'import', '2025-08-02 16:26:25', 16, 'Beérkezés - Heti készlet feltöltés - C épület - YZA-567 rendszám', '2025-08-04', 38, 'canceled'),
(121, 'TR-1754181602-5F1C', 553, 22, 'export', '2025-08-03 02:40:02', 16, 'Kiszállítás - Sürgős utánpótlás - H épület - NOP-012 rendszám', '2025-08-10', 35, 'canceled'),
(122, 'TR-1754181602-5F1C', 553, 25, 'import', '2025-08-03 02:40:02', 16, 'Beérkezés - Sürgős utánpótlás - H épület - NOP-012 rendszám', '2025-08-10', 35, 'canceled'),
(123, 'TR-1754304282-7311', 579, 24, 'export', '2025-08-04 12:44:42', 16, 'Kiszállítás - Ütemezett szállítás - C épület - KLM-789 rendszám', '2025-08-09', 16, 'canceled'),
(124, 'TR-1754304282-7311', 579, 31, 'import', '2025-08-04 12:44:42', 16, 'Beérkezés - Ütemezett szállítás - C épület - KLM-789 rendszám', '2025-08-09', 16, 'canceled'),
(125, 'TR-1754357060-B5DB', 570, 29, 'export', '2025-08-05 03:24:20', 16, 'Kiszállítás - Rendkívüli szállítmány - E épület - EFG-123 rendszám', '2025-08-08', 2, 'canceled'),
(126, 'TR-1754357060-B5DB', 570, 31, 'import', '2025-08-05 03:24:20', 16, 'Beérkezés - Rendkívüli szállítmány - E épület - EFG-123 rendszám', '2025-08-08', 2, 'canceled'),
(127, 'TR-1754436026-72D6', 470, 29, 'export', '2025-08-06 01:20:26', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - MNO-345 rendszám', '2025-08-09', 47, 'canceled'),
(128, 'TR-1754436026-72D6', 470, 21, 'import', '2025-08-06 01:20:26', 16, 'Beérkezés - Heti készlet feltöltés - H épület - MNO-345 rendszám', '2025-08-09', 47, 'canceled'),
(129, 'TR-1754588373-89A2', 493, 21, 'export', '2025-08-07 19:39:33', 16, 'Kiszállítás - Sürgős utánpótlás - D épület - TUV-678 rendszám', '2025-08-12', 39, 'completed'),
(130, 'TR-1754588373-89A2', 493, 31, 'import', '2025-08-07 19:39:33', 16, 'Beérkezés - Sürgős utánpótlás - D épület - TUV-678 rendszám', '2025-08-12', 39, 'completed'),
(131, 'TR-1754636871-161F', 490, 28, 'export', '2025-08-08 09:07:51', 16, 'Kiszállítás - Ütemezett szállítás - B épület - HIJ-456 rendszám', '2025-08-14', 25, 'completed'),
(132, 'TR-1754636871-161F', 490, 24, 'import', '2025-08-08 09:07:51', 16, 'Beérkezés - Ütemezett szállítás - B épület - HIJ-456 rendszám', '2025-08-14', 25, 'completed'),
(133, 'TR-1754709975-396D', 546, 29, 'export', '2025-08-09 05:26:15', 16, 'Kiszállítás - Sürgős utánpótlás - F épület - JKL-012 rendszám', '2025-08-12', 30, 'completed'),
(134, 'TR-1754709975-396D', 546, 21, 'import', '2025-08-09 05:26:15', 16, 'Beérkezés - Sürgős utánpótlás - F épület - JKL-012 rendszám', '2025-08-12', 30, 'completed'),
(135, 'TR-1754861937-0570', 483, 25, 'export', '2025-08-10 23:38:57', 16, 'Kiszállítás - Heti készlet feltöltés - E épület - FGH-890 rendszám', '2025-08-15', 44, 'completed'),
(136, 'TR-1754861937-0570', 483, 23, 'import', '2025-08-10 23:38:57', 16, 'Beérkezés - Heti készlet feltöltés - E épület - FGH-890 rendszám', '2025-08-15', 44, 'completed'),
(137, 'TR-1754911982-E044', 505, 23, 'export', '2025-08-11 13:33:02', 16, 'Kiszállítás - Heti készlet feltöltés - F épület - QRS-345 rendszám', '2025-08-16', 5, 'completed'),
(138, 'TR-1754911982-E044', 505, 22, 'import', '2025-08-11 13:33:02', 16, 'Beérkezés - Heti készlet feltöltés - F épület - QRS-345 rendszám', '2025-08-16', 5, 'completed'),
(139, 'TR-1754988044-4D1A', 571, 27, 'export', '2025-08-12 10:40:44', 16, 'Kiszállítás - Sürgős utánpótlás - E épület - STU-901 rendszám', '2025-08-19', 37, 'completed'),
(140, 'TR-1754988044-4D1A', 571, 24, 'import', '2025-08-12 10:40:44', 16, 'Beérkezés - Sürgős utánpótlás - E épület - STU-901 rendszám', '2025-08-19', 37, 'completed'),
(141, 'TR-1755088381-3E46', 497, 22, 'export', '2025-08-13 14:33:01', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - ABC-123 rendszám', '2025-08-15', 20, 'completed'),
(142, 'TR-1755088381-3E46', 497, 25, 'import', '2025-08-13 14:33:01', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - ABC-123 rendszám', '2025-08-15', 20, 'completed'),
(143, 'TR-1755132224-1A80', 486, 25, 'export', '2025-08-14 02:43:44', 16, 'Kiszállítás - Raktárak közötti átmozgatás - E épület - TUV-678 rendszám', '2025-08-20', 4, 'completed'),
(144, 'TR-1755132224-1A80', 486, 31, 'import', '2025-08-14 02:43:44', 16, 'Beérkezés - Raktárak közötti átmozgatás - E épület - TUV-678 rendszám', '2025-08-20', 4, 'completed'),
(145, 'TR-1755211895-61D2', 468, 26, 'export', '2025-08-15 00:51:35', 16, 'Kiszállítás - Raktárak közötti átmozgatás - F épület - FGH-890 rendszám', '2025-08-19', 23, 'completed'),
(146, 'TR-1755211895-61D2', 468, 24, 'import', '2025-08-15 00:51:35', 16, 'Beérkezés - Raktárak közötti átmozgatás - F épület - FGH-890 rendszám', '2025-08-19', 23, 'completed'),
(147, 'TR-1755355281-D29A', 553, 30, 'export', '2025-08-16 16:41:21', 16, 'Kiszállítás - Sürgős utánpótlás - G épület - VWX-234 rendszám', '2025-08-22', 17, 'completed'),
(148, 'TR-1755355281-D29A', 553, 27, 'import', '2025-08-16 16:41:21', 16, 'Beérkezés - Sürgős utánpótlás - G épület - VWX-234 rendszám', '2025-08-22', 17, 'completed'),
(149, 'TR-1755430481-180F', 507, 22, 'export', '2025-08-17 13:34:41', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - CDE-567 rendszám', '2025-08-22', 46, 'completed'),
(150, 'TR-1755430481-180F', 507, 30, 'import', '2025-08-17 13:34:41', 16, 'Beérkezés - Heti készlet feltöltés - G épület - CDE-567 rendszám', '2025-08-22', 46, 'completed'),
(151, 'TR-1755481330-6567', 490, 27, 'export', '2025-08-18 03:42:10', 15, 'Kiszállítás - Heti készlet feltöltés - C épület - EFG-123 rendszám', '2025-08-23', 44, 'completed'),
(152, 'TR-1755481330-6567', 490, 28, 'import', '2025-08-18 03:42:10', 15, 'Beérkezés - Heti készlet feltöltés - C épület - EFG-123 rendszám', '2025-08-23', 44, 'completed'),
(153, 'TR-1755571147-931A', 571, 27, 'export', '2025-08-19 04:39:07', 16, 'Kiszállítás - Heti készlet feltöltés - F épület - FGH-890 rendszám', '2025-08-26', 5, 'completed'),
(154, 'TR-1755571147-931A', 571, 23, 'import', '2025-08-19 04:39:07', 16, 'Beérkezés - Heti készlet feltöltés - F épület - FGH-890 rendszám', '2025-08-26', 5, 'completed'),
(155, 'TR-1755721231-5316', 567, 23, 'export', '2025-08-20 22:20:31', 16, 'Kiszállítás - Raktárak közötti átmozgatás - B épület - DEF-456 rendszám', '2025-08-22', 18, 'completed'),
(156, 'TR-1755721231-5316', 567, 26, 'import', '2025-08-20 22:20:31', 16, 'Beérkezés - Raktárak közötti átmozgatás - B épület - DEF-456 rendszám', '2025-08-22', 18, 'completed'),
(157, 'TR-1755734036-5FD3', 555, 26, 'export', '2025-08-21 01:53:56', 16, 'Kiszállítás - Ütemezett szállítás - G épület - QRS-345 rendszám', '2025-08-28', 39, 'completed'),
(158, 'TR-1755734036-5FD3', 555, 23, 'import', '2025-08-21 01:53:56', 16, 'Beérkezés - Ütemezett szállítás - G épület - QRS-345 rendszám', '2025-08-28', 39, 'completed'),
(159, 'TR-1755827667-B192', 541, 23, 'export', '2025-08-22 03:54:27', 16, 'Kiszállítás - Sürgős utánpótlás - A épület - YZA-567 rendszám', '2025-08-24', 29, 'completed'),
(160, 'TR-1755827667-B192', 541, 31, 'import', '2025-08-22 03:54:27', 16, 'Beérkezés - Sürgős utánpótlás - A épület - YZA-567 rendszám', '2025-08-24', 29, 'completed'),
(161, 'TR-1755978910-EFAA', 528, 26, 'export', '2025-08-23 21:55:10', 16, 'Kiszállítás - Rendkívüli szállítmány - G épület - VWX-234 rendszám', '2025-08-26', 17, 'completed'),
(162, 'TR-1755978910-EFAA', 528, 25, 'import', '2025-08-23 21:55:10', 16, 'Beérkezés - Rendkívüli szállítmány - G épület - VWX-234 rendszám', '2025-08-26', 17, 'completed'),
(163, 'TR-1756027071-8484', 554, 29, 'export', '2025-08-24 11:17:51', 16, 'Kiszállítás - Ütemezett szállítás - C épület - ZAB-234 rendszám', '2025-08-30', 28, 'completed'),
(164, 'TR-1756027071-8484', 554, 30, 'import', '2025-08-24 11:17:51', 16, 'Beérkezés - Ütemezett szállítás - C épület - ZAB-234 rendszám', '2025-08-30', 28, 'completed'),
(165, 'TR-1756151588-94D1', 541, 25, 'export', '2025-08-25 21:53:08', 16, 'Kiszállítás - Sürgős utánpótlás - G épület - STU-901 rendszám', '2025-08-30', 32, 'completed'),
(166, 'TR-1756151588-94D1', 541, 31, 'import', '2025-08-25 21:53:08', 16, 'Beérkezés - Sürgős utánpótlás - G épület - STU-901 rendszám', '2025-08-30', 32, 'completed'),
(167, 'TR-1756209338-A6A1', 556, 29, 'export', '2025-08-26 13:55:38', 16, 'Kiszállítás - Raktárak közötti átmozgatás - F épület - FGH-890 rendszám', '2025-08-31', 19, 'completed'),
(168, 'TR-1756209338-A6A1', 556, 23, 'import', '2025-08-26 13:55:38', 16, 'Beérkezés - Raktárak közötti átmozgatás - F épület - FGH-890 rendszám', '2025-08-31', 19, 'completed'),
(169, 'TR-1756255392-47E6', 486, 26, 'export', '2025-08-27 02:43:12', 16, 'Kiszállítás - Raktárak közötti átmozgatás - E épület - JKL-012 rendszám', '2025-08-30', 13, 'completed'),
(170, 'TR-1756255392-47E6', 486, 22, 'import', '2025-08-27 02:43:12', 16, 'Beérkezés - Raktárak közötti átmozgatás - E épület - JKL-012 rendszám', '2025-08-30', 13, 'completed'),
(171, 'TR-1756359475-24BB', 469, 21, 'export', '2025-08-28 07:37:55', 16, 'Kiszállítás - Rendkívüli szállítmány - C épület - JKL-012 rendszám', '2025-08-31', 30, 'completed'),
(172, 'TR-1756359475-24BB', 469, 25, 'import', '2025-08-28 07:37:55', 16, 'Beérkezés - Rendkívüli szállítmány - C épület - JKL-012 rendszám', '2025-08-31', 30, 'completed'),
(173, 'TR-1756438051-735B', 589, 24, 'export', '2025-08-29 05:27:31', 16, 'Kiszállítás - Raktárak közötti átmozgatás - G épület - PQR-678 rendszám', '2025-09-02', 50, 'completed'),
(174, 'TR-1756438051-735B', 589, 31, 'import', '2025-08-29 05:27:31', 16, 'Beérkezés - Raktárak közötti átmozgatás - G épület - PQR-678 rendszám', '2025-09-02', 50, 'completed'),
(175, 'TR-1756581139-C3C8', 479, 23, 'export', '2025-08-30 21:12:19', 16, 'Kiszállítás - Ütemezett szállítás - E épület - DEF-456 rendszám', '2025-09-06', 13, 'completed'),
(176, 'TR-1756581139-C3C8', 479, 24, 'import', '2025-08-30 21:12:19', 16, 'Beérkezés - Ütemezett szállítás - E épület - DEF-456 rendszám', '2025-09-06', 13, 'completed'),
(177, 'TR-1756652919-681D', 486, 21, 'export', '2025-08-31 17:08:39', 16, 'Kiszállítás - Rendkívüli szállítmány - C épület - NOP-012 rendszám', '2025-09-07', 10, 'completed'),
(178, 'TR-1756652919-681D', 486, 26, 'import', '2025-08-31 17:08:39', 16, 'Beérkezés - Rendkívüli szállítmány - C épület - NOP-012 rendszám', '2025-09-07', 10, 'completed'),
(179, 'TR-1756723013-107B', 584, 23, 'export', '2025-09-01 12:36:53', 16, 'Kiszállítás - Rendkívüli szállítmány - C épület - PQR-678 rendszám', '2025-09-08', 30, 'completed'),
(180, 'TR-1756723013-107B', 584, 31, 'import', '2025-09-01 12:36:53', 16, 'Beérkezés - Rendkívüli szállítmány - C épület - PQR-678 rendszám', '2025-09-08', 30, 'completed'),
(181, 'TR-1756777942-DE46', 555, 26, 'export', '2025-09-02 03:52:22', 16, 'Kiszállítás - Sürgős utánpótlás - H épület - PQR-678 rendszám', '2025-09-07', 12, 'completed'),
(182, 'TR-1756777942-DE46', 555, 22, 'import', '2025-09-02 03:52:22', 16, 'Beérkezés - Sürgős utánpótlás - H épület - PQR-678 rendszám', '2025-09-07', 12, 'completed'),
(183, 'TR-1756905994-F48F', 579, 29, 'export', '2025-09-03 15:26:34', 16, 'Kiszállítás - Ütemezett szállítás - B épület - CDE-567 rendszám', '2025-09-06', 12, 'completed'),
(184, 'TR-1756905994-F48F', 579, 23, 'import', '2025-09-03 15:26:34', 16, 'Beérkezés - Ütemezett szállítás - B épület - CDE-567 rendszám', '2025-09-06', 12, 'completed'),
(185, 'TR-1756967931-4B36', 567, 25, 'export', '2025-09-04 08:38:51', 16, 'Kiszállítás - Heti készlet feltöltés - E épület - FGH-890 rendszám', '2025-09-11', 45, 'completed'),
(186, 'TR-1756967931-4B36', 567, 21, 'import', '2025-09-04 08:38:51', 16, 'Beérkezés - Heti készlet feltöltés - E épület - FGH-890 rendszám', '2025-09-11', 45, 'completed'),
(187, 'TR-1757054725-24FF', 597, 22, 'export', '2025-09-05 08:45:25', 16, 'Kiszállítás - Rendkívüli szállítmány - D épület - CDE-567 rendszám', '2025-09-11', 6, 'completed'),
(188, 'TR-1757054725-24FF', 597, 29, 'import', '2025-09-05 08:45:25', 16, 'Beérkezés - Rendkívüli szállítmány - D épület - CDE-567 rendszám', '2025-09-11', 6, 'completed'),
(189, 'TR-1757156779-34BF', 473, 23, 'export', '2025-09-06 13:06:19', 16, 'Kiszállítás - Ütemezett szállítás - E épület - NOP-012 rendszám', '2025-09-11', 26, 'completed'),
(190, 'TR-1757156779-34BF', 473, 27, 'import', '2025-09-06 13:06:19', 16, 'Beérkezés - Ütemezett szállítás - E épület - NOP-012 rendszám', '2025-09-11', 26, 'completed'),
(191, 'TR-1757236298-5F99', 486, 30, 'export', '2025-09-07 11:11:38', 16, 'Kiszállítás - Rendkívüli szállítmány - G épület - WXY-901 rendszám', '2025-09-14', 46, 'completed'),
(192, 'TR-1757236298-5F99', 486, 22, 'import', '2025-09-07 11:11:38', 16, 'Beérkezés - Rendkívüli szállítmány - G épület - WXY-901 rendszám', '2025-09-14', 46, 'completed'),
(193, 'TR-1757332506-2885', 494, 30, 'export', '2025-09-08 13:55:06', 16, 'Kiszállítás - Ütemezett szállítás - D épület - DEF-456 rendszám', '2025-09-14', 11, 'completed'),
(194, 'TR-1757332506-2885', 494, 24, 'import', '2025-09-08 13:55:06', 16, 'Beérkezés - Ütemezett szállítás - D épület - DEF-456 rendszám', '2025-09-14', 11, 'completed'),
(195, 'TR-1757376595-2BDB', 560, 25, 'export', '2025-09-09 02:09:55', 16, 'Kiszállítás - Ütemezett szállítás - G épület - ABC-123 rendszám', '2025-09-15', 49, 'completed'),
(196, 'TR-1757376595-2BDB', 560, 21, 'import', '2025-09-09 02:09:55', 16, 'Beérkezés - Ütemezett szállítás - G épület - ABC-123 rendszám', '2025-09-15', 49, 'completed'),
(197, 'TR-1757487919-26EB', 518, 24, 'export', '2025-09-10 09:05:19', 16, 'Kiszállítás - Sürgős utánpótlás - B épület - MNO-345 rendszám', '2025-09-12', 14, 'completed'),
(198, 'TR-1757487919-26EB', 518, 25, 'import', '2025-09-10 09:05:19', 16, 'Beérkezés - Sürgős utánpótlás - B épület - MNO-345 rendszám', '2025-09-12', 14, 'completed'),
(199, 'TR-1757569663-1211', 554, 22, 'export', '2025-09-11 07:47:43', 15, 'Kiszállítás - Rendkívüli szállítmány - C épület - ABC-123 rendszám', '2025-09-17', 42, 'completed'),
(200, 'TR-1757569663-1211', 554, 27, 'import', '2025-09-11 07:47:43', 15, 'Beérkezés - Rendkívüli szállítmány - C épület - ABC-123 rendszám', '2025-09-17', 42, 'completed'),
(201, 'TR-1757699391-7FD5', 574, 29, 'export', '2025-09-12 19:49:51', 16, 'Kiszállítás - Raktárak közötti átmozgatás - G épület - BCD-890 rendszám', '2025-09-14', 27, 'completed'),
(202, 'TR-1757699391-7FD5', 574, 26, 'import', '2025-09-12 19:49:51', 16, 'Beérkezés - Raktárak közötti átmozgatás - G épület - BCD-890 rendszám', '2025-09-14', 27, 'completed'),
(203, 'TR-1757749153-264B', 557, 24, 'export', '2025-09-13 09:39:13', 16, 'Kiszállítás - Ütemezett szállítás - G épület - DEF-456 rendszám', '2025-09-16', 37, 'completed'),
(204, 'TR-1757749153-264B', 557, 30, 'import', '2025-09-13 09:39:13', 16, 'Beérkezés - Ütemezett szállítás - G épület - DEF-456 rendszám', '2025-09-16', 37, 'completed'),
(205, 'TR-1757803689-9287', 587, 28, 'export', '2025-09-14 00:48:09', 16, 'Kiszállítás - Heti készlet feltöltés - E épület - HIJ-456 rendszám', '2025-09-18', 20, 'completed'),
(206, 'TR-1757803689-9287', 587, 25, 'import', '2025-09-14 00:48:09', 16, 'Beérkezés - Heti készlet feltöltés - E épület - HIJ-456 rendszám', '2025-09-18', 20, 'completed'),
(207, 'TR-1757888850-6FB9', 483, 21, 'export', '2025-09-15 00:27:30', 16, 'Kiszállítás - Ütemezett szállítás - F épület - JKL-012 rendszám', '2025-09-20', 29, 'completed'),
(208, 'TR-1757888850-6FB9', 483, 29, 'import', '2025-09-15 00:27:30', 16, 'Beérkezés - Ütemezett szállítás - F épület - JKL-012 rendszám', '2025-09-20', 29, 'completed'),
(209, 'TR-1758005033-F2CE', 566, 24, 'export', '2025-09-16 08:43:53', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - CDE-567 rendszám', '2025-09-19', 36, 'completed'),
(210, 'TR-1758005033-F2CE', 566, 23, 'import', '2025-09-16 08:43:53', 16, 'Beérkezés - Heti készlet feltöltés - G épület - CDE-567 rendszám', '2025-09-19', 36, 'completed'),
(211, 'TR-1758131938-4789', 489, 23, 'export', '2025-09-17 19:58:58', 16, 'Kiszállítás - Raktárak közötti átmozgatás - E épület - BCD-890 rendszám', '2025-09-19', 49, 'completed'),
(212, 'TR-1758131938-4789', 489, 27, 'import', '2025-09-17 19:58:58', 16, 'Beérkezés - Raktárak közötti átmozgatás - E épület - BCD-890 rendszám', '2025-09-19', 49, 'completed'),
(213, 'TR-1758229240-5C8C', 584, 27, 'export', '2025-09-18 23:00:40', 16, 'Kiszállítás - Sürgős utánpótlás - G épület - CDE-567 rendszám', '2025-09-20', 10, 'completed'),
(214, 'TR-1758229240-5C8C', 584, 22, 'import', '2025-09-18 23:00:40', 16, 'Beérkezés - Sürgős utánpótlás - G épület - CDE-567 rendszám', '2025-09-20', 10, 'completed'),
(215, 'TR-1758278687-2EC0', 483, 29, 'export', '2025-09-19 12:44:47', 16, 'Kiszállítás - Ütemezett szállítás - F épület - HIJ-456 rendszám', '2025-09-25', 40, 'completed'),
(216, 'TR-1758278687-2EC0', 483, 21, 'import', '2025-09-19 12:44:47', 16, 'Beérkezés - Ütemezett szállítás - F épület - HIJ-456 rendszám', '2025-09-25', 40, 'completed'),
(217, 'TR-1758375628-9822', 546, 22, 'export', '2025-09-20 15:40:28', 16, 'Kiszállítás - Raktárak közötti átmozgatás - D épület - NOP-012 rendszám', '2025-09-23', 50, 'completed'),
(218, 'TR-1758375628-9822', 546, 29, 'import', '2025-09-20 15:40:28', 16, 'Beérkezés - Raktárak közötti átmozgatás - D épület - NOP-012 rendszám', '2025-09-23', 50, 'completed'),
(219, 'TR-1758471858-612E', 508, 29, 'export', '2025-09-21 18:24:18', 16, 'Kiszállítás - Sürgős utánpótlás - D épület - JKL-012 rendszám', '2025-09-26', 43, 'completed'),
(220, 'TR-1758471858-612E', 508, 25, 'import', '2025-09-21 18:24:18', 16, 'Beérkezés - Sürgős utánpótlás - D épület - JKL-012 rendszám', '2025-09-26', 43, 'completed'),
(221, 'TR-1758494652-0233', 508, 25, 'export', '2025-09-22 00:44:12', 16, 'Kiszállítás - Raktárak közötti átmozgatás - B épület - MNO-345 rendszám', '2025-09-24', 16, 'completed'),
(222, 'TR-1758494652-0233', 508, 28, 'import', '2025-09-22 00:44:12', 16, 'Beérkezés - Raktárak közötti átmozgatás - B épület - MNO-345 rendszám', '2025-09-24', 16, 'completed'),
(223, 'TR-1758648298-745D', 524, 21, 'export', '2025-09-23 19:24:58', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - VWX-234 rendszám', '2025-09-28', 50, 'completed'),
(224, 'TR-1758648298-745D', 524, 26, 'import', '2025-09-23 19:24:58', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - VWX-234 rendszám', '2025-09-28', 50, 'completed'),
(225, 'TR-1758666835-AEE7', 579, 26, 'export', '2025-09-24 00:33:55', 16, 'Kiszállítás - Raktárak közötti átmozgatás - D épület - HIJ-456 rendszám', '2025-09-29', 29, 'completed'),
(226, 'TR-1758666835-AEE7', 579, 31, 'import', '2025-09-24 00:33:55', 16, 'Beérkezés - Raktárak közötti átmozgatás - D épület - HIJ-456 rendszám', '2025-09-29', 29, 'completed'),
(227, 'TR-1758763778-AC01', 479, 26, 'export', '2025-09-25 03:29:38', 16, 'Kiszállítás - Sürgős utánpótlás - B épület - ABC-123 rendszám', '2025-09-28', 31, 'completed'),
(228, 'TR-1758763778-AC01', 479, 29, 'import', '2025-09-25 03:29:38', 16, 'Beérkezés - Sürgős utánpótlás - B épület - ABC-123 rendszám', '2025-09-28', 31, 'completed'),
(229, 'TR-1758851974-5CCD', 572, 25, 'export', '2025-09-26 03:59:34', 16, 'Kiszállítás - Ütemezett szállítás - C épület - CDE-567 rendszám', '2025-10-01', 28, 'completed'),
(230, 'TR-1758851974-5CCD', 572, 29, 'import', '2025-09-26 03:59:34', 16, 'Beérkezés - Ütemezett szállítás - C épület - CDE-567 rendszám', '2025-10-01', 28, 'completed'),
(231, 'TR-1758946693-5D96', 558, 22, 'export', '2025-09-27 06:18:13', 16, 'Kiszállítás - Sürgős utánpótlás - B épület - QRS-345 rendszám', '2025-10-03', 3, 'completed'),
(232, 'TR-1758946693-5D96', 558, 25, 'import', '2025-09-27 06:18:13', 16, 'Beérkezés - Sürgős utánpótlás - B épület - QRS-345 rendszám', '2025-10-03', 3, 'completed'),
(233, 'TR-1759039114-5DDD', 504, 29, 'export', '2025-09-28 07:58:34', 15, 'Kiszállítás - Sürgős utánpótlás - H épület - PQR-678 rendszám', '2025-10-02', 42, 'completed'),
(234, 'TR-1759039114-5DDD', 504, 22, 'import', '2025-09-28 07:58:34', 15, 'Beérkezés - Sürgős utánpótlás - H épület - PQR-678 rendszám', '2025-10-02', 42, 'completed'),
(235, 'TR-1759160029-BF7F', 513, 26, 'export', '2025-09-29 17:33:49', 16, 'Kiszállítás - Heti készlet feltöltés - C épület - ZAB-234 rendszám', '2025-10-04', 39, 'completed'),
(236, 'TR-1759160029-BF7F', 513, 21, 'import', '2025-09-29 17:33:49', 16, 'Beérkezés - Heti készlet feltöltés - C épület - ZAB-234 rendszám', '2025-10-04', 39, 'completed'),
(237, 'TR-1759189494-5777', 538, 27, 'export', '2025-09-30 01:44:54', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - VWX-234 rendszám', '2025-10-03', 35, 'completed'),
(238, 'TR-1759189494-5777', 538, 22, 'import', '2025-09-30 01:44:54', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - VWX-234 rendszám', '2025-10-03', 35, 'completed'),
(239, 'TR-1759289091-7A5C', 560, 28, 'export', '2025-10-01 05:24:51', 16, 'Kiszállítás - Rendkívüli szállítmány - E épület - ZAB-234 rendszám', '2025-10-05', 48, 'completed'),
(240, 'TR-1759289091-7A5C', 560, 31, 'import', '2025-10-01 05:24:51', 16, 'Beérkezés - Rendkívüli szállítmány - E épület - ZAB-234 rendszám', '2025-10-05', 48, 'completed'),
(241, 'TR-1759411548-BCFD', 528, 21, 'export', '2025-10-02 15:25:48', 16, 'Kiszállítás - Rendkívüli szállítmány - B épület - PQR-678 rendszám', '2025-10-06', 18, 'completed'),
(242, 'TR-1759411548-BCFD', 528, 23, 'import', '2025-10-02 15:25:48', 16, 'Beérkezés - Rendkívüli szállítmány - B épület - PQR-678 rendszám', '2025-10-06', 18, 'completed'),
(243, 'TR-1759457397-8C58', 554, 28, 'export', '2025-10-03 04:09:57', 16, 'Kiszállítás - Ütemezett szállítás - G épület - KLM-789 rendszám', '2025-10-09', 34, 'completed'),
(244, 'TR-1759457397-8C58', 554, 29, 'import', '2025-10-03 04:09:57', 16, 'Beérkezés - Ütemezett szállítás - G épület - KLM-789 rendszám', '2025-10-09', 34, 'completed'),
(245, 'TR-1759559193-4815', 574, 29, 'export', '2025-10-04 08:26:33', 16, 'Kiszállítás - Heti készlet feltöltés - C épület - TUV-678 rendszám', '2025-10-11', 30, 'completed'),
(246, 'TR-1759559193-4815', 574, 28, 'import', '2025-10-04 08:26:33', 16, 'Beérkezés - Heti készlet feltöltés - C épület - TUV-678 rendszám', '2025-10-11', 30, 'completed'),
(247, 'TR-1759685649-B539', 578, 24, 'export', '2025-10-05 19:34:09', 16, 'Kiszállítás - Raktárak közötti átmozgatás - D épület - MNO-345 rendszám', '2025-10-11', 32, 'completed'),
(248, 'TR-1759685649-B539', 578, 28, 'import', '2025-10-05 19:34:09', 16, 'Beérkezés - Raktárak közötti átmozgatás - D épület - MNO-345 rendszám', '2025-10-11', 32, 'completed'),
(249, 'TR-1759702601-D08B', 500, 26, 'export', '2025-10-06 00:16:41', 16, 'Kiszállítás - Rendkívüli szállítmány - H épület - YZA-567 rendszám', '2025-10-12', 28, 'completed'),
(250, 'TR-1759702601-D08B', 500, 27, 'import', '2025-10-06 00:16:41', 16, 'Beérkezés - Rendkívüli szállítmány - H épület - YZA-567 rendszám', '2025-10-12', 28, 'completed'),
(251, 'TR-1759840774-F75F', 570, 28, 'export', '2025-10-07 14:39:34', 16, 'Kiszállítás - Rendkívüli szállítmány - H épület - YZA-567 rendszám', '2025-10-10', 7, 'completed'),
(252, 'TR-1759840774-F75F', 570, 21, 'import', '2025-10-07 14:39:34', 16, 'Beérkezés - Rendkívüli szállítmány - H épület - YZA-567 rendszám', '2025-10-10', 7, 'completed'),
(253, 'TR-1759907579-BB56', 487, 27, 'export', '2025-10-08 09:12:59', 16, 'Kiszállítás - Ütemezett szállítás - F épület - TUV-678 rendszám', '2025-10-11', 11, 'completed'),
(254, 'TR-1759907579-BB56', 487, 31, 'import', '2025-10-08 09:12:59', 16, 'Beérkezés - Ütemezett szállítás - F épület - TUV-678 rendszám', '2025-10-11', 11, 'completed'),
(255, 'TR-1760039428-3171', 468, 28, 'export', '2025-10-09 21:50:28', 16, 'Kiszállítás - Raktárak közötti átmozgatás - G épület - EFG-123 rendszám', '2025-10-14', 8, 'completed'),
(256, 'TR-1760039428-3171', 468, 21, 'import', '2025-10-09 21:50:28', 16, 'Beérkezés - Raktárak közötti átmozgatás - G épület - EFG-123 rendszám', '2025-10-14', 8, 'completed'),
(257, 'TR-1760048230-F5D8', 571, 22, 'export', '2025-10-10 00:17:10', 16, 'Kiszállítás - Ütemezett szállítás - G épület - TUV-678 rendszám', '2025-10-15', 42, 'completed'),
(258, 'TR-1760048230-F5D8', 571, 23, 'import', '2025-10-10 00:17:10', 16, 'Beérkezés - Ütemezett szállítás - G épület - TUV-678 rendszám', '2025-10-15', 42, 'completed'),
(259, 'TR-1760195995-ABE9', 567, 27, 'export', '2025-10-11 17:19:55', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - ABC-123 rendszám', '2025-10-13', 31, 'completed'),
(260, 'TR-1760195995-ABE9', 567, 30, 'import', '2025-10-11 17:19:55', 16, 'Beérkezés - Heti készlet feltöltés - G épület - ABC-123 rendszám', '2025-10-13', 31, 'completed'),
(261, 'TR-1760272993-7F2D', 520, 30, 'export', '2025-10-12 14:43:13', 16, 'Kiszállítás - Raktárak közötti átmozgatás - B épület - TUV-678 rendszám', '2025-10-14', 12, 'completed'),
(262, 'TR-1760272993-7F2D', 520, 27, 'import', '2025-10-12 14:43:13', 16, 'Beérkezés - Raktárak közötti átmozgatás - B épület - TUV-678 rendszám', '2025-10-14', 12, 'completed'),
(263, 'TR-1760382733-8576', 532, 29, 'export', '2025-10-13 21:12:13', 16, 'Kiszállítás - Ütemezett szállítás - D épület - GHI-789 rendszám', '2025-10-15', 31, 'completed'),
(264, 'TR-1760382733-8576', 532, 25, 'import', '2025-10-13 21:12:13', 16, 'Beérkezés - Ütemezett szállítás - D épület - GHI-789 rendszám', '2025-10-15', 31, 'completed'),
(265, 'TR-1760474795-DE0C', 468, 25, 'export', '2025-10-14 22:46:35', 16, 'Kiszállítás - Raktárak közötti átmozgatás - D épület - FGH-890 rendszám', '2025-10-17', 31, 'completed'),
(266, 'TR-1760474795-DE0C', 468, 22, 'import', '2025-10-14 22:46:35', 16, 'Beérkezés - Raktárak közötti átmozgatás - D épület - FGH-890 rendszám', '2025-10-17', 31, 'completed'),
(267, 'TR-1760494211-F122', 599, 26, 'export', '2025-10-15 04:10:11', 16, 'Kiszállítás - Ütemezett szállítás - F épület - EFG-123 rendszám', '2025-10-18', 9, 'completed'),
(268, 'TR-1760494211-F122', 599, 24, 'import', '2025-10-15 04:10:11', 16, 'Beérkezés - Ütemezett szállítás - F épület - EFG-123 rendszám', '2025-10-18', 9, 'completed'),
(269, 'TR-1760606751-7097', 485, 22, 'export', '2025-10-16 11:25:51', 16, 'Kiszállítás - Heti készlet feltöltés - B épület - NOP-012 rendszám', '2025-10-21', 33, 'completed'),
(270, 'TR-1760606751-7097', 485, 23, 'import', '2025-10-16 11:25:51', 16, 'Beérkezés - Heti készlet feltöltés - B épület - NOP-012 rendszám', '2025-10-21', 33, 'completed'),
(271, 'TR-1760717748-47ED', 522, 21, 'export', '2025-10-17 18:15:48', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - TUV-678 rendszám', '2025-10-22', 36, 'completed'),
(272, 'TR-1760717748-47ED', 522, 28, 'import', '2025-10-17 18:15:48', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - TUV-678 rendszám', '2025-10-22', 36, 'completed'),
(273, 'TR-1760742163-B701', 503, 30, 'export', '2025-10-18 01:02:43', 16, 'Kiszállítás - Sürgős utánpótlás - C épület - KLM-789 rendszám', '2025-10-25', 14, 'completed'),
(274, 'TR-1760742163-B701', 503, 24, 'import', '2025-10-18 01:02:43', 16, 'Beérkezés - Sürgős utánpótlás - C épület - KLM-789 rendszám', '2025-10-25', 14, 'completed'),
(275, 'TR-1760902437-8305', 503, 25, 'export', '2025-10-19 21:33:57', 16, 'Kiszállítás - Ütemezett szállítás - C épület - ZAB-234 rendszám', '2025-10-21', 10, 'completed'),
(276, 'TR-1760902437-8305', 503, 26, 'import', '2025-10-19 21:33:57', 16, 'Beérkezés - Ütemezett szállítás - C épület - ZAB-234 rendszám', '2025-10-21', 10, 'completed'),
(277, 'TR-1760930329-5317', 514, 28, 'export', '2025-10-20 05:18:49', 16, 'Kiszállítás - Rendkívüli szállítmány - G épület - EFG-123 rendszám', '2025-10-25', 5, 'completed'),
(278, 'TR-1760930329-5317', 514, 22, 'import', '2025-10-20 05:18:49', 16, 'Beérkezés - Rendkívüli szállítmány - G épület - EFG-123 rendszám', '2025-10-25', 5, 'completed'),
(279, 'TR-1761008916-8316', 590, 22, 'export', '2025-10-21 03:08:36', 16, 'Kiszállítás - Heti készlet feltöltés - A épület - BCD-890 rendszám', '2025-10-27', 41, 'completed'),
(280, 'TR-1761008916-8316', 590, 28, 'import', '2025-10-21 03:08:36', 16, 'Beérkezés - Heti készlet feltöltés - A épület - BCD-890 rendszám', '2025-10-27', 41, 'completed'),
(281, 'TR-1761092555-56FD', 544, 22, 'export', '2025-10-22 02:22:35', 16, 'Kiszállítás - Ütemezett szállítás - C épület - EFG-123 rendszám', '2025-10-25', 12, 'completed'),
(282, 'TR-1761092555-56FD', 544, 21, 'import', '2025-10-22 02:22:35', 16, 'Beérkezés - Ütemezett szállítás - C épület - EFG-123 rendszám', '2025-10-25', 12, 'completed'),
(283, 'TR-1761242002-364A', 509, 23, 'export', '2025-10-23 19:53:22', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - WXY-901 rendszám', '2025-10-26', 38, 'completed'),
(284, 'TR-1761242002-364A', 509, 25, 'import', '2025-10-23 19:53:22', 16, 'Beérkezés - Heti készlet feltöltés - H épület - WXY-901 rendszám', '2025-10-26', 38, 'completed'),
(285, 'TR-1761329737-AC2B', 530, 27, 'export', '2025-10-24 20:15:37', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - CDE-567 rendszám', '2025-10-31', 37, 'completed'),
(286, 'TR-1761329737-AC2B', 530, 29, 'import', '2025-10-24 20:15:37', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - CDE-567 rendszám', '2025-10-31', 37, 'completed'),
(287, 'TR-1761375571-BE9C', 501, 24, 'export', '2025-10-25 08:59:31', 16, 'Kiszállítás - Raktárak közötti átmozgatás - H épület - ABC-123 rendszám', '2025-10-27', 47, 'completed'),
(288, 'TR-1761375571-BE9C', 501, 23, 'import', '2025-10-25 08:59:31', 16, 'Beérkezés - Raktárak közötti átmozgatás - H épület - ABC-123 rendszám', '2025-10-27', 47, 'completed'),
(289, 'TR-1761467808-EBE3', 596, 23, 'export', '2025-10-26 09:36:48', 16, 'Kiszállítás - Rendkívüli szállítmány - B épület - ZAB-234 rendszám', '2025-11-01', 23, 'completed'),
(290, 'TR-1761467808-EBE3', 596, 29, 'import', '2025-10-26 09:36:48', 16, 'Beérkezés - Rendkívüli szállítmány - B épület - ZAB-234 rendszám', '2025-11-01', 23, 'completed'),
(291, 'TR-1761590091-2A5C', 495, 27, 'export', '2025-10-27 19:34:51', 16, 'Kiszállítás - Ütemezett szállítás - G épület - DEF-456 rendszám', '2025-10-30', 13, 'completed'),
(292, 'TR-1761590091-2A5C', 495, 28, 'import', '2025-10-27 19:34:51', 16, 'Beérkezés - Ütemezett szállítás - G épület - DEF-456 rendszám', '2025-10-30', 13, 'completed'),
(293, 'TR-1761666187-8922', 597, 23, 'export', '2025-10-28 16:43:07', 16, 'Kiszállítás - Raktárak közötti átmozgatás - D épület - PQR-678 rendszám', '2025-10-30', 44, 'completed'),
(294, 'TR-1761666187-8922', 597, 31, 'import', '2025-10-28 16:43:07', 16, 'Beérkezés - Raktárak közötti átmozgatás - D épület - PQR-678 rendszám', '2025-10-30', 44, 'completed'),
(295, 'TR-1761712108-CF72', 537, 26, 'export', '2025-10-29 05:28:28', 16, 'Kiszállítás - Ütemezett szállítás - C épület - EFG-123 rendszám', '2025-11-03', 20, 'completed'),
(296, 'TR-1761712108-CF72', 537, 28, 'import', '2025-10-29 05:28:28', 16, 'Beérkezés - Ütemezett szállítás - C épület - EFG-123 rendszám', '2025-11-03', 20, 'completed'),
(297, 'TR-1761792969-ED2E', 527, 27, 'export', '2025-10-30 03:56:09', 16, 'Kiszállítás - Ütemezett szállítás - G épület - QRS-345 rendszám', '2025-11-03', 2, 'completed'),
(298, 'TR-1761792969-ED2E', 527, 30, 'import', '2025-10-30 03:56:09', 16, 'Beérkezés - Ütemezett szállítás - G épület - QRS-345 rendszám', '2025-11-03', 2, 'completed'),
(299, 'TR-1761923297-4DBC', 589, 23, 'export', '2025-10-31 16:08:17', 16, 'Kiszállítás - Heti készlet feltöltés - C épület - KLM-789 rendszám', '2025-11-07', 43, 'completed'),
(300, 'TR-1761923297-4DBC', 589, 24, 'import', '2025-10-31 16:08:17', 16, 'Beérkezés - Heti készlet feltöltés - C épület - KLM-789 rendszám', '2025-11-07', 43, 'completed'),
(301, 'TR-1762032776-319D', 491, 26, 'export', '2025-11-01 22:32:56', 16, 'Kiszállítás - Heti készlet feltöltés - A épület - ABC-123 rendszám', '2025-11-06', 23, 'completed'),
(302, 'TR-1762032776-319D', 491, 21, 'import', '2025-11-01 22:32:56', 16, 'Beérkezés - Heti készlet feltöltés - A épület - ABC-123 rendszám', '2025-11-06', 23, 'completed'),
(303, 'TR-1762057146-86A5', 551, 25, 'export', '2025-11-02 05:19:06', 16, 'Kiszállítás - Sürgős utánpótlás - F épület - STU-901 rendszám', '2025-11-09', 42, 'completed'),
(304, 'TR-1762057146-86A5', 551, 26, 'import', '2025-11-02 05:19:06', 16, 'Beérkezés - Sürgős utánpótlás - F épület - STU-901 rendszám', '2025-11-09', 42, 'completed'),
(305, 'TR-1762127511-C845', 491, 27, 'export', '2025-11-03 00:51:51', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - CDE-567 rendszám', '2025-11-06', 31, 'completed'),
(306, 'TR-1762127511-C845', 491, 29, 'import', '2025-11-03 00:51:51', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - CDE-567 rendszám', '2025-11-06', 31, 'completed'),
(307, 'TR-1762235840-EAED', 598, 21, 'export', '2025-11-04 06:57:20', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - ZAB-234 rendszám', '2025-11-09', 27, 'completed'),
(308, 'TR-1762235840-EAED', 598, 25, 'import', '2025-11-04 06:57:20', 16, 'Beérkezés - Heti készlet feltöltés - H épület - ZAB-234 rendszám', '2025-11-09', 27, 'completed'),
(309, 'TR-1762360302-5858', 554, 22, 'export', '2025-11-05 17:31:42', 16, 'Kiszállítás - Sürgős utánpótlás - E épület - FGH-890 rendszám', '2025-11-08', 39, 'completed'),
(310, 'TR-1762360302-5858', 554, 30, 'import', '2025-11-05 17:31:42', 16, 'Beérkezés - Sürgős utánpótlás - E épület - FGH-890 rendszám', '2025-11-08', 39, 'completed'),
(311, 'TR-1762409350-353A', 567, 29, 'export', '2025-11-06 07:09:10', 16, 'Kiszállítás - Heti készlet feltöltés - B épület - QRS-345 rendszám', '2025-11-08', 23, 'completed'),
(312, 'TR-1762409350-353A', 567, 24, 'import', '2025-11-06 07:09:10', 16, 'Beérkezés - Heti készlet feltöltés - B épület - QRS-345 rendszám', '2025-11-08', 23, 'completed'),
(313, 'TR-1762501905-EA0E', 477, 25, 'export', '2025-11-07 08:51:45', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - FGH-890 rendszám', '2025-11-13', 32, 'completed'),
(314, 'TR-1762501905-EA0E', 477, 28, 'import', '2025-11-07 08:51:45', 16, 'Beérkezés - Heti készlet feltöltés - G épület - FGH-890 rendszám', '2025-11-13', 32, 'completed'),
(315, 'TR-1762624076-D014', 599, 24, 'export', '2025-11-08 18:47:56', 16, 'Kiszállítás - Heti készlet feltöltés - D épület - VWX-234 rendszám', '2025-11-14', 7, 'completed'),
(316, 'TR-1762624076-D014', 599, 21, 'import', '2025-11-08 18:47:56', 16, 'Beérkezés - Heti készlet feltöltés - D épület - VWX-234 rendszám', '2025-11-14', 7, 'completed'),
(317, 'TR-1762644971-AD6E', 507, 29, 'export', '2025-11-09 00:36:11', 16, 'Kiszállítás - Rendkívüli szállítmány - C épület - JKL-012 rendszám', '2025-11-16', 20, 'completed'),
(318, 'TR-1762644971-AD6E', 507, 30, 'import', '2025-11-09 00:36:11', 16, 'Beérkezés - Rendkívüli szállítmány - C épület - JKL-012 rendszám', '2025-11-16', 20, 'completed'),
(319, 'TR-1762812154-0CE2', 521, 29, 'export', '2025-11-10 23:02:34', 16, 'Kiszállítás - Raktárak közötti átmozgatás - A épület - PQR-678 rendszám', '2025-11-13', 5, 'completed'),
(320, 'TR-1762812154-0CE2', 521, 27, 'import', '2025-11-10 23:02:34', 16, 'Beérkezés - Raktárak közötti átmozgatás - A épület - PQR-678 rendszám', '2025-11-13', 5, 'completed'),
(321, 'TR-1762844267-649A', 512, 22, 'export', '2025-11-11 07:57:47', 16, 'Kiszállítás - Ütemezett szállítás - H épület - JKL-012 rendszám', '2025-11-13', 24, 'completed'),
(322, 'TR-1762844267-649A', 512, 29, 'import', '2025-11-11 07:57:47', 16, 'Beérkezés - Ütemezett szállítás - H épület - JKL-012 rendszám', '2025-11-13', 24, 'completed'),
(323, 'TR-1762904221-FB39', 552, 30, 'export', '2025-11-12 00:37:01', 16, 'Kiszállítás - Sürgős utánpótlás - D épület - GHI-789 rendszám', '2025-11-15', 45, 'completed'),
(324, 'TR-1762904221-FB39', 552, 28, 'import', '2025-11-12 00:37:01', 16, 'Beérkezés - Sürgős utánpótlás - D épület - GHI-789 rendszám', '2025-11-15', 45, 'completed'),
(325, 'TR-1763060860-5DB7', 486, 22, 'export', '2025-11-13 20:07:40', 16, 'Kiszállítás - Ütemezett szállítás - G épület - ABC-123 rendszám', '2025-11-17', 9, 'completed'),
(326, 'TR-1763060860-5DB7', 486, 23, 'import', '2025-11-13 20:07:40', 16, 'Beérkezés - Ütemezett szállítás - G épület - ABC-123 rendszám', '2025-11-17', 9, 'completed'),
(327, 'TR-1763136127-63A9', 581, 28, 'export', '2025-11-14 17:02:07', 16, 'Kiszállítás - Rendkívüli szállítmány - G épület - PQR-678 rendszám', '2025-11-16', 1, 'completed'),
(328, 'TR-1763136127-63A9', 581, 22, 'import', '2025-11-14 17:02:07', 16, 'Beérkezés - Rendkívüli szállítmány - G épület - PQR-678 rendszám', '2025-11-16', 1, 'completed'),
(329, 'TR-1763196739-C777', 592, 28, 'export', '2025-11-15 09:52:19', 16, 'Kiszállítás - Sürgős utánpótlás - B épület - ZAB-234 rendszám', '2025-11-22', 31, 'completed'),
(330, 'TR-1763196739-C777', 592, 25, 'import', '2025-11-15 09:52:19', 16, 'Beérkezés - Sürgős utánpótlás - B épület - ZAB-234 rendszám', '2025-11-22', 31, 'completed'),
(331, 'TR-1763285404-A68D', 538, 21, 'export', '2025-11-16 10:30:04', 16, 'Kiszállítás - Ütemezett szállítás - G épület - PQR-678 rendszám', '2025-11-19', 15, 'completed'),
(332, 'TR-1763285404-A68D', 538, 28, 'import', '2025-11-16 10:30:04', 16, 'Beérkezés - Ütemezett szállítás - G épület - PQR-678 rendszám', '2025-11-19', 15, 'completed'),
(333, 'TR-1763386349-D83A', 574, 21, 'export', '2025-11-17 14:32:29', 16, 'Kiszállítás - Raktárak közötti átmozgatás - H épület - VWX-234 rendszám', '2025-11-19', 27, 'completed'),
(334, 'TR-1763386349-D83A', 574, 26, 'import', '2025-11-17 14:32:29', 16, 'Beérkezés - Raktárak közötti átmozgatás - H épület - VWX-234 rendszám', '2025-11-19', 27, 'completed'),
(335, 'TR-1763460815-F88D', 583, 30, 'export', '2025-11-18 11:13:35', 16, 'Kiszállítás - Ütemezett szállítás - H épület - ZAB-234 rendszám', '2025-11-20', 43, 'completed'),
(336, 'TR-1763460815-F88D', 583, 29, 'import', '2025-11-18 11:13:35', 16, 'Beérkezés - Ütemezett szállítás - H épület - ZAB-234 rendszám', '2025-11-20', 43, 'completed'),
(337, 'TR-1763544886-A0D8', 571, 23, 'export', '2025-11-19 10:34:46', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - QRS-345 rendszám', '2025-11-25', 25, 'completed'),
(338, 'TR-1763544886-A0D8', 571, 28, 'import', '2025-11-19 10:34:46', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - QRS-345 rendszám', '2025-11-25', 25, 'completed'),
(339, 'TR-1763638652-FB8D', 592, 22, 'export', '2025-11-20 12:37:32', 16, 'Kiszállítás - Sürgős utánpótlás - H épület - HIJ-456 rendszám', '2025-11-22', 18, 'completed'),
(340, 'TR-1763638652-FB8D', 592, 24, 'import', '2025-11-20 12:37:32', 16, 'Beérkezés - Sürgős utánpótlás - H épület - HIJ-456 rendszám', '2025-11-22', 18, 'completed'),
(341, 'TR-1763690395-1585', 478, 27, 'export', '2025-11-21 02:59:55', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - NOP-012 rendszám', '2025-11-25', 21, 'completed'),
(342, 'TR-1763690395-1585', 478, 22, 'import', '2025-11-21 02:59:55', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - NOP-012 rendszám', '2025-11-25', 21, 'completed'),
(343, 'TR-1763791463-F098', 494, 28, 'export', '2025-11-22 07:04:23', 16, 'Kiszállítás - Ütemezett szállítás - A épület - JKL-012 rendszám', '2025-11-27', 29, 'completed'),
(344, 'TR-1763791463-F098', 494, 23, 'import', '2025-11-22 07:04:23', 16, 'Beérkezés - Ütemezett szállítás - A épület - JKL-012 rendszám', '2025-11-27', 29, 'completed'),
(345, 'TR-1763904197-B581', 555, 21, 'export', '2025-11-23 14:23:17', 16, 'Kiszállítás - Heti készlet feltöltés - D épület - NOP-012 rendszám', '2025-11-25', 46, 'completed'),
(346, 'TR-1763904197-B581', 555, 30, 'import', '2025-11-23 14:23:17', 16, 'Beérkezés - Heti készlet feltöltés - D épület - NOP-012 rendszám', '2025-11-25', 46, 'completed'),
(347, 'TR-1763942977-C9EE', 481, 27, 'export', '2025-11-24 01:09:37', 16, 'Kiszállítás - Sürgős utánpótlás - D épület - JKL-012 rendszám', '2025-11-26', 45, 'completed'),
(348, 'TR-1763942977-C9EE', 481, 23, 'import', '2025-11-24 01:09:37', 16, 'Beérkezés - Sürgős utánpótlás - D épület - JKL-012 rendszám', '2025-11-26', 45, 'completed'),
(349, 'TR-1764051984-AE40', 517, 29, 'export', '2025-11-25 07:26:24', 16, 'Kiszállítás - Rendkívüli szállítmány - E épület - KLM-789 rendszám', '2025-11-28', 12, 'completed'),
(350, 'TR-1764051984-AE40', 517, 22, 'import', '2025-11-25 07:26:24', 16, 'Beérkezés - Rendkívüli szállítmány - E épület - KLM-789 rendszám', '2025-11-28', 12, 'completed'),
(351, 'TR-1764174557-E837', 578, 26, 'export', '2025-11-26 17:29:17', 16, 'Kiszállítás - Sürgős utánpótlás - F épület - ZAB-234 rendszám', '2025-12-02', 45, 'completed'),
(352, 'TR-1764174557-E837', 578, 23, 'import', '2025-11-26 17:29:17', 16, 'Beérkezés - Sürgős utánpótlás - F épület - ZAB-234 rendszám', '2025-12-02', 45, 'completed'),
(353, 'TR-1764271233-8038', 522, 24, 'export', '2025-11-27 20:20:33', 16, 'Kiszállítás - Heti készlet feltöltés - A épület - QRS-345 rendszám', '2025-12-04', 30, 'completed'),
(354, 'TR-1764271233-8038', 522, 29, 'import', '2025-11-27 20:20:33', 16, 'Beérkezés - Heti készlet feltöltés - A épület - QRS-345 rendszám', '2025-12-04', 30, 'completed'),
(355, 'TR-1764313047-04EE', 591, 29, 'export', '2025-11-28 07:57:27', 16, 'Kiszállítás - Sürgős utánpótlás - C épület - HIJ-456 rendszám', '2025-12-02', 46, 'completed'),
(356, 'TR-1764313047-04EE', 591, 24, 'import', '2025-11-28 07:57:27', 16, 'Beérkezés - Sürgős utánpótlás - C épület - HIJ-456 rendszám', '2025-12-02', 46, 'completed'),
(357, 'TR-1764453677-1D5B', 478, 24, 'export', '2025-11-29 23:01:17', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - NOP-012 rendszám', '2025-12-03', 50, 'completed'),
(358, 'TR-1764453677-1D5B', 478, 21, 'import', '2025-11-29 23:01:17', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - NOP-012 rendszám', '2025-12-03', 50, 'completed'),
(359, 'TR-1764536222-826D', 478, 24, 'export', '2025-11-30 21:57:02', 16, 'Kiszállítás - Heti készlet feltöltés - F épület - VWX-234 rendszám', '2025-12-07', 49, 'completed'),
(360, 'TR-1764536222-826D', 478, 30, 'import', '2025-11-30 21:57:02', 16, 'Beérkezés - Heti készlet feltöltés - F épület - VWX-234 rendszám', '2025-12-07', 49, 'completed'),
(361, 'TR-1764613383-305F', 566, 25, 'export', '2025-12-01 19:23:03', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - GHI-789 rendszám', '2025-12-06', 4, 'completed'),
(362, 'TR-1764613383-305F', 566, 31, 'import', '2025-12-01 19:23:03', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - GHI-789 rendszám', '2025-12-06', 4, 'completed'),
(363, 'TR-1764667124-2B50', 487, 24, 'export', '2025-12-02 10:18:44', 16, 'Kiszállítás - Sürgős utánpótlás - A épület - ZAB-234 rendszám', '2025-12-07', 2, 'completed'),
(364, 'TR-1764667124-2B50', 487, 27, 'import', '2025-12-02 10:18:44', 16, 'Beérkezés - Sürgős utánpótlás - A épület - ZAB-234 rendszám', '2025-12-07', 2, 'completed'),
(365, 'TR-1764766103-F6C0', 538, 28, 'export', '2025-12-03 13:48:23', 16, 'Kiszállítás - Heti készlet feltöltés - B épület - STU-901 rendszám', '2025-12-08', 11, 'completed'),
(366, 'TR-1764766103-F6C0', 538, 21, 'import', '2025-12-03 13:48:23', 16, 'Beérkezés - Heti készlet feltöltés - B épület - STU-901 rendszám', '2025-12-08', 11, 'completed'),
(367, 'TR-1764809877-3502', 540, 25, 'export', '2025-12-04 01:57:57', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - GHI-789 rendszám', '2025-12-11', 10, 'completed'),
(368, 'TR-1764809877-3502', 540, 27, 'import', '2025-12-04 01:57:57', 16, 'Beérkezés - Heti készlet feltöltés - H épület - GHI-789 rendszám', '2025-12-11', 10, 'completed'),
(369, 'TR-1764956873-BC43', 573, 23, 'export', '2025-12-05 18:47:53', 16, 'Kiszállítás - Sürgős utánpótlás - F épület - JKL-012 rendszám', '2025-12-10', 4, 'completed'),
(370, 'TR-1764956873-BC43', 573, 31, 'import', '2025-12-05 18:47:53', 16, 'Beérkezés - Sürgős utánpótlás - F épület - JKL-012 rendszám', '2025-12-10', 4, 'completed'),
(371, 'TR-1765026753-6573', 557, 28, 'export', '2025-12-06 14:12:33', 16, 'Kiszállítás - Sürgős utánpótlás - A épület - MNO-345 rendszám', '2025-12-13', 32, 'completed'),
(372, 'TR-1765026753-6573', 557, 27, 'import', '2025-12-06 14:12:33', 16, 'Beérkezés - Sürgős utánpótlás - A épület - MNO-345 rendszám', '2025-12-13', 32, 'completed'),
(373, 'TR-1765084292-532F', 555, 24, 'export', '2025-12-07 06:11:32', 15, 'Kiszállítás - Rendkívüli szállítmány - B épület - JKL-012 rendszám', '2025-12-10', 3, 'completed'),
(374, 'TR-1765084292-532F', 555, 26, 'import', '2025-12-07 06:11:32', 15, 'Beérkezés - Rendkívüli szállítmány - B épület - JKL-012 rendszám', '2025-12-10', 3, 'completed'),
(375, 'TR-1765228485-336E', 549, 26, 'export', '2025-12-08 22:14:45', 16, 'Kiszállítás - Ütemezett szállítás - D épület - TUV-678 rendszám', '2025-12-14', 16, 'completed'),
(376, 'TR-1765228485-336E', 549, 23, 'import', '2025-12-08 22:14:45', 16, 'Beérkezés - Ütemezett szállítás - D épület - TUV-678 rendszám', '2025-12-14', 16, 'completed'),
(377, 'TR-1765254122-2FEF', 570, 25, 'export', '2025-12-09 05:22:02', 16, 'Kiszállítás - Rendkívüli szállítmány - B épület - WXY-901 rendszám', '2025-12-13', 4, 'completed'),
(378, 'TR-1765254122-2FEF', 570, 29, 'import', '2025-12-09 05:22:02', 16, 'Beérkezés - Rendkívüli szállítmány - B épület - WXY-901 rendszám', '2025-12-13', 4, 'completed'),
(379, 'TR-1765345261-0321', 586, 30, 'export', '2025-12-10 06:41:01', 16, 'Kiszállítás - Ütemezett szállítás - C épület - MNO-345 rendszám', '2025-12-13', 35, 'completed'),
(380, 'TR-1765345261-0321', 586, 23, 'import', '2025-12-10 06:41:01', 16, 'Beérkezés - Ütemezett szállítás - C épület - MNO-345 rendszám', '2025-12-13', 35, 'completed'),
(381, 'TR-1765474356-4893', 541, 30, 'export', '2025-12-11 18:32:36', 15, 'Kiszállítás - Raktárak közötti átmozgatás - F épület - PQR-678 rendszám', '2025-12-13', 26, 'completed');
INSERT INTO `transports` (`ID`, `batch_id`, `product_ID`, `warehouse_ID`, `type`, `date`, `user_ID`, `description`, `arriveIn`, `quantity`, `status`) VALUES
(382, 'TR-1765474356-4893', 541, 29, 'import', '2025-12-11 18:32:36', 15, 'Beérkezés - Raktárak közötti átmozgatás - F épület - PQR-678 rendszám', '2025-12-13', 26, 'completed'),
(383, 'TR-1765568445-1EAC', 515, 26, 'export', '2025-12-12 20:40:45', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - ABC-123 rendszám', '2025-12-17', 35, 'completed'),
(384, 'TR-1765568445-1EAC', 515, 31, 'import', '2025-12-12 20:40:45', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - ABC-123 rendszám', '2025-12-17', 35, 'completed'),
(385, 'TR-1765600718-1A0A', 562, 26, 'export', '2025-12-13 05:38:38', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - ABC-123 rendszám', '2025-12-17', 35, 'completed'),
(386, 'TR-1765600718-1A0A', 562, 29, 'import', '2025-12-13 05:38:38', 16, 'Beérkezés - Heti készlet feltöltés - G épület - ABC-123 rendszám', '2025-12-17', 35, 'completed'),
(387, 'TR-1765725402-2845', 560, 22, 'export', '2025-12-14 16:16:42', 16, 'Kiszállítás - Heti készlet feltöltés - F épület - EFG-123 rendszám', '2025-12-17', 14, 'completed'),
(388, 'TR-1765725402-2845', 560, 29, 'import', '2025-12-14 16:16:42', 16, 'Beérkezés - Heti készlet feltöltés - F épület - EFG-123 rendszám', '2025-12-17', 14, 'completed'),
(389, 'TR-1765796160-9532', 592, 21, 'export', '2025-12-15 11:56:00', 16, 'Kiszállítás - Raktárak közötti átmozgatás - D épület - KLM-789 rendszám', '2025-12-17', 30, 'completed'),
(390, 'TR-1765796160-9532', 592, 25, 'import', '2025-12-15 11:56:00', 16, 'Beérkezés - Raktárak közötti átmozgatás - D épület - KLM-789 rendszám', '2025-12-17', 30, 'completed'),
(391, 'TR-1765843686-2AA3', 565, 24, 'export', '2025-12-16 01:08:06', 16, 'Kiszállítás - Raktárak közötti átmozgatás - A épület - NOP-012 rendszám', '2025-12-22', 35, 'completed'),
(392, 'TR-1765843686-2AA3', 565, 29, 'import', '2025-12-16 01:08:06', 16, 'Beérkezés - Raktárak közötti átmozgatás - A épület - NOP-012 rendszám', '2025-12-22', 35, 'completed'),
(393, 'TR-1765926961-6E55', 595, 24, 'export', '2025-12-17 00:16:01', 16, 'Kiszállítás - Sürgős utánpótlás - D épület - KLM-789 rendszám', '2025-12-23', 1, 'completed'),
(394, 'TR-1765926961-6E55', 595, 29, 'import', '2025-12-17 00:16:01', 16, 'Beérkezés - Sürgős utánpótlás - D épület - KLM-789 rendszám', '2025-12-23', 1, 'completed'),
(395, 'TR-1766063329-C835', 489, 28, 'export', '2025-12-18 14:08:49', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - STU-901 rendszám', '2025-12-20', 47, 'completed'),
(396, 'TR-1766063329-C835', 489, 22, 'import', '2025-12-18 14:08:49', 16, 'Beérkezés - Heti készlet feltöltés - G épület - STU-901 rendszám', '2025-12-20', 47, 'completed'),
(397, 'TR-1766172084-B58A', 554, 30, 'export', '2025-12-19 20:21:24', 16, 'Kiszállítás - Heti készlet feltöltés - G épület - FGH-890 rendszám', '2025-12-21', 4, 'completed'),
(398, 'TR-1766172084-B58A', 554, 29, 'import', '2025-12-19 20:21:24', 16, 'Beérkezés - Heti készlet feltöltés - G épület - FGH-890 rendszám', '2025-12-21', 4, 'completed'),
(399, 'TR-1766201996-C068', 471, 23, 'export', '2025-12-20 04:39:56', 16, 'Kiszállítás - Heti készlet feltöltés - D épület - YZA-567 rendszám', '2025-12-25', 19, 'completed'),
(400, 'TR-1766201996-C068', 471, 25, 'import', '2025-12-20 04:39:56', 16, 'Beérkezés - Heti készlet feltöltés - D épület - YZA-567 rendszám', '2025-12-25', 19, 'completed'),
(401, 'TR-1766327049-A44B', 588, 22, 'export', '2025-12-21 15:24:09', 16, 'Kiszállítás - Rendkívüli szállítmány - G épület - MNO-345 rendszám', '2025-12-24', 40, 'completed'),
(402, 'TR-1766327049-A44B', 588, 27, 'import', '2025-12-21 15:24:09', 16, 'Beérkezés - Rendkívüli szállítmány - G épület - MNO-345 rendszám', '2025-12-24', 40, 'completed'),
(403, 'TR-1766401157-63F7', 478, 21, 'export', '2025-12-22 11:59:17', 16, 'Kiszállítás - Ütemezett szállítás - G épület - FGH-890 rendszám', '2025-12-25', 31, 'completed'),
(404, 'TR-1766401157-63F7', 478, 25, 'import', '2025-12-22 11:59:17', 16, 'Beérkezés - Ütemezett szállítás - G épület - FGH-890 rendszám', '2025-12-25', 31, 'completed'),
(405, 'TR-1766459165-0E9F', 508, 27, 'export', '2025-12-23 04:06:05', 16, 'Kiszállítás - Heti készlet feltöltés - D épület - GHI-789 rendszám', '2025-12-26', 13, 'completed'),
(406, 'TR-1766459165-0E9F', 508, 28, 'import', '2025-12-23 04:06:05', 16, 'Beérkezés - Heti készlet feltöltés - D épület - GHI-789 rendszám', '2025-12-26', 13, 'completed'),
(407, 'TR-1766607183-FD90', 583, 29, 'export', '2025-12-24 21:13:03', 16, 'Kiszállítás - Rendkívüli szállítmány - B épület - PQR-678 rendszám', '2025-12-27', 10, 'completed'),
(408, 'TR-1766607183-FD90', 583, 26, 'import', '2025-12-24 21:13:03', 16, 'Beérkezés - Rendkívüli szállítmány - B épület - PQR-678 rendszám', '2025-12-27', 10, 'completed'),
(409, 'TR-1766667831-C306', 539, 22, 'export', '2025-12-25 14:03:51', 16, 'Kiszállítás - Ütemezett szállítás - B épület - QRS-345 rendszám', '2025-12-28', 10, 'completed'),
(410, 'TR-1766667831-C306', 539, 28, 'import', '2025-12-25 14:03:51', 16, 'Beérkezés - Ütemezett szállítás - B épület - QRS-345 rendszám', '2025-12-28', 10, 'completed'),
(411, 'TR-1766758474-E707', 532, 24, 'export', '2025-12-26 15:14:34', 16, 'Kiszállítás - Ütemezett szállítás - C épület - KLM-789 rendszám', '2025-12-31', 20, 'completed'),
(412, 'TR-1766758474-E707', 532, 31, 'import', '2025-12-26 15:14:34', 16, 'Beérkezés - Ütemezett szállítás - C épület - KLM-789 rendszám', '2025-12-31', 20, 'completed'),
(413, 'TR-1766817216-6C1C', 528, 25, 'export', '2025-12-27 07:33:36', 16, 'Kiszállítás - Sürgős utánpótlás - B épület - WXY-901 rendszám', '2026-01-03', 23, 'completed'),
(414, 'TR-1766817216-6C1C', 528, 21, 'import', '2025-12-27 07:33:36', 16, 'Beérkezés - Sürgős utánpótlás - B épület - WXY-901 rendszám', '2026-01-03', 23, 'completed'),
(415, 'TR-1766919494-B232', 575, 26, 'export', '2025-12-28 11:58:14', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - NOP-012 rendszám', '2026-01-02', 35, 'completed'),
(416, 'TR-1766919494-B232', 575, 30, 'import', '2025-12-28 11:58:14', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - NOP-012 rendszám', '2026-01-02', 35, 'completed'),
(417, 'TR-1766993474-E74D', 468, 24, 'export', '2025-12-29 08:31:14', 16, 'Kiszállítás - Raktárak közötti átmozgatás - H épület - NOP-012 rendszám', '2025-12-31', 31, 'completed'),
(418, 'TR-1766993474-E74D', 468, 23, 'import', '2025-12-29 08:31:14', 16, 'Beérkezés - Raktárak közötti átmozgatás - H épület - NOP-012 rendszám', '2025-12-31', 31, 'completed'),
(419, 'TR-1767091330-C045', 597, 27, 'export', '2025-12-30 11:42:10', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - NOP-012 rendszám', '2026-01-01', 40, 'completed'),
(420, 'TR-1767091330-C045', 597, 29, 'import', '2025-12-30 11:42:10', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - NOP-012 rendszám', '2026-01-01', 40, 'completed'),
(421, 'TR-1767196943-C9F2', 582, 27, 'export', '2025-12-31 17:02:23', 15, 'Kiszállítás - Ütemezett szállítás - C épület - PQR-678 rendszám', '2026-01-07', 23, 'completed'),
(422, 'TR-1767196943-C9F2', 582, 21, 'import', '2025-12-31 17:02:23', 15, 'Beérkezés - Ütemezett szállítás - C épület - PQR-678 rendszám', '2026-01-07', 23, 'completed'),
(423, 'TR-1767272759-745C', 473, 30, 'export', '2026-01-01 14:05:59', 16, 'Kiszállítás - Heti készlet feltöltés - B épület - STU-901 rendszám', '2026-01-04', 46, 'completed'),
(424, 'TR-1767272759-745C', 473, 24, 'import', '2026-01-01 14:05:59', 16, 'Beérkezés - Heti készlet feltöltés - B épület - STU-901 rendszám', '2026-01-04', 46, 'completed'),
(425, 'TR-1767364562-BF23', 568, 22, 'export', '2026-01-02 15:36:02', 16, 'Kiszállítás - Ütemezett szállítás - E épület - STU-901 rendszám', '2026-01-04', 42, 'completed'),
(426, 'TR-1767364562-BF23', 568, 30, 'import', '2026-01-02 15:36:02', 16, 'Beérkezés - Ütemezett szállítás - E épület - STU-901 rendszám', '2026-01-04', 42, 'completed'),
(427, 'TR-1767405901-04BA', 555, 27, 'export', '2026-01-03 03:05:01', 16, 'Kiszállítás - Ütemezett szállítás - C épület - PQR-678 rendszám', '2026-01-09', 50, 'completed'),
(428, 'TR-1767405901-04BA', 555, 23, 'import', '2026-01-03 03:05:01', 16, 'Beérkezés - Ütemezett szállítás - C épület - PQR-678 rendszám', '2026-01-09', 50, 'completed'),
(429, 'TR-1767513821-05F8', 510, 29, 'export', '2026-01-04 09:03:41', 16, 'Kiszállítás - Heti készlet feltöltés - B épület - STU-901 rendszám', '2026-01-07', 10, 'completed'),
(430, 'TR-1767513821-05F8', 510, 25, 'import', '2026-01-04 09:03:41', 16, 'Beérkezés - Heti készlet feltöltés - B épület - STU-901 rendszám', '2026-01-07', 10, 'completed'),
(431, 'TR-1767573473-03C3', 477, 24, 'export', '2026-01-05 01:37:53', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - TUV-678 rendszám', '2026-01-12', 4, 'completed'),
(432, 'TR-1767573473-03C3', 477, 30, 'import', '2026-01-05 01:37:53', 16, 'Beérkezés - Heti készlet feltöltés - H épület - TUV-678 rendszám', '2026-01-12', 4, 'completed'),
(433, 'TR-1767674209-F595', 476, 29, 'export', '2026-01-06 05:36:49', 16, 'Kiszállítás - Raktárak közötti átmozgatás - F épület - PQR-678 rendszám', '2026-01-11', 29, 'completed'),
(434, 'TR-1767674209-F595', 476, 21, 'import', '2026-01-06 05:36:49', 16, 'Beérkezés - Raktárak közötti átmozgatás - F épület - PQR-678 rendszám', '2026-01-11', 29, 'completed'),
(435, 'TR-1767801849-3CE7', 502, 28, 'export', '2026-01-07 17:04:09', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - VWX-234 rendszám', '2026-01-14', 2, 'completed'),
(436, 'TR-1767801849-3CE7', 502, 31, 'import', '2026-01-07 17:04:09', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - VWX-234 rendszám', '2026-01-14', 2, 'completed'),
(437, 'TR-1767868345-D7DC', 591, 22, 'export', '2026-01-08 11:32:25', 16, 'Kiszállítás - Raktárak közötti átmozgatás - F épület - CDE-567 rendszám', '2026-01-12', 18, 'completed'),
(438, 'TR-1767868345-D7DC', 591, 29, 'import', '2026-01-08 11:32:25', 16, 'Beérkezés - Raktárak közötti átmozgatás - F épület - CDE-567 rendszám', '2026-01-12', 18, 'completed'),
(439, 'TR-1767953995-F1F9', 502, 22, 'export', '2026-01-09 11:19:55', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - NOP-012 rendszám', '2026-01-16', 13, 'completed'),
(440, 'TR-1767953995-F1F9', 502, 24, 'import', '2026-01-09 11:19:55', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - NOP-012 rendszám', '2026-01-16', 13, 'completed'),
(441, 'TR-1768066616-CD06', 552, 27, 'export', '2026-01-10 18:36:56', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - HIJ-456 rendszám', '2026-01-13', 22, 'completed'),
(442, 'TR-1768066616-CD06', 552, 29, 'import', '2026-01-10 18:36:56', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - HIJ-456 rendszám', '2026-01-13', 22, 'completed'),
(443, 'TR-1768163386-249C', 503, 30, 'export', '2026-01-11 21:29:46', 16, 'Kiszállítás - Raktárak közötti átmozgatás - C épület - QRS-345 rendszám', '2026-01-15', 22, 'completed'),
(444, 'TR-1768163386-249C', 503, 26, 'import', '2026-01-11 21:29:46', 16, 'Beérkezés - Raktárak közötti átmozgatás - C épület - QRS-345 rendszám', '2026-01-15', 22, 'completed'),
(445, 'TR-1768184728-60B8', 493, 27, 'export', '2026-01-12 03:25:28', 16, 'Kiszállítás - Raktárak közötti átmozgatás - B épület - ZAB-234 rendszám', '2026-01-16', 39, 'completed'),
(446, 'TR-1768184728-60B8', 493, 25, 'import', '2026-01-12 03:25:28', 16, 'Beérkezés - Raktárak közötti átmozgatás - B épület - ZAB-234 rendszám', '2026-01-16', 39, 'completed'),
(447, 'TR-1768316257-BC5C', 518, 30, 'export', '2026-01-13 15:57:37', 16, 'Kiszállítás - Sürgős utánpótlás - A épület - MNO-345 rendszám', '2026-01-20', 7, 'completed'),
(448, 'TR-1768316257-BC5C', 518, 23, 'import', '2026-01-13 15:57:37', 16, 'Beérkezés - Sürgős utánpótlás - A épület - MNO-345 rendszám', '2026-01-20', 7, 'completed'),
(449, 'TR-1768395164-2705', 590, 23, 'export', '2026-01-14 13:52:44', 16, 'Kiszállítás - Heti készlet feltöltés - D épület - TUV-678 rendszám', '2026-01-19', 34, 'completed'),
(450, 'TR-1768395164-2705', 590, 27, 'import', '2026-01-14 13:52:44', 16, 'Beérkezés - Heti készlet feltöltés - D épület - TUV-678 rendszám', '2026-01-19', 34, 'completed'),
(451, 'TR-1768491553-1B85', 516, 29, 'export', '2026-01-15 16:39:13', 16, 'Kiszállítás - Raktárak közötti átmozgatás - A épület - EFG-123 rendszám', '2026-01-19', 28, 'completed'),
(452, 'TR-1768491553-1B85', 516, 25, 'import', '2026-01-15 16:39:13', 16, 'Beérkezés - Raktárak közötti átmozgatás - A épület - EFG-123 rendszám', '2026-01-19', 28, 'completed'),
(453, 'TR-1768602878-6AFE', 471, 24, 'export', '2026-01-16 23:34:38', 16, 'Kiszállítás - Ütemezett szállítás - E épület - DEF-456 rendszám', '2026-01-22', 19, 'completed'),
(454, 'TR-1768602878-6AFE', 471, 31, 'import', '2026-01-16 23:34:38', 16, 'Beérkezés - Ütemezett szállítás - E épület - DEF-456 rendszám', '2026-01-22', 19, 'completed'),
(455, 'TR-1768663845-0656', 547, 21, 'export', '2026-01-17 16:30:45', 16, 'Kiszállítás - Rendkívüli szállítmány - B épület - YZA-567 rendszám', '2026-01-23', 39, 'completed'),
(456, 'TR-1768663845-0656', 547, 25, 'import', '2026-01-17 16:30:45', 16, 'Beérkezés - Rendkívüli szállítmány - B épület - YZA-567 rendszám', '2026-01-23', 39, 'completed'),
(457, 'TR-1768727684-EC8F', 515, 27, 'export', '2026-01-18 10:14:44', 16, 'Kiszállítás - Rendkívüli szállítmány - C épület - BCD-890 rendszám', '2026-01-20', 20, 'completed'),
(458, 'TR-1768727684-EC8F', 515, 25, 'import', '2026-01-18 10:14:44', 16, 'Beérkezés - Rendkívüli szállítmány - C épület - BCD-890 rendszám', '2026-01-20', 20, 'completed'),
(459, 'TR-1768819230-0C58', 472, 21, 'export', '2026-01-19 11:40:30', 15, 'Kiszállítás - Raktárak közötti átmozgatás - G épület - TUV-678 rendszám', '2026-01-24', 4, 'completed'),
(460, 'TR-1768819230-0C58', 472, 25, 'import', '2026-01-19 11:40:30', 15, 'Beérkezés - Raktárak közötti átmozgatás - G épület - TUV-678 rendszám', '2026-01-24', 4, 'completed'),
(461, 'TR-1768939636-83B2', 595, 22, 'export', '2026-01-20 21:07:16', 16, 'Kiszállítás - Sürgős utánpótlás - B épület - BCD-890 rendszám', '2026-01-22', 1, 'completed'),
(462, 'TR-1768939636-83B2', 595, 30, 'import', '2026-01-20 21:07:16', 16, 'Beérkezés - Sürgős utánpótlás - B épület - BCD-890 rendszám', '2026-01-22', 1, 'completed'),
(463, 'TR-1769034800-233F', 581, 28, 'export', '2026-01-21 23:33:20', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - TUV-678 rendszám', '2026-01-27', 37, 'completed'),
(464, 'TR-1769034800-233F', 581, 24, 'import', '2026-01-21 23:33:20', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - TUV-678 rendszám', '2026-01-27', 37, 'completed'),
(465, 'TR-1769121465-E14A', 526, 26, 'export', '2026-01-22 23:37:45', 16, 'Kiszállítás - Rendkívüli szállítmány - G épület - EFG-123 rendszám', '2026-01-24', 1, 'completed'),
(466, 'TR-1769121465-E14A', 526, 21, 'import', '2026-01-22 23:37:45', 16, 'Beérkezés - Rendkívüli szállítmány - G épület - EFG-123 rendszám', '2026-01-24', 1, 'completed'),
(467, 'TR-1769185617-389B', 474, 25, 'export', '2026-01-23 17:26:57', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - YZA-567 rendszám', '2026-01-25', 42, 'completed'),
(468, 'TR-1769185617-389B', 474, 26, 'import', '2026-01-23 17:26:57', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - YZA-567 rendszám', '2026-01-25', 42, 'completed'),
(469, 'TR-1769262602-858D', 548, 27, 'export', '2026-01-24 14:50:02', 16, 'Kiszállítás - Raktárak közötti átmozgatás - F épület - DEF-456 rendszám', '2026-01-26', 39, 'completed'),
(470, 'TR-1769262602-858D', 548, 21, 'import', '2026-01-24 14:50:02', 16, 'Beérkezés - Raktárak közötti átmozgatás - F épület - DEF-456 rendszám', '2026-01-26', 39, 'completed'),
(471, 'TR-1769324526-B7C9', 556, 23, 'export', '2026-01-25 08:02:06', 16, 'Kiszállítás - Rendkívüli szállítmány - H épület - DEF-456 rendszám', '2026-01-27', 45, 'completed'),
(472, 'TR-1769324526-B7C9', 556, 27, 'import', '2026-01-25 08:02:06', 16, 'Beérkezés - Rendkívüli szállítmány - H épület - DEF-456 rendszám', '2026-01-27', 45, 'completed'),
(473, 'TR-1769400400-2EEF', 595, 30, 'export', '2026-01-26 05:06:40', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - BCD-890 rendszám', '2026-02-02', 14, 'completed'),
(474, 'TR-1769400400-2EEF', 595, 24, 'import', '2026-01-26 05:06:40', 16, 'Beérkezés - Heti készlet feltöltés - H épület - BCD-890 rendszám', '2026-02-02', 14, 'completed'),
(475, 'TR-1769542081-C5C2', 479, 28, 'export', '2026-01-27 20:28:01', 16, 'Kiszállítás - Rendkívüli szállítmány - F épület - VWX-234 rendszám', '2026-02-03', 30, 'completed'),
(476, 'TR-1769542081-C5C2', 479, 31, 'import', '2026-01-27 20:28:01', 16, 'Beérkezés - Rendkívüli szállítmány - F épület - VWX-234 rendszám', '2026-02-03', 30, 'completed'),
(477, 'TR-1769576857-9A84', 470, 30, 'export', '2026-01-28 06:07:37', 16, 'Kiszállítás - Raktárak közötti átmozgatás - E épület - JKL-012 rendszám', '2026-02-01', 39, 'completed'),
(478, 'TR-1769576857-9A84', 470, 21, 'import', '2026-01-28 06:07:37', 16, 'Beérkezés - Raktárak közötti átmozgatás - E épület - JKL-012 rendszám', '2026-02-01', 39, 'completed'),
(479, 'TR-1769680623-944F', 576, 25, 'export', '2026-01-29 10:57:03', 16, 'Kiszállítás - Heti készlet feltöltés - E épület - PQR-678 rendszám', '2026-02-05', 16, 'completed'),
(480, 'TR-1769680623-944F', 576, 30, 'import', '2026-01-29 10:57:03', 16, 'Beérkezés - Heti készlet feltöltés - E épület - PQR-678 rendszám', '2026-02-05', 16, 'completed'),
(481, 'TR-1769733498-5DAA', 478, 23, 'export', '2026-01-30 01:38:18', 16, 'Kiszállítás - Heti készlet feltöltés - E épület - MNO-345 rendszám', '2026-02-06', 43, 'completed'),
(482, 'TR-1769733498-5DAA', 478, 21, 'import', '2026-01-30 01:38:18', 16, 'Beérkezés - Heti készlet feltöltés - E épület - MNO-345 rendszám', '2026-02-06', 43, 'completed'),
(483, 'TR-1769881239-C394', 503, 30, 'export', '2026-01-31 18:40:39', 16, 'Kiszállítás - Rendkívüli szállítmány - A épület - FGH-890 rendszám', '2026-02-05', 37, 'completed'),
(484, 'TR-1769881239-C394', 503, 27, 'import', '2026-01-31 18:40:39', 16, 'Beérkezés - Rendkívüli szállítmány - A épület - FGH-890 rendszám', '2026-02-05', 37, 'completed'),
(485, 'TR-1769939048-B4D3', 569, 30, 'export', '2026-02-01 10:44:08', 16, 'Kiszállítás - Sürgős utánpótlás - A épület - MNO-345 rendszám', '2026-02-04', 34, 'completed'),
(486, 'TR-1769939048-B4D3', 569, 26, 'import', '2026-02-01 10:44:08', 16, 'Beérkezés - Sürgős utánpótlás - A épület - MNO-345 rendszám', '2026-02-04', 34, 'completed'),
(487, 'TR-1770038990-FA3D', 598, 27, 'export', '2026-02-02 14:29:50', 16, 'Kiszállítás - Heti készlet feltöltés - H épület - FGH-890 rendszám', '2026-02-08', 26, 'completed'),
(488, 'TR-1770038990-FA3D', 598, 29, 'import', '2026-02-02 14:29:50', 16, 'Beérkezés - Heti készlet feltöltés - H épület - FGH-890 rendszám', '2026-02-08', 26, 'completed'),
(489, 'TR-1770092102-0167', 511, 30, 'export', '2026-02-03 05:15:02', 16, 'Kiszállítás - Raktárak közötti átmozgatás - E épület - JKL-012 rendszám', '2026-02-10', 4, 'completed'),
(490, 'TR-1770092102-0167', 511, 31, 'import', '2026-02-03 05:15:02', 16, 'Beérkezés - Raktárak közötti átmozgatás - E épület - JKL-012 rendszám', '2026-02-10', 4, 'completed'),
(491, 'TR-1770186897-ED4E', 524, 29, 'export', '2026-02-04 07:34:57', 16, 'Kiszállítás - Sürgős utánpótlás - C épület - PQR-678 rendszám', '2026-02-08', 28, 'completed'),
(492, 'TR-1770186897-ED4E', 524, 24, 'import', '2026-02-04 07:34:57', 16, 'Beérkezés - Sürgős utánpótlás - C épület - PQR-678 rendszám', '2026-02-08', 28, 'completed'),
(493, 'TR-1770288504-C03A', 478, 27, 'export', '2026-02-05 11:48:24', 16, 'Kiszállítás - Raktárak közötti átmozgatás - H épület - PQR-678 rendszám', '2026-02-09', 29, 'completed'),
(494, 'TR-1770288504-C03A', 478, 23, 'import', '2026-02-05 11:48:24', 16, 'Beérkezés - Raktárak közötti átmozgatás - H épület - PQR-678 rendszám', '2026-02-09', 29, 'completed'),
(495, 'TR-1770406320-C7B6', 535, 23, 'export', '2026-02-06 20:32:00', 16, 'Kiszállítás - Ütemezett szállítás - D épület - STU-901 rendszám', '2026-02-12', 26, 'completed'),
(496, 'TR-1770406320-C7B6', 535, 31, 'import', '2026-02-06 20:32:00', 16, 'Beérkezés - Ütemezett szállítás - D épület - STU-901 rendszám', '2026-02-12', 26, 'completed'),
(497, 'TR-1770443819-5F61', 584, 24, 'export', '2026-02-07 06:56:59', 16, 'Kiszállítás - Sürgős utánpótlás - G épület - QRS-345 rendszám', '2026-02-13', 49, 'completed'),
(498, 'TR-1770443819-5F61', 584, 22, 'import', '2026-02-07 06:56:59', 16, 'Beérkezés - Sürgős utánpótlás - G épület - QRS-345 rendszám', '2026-02-13', 49, 'completed'),
(499, 'TR-1770546235-53B6', 541, 29, 'export', '2026-02-08 11:23:55', 15, 'Kiszállítás - Rendkívüli szállítmány - F épület - WXY-901 rendszám', '2026-02-10', 18, 'completed'),
(500, 'TR-1770546235-53B6', 541, 24, 'import', '2026-02-08 11:23:55', 15, 'Beérkezés - Rendkívüli szállítmány - F épület - WXY-901 rendszám', '2026-02-10', 18, 'completed'),
(501, 'TR-1770664728-5DC0', 567, 27, 'export', '2026-02-09 20:18:48', 16, 'Kiszállítás - Raktárak közötti átmozgatás - H épület - ABC-123 rendszám', '2026-02-14', 7, 'completed'),
(502, 'TR-1770664728-5DC0', 567, 23, 'import', '2026-02-09 20:18:48', 16, 'Beérkezés - Raktárak közötti átmozgatás - H épület - ABC-123 rendszám', '2026-02-14', 7, 'completed'),
(503, 'TR-1770721851-560D', 518, 29, 'export', '2026-02-10 12:10:51', 16, 'Kiszállítás - Heti készlet feltöltés - A épület - NOP-012 rendszám', '2026-02-13', 26, 'completed'),
(504, 'TR-1770721851-560D', 518, 28, 'import', '2026-02-10 12:10:51', 16, 'Beérkezés - Heti készlet feltöltés - A épület - NOP-012 rendszám', '2026-02-13', 26, 'completed'),
(505, 'TR-1770785347-E3EE', 492, 27, 'export', '2026-02-11 05:49:07', 16, 'Kiszállítás - Ütemezett szállítás - G épület - QRS-345 rendszám', '2026-02-13', 33, 'completed'),
(506, 'TR-1770785347-E3EE', 492, 23, 'import', '2026-02-11 05:49:07', 16, 'Beérkezés - Ütemezett szállítás - G épület - QRS-345 rendszám', '2026-02-13', 33, 'completed'),
(507, 'TR-1770893553-38F2', 573, 29, 'export', '2026-02-12 11:52:33', 16, 'Kiszállítás - Rendkívüli szállítmány - H épület - DEF-456 rendszám', '2026-02-16', 28, 'completed'),
(508, 'TR-1770893553-38F2', 573, 23, 'import', '2026-02-12 11:52:33', 16, 'Beérkezés - Rendkívüli szállítmány - H épület - DEF-456 rendszám', '2026-02-16', 28, 'completed'),
(509, 'TR-1761040665-5041', 513, 30, 'export', '2025-10-21 11:57:45', 16, 'Kiszállítás - Átmozgatás - A épület - WXY-901', '2025-10-24', 63, 'completed'),
(510, 'TR-1761040665-5041', 513, 24, 'import', '2025-10-21 11:57:45', 16, 'Beérkezés - Átmozgatás - A épület - WXY-901', '2025-10-24', 63, 'completed'),
(511, 'TR-1762649030-E8AF', 487, 23, 'export', '2025-11-09 01:43:50', 16, 'Kiszállítás - Átmozgatás - A épület - WXY-901', '2025-11-15', 71, 'completed'),
(512, 'TR-1762649030-E8AF', 487, 24, 'import', '2025-11-09 01:43:50', 16, 'Beérkezés - Átmozgatás - A épület - WXY-901', '2025-11-15', 71, 'completed'),
(513, 'TR-1763533065-D9D0', 599, 22, 'export', '2025-11-19 07:17:45', 16, 'Kiszállítás - Utánpótlás - A épület - VWX-234', '2025-11-25', 53, 'completed'),
(514, 'TR-1763533065-D9D0', 599, 25, 'import', '2025-11-19 07:17:45', 16, 'Beérkezés - Utánpótlás - A épület - VWX-234', '2025-11-25', 53, 'completed'),
(515, 'TR-1757739306-E535', 544, 30, 'export', '2025-09-13 06:55:06', 16, 'Kiszállítás - Utánpótlás - B épület - GHI-789', '2025-09-19', 78, 'completed'),
(516, 'TR-1757739306-E535', 544, 31, 'import', '2025-09-13 06:55:06', 16, 'Beérkezés - Utánpótlás - B épület - GHI-789', '2025-09-19', 78, 'completed'),
(517, 'TR-1770599123-B556', 562, 30, 'export', '2026-02-09 02:05:23', 16, 'Kiszállítás - Feltöltés - F épület - ZAB-234', '2026-02-13', 96, 'completed'),
(518, 'TR-1770599123-B556', 562, 27, 'import', '2026-02-09 02:05:23', 16, 'Beérkezés - Feltöltés - F épület - ZAB-234', '2026-02-13', 96, 'completed'),
(519, 'TR-1766132790-D036', 593, 26, 'export', '2025-12-19 09:26:30', 16, 'Kiszállítás - Átmozgatás - F épület - ABC-123', '2025-12-25', 69, 'completed'),
(520, 'TR-1766132790-D036', 593, 23, 'import', '2025-12-19 09:26:30', 16, 'Beérkezés - Átmozgatás - F épület - ABC-123', '2025-12-25', 69, 'completed'),
(521, 'TR-1755661081-DC39', 477, 21, 'export', '2025-08-20 05:38:01', 16, 'Kiszállítás - Szállítmány - F épület - QRS-345', '2025-08-26', 66, 'completed'),
(522, 'TR-1755661081-DC39', 477, 30, 'import', '2025-08-20 05:38:01', 16, 'Beérkezés - Szállítmány - F épület - QRS-345', '2025-08-26', 66, 'completed'),
(523, 'TR-1761521545-6AE6', 515, 21, 'export', '2025-10-27 00:32:25', 16, 'Kiszállítás - Szállítmány - E épület - WXY-901', '2025-11-02', 79, 'completed'),
(524, 'TR-1761521545-6AE6', 515, 31, 'import', '2025-10-27 00:32:25', 16, 'Beérkezés - Szállítmány - E épület - WXY-901', '2025-11-02', 79, 'completed'),
(525, 'TR-1754332005-1B6D', 577, 26, 'export', '2025-08-04 20:26:45', 16, 'Kiszállítás - Átmozgatás - G épület - ZAB-234', '2025-08-06', 86, 'completed'),
(526, 'TR-1754332005-1B6D', 577, 29, 'import', '2025-08-04 20:26:45', 16, 'Beérkezés - Átmozgatás - G épület - ZAB-234', '2025-08-06', 86, 'completed'),
(527, 'TR-1756087125-FC25', 570, 21, 'export', '2025-08-25 03:58:45', 16, 'Kiszállítás - Feltöltés - G épület - TUV-678', '2025-08-29', 40, 'canceled'),
(528, 'TR-1756087125-FC25', 570, 23, 'import', '2025-08-25 03:58:45', 16, 'Beérkezés - Feltöltés - G épület - TUV-678', '2025-08-29', 40, 'canceled'),
(529, 'TR-1761778602-ADE5', 481, 21, 'export', '2025-10-29 23:56:42', 16, 'Kiszállítás - Feltöltés - C épület - FGH-890', '2025-11-03', 11, 'completed'),
(530, 'TR-1761778602-ADE5', 481, 26, 'import', '2025-10-29 23:56:42', 16, 'Beérkezés - Feltöltés - C épület - FGH-890', '2025-11-03', 11, 'completed'),
(531, 'TR-1757385828-DCD0', 579, 30, 'export', '2025-09-09 04:43:48', 16, 'Kiszállítás - Export/Import - F épület - EFG-123', '2025-09-12', 21, 'completed'),
(532, 'TR-1757385828-DCD0', 579, 21, 'import', '2025-09-09 04:43:48', 16, 'Beérkezés - Export/Import - F épület - EFG-123', '2025-09-12', 21, 'completed'),
(533, 'TR-1760241059-E244', 499, 27, 'export', '2025-10-12 05:50:59', 15, 'Kiszállítás - Utánpótlás - E épület - TUV-678', '2025-10-14', 35, 'completed'),
(534, 'TR-1760241059-E244', 499, 30, 'import', '2025-10-12 05:50:59', 15, 'Beérkezés - Utánpótlás - E épület - TUV-678', '2025-10-14', 35, 'completed'),
(535, 'TR-1770679678-A63D', 504, 30, 'export', '2026-02-10 00:27:58', 16, 'Kiszállítás - Szállítmány - F épület - GHI-789', '2026-02-12', 71, 'completed'),
(536, 'TR-1770679678-A63D', 504, 29, 'import', '2026-02-10 00:27:58', 16, 'Beérkezés - Szállítmány - F épület - GHI-789', '2026-02-12', 71, 'completed'),
(537, 'TR-1762662429-8F35', 486, 26, 'export', '2025-11-09 05:27:09', 16, 'Kiszállítás - Feltöltés - D épület - EFG-123', '2025-11-14', 41, 'completed'),
(538, 'TR-1762662429-8F35', 486, 22, 'import', '2025-11-09 05:27:09', 16, 'Beérkezés - Feltöltés - D épület - EFG-123', '2025-11-14', 41, 'completed'),
(539, 'TR-1767049180-CF27', 503, 25, 'export', '2025-12-29 23:59:40', 16, 'Kiszállítás - Utánpótlás - G épület - NOP-012', '2026-01-04', 73, 'completed'),
(540, 'TR-1767049180-CF27', 503, 27, 'import', '2025-12-29 23:59:40', 16, 'Beérkezés - Utánpótlás - G épület - NOP-012', '2026-01-04', 73, 'completed'),
(541, 'TR-1769439112-3AB1', 520, 27, 'export', '2026-01-26 15:51:52', 16, 'Kiszállítás - Átmozgatás - G épület - TUV-678', '2026-01-31', 50, 'completed'),
(542, 'TR-1769439112-3AB1', 520, 26, 'import', '2026-01-26 15:51:52', 16, 'Beérkezés - Átmozgatás - G épület - TUV-678', '2026-01-31', 50, 'completed'),
(543, 'TR-1758034557-A8DE', 582, 24, 'export', '2025-09-16 16:55:57', 16, 'Kiszállítás - Feltöltés - A épület - EFG-123', '2025-09-22', 13, 'completed'),
(544, 'TR-1758034557-A8DE', 582, 25, 'import', '2025-09-16 16:55:57', 16, 'Beérkezés - Feltöltés - A épület - EFG-123', '2025-09-22', 13, 'completed'),
(545, 'TR-1764883622-0AE3', 519, 26, 'export', '2025-12-04 22:27:02', 16, 'Kiszállítás - Átmozgatás - C épület - KLM-789', '2025-12-10', 9, 'canceled'),
(546, 'TR-1764883622-0AE3', 519, 27, 'import', '2025-12-04 22:27:02', 16, 'Beérkezés - Átmozgatás - C épület - KLM-789', '2025-12-10', 9, 'canceled'),
(547, 'TR-1762748561-9139', 531, 27, 'export', '2025-11-10 05:22:41', 16, 'Kiszállítás - Átmozgatás - G épület - YZA-567', '2025-11-15', 87, 'completed'),
(548, 'TR-1762748561-9139', 531, 21, 'import', '2025-11-10 05:22:41', 16, 'Beérkezés - Átmozgatás - G épület - YZA-567', '2025-11-15', 87, 'completed'),
(549, 'TR-1754240290-16B1', 553, 26, 'export', '2025-08-03 18:58:10', 16, 'Kiszállítás - Export/Import - G épület - VWX-234', '2025-08-10', 67, 'completed'),
(550, 'TR-1754240290-16B1', 553, 21, 'import', '2025-08-03 18:58:10', 16, 'Beérkezés - Export/Import - G épület - VWX-234', '2025-08-10', 67, 'completed'),
(551, 'TR-1760663750-0318', 594, 25, 'export', '2025-10-17 03:15:50', 16, 'Kiszállítás - Átmozgatás - D épület - CDE-567', '2025-10-21', 79, 'completed'),
(552, 'TR-1760663750-0318', 594, 31, 'import', '2025-10-17 03:15:50', 16, 'Beérkezés - Átmozgatás - D épület - CDE-567', '2025-10-21', 79, 'completed'),
(553, 'TR-1755472233-0E3B', 573, 24, 'export', '2025-08-18 01:10:33', 15, 'Kiszállítás - Feltöltés - C épület - NOP-012', '2025-08-25', 67, 'completed'),
(554, 'TR-1755472233-0E3B', 573, 26, 'import', '2025-08-18 01:10:33', 15, 'Beérkezés - Feltöltés - C épület - NOP-012', '2025-08-25', 67, 'completed'),
(555, 'TR-1762154505-1FC5', 570, 23, 'export', '2025-11-03 08:21:45', 16, 'Kiszállítás - Utánpótlás - A épület - PQR-678', '2025-11-06', 61, 'completed'),
(556, 'TR-1762154505-1FC5', 570, 21, 'import', '2025-11-03 08:21:45', 16, 'Beérkezés - Utánpótlás - A épület - PQR-678', '2025-11-06', 61, 'completed'),
(557, 'TR-1755451322-7BA8', 474, 21, 'export', '2025-08-17 19:22:02', 16, 'Kiszállítás - Utánpótlás - H épület - WXY-901', '2025-08-23', 87, 'completed'),
(558, 'TR-1755451322-7BA8', 474, 22, 'import', '2025-08-17 19:22:02', 16, 'Beérkezés - Utánpótlás - H épület - WXY-901', '2025-08-23', 87, 'completed'),
(559, 'TR-1761205051-1A8B', 535, 24, 'export', '2025-10-23 09:37:31', 16, 'Kiszállítás - Átmozgatás - F épület - QRS-345', '2025-10-29', 2, 'canceled'),
(560, 'TR-1761205051-1A8B', 535, 28, 'import', '2025-10-23 09:37:31', 16, 'Beérkezés - Átmozgatás - F épület - QRS-345', '2025-10-29', 2, 'canceled'),
(561, 'TR-1755129928-DB05', 484, 23, 'export', '2025-08-14 02:05:28', 16, 'Kiszállítás - Export/Import - G épület - FGH-890', '2025-08-17', 8, 'completed'),
(562, 'TR-1755129928-DB05', 484, 24, 'import', '2025-08-14 02:05:28', 16, 'Beérkezés - Export/Import - G épület - FGH-890', '2025-08-17', 8, 'completed'),
(563, 'TR-1762967147-67AE', 473, 29, 'export', '2025-11-12 18:05:47', 16, 'Kiszállítás - Utánpótlás - F épület - VWX-234', '2025-11-14', 23, 'completed'),
(564, 'TR-1762967147-67AE', 473, 30, 'import', '2025-11-12 18:05:47', 16, 'Beérkezés - Utánpótlás - F épület - VWX-234', '2025-11-14', 23, 'completed'),
(565, 'TR-1754835842-E260', 557, 23, 'export', '2025-08-10 16:24:02', 16, 'Kiszállítás - Export/Import - F épület - ZAB-234', '2025-08-13', 77, 'completed'),
(566, 'TR-1754835842-E260', 557, 26, 'import', '2025-08-10 16:24:02', 16, 'Beérkezés - Export/Import - F épület - ZAB-234', '2025-08-13', 77, 'completed'),
(567, 'TR-1761829550-FF5C', 524, 28, 'export', '2025-10-30 14:05:50', 16, 'Kiszállítás - Átmozgatás - H épület - QRS-345', '2025-11-04', 15, 'completed'),
(568, 'TR-1761829550-FF5C', 524, 25, 'import', '2025-10-30 14:05:50', 16, 'Beérkezés - Átmozgatás - H épület - QRS-345', '2025-11-04', 15, 'completed'),
(569, 'TR-1760891254-76A4', 597, 27, 'export', '2025-10-19 18:27:34', 16, 'Kiszállítás - Átmozgatás - F épület - FGH-890', '2025-10-24', 21, 'completed'),
(570, 'TR-1760891254-76A4', 597, 29, 'import', '2025-10-19 18:27:34', 16, 'Beérkezés - Átmozgatás - F épület - FGH-890', '2025-10-24', 21, 'completed'),
(571, 'TR-1756463711-3D3B', 472, 30, 'export', '2025-08-29 12:35:11', 16, 'Kiszállítás - Utánpótlás - F épület - GHI-789', '2025-08-31', 35, 'completed'),
(572, 'TR-1756463711-3D3B', 472, 29, 'import', '2025-08-29 12:35:11', 16, 'Beérkezés - Utánpótlás - F épület - GHI-789', '2025-08-31', 35, 'completed'),
(573, 'TR-1761010851-828C', 492, 30, 'export', '2025-10-21 03:40:51', 16, 'Kiszállítás - Export/Import - E épület - KLM-789', '2025-10-25', 34, 'completed'),
(574, 'TR-1761010851-828C', 492, 24, 'import', '2025-10-21 03:40:51', 16, 'Beérkezés - Export/Import - E épület - KLM-789', '2025-10-25', 34, 'completed'),
(575, 'TR-1768921567-172E', 586, 22, 'export', '2026-01-20 16:06:07', 15, 'Kiszállítás - Export/Import - A épület - YZA-567', '2026-01-26', 61, 'completed'),
(576, 'TR-1768921567-172E', 586, 31, 'import', '2026-01-20 16:06:07', 15, 'Beérkezés - Export/Import - A épület - YZA-567', '2026-01-26', 61, 'completed'),
(577, 'TR-1760501530-A99F', 530, 30, 'export', '2025-10-15 06:12:10', 16, 'Kiszállítás - Export/Import - E épület - HIJ-456', '2025-10-17', 66, 'completed'),
(578, 'TR-1760501530-A99F', 530, 25, 'import', '2025-10-15 06:12:10', 16, 'Beérkezés - Export/Import - E épület - HIJ-456', '2025-10-17', 66, 'completed'),
(579, 'TR-1767956845-BDC1', 493, 28, 'export', '2026-01-09 12:07:25', 16, 'Kiszállítás - Utánpótlás - G épület - HIJ-456', '2026-01-11', 16, 'completed'),
(580, 'TR-1767956845-BDC1', 493, 23, 'import', '2026-01-09 12:07:25', 16, 'Beérkezés - Utánpótlás - G épület - HIJ-456', '2026-01-11', 16, 'completed'),
(581, 'TR-1768214860-93A4', 520, 22, 'export', '2026-01-12 11:47:40', 16, 'Kiszállítás - Szállítmány - G épület - ABC-123', '2026-01-19', 63, 'completed'),
(582, 'TR-1768214860-93A4', 520, 26, 'import', '2026-01-12 11:47:40', 16, 'Beérkezés - Szállítmány - G épület - ABC-123', '2026-01-19', 63, 'completed'),
(583, 'TR-1759806219-7B26', 525, 23, 'export', '2025-10-07 05:03:39', 16, 'Kiszállítás - Szállítmány - E épület - WXY-901', '2025-10-14', 91, 'completed'),
(584, 'TR-1759806219-7B26', 525, 29, 'import', '2025-10-07 05:03:39', 16, 'Beérkezés - Szállítmány - E épület - WXY-901', '2025-10-14', 91, 'completed'),
(585, 'TR-1756805782-63B6', 587, 29, 'export', '2025-09-02 11:36:22', 16, 'Kiszállítás - Átmozgatás - E épület - TUV-678', '2025-09-09', 34, 'completed'),
(586, 'TR-1756805782-63B6', 587, 26, 'import', '2025-09-02 11:36:22', 16, 'Beérkezés - Átmozgatás - E épület - TUV-678', '2025-09-09', 34, 'completed'),
(587, 'TR-1766644769-B0D7', 570, 27, 'export', '2025-12-25 07:39:29', 16, 'Kiszállítás - Átmozgatás - A épület - EFG-123', '2025-12-29', 26, 'completed'),
(588, 'TR-1766644769-B0D7', 570, 29, 'import', '2025-12-25 07:39:29', 16, 'Beérkezés - Átmozgatás - A épület - EFG-123', '2025-12-29', 26, 'completed'),
(589, 'TR-1762385564-244E', 479, 30, 'export', '2025-11-06 00:32:44', 16, 'Kiszállítás - Átmozgatás - A épület - VWX-234', '2025-11-11', 82, 'completed'),
(590, 'TR-1762385564-244E', 479, 31, 'import', '2025-11-06 00:32:44', 16, 'Beérkezés - Átmozgatás - A épület - VWX-234', '2025-11-11', 82, 'completed'),
(591, 'TR-1756709433-94F8', 487, 25, 'export', '2025-09-01 08:50:33', 16, 'Kiszállítás - Utánpótlás - F épület - NOP-012', '2025-09-04', 95, 'canceled'),
(592, 'TR-1756709433-94F8', 487, 26, 'import', '2025-09-01 08:50:33', 16, 'Beérkezés - Utánpótlás - F épület - NOP-012', '2025-09-04', 95, 'canceled'),
(593, 'TR-1768090236-F2E7', 479, 21, 'export', '2026-01-11 01:10:36', 16, 'Kiszállítás - Szállítmány - E épület - ABC-123', '2026-01-17', 69, 'completed'),
(594, 'TR-1768090236-F2E7', 479, 27, 'import', '2026-01-11 01:10:36', 16, 'Beérkezés - Szállítmány - E épület - ABC-123', '2026-01-17', 69, 'completed'),
(595, 'TR-1762498938-BE53', 472, 26, 'export', '2025-11-07 08:02:18', 16, 'Kiszállítás - Feltöltés - G épület - YZA-567', '2025-11-09', 42, 'completed'),
(596, 'TR-1762498938-BE53', 472, 29, 'import', '2025-11-07 08:02:18', 16, 'Beérkezés - Feltöltés - G épület - YZA-567', '2025-11-09', 42, 'completed'),
(597, 'TR-1766340880-FF1F', 469, 30, 'export', '2025-12-21 19:14:40', 16, 'Kiszállítás - Utánpótlás - A épület - KLM-789', '2025-12-27', 87, 'completed'),
(598, 'TR-1766340880-FF1F', 469, 22, 'import', '2025-12-21 19:14:40', 16, 'Beérkezés - Utánpótlás - A épület - KLM-789', '2025-12-27', 87, 'completed'),
(599, 'TR-1757394404-5E97', 522, 29, 'export', '2025-09-09 07:06:44', 16, 'Kiszállítás - Átmozgatás - D épület - JKL-012', '2025-09-15', 1, 'completed'),
(600, 'TR-1757394404-5E97', 522, 21, 'import', '2025-09-09 07:06:44', 16, 'Beérkezés - Átmozgatás - D épület - JKL-012', '2025-09-15', 1, 'completed'),
(601, 'TR-1760512984-2538', 567, 25, 'export', '2025-10-15 09:23:04', 16, 'Kiszállítás - Átmozgatás - E épület - EFG-123', '2025-10-21', 71, 'completed'),
(602, 'TR-1760512984-2538', 567, 28, 'import', '2025-10-15 09:23:04', 16, 'Beérkezés - Átmozgatás - E épület - EFG-123', '2025-10-21', 71, 'completed'),
(603, 'TR-1758595105-9E3D', 501, 28, 'export', '2025-09-23 04:38:25', 16, 'Kiszállítás - Átmozgatás - C épület - PQR-678', '2025-09-25', 22, 'completed'),
(604, 'TR-1758595105-9E3D', 501, 26, 'import', '2025-09-23 04:38:25', 16, 'Beérkezés - Átmozgatás - C épület - PQR-678', '2025-09-25', 22, 'completed'),
(605, 'TR-1753677998-8099', 545, 21, 'export', '2025-07-28 06:46:38', 16, 'Kiszállítás - Átmozgatás - D épület - HIJ-456', '2025-08-01', 56, 'completed'),
(606, 'TR-1753677998-8099', 545, 22, 'import', '2025-07-28 06:46:38', 16, 'Beérkezés - Átmozgatás - D épület - HIJ-456', '2025-08-01', 56, 'completed'),
(607, 'TR-1757860055-892F', 546, 28, 'export', '2025-09-14 16:27:35', 16, 'Kiszállítás - Utánpótlás - H épület - MNO-345', '2025-09-20', 20, 'completed'),
(608, 'TR-1757860055-892F', 546, 24, 'import', '2025-09-14 16:27:35', 16, 'Beérkezés - Utánpótlás - H épület - MNO-345', '2025-09-20', 20, 'completed'),
(609, 'TR-1759698514-327B', 482, 29, 'export', '2025-10-05 23:08:34', 16, 'Kiszállítás - Export/Import - E épület - NOP-012', '2025-10-11', 23, 'completed'),
(610, 'TR-1759698514-327B', 482, 30, 'import', '2025-10-05 23:08:34', 16, 'Beérkezés - Export/Import - E épület - NOP-012', '2025-10-11', 23, 'completed'),
(611, 'TR-1767591917-A025', 517, 29, 'export', '2026-01-05 06:45:17', 16, 'Kiszállítás - Átmozgatás - A épület - KLM-789', '2026-01-12', 75, 'completed'),
(612, 'TR-1767591917-A025', 517, 28, 'import', '2026-01-05 06:45:17', 16, 'Beérkezés - Átmozgatás - A épület - KLM-789', '2026-01-12', 75, 'completed'),
(613, 'TR-1760688495-1F75', 509, 30, 'export', '2025-10-17 10:08:15', 15, 'Kiszállítás - Export/Import - B épület - YZA-567', '2025-10-24', 79, 'canceled'),
(614, 'TR-1760688495-1F75', 509, 25, 'import', '2025-10-17 10:08:15', 15, 'Beérkezés - Export/Import - B épület - YZA-567', '2025-10-24', 79, 'canceled'),
(615, 'TR-1761768455-519B', 575, 23, 'export', '2025-10-29 21:07:35', 16, 'Kiszállítás - Feltöltés - E épület - YZA-567', '2025-11-05', 1, 'completed'),
(616, 'TR-1761768455-519B', 575, 26, 'import', '2025-10-29 21:07:35', 16, 'Beérkezés - Feltöltés - E épület - YZA-567', '2025-11-05', 1, 'completed'),
(617, 'TR-1761730615-1AA7', 521, 25, 'export', '2025-10-29 10:36:55', 16, 'Kiszállítás - Utánpótlás - D épület - ABC-123', '2025-11-04', 46, 'completed'),
(618, 'TR-1761730615-1AA7', 521, 23, 'import', '2025-10-29 10:36:55', 16, 'Beérkezés - Utánpótlás - D épület - ABC-123', '2025-11-04', 46, 'completed'),
(619, 'TR-1759937096-C08A', 556, 27, 'export', '2025-10-08 17:24:56', 16, 'Kiszállítás - Átmozgatás - G épület - ZAB-234', '2025-10-13', 40, 'completed'),
(620, 'TR-1759937096-C08A', 556, 21, 'import', '2025-10-08 17:24:56', 16, 'Beérkezés - Átmozgatás - G épület - ZAB-234', '2025-10-13', 40, 'completed'),
(621, 'TR-1757581829-109F', 488, 27, 'export', '2025-09-11 11:10:29', 16, 'Kiszállítás - Export/Import - C épület - PQR-678', '2025-09-16', 71, 'completed'),
(622, 'TR-1757581829-109F', 488, 26, 'import', '2025-09-11 11:10:29', 16, 'Beérkezés - Export/Import - C épület - PQR-678', '2025-09-16', 71, 'completed'),
(623, 'TR-1756270131-0991', 585, 26, 'export', '2025-08-27 06:48:51', 16, 'Kiszállítás - Utánpótlás - F épület - STU-901', '2025-09-03', 32, 'completed'),
(624, 'TR-1756270131-0991', 585, 21, 'import', '2025-08-27 06:48:51', 16, 'Beérkezés - Utánpótlás - F épület - STU-901', '2025-09-03', 32, 'completed'),
(625, 'TR-1761469702-3079', 516, 22, 'export', '2025-10-26 10:08:22', 16, 'Kiszállítás - Utánpótlás - B épület - HIJ-456', '2025-11-01', 100, 'canceled'),
(626, 'TR-1761469702-3079', 516, 23, 'import', '2025-10-26 10:08:22', 16, 'Beérkezés - Utánpótlás - B épület - HIJ-456', '2025-11-01', 100, 'canceled'),
(627, 'TR-1768200321-5A50', 510, 26, 'export', '2026-01-12 07:45:21', 16, 'Kiszállítás - Utánpótlás - F épület - TUV-678', '2026-01-14', 55, 'completed'),
(628, 'TR-1768200321-5A50', 510, 22, 'import', '2026-01-12 07:45:21', 16, 'Beérkezés - Utánpótlás - F épület - TUV-678', '2026-01-14', 55, 'completed'),
(629, 'TR-1763754902-1A89', 581, 22, 'export', '2025-11-21 20:55:02', 16, 'Kiszállítás - Utánpótlás - A épület - TUV-678', '2025-11-26', 62, 'canceled'),
(630, 'TR-1763754902-1A89', 581, 26, 'import', '2025-11-21 20:55:02', 16, 'Beérkezés - Utánpótlás - A épület - TUV-678', '2025-11-26', 62, 'canceled'),
(631, 'TR-1754022791-66E8', 588, 21, 'export', '2025-08-01 06:33:11', 16, 'Kiszállítás - Feltöltés - B épület - ABC-123', '2025-08-04', 64, 'canceled'),
(632, 'TR-1754022791-66E8', 588, 23, 'import', '2025-08-01 06:33:11', 16, 'Beérkezés - Feltöltés - B épület - ABC-123', '2025-08-04', 64, 'canceled'),
(633, 'TR-1768989224-59E6', 525, 27, 'export', '2026-01-21 10:53:44', 16, 'Kiszállítás - Utánpótlás - C épület - STU-901', '2026-01-26', 10, 'completed'),
(634, 'TR-1768989224-59E6', 525, 21, 'import', '2026-01-21 10:53:44', 16, 'Beérkezés - Utánpótlás - C épület - STU-901', '2026-01-26', 10, 'completed'),
(635, 'TR-1767398370-B4AB', 518, 23, 'export', '2026-01-03 00:59:30', 16, 'Kiszállítás - Szállítmány - E épület - CDE-567', '2026-01-09', 47, 'completed'),
(636, 'TR-1767398370-B4AB', 518, 28, 'import', '2026-01-03 00:59:30', 16, 'Beérkezés - Szállítmány - E épület - CDE-567', '2026-01-09', 47, 'completed'),
(637, 'TR-1760829031-3764', 485, 29, 'export', '2025-10-19 01:10:31', 16, 'Kiszállítás - Export/Import - E épület - FGH-890', '2025-10-26', 77, 'completed'),
(638, 'TR-1760829031-3764', 485, 26, 'import', '2025-10-19 01:10:31', 16, 'Beérkezés - Export/Import - E épület - FGH-890', '2025-10-26', 77, 'completed'),
(639, 'TR-1760880252-C20C', 525, 26, 'export', '2025-10-19 15:24:12', 16, 'Kiszállítás - Szállítmány - A épület - QRS-345', '2025-10-26', 72, 'completed'),
(640, 'TR-1760880252-C20C', 525, 29, 'import', '2025-10-19 15:24:12', 16, 'Beérkezés - Szállítmány - A épület - QRS-345', '2025-10-26', 72, 'completed'),
(641, 'TR-1754533495-FFA1', 580, 25, 'export', '2025-08-07 04:24:55', 16, 'Kiszállítás - Export/Import - E épület - VWX-234', '2025-08-13', 34, 'completed'),
(642, 'TR-1754533495-FFA1', 580, 24, 'import', '2025-08-07 04:24:55', 16, 'Beérkezés - Export/Import - E épület - VWX-234', '2025-08-13', 34, 'completed'),
(643, 'TR-1768119218-EFD7', 563, 26, 'export', '2026-01-11 09:13:38', 16, 'Kiszállítás - Szállítmány - C épület - CDE-567', '2026-01-15', 91, 'completed'),
(644, 'TR-1768119218-EFD7', 563, 28, 'import', '2026-01-11 09:13:38', 16, 'Beérkezés - Szállítmány - C épület - CDE-567', '2026-01-15', 91, 'completed'),
(645, 'TR-1764251675-1E3A', 597, 28, 'export', '2025-11-27 14:54:35', 16, 'Kiszállítás - Feltöltés - G épület - GHI-789', '2025-11-30', 10, 'completed'),
(646, 'TR-1764251675-1E3A', 597, 25, 'import', '2025-11-27 14:54:35', 16, 'Beérkezés - Feltöltés - G épület - GHI-789', '2025-11-30', 10, 'completed'),
(647, 'TR-1770841864-EC0E', 551, 29, 'export', '2026-02-11 21:31:04', 16, 'Kiszállítás - Utánpótlás - E épület - QRS-345', '2026-02-16', 61, 'completed'),
(648, 'TR-1770841864-EC0E', 551, 27, 'import', '2026-02-11 21:31:04', 16, 'Beérkezés - Utánpótlás - E épület - QRS-345', '2026-02-16', 61, 'completed'),
(649, 'TR-1756400537-9C8E', 517, 25, 'export', '2025-08-28 19:02:17', 16, 'Kiszállítás - Feltöltés - B épület - STU-901', '2025-09-01', 27, 'completed'),
(650, 'TR-1756400537-9C8E', 517, 26, 'import', '2025-08-28 19:02:17', 16, 'Beérkezés - Feltöltés - B épület - STU-901', '2025-09-01', 27, 'completed'),
(651, 'TR-1759921096-0D87', 527, 21, 'export', '2025-10-08 12:58:16', 16, 'Kiszállítás - Feltöltés - A épület - WXY-901', '2025-10-13', 24, 'completed'),
(652, 'TR-1759921096-0D87', 527, 28, 'import', '2025-10-08 12:58:16', 16, 'Beérkezés - Feltöltés - A épület - WXY-901', '2025-10-13', 24, 'completed'),
(653, 'TR-1764355361-3318', 481, 26, 'export', '2025-11-28 19:42:41', 16, 'Kiszállítás - Szállítmány - C épület - QRS-345', '2025-12-01', 10, 'completed'),
(654, 'TR-1764355361-3318', 481, 30, 'import', '2025-11-28 19:42:41', 16, 'Beérkezés - Szállítmány - C épület - QRS-345', '2025-12-01', 10, 'completed'),
(655, 'TR-1754933661-39B4', 525, 26, 'export', '2025-08-11 19:34:21', 16, 'Kiszállítás - Szállítmány - B épület - MNO-345', '2025-08-18', 4, 'completed'),
(656, 'TR-1754933661-39B4', 525, 28, 'import', '2025-08-11 19:34:21', 16, 'Beérkezés - Szállítmány - B épület - MNO-345', '2025-08-18', 4, 'completed'),
(657, 'TR-1764202341-BCED', 592, 27, 'export', '2025-11-27 01:12:21', 16, 'Kiszállítás - Export/Import - F épület - WXY-901', '2025-12-01', 14, 'completed'),
(658, 'TR-1764202341-BCED', 592, 26, 'import', '2025-11-27 01:12:21', 16, 'Beérkezés - Export/Import - F épület - WXY-901', '2025-12-01', 14, 'completed'),
(659, 'TR-1763425246-1D9F', 582, 28, 'export', '2025-11-18 01:20:46', 16, 'Kiszállítás - Utánpótlás - C épület - VWX-234', '2025-11-23', 73, 'completed'),
(660, 'TR-1763425246-1D9F', 582, 24, 'import', '2025-11-18 01:20:46', 16, 'Beérkezés - Utánpótlás - C épület - VWX-234', '2025-11-23', 73, 'completed'),
(661, 'TR-1754437983-22B6', 486, 27, 'export', '2025-08-06 01:53:03', 16, 'Kiszállítás - Export/Import - C épület - HIJ-456', '2025-08-09', 96, 'completed'),
(662, 'TR-1754437983-22B6', 486, 24, 'import', '2025-08-06 01:53:03', 16, 'Beérkezés - Export/Import - C épület - HIJ-456', '2025-08-09', 96, 'completed'),
(663, 'TR-1764756683-3398', 480, 24, 'export', '2025-12-03 11:11:23', 16, 'Kiszállítás - Feltöltés - C épület - PQR-678', '2025-12-07', 77, 'completed'),
(664, 'TR-1764756683-3398', 480, 21, 'import', '2025-12-03 11:11:23', 16, 'Beérkezés - Feltöltés - C épület - PQR-678', '2025-12-07', 77, 'completed'),
(665, 'TR-1768869934-C6C4', 531, 21, 'export', '2026-01-20 01:45:34', 16, 'Kiszállítás - Szállítmány - A épület - DEF-456', '2026-01-26', 45, 'completed'),
(666, 'TR-1768869934-C6C4', 531, 27, 'import', '2026-01-20 01:45:34', 16, 'Beérkezés - Szállítmány - A épület - DEF-456', '2026-01-26', 45, 'completed'),
(667, 'TR-1764336193-286E', 593, 23, 'export', '2025-11-28 14:23:13', 16, 'Kiszállítás - Export/Import - A épület - MNO-345', '2025-11-30', 91, 'canceled'),
(668, 'TR-1764336193-286E', 593, 25, 'import', '2025-11-28 14:23:13', 16, 'Beérkezés - Export/Import - A épület - MNO-345', '2025-11-30', 91, 'canceled'),
(669, 'TR-1757929028-B697', 551, 28, 'export', '2025-09-15 11:37:08', 16, 'Kiszállítás - Utánpótlás - A épület - KLM-789', '2025-09-21', 30, 'completed'),
(670, 'TR-1757929028-B697', 551, 22, 'import', '2025-09-15 11:37:08', 16, 'Beérkezés - Utánpótlás - A épület - KLM-789', '2025-09-21', 30, 'completed'),
(671, 'TR-1760662081-4F39', 545, 26, 'export', '2025-10-17 02:48:01', 16, 'Kiszállítás - Szállítmány - A épület - VWX-234', '2025-10-21', 34, 'completed'),
(672, 'TR-1760662081-4F39', 545, 21, 'import', '2025-10-17 02:48:01', 16, 'Beérkezés - Szállítmány - A épület - VWX-234', '2025-10-21', 34, 'completed'),
(673, 'TR-1764954954-B46B', 539, 28, 'export', '2025-12-05 18:15:54', 16, 'Kiszállítás - Utánpótlás - D épület - ABC-123', '2025-12-12', 60, 'completed'),
(674, 'TR-1764954954-B46B', 539, 31, 'import', '2025-12-05 18:15:54', 16, 'Beérkezés - Utánpótlás - D épület - ABC-123', '2025-12-12', 60, 'completed'),
(675, 'TR-1768124420-BF86', 511, 21, 'export', '2026-01-11 10:40:20', 16, 'Kiszállítás - Szállítmány - E épület - WXY-901', '2026-01-17', 7, 'completed'),
(676, 'TR-1768124420-BF86', 511, 27, 'import', '2026-01-11 10:40:20', 16, 'Beérkezés - Szállítmány - E épület - WXY-901', '2026-01-17', 7, 'completed'),
(677, 'TR-1759805157-2C77', 473, 26, 'export', '2025-10-07 04:45:57', 16, 'Kiszállítás - Szállítmány - A épület - KLM-789', '2025-10-14', 73, 'completed'),
(678, 'TR-1759805157-2C77', 473, 25, 'import', '2025-10-07 04:45:57', 16, 'Beérkezés - Szállítmány - A épület - KLM-789', '2025-10-14', 73, 'completed'),
(679, 'TR-1767437719-13EF', 540, 24, 'export', '2026-01-03 11:55:19', 16, 'Kiszállítás - Utánpótlás - B épület - CDE-567', '2026-01-05', 14, 'completed'),
(680, 'TR-1767437719-13EF', 540, 28, 'import', '2026-01-03 11:55:19', 16, 'Beérkezés - Utánpótlás - B épület - CDE-567', '2026-01-05', 14, 'completed'),
(681, 'TR-1767142066-65A9', 556, 30, 'export', '2025-12-31 01:47:46', 16, 'Kiszállítás - Átmozgatás - F épület - PQR-678', '2026-01-02', 17, 'completed'),
(682, 'TR-1767142066-65A9', 556, 26, 'import', '2025-12-31 01:47:46', 16, 'Beérkezés - Átmozgatás - F épület - PQR-678', '2026-01-02', 17, 'completed'),
(683, 'TR-1758234203-7FE9', 470, 27, 'export', '2025-09-19 00:23:23', 16, 'Kiszállítás - Átmozgatás - C épület - NOP-012', '2025-09-22', 78, 'canceled'),
(684, 'TR-1758234203-7FE9', 470, 25, 'import', '2025-09-19 00:23:23', 16, 'Beérkezés - Átmozgatás - C épület - NOP-012', '2025-09-22', 78, 'canceled'),
(685, 'TR-1761950345-7FF9', 470, 26, 'export', '2025-10-31 23:39:05', 16, 'Kiszállítás - Export/Import - E épület - VWX-234', '2025-11-04', 78, 'completed'),
(686, 'TR-1761950345-7FF9', 470, 29, 'import', '2025-10-31 23:39:05', 16, 'Beérkezés - Export/Import - E épület - VWX-234', '2025-11-04', 78, 'completed'),
(687, 'TR-1770238176-DA28', 497, 21, 'export', '2026-02-04 21:49:36', 16, 'Kiszállítás - Export/Import - G épület - ABC-123', '2026-02-10', 1, 'completed'),
(688, 'TR-1770238176-DA28', 497, 31, 'import', '2026-02-04 21:49:36', 16, 'Beérkezés - Export/Import - G épület - ABC-123', '2026-02-10', 1, 'completed'),
(689, 'TR-1768230502-D864', 522, 25, 'export', '2026-01-12 16:08:22', 16, 'Kiszállítás - Szállítmány - B épület - PQR-678', '2026-01-18', 59, 'completed'),
(690, 'TR-1768230502-D864', 522, 27, 'import', '2026-01-12 16:08:22', 16, 'Beérkezés - Szállítmány - B épület - PQR-678', '2026-01-18', 59, 'completed');
INSERT INTO `transports` (`ID`, `batch_id`, `product_ID`, `warehouse_ID`, `type`, `date`, `user_ID`, `description`, `arriveIn`, `quantity`, `status`) VALUES
(691, 'TR-1759450621-6CDA', 538, 23, 'export', '2025-10-03 02:17:01', 16, 'Kiszállítás - Feltöltés - G épület - BCD-890', '2025-10-07', 50, 'completed'),
(692, 'TR-1759450621-6CDA', 538, 26, 'import', '2025-10-03 02:17:01', 16, 'Beérkezés - Feltöltés - G épület - BCD-890', '2025-10-07', 50, 'completed'),
(693, 'TR-1765541920-ACBD', 483, 24, 'export', '2025-12-12 13:18:40', 16, 'Kiszállítás - Szállítmány - A épület - BCD-890', '2025-12-18', 20, 'completed'),
(694, 'TR-1765541920-ACBD', 483, 25, 'import', '2025-12-12 13:18:40', 16, 'Beérkezés - Szállítmány - A épület - BCD-890', '2025-12-18', 20, 'completed'),
(695, 'TR-1758053951-F342', 498, 28, 'export', '2025-09-16 22:19:11', 16, 'Kiszállítás - Export/Import - D épület - DEF-456', '2025-09-23', 29, 'canceled'),
(696, 'TR-1758053951-F342', 498, 31, 'import', '2025-09-16 22:19:11', 16, 'Beérkezés - Export/Import - D épület - DEF-456', '2025-09-23', 29, 'canceled'),
(697, 'TR-1762438655-FBE6', 562, 23, 'export', '2025-11-06 15:17:35', 16, 'Kiszállítás - Feltöltés - H épület - HIJ-456', '2025-11-11', 9, 'completed'),
(698, 'TR-1762438655-FBE6', 562, 21, 'import', '2025-11-06 15:17:35', 16, 'Beérkezés - Feltöltés - H épület - HIJ-456', '2025-11-11', 9, 'completed'),
(699, 'TR-1764470300-F589', 499, 29, 'export', '2025-11-30 03:38:20', 16, 'Kiszállítás - Export/Import - B épület - HIJ-456', '2025-12-07', 74, 'completed'),
(700, 'TR-1764470300-F589', 499, 24, 'import', '2025-11-30 03:38:20', 16, 'Beérkezés - Export/Import - B épület - HIJ-456', '2025-12-07', 74, 'completed'),
(701, 'TR-1760667367-7A72', 491, 23, 'export', '2025-10-17 04:16:07', 16, 'Kiszállítás - Utánpótlás - B épület - JKL-012', '2025-10-22', 57, 'completed'),
(702, 'TR-1760667367-7A72', 491, 29, 'import', '2025-10-17 04:16:07', 16, 'Beérkezés - Utánpótlás - B épület - JKL-012', '2025-10-22', 57, 'completed'),
(703, 'TR-1760549551-B894', 498, 21, 'export', '2025-10-15 19:32:31', 16, 'Kiszállítás - Feltöltés - E épület - QRS-345', '2025-10-22', 55, 'completed'),
(704, 'TR-1760549551-B894', 498, 25, 'import', '2025-10-15 19:32:31', 16, 'Beérkezés - Feltöltés - E épület - QRS-345', '2025-10-22', 55, 'completed'),
(705, 'TR-1756104422-56A1', 506, 30, 'export', '2025-08-25 08:47:02', 16, 'Kiszállítás - Szállítmány - F épület - TUV-678', '2025-08-29', 87, 'completed'),
(706, 'TR-1756104422-56A1', 506, 28, 'import', '2025-08-25 08:47:02', 16, 'Beérkezés - Szállítmány - F épület - TUV-678', '2025-08-29', 87, 'completed'),
(707, 'TR-1765400369-CC94', 526, 29, 'export', '2025-12-10 21:59:29', 16, 'Kiszállítás - Export/Import - F épület - MNO-345', '2025-12-15', 12, 'completed'),
(708, 'TR-1765400369-CC94', 526, 26, 'import', '2025-12-10 21:59:29', 16, 'Beérkezés - Export/Import - F épület - MNO-345', '2025-12-15', 12, 'completed');

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
  `login_at` datetime NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`ID`, `username`, `active`, `password`, `role`, `created_at`, `login_at`, `email`) VALUES
(15, 'felhasznalo1', 1, '$2y$10$M2hvwIJS5YT9UrNkfhrpeO61U6nFM3tHv42/iL92YJW8MeGRLBEFm', 'owner', '2026-02-13 19:03:55', '2026-02-13 20:20:36', 'felhasznalo1@gmail.com'),
(16, 'felhasznalo2', 1, '$2y$10$gukWwif/j81rqShddnMkguwMyZOjWsJmIBvnGc7YBuwFuXiORnAay', 'admin', '2026-02-13 19:04:17', '2026-02-13 20:18:51', 'felhasznalo2@gmail.com'),
(17, 'felhasznalo3', 1, '$2y$10$xrElzJTT/9t427SdspeD4u3AHltaSSdPB867hA58PQmW9.UxHMdoC', 'user', '2026-02-13 19:04:51', '2026-02-13 19:04:51', 'felhasznalo3@gmail.com');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_error`
--

CREATE TABLE `user_error` (
  `errID` int(11) NOT NULL,
  `user_ID` int(11) DEFAULT NULL,
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
(22, 16, 22),
(21, 16, 23),
(23, 16, 28),
(24, 16, 31),
(25, 17, 26),
(26, 17, 27);

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
(24, 'Keleti Elosztó', 'Debrecen, Külső-Böszörményi út 44.', 100000, 'warehouse', 1),
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
  ADD UNIQUE KEY `email` (`email`);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT a táblához `inventory`
--
ALTER TABLE `inventory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=780;

--
-- AUTO_INCREMENT a táblához `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=601;

--
-- AUTO_INCREMENT a táblához `transports`
--
ALTER TABLE `transports`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=709;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT a táblához `user_error`
--
ALTER TABLE `user_error`
  MODIFY `errID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `user_warehouse_access`
--
ALTER TABLE `user_warehouse_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

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
