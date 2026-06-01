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
| DELETE BOOKING
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = (int)$_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM bookings WHERE id = ?"
    );

    $stmt->bind_param("i", $id);

    $stmt->execute();

    header("Location: bookings.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| ADD BOOKING
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $guest_id = (int)$_POST["guest_id"];
    $room_id = (int)$_POST["room_id"];
    $check_in = $_POST["check_in"];
    $check_out = $_POST["check_out"];

    $stmt = $conn->prepare(
        "INSERT INTO bookings
        (guest_id, room_id, check_in, check_out)
        VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iiss",
        $guest_id,
        $room_id,
        $check_in,
        $check_out
    );

    $stmt->execute();

    header("Location: bookings.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| LOAD GUESTS
|--------------------------------------------------------------------------
*/

$guests = [];

$result = $conn->query(
    "SELECT * FROM guests ORDER BY name"
);

while ($row = $result->fetch_assoc()) {
    $guests[] = $row;
}

/*
|--------------------------------------------------------------------------
| LOAD ROOMS
|--------------------------------------------------------------------------
*/

$rooms = [];

$result = $conn->query(
    "SELECT * FROM rooms ORDER BY room_number"
);

while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

/*
|--------------------------------------------------------------------------
| LOAD BOOKINGS
|--------------------------------------------------------------------------
*/

$bookings = [];

$result = $conn->query(
    "SELECT
        bookings.id,
        guests.name AS guest_name,
        rooms.room_number,
        bookings.check_in,
        bookings.check_out
    FROM bookings
    JOIN guests
        ON bookings.guest_id = guests.id
    JOIN rooms
        ON bookings.room_id = rooms.id
    ORDER BY bookings.id DESC"
);

while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Booking Management</title>

<style>

body{
    font-family:Arial,sans-serif;
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

input, select{
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
    <a href="bookings.php">Bookings</a>
</div>

<h1>📅 Booking Management</h1>

<h2>Create Booking</h2>

<form method="POST">

    <select name="guest_id" required>
        <option value="">Select Guest</option>

        <?php foreach($guests as $guest): ?>

            <option value="<?= $guest['id'] ?>">
                <?= htmlspecialchars($guest['name']) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <select name="room_id" required>
        <option value="">Select Room</option>

        <?php foreach($rooms as $room): ?>

            <option value="<?= $room['id'] ?>">
                Room <?= htmlspecialchars($room['room_number']) ?>
            </option>

        <?php endforeach; ?>

    </select>

    <input
        type="date"
        name="check_in"
        required
    >

    <input
        type="date"
        name="check_out"
        required
    >

    <button type="submit">
        Create Booking
    </button>

</form>

<hr>

<h2>Booking List</h2>

<table>

<tr>
    <th>ID</th>
    <th>Guest</th>
    <th>Room</th>
    <th>Check In</th>
    <th>Check Out</th>
    <th>Action</th>
</tr>

<?php foreach($bookings as $booking): ?>

<tr>

    <td><?= $booking["id"] ?></td>
    <td><?= htmlspecialchars($booking["guest_name"]) ?></td>
    <td><?= htmlspecialchars($booking["room_number"]) ?></td>
    <td><?= htmlspecialchars($booking["check_in"]) ?></td>
    <td><?= htmlspecialchars($booking["check_out"]) ?></td>

    <td>
        <a
            class="delete-btn"
            href="?delete=<?= $booking["id"] ?>"
            onclick="return confirm('Delete booking?')"
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