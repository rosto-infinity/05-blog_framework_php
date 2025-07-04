<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="/paginate.css">
  <link rel="stylesheet" href="/output.css">
  <title>Cours blog PHP 2024 - <?= $pageTitle ?> </title>
</head>

<body>
  <style>
    .delete-comment-link {
  color: red;
  text-decoration: none;
  margin-left: 10px;
}

.delete-comment-link:hover {
  text-decoration: underline;
}
  </style>
  <header>
    <div class="logo">
      <h2>
        <a href="/">Blog-2025
        </a>
      </h2>
    </div>

    <nav>
      <ul>
        <?php
        switch (true) {
          case isset($_SESSION['auth']) && ($_SESSION['role'] == 'admin'):
        ?>
            <li><a id="gcu" href="logout.php">Se déconnecter</a></li>
            <li><a id="gcu" href="admin.php">Dashboard Admin</a></li>
          <?php
            break;

          case isset($_SESSION['auth']):
          ?>
            <li><a id="gcu" href="logout.php">Se déconnecter</a></li>
          <?php
            break;

          default:
          ?>
            <li><a id="lien-header" href="register.php">S'inscrire</a></li>
            <li><a href="login.php">Se connecter</a></li>
        <?php
            break;
        }
        ?>

      </ul>
    </nav>

  </header>

  <div class="main">
    <?php
    if (!empty($errors)) {

      echo '<div style=" background:red; text-align:center; color:white; padding:2px 8px; font-size:25px;">'
        . reset($errors) .
        '</div>';
    }
    elseif (isset($success)) {

      echo '<div style=" background:green; text-align:center; color:white; padding:2px 8px; font-size:25px;">'
        . $success.
        '</div>';
    }
    ?>
    <?= $pageContent ?>
  </div>
</body>

</html>


