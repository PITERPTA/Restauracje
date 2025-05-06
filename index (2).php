<?php
require 'db.php';

$id = $_GET['id'] ?? 'home';

// nagłówek
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Restauracja</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Restauracja</h1>
    <nav>
      <a href="?id=home"          <?= $id==='home'          ? 'class="active"' : '' ?>>Strona główna</a>
      <a href="?id=menu"          <?= $id==='menu'          ? 'class="active"' : '' ?>>Menu</a>
      <a href="?id=klienci"       <?= $id==='klienci'       ? 'class="active"' : '' ?>>Klienci</a>
      <a href="?id=zamowienia"    <?= $id==='zamowienia'    ? 'class="active"' : '' ?>>Zamówienia</a>
      <a href="?id=kategorie"     <?= $id==='kategorie'     ? 'class="active"' : '' ?>>Zest. według kategorii</a>
      <a href="?id=najlepszy"     <?= $id==='najlepszy'     ? 'class="active"' : '' ?>>Najlepszy klient</a>
    </nav>
  </header>

  <main>
  <?php if ($id === 'home'): ?>
    <h2>Witamy w naszej restauracji!</h2>


  <?php elseif ($id === 'menu'): ?>
    <h2>Menu</h2>
    <table>
      <tr><th>Lp</th><th>Nazwa</th><th>Cena</th><th>Kategoria</th></tr>
      <?php
      $i = 1;
      $stmt = $pdo->query("SELECT nazwa_pozycja, cena, kategoria FROM rest_menu");
      while($row = $stmt->fetch()) {
        echo "<tr>
                <td>{$i}</td>
                <td>{$row['nazwa_pozycja']}</td>
                <td>".number_format($row['cena'],2,".",",")."</td>
                <td>{$row['kategoria']}</td>
              </tr>";
        $i++;
      }
      ?>

  <?php elseif ($id === 'klienci'): ?>
    <h2>Klienci</h2>
    <table>
      <tr><th>Lp</th><th>Imię</th><th>Nazwisko</th><th>Telefon</th></tr>
      <?php
      $i = 1;
      $stmt = $pdo->query("SELECT imie, nazwisko, numer_telefonu FROM rest_klienci");
      while($row = $stmt->fetch()) {
        echo "<tr>
                <td>{$i}</td>
                <td>{$row['imie']}</td>
                <td>{$row['nazwisko']}</td>
                <td>{$row['numer_telefonu']}</td>
              </tr>";
        $i++;
      }
      ?>

  <?php elseif ($id === 'zamowienia'): ?>
    <h2>Zamówienia</h2>
    <table>
      <tr><th>Lp</th><th>Imię</th><th>Nazwisko</th><th>Data</th><th>Nazwa</th><th>Ilość</th><th>Cena</th></tr>
      <?php
      $i = 1;
      $sql = "
        SELECT c.imie, c.nazwisko, w.data_zamowienia, m.nazwa_pozycja, w.ilosc, m.cena
        FROM rest_zamowienia w
        JOIN rest_klienci c ON w.id_klienta = c.id_klienta
        JOIN rest_menu m    ON w.id_pozycja = m.id_pozycja
        ORDER BY w.data_zamowienia DESC
      ";
      $stmt = $pdo->query($sql);
      while($row = $stmt->fetch()) {
        echo "<tr>
                <td>{$i}</td>
                <td>{$row['imie']}</td>
                <td>{$row['nazwisko']}</td>
                <td>{$row['data_zamowienia']}</td>
                <td>{$row['nazwa_pozycja']}</td>
                <td>{$row['ilosc']}</td>
                <td>".number_format($row['cena'],2,".",",")."</td>
              </tr>";
        $i++;
      }
      ?>

  <?php elseif ($id === 'kategorie'): ?>
    <h2>Zestawienie zamówień według kategorii</h2>
    <table>
      <tr><th>Lp</th><th>Kategoria</th><th>Łączna ilość</th></tr>
      <?php
      $i = 1;
      $sql = "
        SELECT m.kategoria, SUM(w.ilosc) AS suma
        FROM rest_zamowienia w
        JOIN rest_menu m ON w.id_pozycja = m.id_pozycja
        GROUP BY m.kategoria
        ORDER BY suma DESC
      ";
      $stmt = $pdo->query($sql);
      while($row = $stmt->fetch()) {
        echo "<tr>
                <td>{$i}</td>
                <td>{$row['kategoria']}</td>
                <td>{$row['suma']}</td>
              </tr>";
        $i++;
      }
      ?>

  <?php elseif ($id === 'najlepszy'): ?>
    <h2>Najlepszy klient</h2>
    <table>
      <tr><th>Lp</th><th>Imię</th><th>Nazwisko</th><th>Kwota</th></tr>
      <?php
      $sql = "
        SELECT c.imie, c.nazwisko, SUM(w.ilosc*m.cena) AS kwota
        FROM rest_zamowienia w
        JOIN rest_klienci c ON w.id_klienta = c.id_klienta
        JOIN rest_menu m    ON w.id_pozycja = m.id_pozycja
        GROUP BY c.id_klienta
        ORDER BY kwota DESC
        LIMIT 1
      ";
      $row = $pdo->query($sql)->fetch();
      echo "<tr>
              <td>1</td>
              <td>{$row['imie']}</td>
              <td>{$row['nazwisko']}</td>
              <td>".number_format($row['kwota'],2,".",",")."</td>
            </tr>";
      ?>

  <?php else: ?>
    <h2>Nieznana strona</h2>
  <?php endif; ?>
  </main>
</body>
</html>
