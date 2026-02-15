# CRUD-X

> Egyszerű, gyors és áttekinthető raktár- és készletkezelő webalkalmazás PHP + MySQL alapokon.

## 🚀 Főbb jellemzők

* **Többszintű jogosultságkezelés:** admin, owner és user szerepkörök, raktárankénti jogosultságok.
* **Készletfigyelés:** Automatikus alacsony készletszint figyelmeztetések és minimum mennyiségek kezelése.
* **Logisztika:** Szállítmányok (be- és kiadás) követése Batch ID-kkal és késés jelzéssel.
* **Kapacitáskezelés:** Raktárkihasználtsági mutatók és interaktív dashboard.
* **Gyors telepítés:** Teljes Docker támogatás és előre konfigurált SQL dump a gyors indításhoz.

---

## 🛠️ Technológiai stukk

* **Backend:** PHP 8.x (PDO)
* **Adatbázis:** MySQL / MariaDB
* **Környezet:** Docker vagy XAMPP (Apache)

---

## 💻 Telepítés és indítás

### 1. Docker (Ajánlott)
Futtasd az alábbi parancsot a projekt gyökerében:
```
    docker-compose up --build -d
```
* **Webfelület:** http://localhost/
* **phpMyAdmin:** http://localhost:8080
*(Az adatbázis importálása az első indításkor automatikusan megtörténik a crudx.sql fájlból.)*

### 2. XAMPP / Manuális telepítés
1. Másold a projekt mappáját az Apache htdocs könyvtárába.
2. Hozz létre egy crudx nevű adatbázist.
3. Importáld a crudx.sql fájlt:
```
    mysql -u root -p crudx < crudx.sql
```
4. Szükség esetén módosítsd a config.php fájlban az adatbázis hozzáféréseket.
5. Nyisd meg a böngészőben: http://localhost/[mappaneved]/

---

## 📖 Használati útmutató

| Funkció | Elérés | Leírás |
| :--- | :--- | :--- |
| **Dashboard** | index.php | Áttekintés: statisztikák, alacsony készlet, napi mozgások és késések. |
| **Termékek** | products.php | Terméklista és részletes adatok szerkesztése. |
| **Készlet** | inventory.php | Raktárankénti készletkezelés és minimum szintek beállítása. |
| **Szállítmányok** | transports.php | Logisztikai folyamatok kezelése és Batch követés. |
| **Admin felület** | admin.php | Új termékek felvitele és adminisztratív műveletek. |

---

## ⚙️ Konfiguráció

A rendszer a config.php fájlt használja a csatlakozáshoz. Docker környezetben az alábbi változók a docker-compose.yml-ben kerülnek definiálásra:
* DB_HOST, DB_NAME, DB_USER, DB_PASSWORD, BASE_URL

---

## 🛠 Hibakeresés

Ha problémába ütközöl, ellenőrizd a naplófájlokat:
* **PHP hibák:** error.log (a konfiguráció szerint).
* **Docker logok:**
    
    docker-compose logs web
    docker-compose logs db

---

## 🤝 Közreműködés és támogatás

Ha hibát találtál, vagy új funkciót javasolnál:
1. Nyiss egy **Issue**-t a repository-ban.
2. Ha fejleszteni szeretnél, olvasd el a CONTRIBUTING.md fájlt és küldj egy **Pull Request**-et.

**Karbantartók:**
* @entorge
* @tokesroland

---
*Licenc: A projekt szabadon felhasználható és módosítható.*