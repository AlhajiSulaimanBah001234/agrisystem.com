
<?php

/* =======================
   DATABASE CONFIGURATION
======================= */

$host = "localhost";

$user = "root";

/*
CHANGE THIS TO YOUR REAL MYSQL PASSWORD
Examples:
""          → if empty
"root"
"1234"
*/
$pass = "1234";

$dbname = "agri_system";


/* =======================
   CREATE CONNECTION
======================= */

$conn = new mysqli(
    $host,
    $user,
    $pass,
    $dbname
);


/* =======================
   CHECK CONNECTION
======================= */

if ($conn->connect_errno) {

    die(
        "Database connection failed:<br>" .
        $conn->connect_error
    );

}


/* =======================
   SET CHARSET
======================= */

$conn->set_charset("utf8mb4");

?>
