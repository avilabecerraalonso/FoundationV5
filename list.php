<?php
session_start();
require_once 'config.php';

if (!isset($_COOKIE['session_id'])) {
    header('Location: ./');
    exit;
}

$cookieuser = $_COOKIE['session_id'];
mysqli_set_charset($conn, "utf8");

$sql = "SELECT * FROM users WHERE cookie = '$cookieuser'";
$resultss = mysqli_query($conn, $sql);

if ($resultss && mysqli_num_rows($resultss) > 0) {
    $user_info = mysqli_fetch_assoc($resultss);
    $name = $user_info['name'];
    $level = $user_info['level'];
    if ($level!=='1') {
      header('Location: ./account');
      exit;
    }
} else {
    echo "Error fetching user information: " . mysqli_error($conn);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['iddelete'])) {

    $iddelete = $_POST['iddelete'];

    $sqldelete = "DELETE FROM users WHERE id = $iddelete";

    // Execute the SQL query
    if (mysqli_query($conn, $sqldelete)) {
            header('Location: list?delete=y');
    } else {
            header('Location: list?delete=n');

    }

    // Close the database connection
    mysqli_close($conn);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idupdate']) && isset($_POST['level'])) {
    
    $idupdate = $_POST['idupdate'];
    $level = $_POST['level'];

    $sql = "UPDATE users SET level = $level WHERE id = $idupdate";

    if (mysqli_query($conn, $sql)) {
      header("Location: list?update=y");
    } else {
      header("Location: list?update=n");
    }

    mysqli_close($conn);
}

$querymodal = "SELECT * FROM users";
$resultmodal = mysqli_query($conn, $querymodal);
?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Fundación Jóvenes Soñadores Por Talaigua - Descripción de la fundación">
    <meta name="keywords" content="Fundación, Jóvenes, Soñadores, Talaigua">
    <meta name="author" content="Martin Avila Becerra">
    <meta name="robots" content="index,follow">
    <meta name="language" content="Spanish">
    <meta name="revisit-after" content="30 days">
    <link rel="canonical" href="https://youngdreamersfortalaigua.org/list">
    <link rel="manifest" href="./manifest.json">
    <title>Lista de Usuarios | Fundación Jóvenes Soñadores Por Talaigua</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link href="./assets/css/main.css" rel="stylesheet"><script type="text/javascript">
		$(document).ready(function(){
			function loadData(page){
				var keyword = $('#keyword').val();
				$.ajax({
					url: 'fetch.php',
					type: 'POST',
					data: {page: page, keyword: keyword},
					success: function(response){
						$('#result').html(response);
					}
				});
			}
			loadData(1);

			$(document).on('click', '.pagination li a', function(e){
				e.preventDefault();
				var page = $(this).data('page');
				loadData(page);
			});

			// Function to handle search input change event
			$('#keyword').on('input', function(){
				loadData(1);
			});
		});
	</script>
  </head>
  <body>
  <div class="preloader" id="preloader">
      <div class="loader"></div>
    </div>
    <div class="conatiner-fluid p-4">
        <h3>Lista De Usuarios</h3>
        <div class="row">
            <div class="form-group has-search col-12">
                <input type="search" class="form-control backgroundalt searchhome" placeholder="Buscar usuario..." id="keyword" name="keyword">
  </div><br/>
            <div class="result" id="result" style="margin-top:8px">

            </div>
            <?php while ($rowmodal = mysqli_fetch_assoc($resultmodal)): 
              $adminlevel ='';
              $editorlevel ='';
              $userlevel ='';
              if($rowmodal['level']=='1'){
                $adminlevel = 'selected';
              }elseif($rowmodal['level']=='2'){
                $editorlevel = 'selected';
              }else{
                $userlevel = 'selected';
              }

