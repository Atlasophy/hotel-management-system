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

/*
|--------------------------------------------------------------------------
| DELETE ROOM
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = (int)$_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM rooms WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    header("Location: rooms.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| ADD ROOM
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $room_number = trim($_POST["room_number"]);
    $room_type = trim($_POST["room_type"]);
    $price = trim($_POST["price"]);
    $status = trim($_POST["status"]);

    $stmt = $conn->prepare(
        "INSERT INTO rooms
        (room_number, room_type, price, status)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssds",
        $room_number,
        $room_type,
        $price,
        $status
    );

    $stmt->execute();

    header("Location: rooms.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| ROOM COUNT
|--------------------------------------------------------------------------
*/

$totalRoomsResult = $conn->query(
    "SELECT COUNT(*) AS total FROM rooms"
);

$totalRooms = $totalRoomsResult->fetch_assoc()["total"];

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if ($search != "") {

    $stmt = $conn->prepare(
        "SELECT * FROM rooms
         WHERE room_number LIKE ?
         ORDER BY id DESC"
    );

    $searchTerm = "%$search%";

    $stmt->bind_param(
        "s",
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $result = $conn->query(
        "SELECT * FROM rooms
         ORDER BY id DESC"
    );
}

$rooms = [];

while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Room Management</title>

<style>

body{
    font-family:Arial, sans-serif;
    background:#f4f4f4;
    padding:30px;
}

.card{
    background:white;
    padding:20px;
    border-radius:10px;
    max-width:1100px;
    margin:auto;
    box-shadow:0 0 10px rgba(0,0,0,.1);
}

.stats{
    background:#28a745;
    color:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:10px;
    margin-bottom:10px;
    box-sizing:border-box;
}

button{
    padding:10px 20px;
    border:none;
    border-radius:5px;
    background:#007bff;
    color:white;
    cursor:pointer;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table, th, td{
    border:1px solid #ddd;
}

th, td{
    padding:12px;
    text-align:left;
}

th{
    background:#f0f0f0;
}

.delete-btn{
    background:#dc3545;
    color:white;
    padding:6px 12px;
    border-radius:5px;
    text-decoration:none;
    margin-left:5px;
}

.edit-btn{
    background:#28a745;
    color:white;
    padding:6px 12px;
    border-radius:5px;
    text-decoration:none;
}

.nav{
    margin-bottom:20px;
}

.nav a{
    margin-right:15px;
    text-decoration:none;
}

</style>

</head>
<body>

<div class="card">

<div class="nav">
    <a href="../index.php">Guests</a>
    <a href="rooms.php">Rooms</a>
</div>

<h1>🛏️ Room Management</h1>

<div class="stats">
    <h3>Total Rooms</h3>
    <h1><?= $totalRooms ?></h1>
</div>

<h2>Add Room</h2>

<form method="POST">

    <input
        type="text"
        name="room_number"
        placeholder="Room Number"
        required
    >

    <input
        type="text"
        name="room_type"
        placeholder="Single / Double / Suite"
        required
    >

    <input
        type="number"
        step="0.01"
        name="price"
        placeholder="Price"
        required
    >

    <input
        type="text"
        name="status"
        placeholder="Available / Occupied"
        required
    >

    <button type="submit">
        Add Room
    </button>

</form>

<hr>

<h2>Search Room</h2>

<form method="GET">

    <input
        type="text"
        name="search"
        placeholder="Search room number..."
        value="<?= htmlspecialchars($search) ?>"
    >

    <button type="submit">
        Search
    </button>

</form>

<hr>

<h2>Room List</h2>

<table>

<tr>
    <th>ID</th>
    <th>Room</th>
    <th>Type</th>
    <th>Price</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach($rooms as $room): ?>

<tr>

    <td><?= $room["id"] ?></td>
    <td><?= htmlspecialchars($room["room_number"]) ?></td>
    <td><?= htmlspecialchars($room["room_type"]) ?></td>
    <td><?= htmlspecialchars($room["price"]) ?></td>
    <td><?= htmlspecialchars($room["status"]) ?></td>

    <td>

        <a
            class="edit-btn"
            href="edit_room.php?id=<?= $room["id"] ?>"
        >
            Edit
        </a>

        <a
            class="delete-btn"
            href="?delete=<?= $room["id"] ?>"
            onclick="return confirm('Delete this room?')"
        >
            Delete
        </a>

    </td>

</tr>

<?php endforeach; ?>

</table>

</div>

</body>
</html>