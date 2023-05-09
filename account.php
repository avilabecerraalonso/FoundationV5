<?php
session_start();
ini_set('session.cookie_httponly', true);
require_once 'config.php';


if (!isset($_COOKIE['session_id'])) {
    header('Location: ./');
    exit;
}

mysqli_set_charset($conn, "utf8");

$sql = "SELECT * FROM users WHERE cookie='" . $_COOKIE['session_id'] . "'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
} else {
    header('Location: ./');
    exit;
}


$cookieuser = $_COOKIE['session_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
    $password = $_POST["password"];
    $rpassword = $_POST["rpassword"];
  if(strlen($password) < 8) {
        header("Location: account?&errorpass=len");
        exit();
     }
if ($password==$rpassword){
  $hashed_password = md5($password);

  $sql = "UPDATE users SET password = '$hashed_password' WHERE cookie = '$cookieuser'";
                  $result = $conn->query($sql);

                  if ($result) {
  
  header("Location: account?success=ypass");
  exit;
                  } else {
  
  header("Location: account?success=npass");
  exit;
                  }
}else{
        header("Location: account?&errorpass=pdm");
        exit();
}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['lastname'])) {

  $cookiecheck = $_COOKIE['session_id'];

  $name = $_POST['name'];
  $lastname = $_POST['lastname'];
  $doctype = $_POST['doctype'];
  $docnumber = $_POST['docnumber'];
  $phone = $_POST['phone'];
  $email = $_POST['email'];
  $gender = $_POST['gender'];
  $birth_date = $_POST['birth_date'];
  $username = $_POST['username'];


  $sqlcheck = "SELECT * FROM users WHERE cookie='$cookieuser'";
  $resultcheck = mysqli_query($conn, $sqlcheck);

  if ($resultcheck && mysqli_num_rows($resultcheck) > 0) {
    $user_infocheck = mysqli_fetch_assoc($resultcheck);
    $docnumbercurrent = $user_infocheck['docnumber'];
    $phonecurrent = $user_infocheck['phone'];
    $emailcurrent = $user_infocheck['email'];
    $usernamecurrent = $user_infocheck['username'];
  } else {
    echo "Error fetching user information: " . mysqli_error($conn);
  }

  $sqlmailcheck = "SELECT COUNT(*) as count FROM users WHERE (email = '$email' AND email != '$emailcurrent')";
  $resultmailcheck = mysqli_query($conn, $sqlmailcheck);

  if ($resultmailcheck) {
    $rowmailcheck = mysqli_fetch_assoc($resultmailcheck);
    $countmailcheck = $rowmailcheck['count'];

    if ($countmailcheck > 0) {
      header('location: ./account?error=mailno');
    } else {

      $sqlphonecheck = "SELECT COUNT(*) as count FROM users WHERE (phone = '$phone' AND phone != '$phonecurrent')";
      $resultphonecheck = mysqli_query($conn, $sqlphonecheck);

      if ($resultphonecheck) {
        $rowphonecheck = mysqli_fetch_assoc($resultphonecheck);
        $countphonecheck = $rowphonecheck['count'];

        if ($countphonecheck > 0) {
          header('location: ./account?error=phoneno');
        } else {


          $sqldoccheck = "SELECT COUNT(*) as count FROM users WHERE (docnumber = '$docnumber' AND docnumber != '$docnumbercurrent')";
          $resultdoccheck = mysqli_query($conn, $sqldoccheck);

          if ($resultdoccheck) {
            $rowdoccheck = mysqli_fetch_assoc($resultdoccheck);
            $countdoccheck = $rowdoccheck['count'];

            if ($countdoccheck > 0) {
              header('location: ./account?error=docnumberno');
            } else {



              $sqlusernamecheck = "SELECT COUNT(*) as count FROM users WHERE (username = '$username' AND username != '$usernamecurrent')";
              $resultusernamecheck = mysqli_query($conn, $sqlusernamecheck);

              if ($resultusernamecheck) {
                $rowusernamecheck = mysqli_fetch_assoc($resultusernamecheck);
                $countusernamecheck = $rowusernamecheck['count'];

                if ($countusernamecheck > 0) {
                  header('location: ./account?error=usernameno');
                } else {



                  $sql = "UPDATE users SET name = '$name', 
                                    lastname = '$lastname',
                                    doctype = '$doctype',
                                    docnumber = '$docnumber',
                                    phone = '$phone',
                                    email = '$email',
                                    gender = '$gender',
                                    birth_date = '$birth_date',
                                    username = '$username'
                                    WHERE cookie = '$cookieuser'";
                  $result = $conn->query($sql);

                  if ($result) {
                    header('location: ./account?success=yes');
                  } else {
                    header('location: ./account?error=bad');
                  }




                }
              } else {
                header('location: ./account?error=usernamecant');
              }



            }
          } else {
            header('location: ./account?error=documentcant');
          }




        }
      } else {
        header('location: ./account?error=phonecant');
      }


    }
  } else {
    header('location: ./account?error=mailcant');
  }
  $conn->close();
}


