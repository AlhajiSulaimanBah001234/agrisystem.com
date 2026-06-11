<?php
session_start();
include("../config/db.php");
include("../includes/functions.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "admin") {
    header("Location: ../login.php");
    exit();
}

$type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';

if ($type && in_array($format, ['csv', 'json'])) {
    logAction($conn, $_SESSION['user_id'], "Exported $type as $format");

    $data = [];
    $filename = "agri_export_" . $type . "_" . date('Y-m-d');

    switch ($type) {
        case 'farmers':
            $result = $conn->query("SELECT id, name, email, status, created_at FROM users WHERE role='farmer'");
            while ($row = $result->fetch_assoc()) $data[] = $row;
            break;
        case 'prices':
            $result = $conn->query("SELECT * FROM market_prices");
            while ($row = $result->fetch_assoc()) $data[] = $row;
            break;
        case 'posts':
            $result = $conn->query("SELECT id, title, category_id, status, created_at FROM posts");
            while ($row = $result->fetch_assoc()) $data[] = $row;
            break;
        case 'questions':
            $result = $conn->query("SELECT id, user_id, question, status, created_at FROM questions");
            while ($row = $result->fetch_assoc()) $data[] = $row;
            break;
    }

    if ($format === 'json') {
        header('Content-Type: application/json');
        header("Content-Disposition: attachment; filename=$filename.json");
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit();
    }

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=$filename.csv");
    $out = fopen('php://output', 'w');
    if (!empty($data)) {
        fputcsv($out, array_keys($data[0]));
        foreach ($data as $row) fputcsv($out, $row);
    }
    fclose($out);
    exit();
}

$base_path = "../";
$page_title = "Export Data";
include("../includes/header.php");
?>

<h1 class="page-title">📤 Export Data</h1>
<p class="page-subtitle">Open data export for Digital Public Good compliance</p>

<div class="card">
    <h3>Available Exports</h3>
    <table class="data-table">
        <tr><th>Dataset</th><th>CSV</th><th>JSON</th></tr>
        <tr>
            <td>Registered Farmers</td>
            <td><a href="?type=farmers&format=csv" class="btn btn-secondary btn-sm">Download CSV</a></td>
            <td><a href="?type=farmers&format=json" class="btn btn-secondary btn-sm">Download JSON</a></td>
        </tr>
        <tr>
            <td>Market Prices</td>
            <td><a href="?type=prices&format=csv" class="btn btn-secondary btn-sm">Download CSV</a></td>
            <td><a href="?type=prices&format=json" class="btn btn-secondary btn-sm">Download JSON</a></td>
        </tr>
        <tr>
            <td>Farming Articles</td>
            <td><a href="?type=posts&format=csv" class="btn btn-secondary btn-sm">Download CSV</a></td>
            <td><a href="?type=posts&format=json" class="btn btn-secondary btn-sm">Download JSON</a></td>
        </tr>
        <tr>
            <td>Farmer Questions</td>
            <td><a href="?type=questions&format=csv" class="btn btn-secondary btn-sm">Download CSV</a></td>
            <td><a href="?type=questions&format=json" class="btn btn-secondary btn-sm">Download JSON</a></td>
        </tr>
    </table>
</div>

<a href="dashboard.php" class="btn btn-secondary">Back</a>

<?php include("../includes/footer.php"); ?>
