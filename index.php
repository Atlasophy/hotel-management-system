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
| DELETE GUEST
|--------------------------------------------------------------------------
*/

if (isset($_GET["delete"])) {

    $id = (int)$_GET["delete"];

    $stmt = $conn->prepare(
        "DELETE FROM guests WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| ADD GUEST
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    $stmt = $conn->prepare(
        "INSERT INTO guests (name, email, phone)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "sss",
        $name,
        $email,
        $phone
    );

    $stmt->execute();

    header("Location: index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| TOTAL GUESTS
|--------------------------------------------------------------------------
*/

$totalGuestsResult = $conn->query(
    "SELECT COUNT(*) AS total FROM guests"
);

$totalGuests = $totalGuestsResult->fetch_assoc()["total"];

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
        "SELECT * FROM guests
         WHERE name LIKE ?
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
        "SELECT * FROM guests
         ORDER BY id DESC"
    );
}

$guests = [];

while ($row = $result->fetch_assoc()) {
    $guests[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Hotel Management System</title>

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
    max-width:1000px;
    margin:auto;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}

.stats-card{
    background:#007bff;
    color:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

.stats-card h3{
    margin:0;
}

.stats-card h1{
    margin-top:10px;
    margin-bottom:0;
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

button:hover{
    opacity:0.9;
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

.edit-btn{
    background:#28a745;
    color:white;
    padding:6px 12px;
    border-radius:5px;
    text-decoration:none;
    margin-right:5px;
}

.delete-btn{
    background:#dc3545;
    color:white;
    padding:6px 12px;
    border-radius:5px;
    text-decoration:none;
}

.search-box{
    margin-top:20px;
}

</style>

</head>
<body>

<div class="card">

    <h1>🏨 Hotel Management System</h1>

    <div class="stats-card">
        <h3>Total Guests</h3>
        <h1><?= $totalGuests ?></h1>
    </div>

    <h2>Add Guest</h2>

    <form method="POST">

        <input
            type="text"
            name="name"
            placeholder="Full Name"
            required
        >

        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >

        <input
            type="text"
            name="phone"
            placeholder="Phone Number"
            required
        >

        <button type="submit">
            Add Guest
        </button>

    </form>

    <hr>

    <h2>Search Guest</h2>

    <form method="GET" class="search-box">

        <input
            type="text"
            name="search"
            placeholder="Search by name..."
            value="<?= htmlspecialchars($search) ?>"
        >

        <button type="submit">
            Search
        </button>

    </form>

    <hr>

    <h2>Guest List</h2>

    <table>

        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>

        <?php foreach($guests as $guest): ?>

        <tr>

            <td><?= $guest["id"] ?></td>

            <td><?= htmlspecialchars($guest["name"]) ?></td>

            <td><?= htmlspecialchars($guest["email"]) ?></td>

            <td><?= htmlspecialchars($guest["phone"]) ?></td>

            <td>

                <a
                    class="edit-btn"
                    href="php/edit.php?id=<?= $guest["id"] ?>"
                >
                    Edit
                </a>

                <a
                    class="delete-btn"
                    href="?delete=<?= $guest["id"] ?>"
                    onclick="return confirm('Delete this guest?')"
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