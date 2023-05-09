<?php
session_start();
require_once 'config.php';
header('Content-Type: text/html; charset=utf-8');
mysqli_set_charset($conn, "utf8");

if (!isset($_COOKIE['session_id'])) {
    header('Location: ./');
    exit;
}
               header('Content-Type: text/html; charset=utf-8');

$keyword = $_POST['keyword'];


$sql = "SELECT * FROM badget";
if (!empty($keyword)) {
  $sql .= " WHERE name LIKE '%$keyword%' OR description LIKE '%$keyword%'";
}

mysqli_set_charset($conn, "utf8");

$result = $conn->query($sql);
if ($result->num_rows > 0) {

  // Output table rows
  while ($row = $result->fetch_assoc()) {
    $output .= '<a href="#"  data-bs-toggle="modal" data-bs-target="#ModalEditBadget' . $row['id'] . '" class="col-md-4 col-4 text-center mb-4 text-decoration-none color10" title="' . $row['name'] . '" "><img src="./assets/images/badgets/' . $row['url'] . '" class="badgetimg" alt="Insignia ' . $row['name'] . '"/><div class="text-decoration-none text-center">' . $row['name'] . '</div></a>';
                $output .=  '<div class="modal fade" id="ModalEditBadget' . $row['id'] . '" tabindex="-1" aria-labelledby="ModalEditBadget' . $row['id'] . 'Label" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content rounded-4 shadow">
                      <div class="modal-header p-5 pb-4 border-bottom-0">
                        <h1 class="fw-bold mb-0 fs-2">Editar Evento</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body p-5 pt-0">
                        <form class="" method="post" action="" enctype="multipart/form-data">
                        <input type="text" name="idbadgetedit" class="visually-hidden" value="' . $row['id'] . '">
                        <input type="text" name="urlbadgetedit" class="visually-hidden" value="' . $row['url'] . '">
                          <div class="form mb-3">
                            <label class="form-label">Titulo</label>
                            <textarea name="titlebadgetedit" class="form-control rounded-3" id="floatingtitle" rows="1" required>' . $row['name'] . '</textarea>
                          </div>
                        <div class="form mb-3">
                            <label class="form-label">Icono</label>
                                <input type="file" class="form-control" name="icobadget" id="inputGroupFile03" aria-describedby="inputGroupFileAddon03" aria-label="Upload">
                        </div>
                          <div class="form mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="content" class="form-control rounded-3" id="floatingcontent" rows="8" required>' . $row['description'] . '</textarea>
                          </div>
                          <button class="w-100 mb-2 btn btn-lg rounded-3 background30 fwhite end-0" name="edit" type="submit">Editar</button>
                        </form>
                        <form class="" method="post" action="">
                        <input type="text" name="idbadgetdelete" class="visually-hidden" value="' . $row['id'] . '">
                        <input type="text" name="urlbadgetdelete" class="visually-hidden" value="' . $row['url'] . '">
                        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-danger fwhite end-0" name="edit" type="submit">Eliminar</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>';
  

  }



} else {
  // Output no results message
  $output = "<p>Sin resultados</p>";
}

$conn->close();
echo $output;