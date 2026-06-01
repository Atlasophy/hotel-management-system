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

if (!isset($_GET["id"])) {
    die("Guest ID not provided.");
}

$id = (int)$_GET["id"];

$stmt = $conn->prepare(
    "SELECT * FROM guests WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

$guest = $result->fetch_assoc();

if (!$guest) {
    die("Guest not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    $stmt = $conn->prepare(
        "UPDATE guests
         SET name = ?, email = ?, phone = ?
         WHERE id = ?"
    );

    $stmt->bind_param(
        "sssi",
        $name,
        $email,
        $phone,
        $id
    );

    $stmt->execute();

    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Guest</title>

    <style>

        body{
            font-family:Arial;
            background:#f4f4f4;
            padding:30px;
        }

        .card{
            background:white;
            max-width:700px;
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

    <h1>✏️ Edit Guest</h1>

    <form method="POST">

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($guest["name"]) ?>"
            required
        >

        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($guest["email"]) ?>"
            required
        >

        <input
            type="text"
            name="phone"
            value="<?= htmlspecialchars($guest["phone"]) ?>"
            required
        >

        <button type="submit">
            Update Guest
        </button>

    </form>

    <br>

    <a href="../index.php">
        ← Back to Dashboard
    </a>

</div>

</body>
</html>