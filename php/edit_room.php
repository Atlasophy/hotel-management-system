<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "hotel_management"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = (int)$_GET["id"];

$stmt = $conn->prepare(
    "SELECT * FROM rooms WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$room = $result->fetch_assoc();

if (!$room) {
    die("Room not found");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $room_number = trim($_POST["room_number"]);
    $room_type = trim($_POST["room_type"]);
    $price = trim($_POST["price"]);
    $status = trim($_POST["status"]);

    $stmt = $conn->prepare(
        "UPDATE rooms
         SET room_number=?,
             room_type=?,
             price=?,
             status=?
         WHERE id=?"
    );

    $stmt->bind_param(
        "ssdsi",
        $room_number,
        $room_type,
        $price,
        $status,
        $id
    );

    $stmt->execute();

    header("Location: rooms.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Room</title>

<style>

body{
    font-family:Arial,sans-serif;
    background:#f4f4f4;
    padding:30px;
}

.card{
    background:white;
    max-width:800px;
    margin:auto;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    box-sizing:border-box;
}

button{
    background:#007bff;
    color:white;
    border:none;
    padding:10px 20px;
    border-radius:5px;
    cursor:pointer;
}

a{
    text-decoration:none;
}

</style>

</head>
<body>

<div class="card">

<h1>🛏️ Edit Room</h1>

<form method="POST">

    <input
        type="text"
        name="room_number"
        value="<?= htmlspecialchars($room["room_number"]) ?>"
        required
    >

    <input
        type="text"
        name="room_type"
        value="<?= htmlspecialchars($room["room_type"]) ?>"
        required
    >

    <input
        type="number"
        step="0.01"
        name="price"
        value="<?= htmlspecialchars($room["price"]) ?>"
        required
    >

    <input
        type="text"
        name="status"
        value="<?= htmlspecialchars($room["status"]) ?>"
        required
    >

    <button type="submit">
        Update Room
    </button>

</form>

<br>

<a href="rooms.php">
    ← Back to Rooms
</a>

</div>

</body>
</html>