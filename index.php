<?php
session_start();
ini_set('session.cookie_httponly', true);
require_once 'config.php';


if (isset($_COOKIE['session_id'])) {
    header('Location: ./dashboard');
    exit;
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emailorusername'])) {
    $emailorusername = mysqli_real_escape_string($conn, $_POST['emailorusername']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $password = md5($password);
    $sql = "SELECT * FROM users WHERE (username='$emailorusername' OR email='$emailorusername')";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $sql = "SELECT * FROM users WHERE (username='$emailorusername' OR email='$emailorusername') AND password='$password'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $session_id = uniqid();
            if($row['last_login'] == NULL){
    $_SESSION['pastlogin'] = NULL;
} else {
    $_SESSION['pastlogin'] = $row['last_login'];
}

            $sql = "UPDATE users SET cookie='$session_id', last_login=NOW() WHERE id=" . $row['id'];
            $conn->query($sql);

            setcookie('session_id', $session_id, time() + (86400 * 30), "/");
            echo "<script>window.localStorage.setItem('session_id', '$session_id');</script>";

              $_SESSION['alert_shown'] = true;

            $_SESSION['session_id'] = $session_id;

            header('Location: ./dashboard');
            exit;
        } else {
            header('Location: ./?error=2');
            exit;
        }
    } else {
        header('Location: ./?error=1');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['lastname'])) {
    $name = $_POST['name'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $doctype = $_POST['doctype'];
    $docnumber = $_POST['docnumber'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rpassword = $_POST['rpassword'];

    if(strlen($phone) != 10) {
        header("Location: ./?errors=phonelen&name=".$name."&lastname=".$lastname."&username=".$username."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&email=".$email."&f=1");
        exit();
     }

     if(strlen($username) < 4) {
        header("Location: ./?errors=usernamelen&name=".$name."&lastname=".$lastname."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&email=".$email."&f=1");
        exit();
     }
     if (strpos($username, ' ') !== false) {
        header("Location: ./?errors=usernamespaces&name=".$name."&lastname=".$lastname."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&email=".$email."&f=1");
  exit;
}

     if(strlen($password) < 8) {
        header("Location: ./?errors=passlen&name=".$name."&lastname=".$lastname."&username=".$username."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&email=".$email."&f=1");
        exit();
     }

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        header("Location: ./?errors=usernameno&name=".$name."&lastname=".$lastname."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&email=".$email."&f=1");
        exit();
    }


    $sql = "SELECT * FROM users WHERE docnumber = '$docnumber'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        header("Location: ./?errors=docnumberno&name=".$name."&lastname=".$lastname."&username=".$username."&doctype=".$doctype."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&email=".$email."&f=1");
        exit();
    }


    $sql = "SELECT * FROM users WHERE phone = '$phone'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        header("Location: ./?errors=phoneno&name=".$name."&lastname=".$lastname."&username=".$username."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&email=".$email."&f=1");
        exit();
    }

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        header("Location: ./?errors=mailno&name=".$name."&lastname=".$lastname."&username=".$username."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&&f=1");
        exit();
    }



    if($password != $rpassword) {
        header("Location: ./?errors=passwordnomatch&name=".$name."&lastname=".$lastname."&username=".$username."&doctype=".$doctype."&docnumber=".$docnumber."&date=".$birth_date."&gender=".$gender."&phone=".$phone."&email=".$email."&f=1");
        exit();
    }

    $password = md5($password);

    
    $sql = "INSERT INTO users (name, lastname, username, doctype, docnumber, birth_date, gender, phone, email, password) VALUES ('$name', '$lastname', '$username', '$doctype', '$docnumber', '$birth_date', '$gender', '$phone', '$email', '$password')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ./?register=y");
    }else{
        header("Location: ./?register=n");
    }
}

$conn->close();
?>
<!DOCTYPE html>
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
  <meta name="theme-color" content="#FFFFFF"/>
  <meta name="revisit-after" content="30 days">
  <link rel="canonical" href="https://youngdreamersfortalaigua.org">
  <script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-analytics.js";
  import { getMessaging } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-messaging.js";
  
  const firebaseConfig = {
    apiKey: "AIzaSyAwniByf6z00j8HOyj0bIkhim4CQNGOsks",
    authDomain: "youngdreamersfortalaigua-01.firebaseapp.com",
    projectId: "youngdreamersfortalaigua-01",
    storageBucket: "youngdreamersfortalaigua-01.appspot.com",
    messagingSenderId: "505716996899",
    appId: "1:505716996899:web:560ff6434ee2ab2841aa96",
    measurementId: "G-G124TRGM7Y"
  };
  
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
  const messaging = getMessaging(app);
  
  if ('firebase' in self && 'messaging' in firebase) {
  // Get the registration token
  const currentToken = await firebase.messaging().getToken({
    vapidKey: 'BKlKTCq5MEuyR3yhY86AkNwetwBJLdNlXRZVks-A0in_hknX1QFvLhOG48XZLO1E6LGqeHjJBXZUYg_phkBFsSU',
    serviceWorkerRegistration: self.registration
  });

  // Show the token in an alert box
  alert(currentToken);
} else {
  console.log('Firebase Messaging is not available');
}

