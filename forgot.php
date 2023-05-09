<?php
session_start();
require_once 'config.php';

if (isset($_COOKIE['session_id'])) {
    header('Location: ./');
    exit;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emailforgot'])) {

  $email = $_POST["emailforgot"];

  $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
  $user = mysqli_fetch_assoc($result);

  if (!$user) {
    header("Location: forgot?error=nfound");
    exit;
  }
  $name = $user['name'];
$lastname = $user['lastname'];
$completename = $name.' '.$lastname;

$expiration_dateuser = $user["resettokenexpiration"];

$expiration_datecomp = strtotime("+1 hour");

if (strtotime($expiration_dateuser) >= $expiration_datecomp) {
  header("Location: forgot?success=ase");
  exit;
}
  $token = bin2hex(random_bytes(32));

  $expiration_date = date("Y-m-d H:i:s", strtotime("+12 hour"));

  mysqli_query($conn, "UPDATE users SET resettoken = '$token', resettokenexpiration = '$expiration_date' WHERE email = '$email'");



$mail = new PHPMailer(true);


$template = file_get_contents("./templates/recover.html");

$link = "https://youngdreamersfortalaigua.org/recover?token=".$token;
$template = str_replace('{{resetlink}}', $link, $template);

    $image_path = 'https://youngdreamersfortalaigua.org/templates/images/lock.png';
    $template = str_replace('{{imageurl}}',  $image_path, $template);

try {
    $mail->SMTPDebug = 0;                      // Enable verbose debug output
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtpout.secureserver.net';                     // Set the SMTP server to send through
    $mail->SMTPAuth   = true;
    $mail->Username   = 'info@youngdreamersfortalaigua.org';                     // SMTP username
    $mail->Password   = '@T28132230';                               // SMTP password
    $mail->SMTPSecure = 'ssl';         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mail->Port       = 465;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('info@youngdreamersfortalaigua.org', 'Jóvenes Soñadores Por Talaigua');
    $mail->addAddress($email, $completename);     // Add a recipient

    // Content
    $mail->isHTML(true);                                 
    $mail->CharSet = 'UTF-8';  
    $mail->Subject = 'Recuperar Contraseña';
    $mail->Body    = $template;
    if($mail->Send()) {
    header('Location: ./forgot?success=y');
    exit;
 }else{
    header('Location: ./forgot?success=w');
    exit;
 }
} catch (Exception $e) {
    header('Location: ./forgot?success=n');
    exit;
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
  <title>Recuperar Contraseña | Fundación Jóvenes Soñadores Por Talaigua</title>
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
    <h1 class="text-body-emphasis">Recuperar Contraseña</h1>
          <form class="" method="post" action="">
    <div class="form-floating mb-3">
              <input type="text" name="emailforgot" class="form-control rounded-3" id="floatingInput"
                value="" placeholder="nombre@email.com"
                required>
              <label for="floatingInput">Correo</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite" name="login"
              type="submit">Recuperar</button>
            </form>
            <?php if(isset($_GET['error'])) {
            $error=$_GET['error'];
            if($error=='nfound'){
                $msg='Correo no registrado';
            }
            echo '<div class="alert alert-danger" role="alert">'.$msg.'</div>';
            } ?><?php if(isset($_GET['success'])) {
            $success=$_GET['success'];
            if($success=='y'){
                $msg='Revisa tu email';
                $color='success';
            }elseif ($success=='w') {
                $msg='Erros desconocido, reintentalo';
                $color='warning';
            }elseif ($success=='ase') {
                $msg='Ya hemos enviado un email, reintentalo en una hora';
                $color='warning';
            }else{
                $msg='Error del servidor, reintentalo';
                $color='danger';
            }
            echo '<div class="alert alert-'.$color.'" role="alert">'.$msg.'</div>';
            } ?>
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