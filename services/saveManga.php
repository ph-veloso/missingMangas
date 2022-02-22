<?php
    require_once('../connections/CONNECT_SQL.php');

    if (!$conn) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO volumes_atual(manga, ultimo_volume, volumes_possuidos) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'sis', $manga, $volumeAtual, $volumePossuido);

    $manga = $_POST['manga'] ;
    $volumePossuido = $_POST['volume_possuido'] ;
    $volumeAtual = $_POST['volume_atual'];

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    mysqli_close($conn);
?>