</script>
  <link rel="manifest" href="./manifest.json">
  <title>Fundación Jóvenes Soñadores Por Talaigua</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <link href="./assets/css/main.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
    <script src="./assets/js/reg.js"></script>
</head>

<body>
  <div class="preloader" id="preloader">
    <div class="loader"></div>
  </div>
  <div class="container-fluid imagecontainer">
    <img src="./assets/images/home.webp" alt="Imagen de incio Donación" />
  </div>
  <div class="container background30 w-100 fixed-bottom curved-top">
    <h1 class="screenhomeh1">Si haces el bien hoy, lo veras multiplicado en el futuro!</h1>
    <h3 class="screenhomeh3">Donemos solo $5.000 cop para construir un mejor futuro.</h3>
    <br />
    <div class="d-flex justify-content-center align-items-center flex-column">
      <button class="btn btn-lg mb-3 firstbutton buttonscreenhome w-100" style="color: #FFFFFF" data-bs-toggle="modal" data-bs-target="#signupmodal">Registrarme</button>
      <button class="btn btn-lg secondbutton buttonscreenhome w-100" data-bs-toggle="modal" data-bs-target="#loginmodal">Iniciar Sesion</button>
    </div>
  </div>
  <!-- Modal -->

  <div class="modal fade" id="signupmodal" tabindex="-1" aria-labelledby="signupmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-5 pb-4 border-bottom-0">
          <h1 class="fw-bold mb-0 fs-2">Registro</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-5 pt-0">
          <form class="" method="post" action="">
            <?php if(isset($_GET['errors'])) {
            $error=$_GET['errors'];
            if($error=='mailno'){
                $msg='Correo está siendo usado';
            }elseif($error=='phoneno'){
              $msg='Celular está siendo usado';
            }elseif($error=='usernameno'){
              $msg='Nombre de usuario en uso';
            }elseif($error=='docnumberno'){
              $msg='Número de documento en uso';
            }elseif($error=='phonelen'){
              $msg='Celular debe contener 10 números';
            }elseif($error=='passlen'){
              $msg='Contraseña minimo 8 caracteres';
            }elseif($error=='usernamelen'){
              $msg='Usuario minimo 4 caracteres';
            }elseif($error=='usernamespaces'){
              $msg='Usuario no puede tener espacios en blanco';
            }else{
                $msg='';
            }
            echo '<div class="alert alert-danger" role="alert">'.$msg.'</div>';
            } ?>
            <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control rounded-3" id="floatingName" placeholder="John" <?php if(isset($_GET['name'])) { echo 'value="'.$_GET['name'].'"'; } ?> required>
              <label for="floatingName">Nombre(s)</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" name="lastname" class="form-control rounded-3" id="floatingLastname" placeholder="Doe" <?php if(isset($_GET['lastname'])) { echo 'value="'.$_GET['lastname'].'"'; } ?> required>
              <label for="floatingLastname">Apellidos</label>
            </div>
            <div class="form-floating mb-3">
              <input type="text" name="username" class="form-control rounded-3" id="floatingusername" placeholder="myusername" <?php if(isset($_GET['username'])) { echo 'value="'.$_GET['username'].'"'; } ?> required>
              <label for="floatingusername">Usuario</label>
            </div>
            <div class="form-floating mb-3">
            <select class="form-select " name="doctype" id="inlineFormSelectPref" required>
              <option selected>Seleccione...</option>
              <option value="1" <?php if(isset($_GET['doctype'])) { if($_GET['doctype']=='1'){echo 'selected';} } ?>>Cédula de ciudadania</option>
              <option value="2" <?php if(isset($_GET['doctype'])) { if($_GET['doctype']=='2'){echo 'selected';} } ?>>Tarjeta de identidad</option>
            </select>
            <label for="floatingDoctype">Tipo de documento</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" name="docnumber" class="form-control rounded-3 col-9" id="floatingdocn" placeholder="**********" <?php if(isset($_GET['docnumber'])) { echo 'value="'.$_GET['docnumber'].'"'; } ?> required>
                <label for="floatingdocn">Documento</label>
            </div>
            <div class="form-floating mb-3">
                <input type="date" name="birth_date" class="form-control rounded-3 col-9" id="floatingdate" <?php if(isset($_GET['date'])) { echo 'value="'.$_GET['date'].'"'; }else{ echo 'value="'.date("Y-m-d").'"';} ?> required>
                <label for="floatingdate">Fecha de nacimiento</label>
            </div>
            <div class="form-floating mb-3">
            <select class="form-select " name="gender" id="inlineFormSelectPrefGen" required>
              <option selected>Seleccione...</option>
              <option value="male" <?php if(isset($_GET['gender'])) { if($_GET['gender']=='male'){echo 'selected';} } ?>>Hombre</option>
              <option value="female" <?php if(isset($_GET['gender'])) { if($_GET['gender']=='female'){echo 'selected';} } ?>>Mujer</option>
              <option value="other" <?php if(isset($_GET['gender'])) { if($_GET['gender']=='other'){echo 'selected';} } ?>>Otro</option>
            </select>
            <label for="floatinggender">Género</label>
            </div>
            <div class="form-floating mb-3">
                <input type="number" name="phone" class="form-control rounded-3 col-9" id="floatingphone" placeholder="3** *** ****" <?php if(isset($_GET['phone'])) { echo 'value="'.$_GET['phone'].'"'; } ?> required>
                <label for="floatingphone">Celular</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control rounded-3 col-9" id="floatingemail" placeholder="mail@ejemplo.com" <?php if(isset($_GET['email'])) { echo 'value="'.$_GET['email'].'"'; } ?> required>
                <label for="floatingemail">Correo</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control rounded-3 col-9" id="floatingpass" placeholder="********" required>
                <label for="floatingpass">Contraseña</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="rpassword" class="form-control rounded-3 col-9" id="floatingrpass" placeholder="********" required>
                <label for="floatingrpass">Repetir Contraseña</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite end-0" name="edit" type="submit">Registrarme</button>
            <small class="text-body-secondary">Recuerda verificar tus datos.</small>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->

  <div class="modal fade" id="loginmodal" tabindex="-1" aria-labelledby="loginmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-5 pb-4 border-bottom-0">
          <h1 class="fw-bold mb-0 fs-2">Iniciar Sesión</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-5 pt-0">
          <form class="" method="post" action="">
            <?php if(isset($_GET['error'])) {
            $error=$_GET['error'];
            if($error=='1'){
                $msg='Usuario o correo no registrados';
            }elseif($error=='2'){
                $msg='Contraseña incorrecta';
            }else{
                $msg='';
            }
            echo '<div class="alert alert-danger" role="alert">'.$msg.'</div>';
            } ?>
            <div class="form-floating mb-3">
              <input type="text" name="emailorusername" class="form-control rounded-3" id="floatingInput"
                value="<?php if(isset($_GET['mail'])) { echo $_GET['mail'];} ?>" placeholder="nombre@email.com"
                required>
              <label for="floatingInput">Correo o usuario</label>
            </div>
            <div class="form-floating mb-3">
              <input type="password" name="password" class="form-control rounded-3" id="floatingPassword"
                placeholder="*********" required>
              <label for="floatingPassword">Contraseña</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite" name="login"
              type="submit">Entrar</button>
            <small class="text-body-secondary">Al entrar aceptas nuestros <a href="./index.html" class=""></a>
              terminos.</small>
              <br/>
            <small class="text-body-secondary"><a href="./forgot" class="text-decoration-none" style="color: #1F2D36 !important;">¿Olvidaste tu contraseña?</a></small>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
    crossorigin="anonymous"></script>

 
  <script>
    window.addEventListener("load", function () {
      const preloader = document.getElementById("preloader");
      preloader.classList.add("hide-preloader");
      if (window.Notification && Notification.permission !== 'granted') {
    Notification.requestPermission().then((permission) => {
      if (permission === 'granted') {
        console.log('Permission for notifications granted');
      } else {
        console.log('Permission for notifications denied');
      }
    });
  }
    });
  </script>
  <?php if(isset($_GET['error'])) {
    echo "
  <script>
    $( document ).ready(function() {
    $('#loginmodal').modal('toggle')
});

  </script>";
} ?><?php if(isset($_GET['errors'])) {
  echo "
<script>
  $( document ).ready(function() {
  $('#signupmodal').modal('toggle')
});

</script>";
} ?>
<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-analytics.js";
  import { getMessaging } from "https://www.gstatic.com/firebasejs/9.21.0/firebase-messaging.js";
  
  const firebaseConfig = {
    apiKey: "AIzaSyAwniByf6z00j8HOyj0bIkhim4CQNGOsks",
    authDomain: "youngdreamersfortalaigua-01.firebaseapp.com",
    projectId: "youngdreamersfortalaigua-01",
    storageBucket: "youngdreamersfortalaigua-01.appspot.com",
    messagingSenderId: "505716996899",
    appId: "1:505716996899:web:560ff6434ee2ab2841aa96",
    measurementId: "G-G124TRGM7Y"
  };
  
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
  const messaging = getMessaging(app);
  
  
  // Register the messaging service worker
  
  // Request permission to receive notifications
  messaging.requestPermission().then(function() {
    console.log('Notification permission granted.');
  }).catch(function(err) {
    console.log('Unable to get permission to notify.', err);
  });
  
  // Handle incoming messages
  messaging.onMessage(function(payload) {
    console.log('Message received. ', payload);
  });
</script>
<script type="text/javascript">
      if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('/sw.js').then(function(registration) {
        console.log('ServiceWorker registration successful with scope: ', registration.scope);
      }, function(err) {
        console.log('ServiceWorker registration failed: ', err);
      });
    });
  }
</script>

</body>

</html>