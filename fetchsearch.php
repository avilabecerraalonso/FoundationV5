<?php
session_start();
require_once 'config.php';
header('Content-Type: text/html; charset=utf-8');
mysqli_set_charset($conn, "utf8");

if (!isset($_COOKIE['session_id'])) {
    header('Location: ./');
    exit;
}
                $search_query = $_POST['keyword'];

                $sql = "SELECT id, title, content, category, created_at FROM news WHERE title LIKE '%$search_query%' OR content LIKE '%$search_query%' OR category LIKE '%$search_query%'
        UNION
        SELECT id, title, content, category, created_at FROM events WHERE title LIKE '%$search_query%' OR content LIKE '%$search_query%' OR category LIKE '%$search_query%'
        LIMIT 10";


                $result = mysqli_query($conn, $sql);


                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }

                $count = mysqli_num_rows($result);
                if($count>0){


                while ($row = mysqli_fetch_assoc($result)) {
                    $lastLoginTime = strtotime($row['created_at']);

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
                    } elseif ($time_diff < 31536000) { 
                        $months = floor($time_diff / 2592000);
                        $time_ago = ($months == 1) ? "1 mes" : "$months meses";
                    } else { 
                        $years = floor($time_diff / 31536000);
                        $time_ago = ($years == 1) ? "1 año" : "$years años";
                    }

                    echo '<a href="./new?id=' . $row['id'] . '" class="text-decoration-none"><div class="card mt-2">
    <h5 class="card-header">' . $row['title'] . '</h5>
    <div class="card-body">
        <p class="card-text text-truncate">' . $row['content'] . '</p>
    </div>
    <div class="card-footer text-body-secondary">
    <div class="row">
    <p class="text-start col-6 mb-0">Categoria: ' . $row['category'] . '</p>
    <p class="text-end col-6 mb-0 text-truncate">Publicado hace: ' . $time_ago  . '</p>
    </div>
    </div>
</div></a>';
                }
            }else{
                echo '<h3>Sin resultados</h3>';
            }
                mysqli_close($conn);
                ?>