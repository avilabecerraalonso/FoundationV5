<?php
session_start();
require_once 'config.php';

header('Content-Type: text/html; charset=utf-8');
mysqli_set_charset($conn, "utf8");

if (!isset($_COOKIE['session_id'])) {
    header('Location: ./');
    exit;
}


$sql = "SELECT * FROM users WHERE cookie='" . $_COOKIE['session_id'] . "'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
} else {
    header('Location: ./');
    exit;
}



$results_per_page = 15;

$page = $_POST['page'];
$keyword = $_POST['keyword'];


$start = ($page - 1) * $results_per_page;
$limit = $results_per_page;

$sql = "SELECT * FROM events";
if (!empty($keyword)) {
	$sql .= " WHERE title LIKE '%$keyword%' OR content LIKE '%$keyword%'";
}
$sql .= " LIMIT $start, $limit";

mysqli_set_charset($conn, "utf8");

$result = $conn->query($sql);
if ($result->num_rows > 0) {
	// Output table header
	$output = "<table class='table table-hover' stile='border-radius:25px;'>
					<thead>
					<tr class='backgroundalt'>
						<th class='col-9' scope='col'>Titulo</th>
						<th class='col-3' scope='col'>Acción</th>
				  	</tr>
					</thead>
					<tbody>";

	// Output table rows
	while ($row = $result->fetch_assoc()) {
		$output .= "<tr>
						<td>" . $row['title'] . "</td>
						<td><button type='button' class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#ModalEdit".$row['id']."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-pencil-fill' viewBox='0 0 16 16'>
						<path d='M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z'/>
					  </svg></button>
					  <button type='button' class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#Modaldelete".$row['id']."'><svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='currentColor' class='bi bi-trash3-fill' viewBox='0 0 16 16'>
					  <path d='M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5Zm-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5ZM4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06Zm6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528ZM8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5Z'/>
					</svg></button></td>
					</tr>
					";

	}

	
	$output .= "</tbody></table>";
	$sql = "SELECT COUNT(*) AS total FROM events";
	if (!empty($keyword)) {
		$sql .= " WHERE title LIKE '%$keyword%' OR content LIKE '%$keyword%'";
	}

	$result = $conn->query($sql);
	$row = $result->fetch_assoc();
	$total_pages = ceil($row['total'] / $results_per_page);
	if($total_pages > 1){
	$output .= "<nav aria-label='userspagination'>
	<ul class='pagination justify-content-center'>
	";
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page) {
        $output .= "<li class='page-item active' aria-current='page'><a href='#' data-page='$i'><span class='page-link'>$i</span></a></li>";
    } else {
        $output .= "<li class='page-item'><a class='page-link' href='#' data-page='$i'>$i</a></li>";
    }
}}
$output .= "</ul>
</nav>";

} else {
	$output = "<p>Sin resultados</p>";
}

$conn->close();
echo $output;
?>
