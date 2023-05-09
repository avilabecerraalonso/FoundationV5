<?php
session_start();
require_once "config.php";

if (!isset($_GET["success"])) {

if (!isset($_GET["token"])) {
  header("Location: recover?error=empty&token=NULL");
  exit;
}

$token = $_GET["token"];
if ($token=="NULL") {

}else{
$result = mysqli_query($conn, "SELECT * FROM users WHERE resettoken = '$token'");
$user = mysqli_fetch_assoc($result);

if (!$user) {
  header("Location: recover?error=irt&token=NULL");
  exit;
}


$expiration_date = $user["resettokenexpiration"];

if (strtotime($expiration_date) < time()) {
  header("Location: recover?error=rte&token=NULL");
  exit;
}
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $password = $_POST["password"];
  $rpassword = $_POST["rpassword"];
  if(strlen($password) < 8) {
        header("Location: recover?token=".$token."&bad=len");
        exit();
     }
if ($password==$rpassword){
  $hashed_password = md5($password);

  mysqli_query($conn, "UPDATE users SET password = '$hashed_password', resettoken = NULL, resettokenexpiration = NULL WHERE id = " . $user["id"]);

  
  header("Location: recover?success=y");
  exit;
}else{
  header("Location: recover?token=".$token."&bad=pdm");
  exit;
}
}
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <link rel="icon" type="image/png" href="/assets/icons/favicon.ico" sizes="48x48">
  <link rel="icon" type="image/png" href="/assets/icons/favicon-16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="/assets/icons/favicon-32x32.png" sizes="32x32">
  <link rel="icon" type="image/png" href="/assets/icons/android-chrome-192x192.png" sizes="192x192">
  <link rel="icon" type="image/png" href="/assets/icons/android-chrome-512x512.png" sizes="512x512">
  <link rel="apple-touch-icon" href="/assets/icons/apple-touch-icon.png" sizes="180x180">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Fundación Jóvenes Soñadores Por Talaigua - Pagina Oficial de nuestra fundación encaminada a ayudar a los pequeños y jóvenes, para que consigan explotar su potencial y ayudar a su comunidad velando por un mejor futuro para ellos, su familia y sus proximas generaciones.">
  <meta name="keywords" content="Fundación, Jóvenes, Soñadores, Talaigua">
  <meta name="author" content="Martin Avila Becerra">
  <meta name="robots" content="index,follow">
  <meta name="language" content="Spanish">
  <meta name="revisit-after" content="30 days">
  <link rel="canonical" href="https://youngdreamersfortalaigua.org/dashboard">
  <link rel="manifest" href="./manifest.json">
  <title>Nueva Contraseña | Fundación Jóvenes Soñadores Por Talaigua</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <link href="./assets/css/main.css" rel="stylesheet">
</head>
<body>
  <div class="preloader" id="preloader">
    <div class="loader"></div>
  </div>


  <div class="conatiner-fluid p-4">

<div class="container my-5">
  <div class="p-5 text-center bg-body-tertiary rounded-3">
    <img src="./assets/icons/android-chrome-512x512.png" width="120px">
    <?php if(isset($_GET['success'])) {
            $success=$_GET['success'];
            if($success=='y'){
                $msg='Contraseña Actualizada <a href="./">Iniciar Sesión</a>';
            }
            echo '<div class="alert alert-success" role="alert">'.$msg.'</div>';
            } ?>
            <?php if(isset($_GET['error'])) {
            $error=$_GET['error'];
            if($error=='empty'){
                $msg='Token No valido, Entrada vacia';
                $color='warning';
            }elseif ($error=='irt') {
                $msg='Token No valido';
                $color='danger';
            }elseif ($error=='rte') {
                $msg='Token Ha Expirado';
                $color='danger';
            }
            echo '<div class="alert alert-'.$color.'" role="alert">'.$msg.'</div>';
            } ?>
            <?php if (isset($_GET['succes']) or isset($_GET['error']) or isset($_GET['success'])) {}else{
              if(isset($_GET['bad'])) {
                $bad=$_GET['bad'];
            if($bad=='pdm'){
                $msg='Contraseñas no coinciden';
            }elseif($bad=='len'){
                $msg='Minimo 8 caracteres';
            }
            echo '<div class="alert alert-danger" role="alert">'.$msg.'</div>';
            }
              echo '
    <h1 class="text-body-emphasis">Nueva Contraseña</h1>
          <form class="" method="post" action="">
    <div class="form-floating mb-3">
              <input type="password" name="password" class="form-control rounded-3" id="floatingInput"
                value="" placeholder="********"
                required>
              <label for="floatingInput">Nueva Contraseña</label>
            </div><div class="form-floating mb-3">
              <input type="password" name="rpassword" class="form-control rounded-3" id="floatingInput"
                value="" placeholder="********"
                required>
              <label for="floatingInput">Confirmar Contraseña</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite" name="login"
              type="submit">Asignar</button>
            </form>';} ?>
            
  </div>
</div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script>
  window.addEventListener("load", function () {
    const preloader = document.getElementById("preloader");
    preloader.classList.add("hide-preloader");
  });
</script>
<script>
  const myCarouselElement = document.querySelector('#homecarousel')

  const carousel = new bootstrap.Carousel(myCarouselElement, {
    interval: 2000,
    touch: true
  })
</script>
<script src="./assets/js/app.js"></script>
</body>
</html>