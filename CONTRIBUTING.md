# Hozzájárulási útmutató (CONTRIBUTING)

Köszönjük, hogy segítesz a CRUD-X fejlesztésében! Kérjük, tartsd a módosításokat célratörőnek és könnyen átláthatónak.

---

## 🛠️ Fejlesztési folyamat

1. **Fork & Branch:** Forkold a repository-t, és hozz létre egy új ágat beszédes névvel:
    például: feature/uj-riport-szuro vagy bugfix/bejelentkezes-javitas
2. **Commit:** Használj világos, rövid commit üzeneteket.
3. **Push & PR:** Pushold a változtatásokat a saját forkodra, majd nyiss egy Pull Requestet a fő ághoz (main), részletezve a módosításokat.

---

## 📜 Kódstílus és elvárások

* **Olvashatóság:** Törekedj a tiszta, jól dokumentált és kis méretű függvényekre.
* **Frontend:** Az új CSS/JS fájlokat a style/ és script/ mappákba rendezd.
* **Adatbázis:** Ha módosítod a sémát:
    - Mindig frissítsd a központi crudx.sql fájlt.
    - Ha szükséges, mellékelj külön migrációs SQL szkriptet is a PR-ban.

---

## 🧪 Ellenőrzés

Mielőtt beküldöd a módosítást, győződj meg róla, hogy:
* Az alkalmazás hiba nélkül fut Docker és XAMPP környezetben is.
* A manuális tesztelés során a CRUD műveletek nem törtek meg.
* Nem maradtak debug üzenetek vagy felesleges var_dump() hívások a kódban.

---

## 💬 Kapcsolat és kérdések

Ha nem vagy biztos egy funkció megvalósításában vagy a technikai irányban, nyiss egy **Issue**-t a megbeszéléshez. A karbantartók (@entorge, @tokesroland) amint tudnak, visszajeleznek.

---
*Minden közreműködést nagyra értékelünk!*