$lastLoginTime = strtotime($rowmodal["last_login"]);
if(empty($rowmodal['last_login'])){
  $time_ago = "Nunca";
}else{
$time_diff = time() - $lastLoginTime;

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
} else { 
    $months = floor($time_diff / 2592000);
    $time_ago = ($months == 1) ? "1 mes" : "$months meses";
} }
              ?>
              <div class="modal fade" id="ModalEdit<? echo $rowmodal['id']; ?>" tabindex="-1" aria-labelledby="ModalEdit<? echo $rowmodal['id']; ?>Label" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-5 pb-4 border-bottom-0">
          <h1 class="fw-bold mb-0 fs-2">Editar cuenta</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-5 pt-0">
        <?php if($rowmodal['id']=='1'){
            echo '<h4>Usuario no se puede modificar</h4>';
            echo '<small class="text-body-secondary">Ultima vez conectado hace '. $time_ago.'.</small>';
          }else{?>
          <form class="" method="post" action="">
            <h2><? echo $rowmodal['name'].' '.$rowmodal['lastname']; ?></h2>
            <div class="form-floating mb-3">
            <input type="text" class="visually-hidden" name="idupdate" value="<? echo $rowmodal['id']; ?>"/>
            <select class="form-select " name="level" id="inlineFormSelectPref" required>
              <option selected>Seleccione...</option>
              <option value="1" <? echo $adminlevel;?>>Administrador</option>
              <option value="2" <? echo $editorlevel;?>>Editor</option>
              <option value="0" <? echo $userlevel;?>>Usuario</option>
			</select>
            <small class="text-body-secondary"><?php if($time_ago=='Nunca'){echo'Nunca se ha conectado';}else{echo "Ultima vez conectado hace " . $time_ago; }?></small>
              
            <label for="floatingDoctype">Tipo de usuario</label>
            </div>
            <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite end-0" name="edit" type="submit">Editar</button>
            
          </form>
          <?php } ?>
        </div>
      </div>
    </div>
	  </div>

    <div class="modal fade" id="Modaldelete<? echo $rowmodal['id']; ?>" tabindex="-1" aria-labelledby="Modaldelete<? echo $rowmodal['id']; ?>Label" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content rounded-4 shadow">
        <div class="modal-header p-5 pb-4 border-bottom-0">
          <h1 class="fw-bold mb-0 fs-2">Eliminar cuenta</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body p-5 pt-0">
          <?php if($rowmodal['id']=='1'){
            echo '<h4>Usuario no se puede eliminar</h4>';
          }else{?>
          <form class="" method="post" action="">
            <input type="text" class="visually-hidden" name="iddelete" value="<? echo $rowmodal['id']; ?>"/>
            <h4>La cuenta de <a class="text-reset fw-bolder text-danger-emphasis"><? echo $rowmodal['name'].' '.$rowmodal['lastname']; ?></a> será eliminada de forma permanente</h4>
            <button class="w-100 mb-2 mt-4 btn btn-lg rounded-3 btn-danger fwhite end-0" name="edit" type="submit">Eliminar</button>
            
          </form>
          <?php } ?>
        </div>
      </div>
    </div>
	  </div>
            <?php endwhile ?>
        </div>
    </div>

    
  <nav class="nav nav-fill fixed-bottom backgroundalt pb-3">
  <a class="nav-link" href="./dashboard"><svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-house-heart" viewBox="0 0 16 16">
  <path d="M8 6.982C9.664 5.309 13.825 8.236 8 12 2.175 8.236 6.336 5.309 8 6.982Z"/>
  <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.707L2 8.207V13.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V8.207l.646.646a.5.5 0 0 0 .708-.707L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.707 1.5ZM13 7.207V13.5a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5V7.207l5-5 5 5Z"/>
</svg><div class="text-decoration-none text-center">Home</div></a>
  <a class="nav-link" href="./aboutus"><svg xmlns="http://www.w3.org/2000/svg" width="100%" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
  <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
</svg><div class="text-decoration-none text-center">Nosotros</div></a>
  <a class="nav-link" href="./donations"><svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
  <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9H5.5zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518l.087.02z"/>
  <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11zm0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"/>
</svg><div class="text-decoration-none text-center">Donaciones</div></a>
  <a class="nav-link active" aria-current="page" href="./account"><svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
  <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
</svg><div class="text-decoration-none text-center">Cuenta</div></a>
</nav>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="./assets/js/app.js"></script>
    <script>
      window.addEventListener("load", function(){
          const preloader = document.getElementById("preloader");
          preloader.classList.add("hide-preloader");
      });
  </script>
  <script src="./assets/js/app.js"></script>
  </body>
</html>

