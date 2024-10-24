<?php
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

$dotenv->load();


// Load .env file

$dbHost = $_SERVER['DB_HOST'];
$dbUser = $_SERVER['DB_USER'];
$dbPass = $_SERVER['DB_PASSWORD'];
$dbName = $_SERVER['DB_NAME'];
$dbPort = $_SERVER['DB_PORT'];


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$connection = mysqli_init();



if (!mysqli_real_connect($connection, $dbHost, $dbUser, $dbPass, $dbName, $dbPort, NULL, MYSQLI_CLIENT_SSL)) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Connection failed: ' . mysqli_connect_error()
    ]));
}

// Fetch data from the users table
if (isset($_POST['action']) && $_POST['action'] == 'getdata') {
    $query = "SELECT * FROM users";
    $result = mysqli_query($connection, $query);
    $arr = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $arr
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => mysqli_error($connection)
        ]);
    }

    exit;
}

// Edit data based on ID
if (isset($_POST['action']) && $_POST['action'] == 'editdata') {
    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $query = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($connection, $query);
    $arr = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $arr[] = $row;
        }
        echo json_encode([
            'status' => 'success',
            'data' => $arr
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => mysqli_error($connection)
        ]);
    }

    exit;
}

// Update user data
if (isset($_POST['action']) && $_POST['action'] == 'update_data') {
    $data = $_POST;

    $id = mysqli_real_escape_string($connection, $data['id']);
    $name = mysqli_real_escape_string($connection, $data['name']);
    $state = mysqli_real_escape_string($connection, $data['state']);
    $city = mysqli_real_escape_string($connection, $data['city']);
    $email = mysqli_real_escape_string($connection, $data['email']);
    $phonenumber = mysqli_real_escape_string($connection, $data['phonenumber']);

    $query = "UPDATE users
              SET name = '$name', 
                  state = '$state', 
                  city = '$city', 
                  email = '$email', 
                  phonenumber = '$phonenumber'
              WHERE id = '$id'";

    $result = mysqli_query($connection, $query);

    echo json_encode([
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'Data updated successfully' : mysqli_error($connection)
    ]);

    exit;
}

// Add new user data
if (isset($_POST['action']) && $_POST['action'] == 'add_data') {
    $data = $_POST;

    $name = mysqli_real_escape_string($connection, $data['name']);
    $state = mysqli_real_escape_string($connection, $data['state']);
    $city = mysqli_real_escape_string($connection, $data['city']);
    $email = mysqli_real_escape_string($connection, $data['email']);
    $phonenumber = mysqli_real_escape_string($connection, $data['phonenumber']);

    $query = "INSERT INTO users (name, state, city, email, phonenumber) 
              VALUES ('$name', '$state', '$city', '$email', '$phonenumber')";

    $result = mysqli_query($connection, $query);

    if ($result) {
        echo json_encode([
            'status' => 'success',
            'id' => mysqli_insert_id($connection)
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => mysqli_error($connection)
        ]);
    }

    exit;
}

// Delete user data based on ID
if (isset($_POST['action']) && $_POST['action'] == 'delete_data') {
    $id = mysqli_real_escape_string($connection, $_POST['id']);
    $query = "DELETE FROM users WHERE id = '$id'";

    $result = mysqli_query($connection, $query);

    echo json_encode([
        'status' => $result ? 'success' : 'error',
        'message' => $result ? 'Data deleted successfully' : mysqli_error($connection)
    ]);

    exit;
}

// Close the connection
mysqli_close($connection);
