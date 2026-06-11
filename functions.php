<?php

// Clean and protect user input
function escape($conn, $value) {
    return mysqli_real_escape_string($conn, trim($value));
}


// Save admin/system activity logs
function logAction($conn, $admin_id, $action) {

    $admin_id = (int)$admin_id;
    $action = escape($conn, $action);

    $sql = "INSERT INTO logs (admin_id, action)
            VALUES ('$admin_id', '$action')";

    return mysqli_query($conn, $sql);
}


// Dashboard trend badge
function trendBadge($trend) {

    $class = "badge-stable";

    if ($trend == "Increasing") {
        $class = "badge-up";
    }

    if ($trend == "Decreasing") {
        $class = "badge-down";
    }

    return "
        <span class='badge $class'>
            $trend
        </span>
    ";
}


// Status badge
function statusBadge($status) {

    $good = [
        "Active",
        "Published",
        "Answered",
        "Completed",
        "Paid"
    ];

    $class = in_array($status, $good)
        ? "badge-published"
        : "badge-draft";

    return "
        <span class='badge $class'>
            $status
        </span>
    ";
}


// Upload image
function uploadImage($file, $prefix = "img") {

    if (
        !isset($file['name']) ||
        empty($file['name'])
    ) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowed = [
        "jpg",
        "jpeg",
        "png",
        "gif",
        "webp"
    ];

    $extension = strtolower(
        pathinfo(
            $file['name'],
            PATHINFO_EXTENSION
        )
    );

    if (!in_array($extension, $allowed)) {
        return false;
    }

    $filename =
        $prefix .
        "_" .
        time() .
        "_" .
        uniqid() .
        "." .
        $extension;

    $upload_folder =
        __DIR__ .
        "/../uploads/";

    if (!file_exists($upload_folder)) {
        mkdir(
            $upload_folder,
            0777,
            true
        );
    }

    $destination =
        $upload_folder .
        $filename;

    if (
        move_uploaded_file(
            $file['tmp_name'],
            $destination
        )
    ) {
        return $filename;
    }

    return false;
}


// Format money
function money($amount) {
    return "Le " .
        number_format(
            $amount,
            2
        );
}


// Count rows
function countRows($conn, $table) {

    $sql =
        "SELECT COUNT(*) total
         FROM $table";

    $result =
        mysqli_query(
            $conn,
            $sql
        );

    $row =
        mysqli_fetch_assoc(
            $result
        );

    return $row['total'];
}

?>