$sql = "SELECT * FROM users WHERE cookie='$cookieuser'";
$resultss = mysqli_query($conn, $sql);

if ($resultss && mysqli_num_rows($resultss) > 0) {
  $user_info = mysqli_fetch_assoc($resultss);
  $name = $user_info['name'];
  $lastname = $user_info['lastname'];
  $doctype = $user_info['doctype'];
  $docnumber = $user_info['docnumber'];
  $phone = $user_info['phone'];
  $email = $user_info['email'];
  $gender = $user_info['gender'];
  $level = $user_info['level'];
  $birth_date = $user_info['birth_date'];
  $username = $user_info['username'];
} else {
  echo "Error fetching user information: " . mysqli_error($conn);
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
  <meta name="description" content="Fundación Jóvenes Soñadores Por Talaigua - Descripción de la fundación">
  <meta name="keywords" content="Fundación, Jóvenes, Soñadores, Talaigua">
  <meta name="author" content="Martin Avila Becerra">
  <meta name="robots" content="index,follow">
  <meta name="language" content="Spanish">
  <meta name="revisit-after" content="30 days">
  <link rel="canonical" href="https://youngdreamersfortalaigua.org/">
  <link rel="manifest" href="./manifest.json">
  <title>
    <?php echo $username; ?> | Fundación Jóvenes Soñadores Por Talaigua
  </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <link href="./assets/css/main.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</head>

<body class="pb-5">
  <div class="preloader" id="preloader">
    <div class="loader"></div>
  </div>
  <div class="conatiner-fluid p-4">
    <div class="row text-center">
      <h3 style="margin-bottom:7px;">
        <?php echo $username; ?>
      </h3>
      <ul class="list-group list-group-flush text-start">
        <a href="#editaccmodal" class="list-group-item" data-bs-toggle="modal" data-bs-target="#editaccmodal">Editar
          Perfil</a>
        <a href="#badgetmodal" class="list-group-item" data-bs-toggle="modal" data-bs-target="#badgetmodal">Consulta
          Insignias</a>
        <?php if ($level == '1') {
          echo '<a href="./badgets" class="list-group-item">Modificar Insignias</a>';
        } 
        if ($level == '1') {
          echo '<a href="./gbadgets" class="list-group-item">Otorgar Insignias</a>';
        } 
         if ($level == '1') {
          echo '<a href="./list" class="list-group-item">Usuarios</a>';
        }
        if ($level == '1' || $level == '2') {
          echo '<a href="./donation" class="list-group-item">Modificar/Agregar Donación</a>';
        }  
         if ($level == '1' || $level == '2') {
          echo '<a href="./events" class="list-group-item">Modificar/Agregar Eventos</a>';
        } 
         if ($level == '1' || $level == '2') {
          echo '<a href="./news" class="list-group-item">Modificar/Agregar Noticias</a>';
        } ?>
        <a href="#cpassmodal" class="list-group-item" data-bs-toggle="modal" data-bs-target="#cpassmodal">Cambiar Contraseña</a>
        <a href="./logout" class="list-group-item">Cerrar Sesión</a>
      </ul>
    </div>
  </div>
  <div class="modal fade" id="editaccmodal" tabindex="-1" aria-labelledby="editaccmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-5 pb-4 border-bottom-0">
          <h1 class="fw-bold mb-0 fs-2">Editar Cuenta</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-5 pt-0">
          <form class="" method="post" action="">
            <?php if (isset($_GET['error'])) {
              $error = $_GET['error'];
              if ($error == 'mailno') {
                $msg = 'Correo está siendo usado';
              } elseif ($error == 'phoneno') {
                $msg = 'Celular está siendo usado';
              } elseif ($error == 'usernameno') {
                $msg = 'Nombre de usuario en uso';
              } elseif ($error == 'docnumberno') {
                $msg = 'Número de documento en uso';
              } else {
                $msg = '';
              }
              echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
            } ?>
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control rounded-3" id="floatingName"
                value="<?php echo $name; ?>" required>
              <label for="floatingName">Nombre(s)</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" name="lastname" class="form-control rounded-3" id="floatingLastname"
                value="<?php echo $lastname; ?>" required>
              <label for="floatingLastname">Apellidos</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" name="username" class="form-control rounded-3" id="floatingusername"
                value="<?php echo $username; ?>" required>
              <label for="floatingusername">Usuario</label>
            </div>
            <div class="form-floating mb-3">
              <select class="form-select " name="doctype" id="inlineFormSelectPrefId" required>
                <option selected>Seleccione...</option>
                <option value="1" <?php if ($doctype == '1') {
                  echo 'selected';
                } ?>>Cédula de ciudadania</option>
                <option value="2" <?php if ($doctype == '2') {
                  echo 'selected';
                } ?>>Tarjeta de identidad</option>
              </select>
              <label for="floatingDoctype">Tipo de documento</label>
            </div>
            <div class="form-floating mb-3">
              <input type="number" name="docnumber" class="form-control rounded-3 col-9" id="floatingdocn"
                value="<?php echo $docnumber; ?>" required>
              <label for="floatingdocn">Documento</label>
            </div>
            <div class="form-floating mb-3">
              <select class="form-select " name="gender" id="inlineFormSelectPref" required>
                <option selected>Seleccione...</option>
                <option value="male" <?php if ($gender == 'male') {
                  echo 'selected';
                } ?>>Hombre</option>
                <option value="female" <?php if ($gender == 'female') {
                  echo 'selected';
                } ?>>Mujer</option>
                <option value="other" <?php if ($gender == 'other') {
                  echo 'selected';
                } ?>>Otro</option>
              </select>
              <label for="floatinggender">Género</label>
            </div>
            <div class="form-floating mb-3">
              <input type="number" name="phone" class="form-control rounded-3 col-9" id="floatingphone"
                value="<?php echo $phone; ?>" required>
              <label for="floatingphone">Celular</label>
            </div>
            <div class="form-floating mb-3">
              <input type="email" name="email" class="form-control rounded-3 col-9" id="floatingemail"
                value="<?php echo $email; ?>" required>
              <label for="floatingemail">Correo</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite end-0" name="edit"
              type="submit">Editar</button>
            <small class="text-body-secondary">Recuerda verificar tus datos.</small>
          </form>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="cpassmodal" tabindex="-1" aria-labelledby="cpassmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-5 pb-4 border-bottom-0">
          <h1 class="fw-bold mb-0 fs-2">Cambiar Contraseña</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-5 pt-0">
          <form class="" method="post" action="">
            <?php if (isset($_GET['errorpass'])) {
              $errorpass = $_GET['errorpass'];
              if ($errorpass == 'len') {
                $msg = 'Minimo 8 caracteres';
              } elseif ($errorpass == 'pdm') {
                $msg = 'Contraseñas no coinciden';
              }else {
                $msg = '';
              }
              echo '<div class="alert alert-danger" role="alert">' . $msg . '</div>';
            } ?>
            <div class="form-floating mb-3">
              <input type="password" name="password" class="form-control rounded-3" id="floatingnpassword"
                value="" placeholder="********" required>
              <label for="floatingLastname">Contraseña</label>
            </div>
            <div class="form-floating mb-3">
              <input type="password" name="rpassword" class="form-control rounded-3" id="floatingrpassword"
                value="" placeholder="********" required>
              <label for="floatingusername">Confirmación de Contraseña</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite end-0" name="edit"
              type="submit">Cambiar</button>
            <small class="text-body-secondary">Recuerda verificar tus datos.</small>
          </form>
        </div>
      </div>
    </div>
  </div>


  <?php
  $sqlgrid = "SELECT * FROM badget";
  $resultgrid = mysqli_query($conn, $sqlgrid); ?>
  <div class="modal fade" id="badgetmodal" tabindex="-1" aria-labelledby="badgetmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-body p-5">
          <h2 class="fw-bold mb-0">Insignias</h2>
          <ul class="d-grid gap-4 my-3 list-unstyled small">
            <div class="row">
              <?php

              while ($rowgrid = mysqli_fetch_assoc($resultgrid)) {
                $current_user_id = $row['id'];
                $badge_id = $rowgrid['id'];

                $sqlcolor = "SELECT COUNT(*) AS total FROM badgetwon WHERE iduser = '$current_user_id' AND idbadget = '$badge_id'";

                $resultcolor = mysqli_query($conn, $sqlcolor);

                if ($resultcolor) {
                  $rowcolor = mysqli_fetch_assoc($resultcolor);
                  $total_rows = $rowcolor['total'];
                } else {
                  $total_rows = 0;
                }
                
                date_timestamp_get($date);

                if ($total_rows > 0) {
                  $colorbadget = '';
                  $fontcolorbadget = '';
                  $idfordate = $rowgrid['id'];
                  $sqldate = "SELECT * FROM badgetwon WHERE iduser = '$current_user_id' AND idbadget = '$idfordate'";
                  $resultfordate = mysqli_query($conn, $sqldate);
                  while ($rowgriddate = mysqli_fetch_assoc($resultfordate)) {

                    $getdate = strtotime($rowmodal["date"]);

                    $time_diff = time() - $getdate;
                    
                    if ($time_diff < 60) { 
                      $time_ago = "1 minuto";
                  } elseif ($time_diff < 3600) { 
                      $minutes = floor($time_diff / 60);
                      $time_ago = ($minutes == 1) ? "1 minuto" : "$minutes minutos";
                  } elseif ($time_diff < 86400) {
                      $hours = floor($time_diff / 3600);
                      $time_ago = ($hours == 1) ? "1 hora" : "$hours horas";
                  } elseif ($time_diff < 604800) {
                      $days = floor($time_diff / 86400);
                      $time_ago = ($days == 1) ? "1 dia" : "$days dias";
                  } elseif ($time_diff < 2592000) { 
                      $weeks = floor($time_diff / 604800);
                      $time_ago = ($weeks == 1) ? "1 semana" : "$weeks semanas";
                  } elseif ($time_diff < 31536000) { 
                      $months = floor($time_diff / 2592000);
                      $time_ago = ($months == 1) ? "1 mes" : "$months meses";
                  } else { 
                      $years = floor($time_diff / 31536000);
                      $time_ago = ($years == 1) ? "1 año" : "$years años";
                  }
                  

                    $dateshow = "Conseguida hace " . $time_ago;
                  }
                } else {
                  $dateshow = '';
                  $colorbadget = 'style="filter: grayscale(1);"';
                  $fontcolorbadget = 'style="color:gray !important;"';
                }

                echo '<a href="#" class="col-md-4 col-4 text-center mb-4 text-decoration-none color10" Data-bs-toggle="popover" title="' . $rowgrid['name'] . '" data-bs-content="' . $rowgrid['description'] . ' ' . $dateshow . ' "><img src="./assets/images/badgets/' . $rowgrid['url'] . '" class="badgetimg" ' . $colorbadget . '  alt="Insignia ' . $rowgrid['name'] . '"/><div class="text-decoration-none text-center" ' . $fontcolorbadget . '>' . $rowgrid['name'] . '</div></a>';

              }
              ?>
            </div>

          </ul>
          <button type="button" class="btn btn-lg btn-primary background30 fwhite mt-3 w-100"
            data-bs-dismiss="modal">Genial!</button>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3 mb-4">
  <div id="liveToast" class="toast mb-5" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
    <div class="toast-body">
      Editado Satisfactoriamente.
    </div>
    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3 mb-4">
  <div id="liveToastpass" class="toast mb-5" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
    <div class="toast-body">
      Contraseña editada Satisfactoriamente.
    </div>
    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
  </div>
  </div>
</div>

  <footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Terminos</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Condiciones</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Estatutos</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">FAQs</a></li>
    </ul>
    <p class="text-center text-body-secondary">© 2023 Jóvenes Soñadores Por Talaigua</p>
  </footer>
  <nav class="nav nav-fill fixed-bottom backgroundalt pb-3">
    <a class="nav-link" href="./dashboard"><svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"
        fill="currentColor" class="bi bi-house-heart" viewBox="0 0 16 16">
        <path d="M8 6.982C9.664 5.309 13.825 8.236 8 12 2.175 8.236 6.336 5.309 8 6.982Z" />
        <path
          d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.707L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.646a.5.5 0 0 0 .708-.707L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z" />
      </svg>
      <div class="text-decoration-none text-center">Home</div>
    </a>
    <a class="nav-link" href="./aboutus"><svg xmlns="http://www.w3.org/2000/svg" width="100%" fill="currentColor"
        class="bi bi-people" viewBox="0 0 16 16">
        <path
          d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" />
      </svg>
      <div class="text-decoration-none text-center">Nosotros</div>
    </a>
    <a class="nav-link" href="./donations"><svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%"
        fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
        <path
          d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z" />
        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
        <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z" />
      </svg>
      <div class="text-decoration-none text-center">Donaciones</div>
    </a>
    <a class="nav-link active" aria-current="page" href="./account"><svg xmlns="http://www.w3.org/2000/svg" width="100%"
        height="100%" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
        <path
          d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z" />
      </svg>
      <div class="text-decoration-none text-center">Cuenta</div>
    </a>
  </nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
<script>
  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]')
  const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl))
</script>
<script>
  window.addEventListener("load", function () {
    const preloader = document.getElementById("preloader");
    preloader.classList.add("hide-preloader");
  });
</script>
<?php if (isset($_GET['error'])) {
  echo "
  <script>
    $( document ).ready(function() {
    $('#editaccmodal').modal('toggle')
});

  </script>";
} ?>
<?php if (isset($_GET['errorpass'])) {
  echo "
  <script>
    $( document ).ready(function() {
    $('#cpassmodal').modal('toggle')
});

  </script>";
} ?>
<script>// Get the toast element
var toastEl = document.getElementById('liveToast');

// Create a new Bootstrap toast object
var toast = new bootstrap.Toast(toastEl);

// Check if the URL parameter "success" is set to "yes"
var urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === 'yes') {
  toast.show();
}
</script>
<script>// Get the toast element
var toastElpass = document.getElementById('liveToastpass');

// Create a new Bootstrap toast object
var toastpass = new bootstrap.Toast(toastElpass);

// Check if the URL parameter "success" is set to "yes"
var urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('success') === 'ypass') {
  toastpass.show();
}
</script>
<script src="./assets/js/app.js"></script>
</body>

</html>