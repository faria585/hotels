<?php
require_once 'db.php';
$hotelId = isset($_GET['hotelId']) ? (int)$_GET['hotelId'] : 0;
$checkIn = isset($_GET['checkIn']) ? $_GET['checkIn'] : '';
$checkOut = isset($_GET['checkOut']) ? $_GET['checkOut'] : '';

$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotelId]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$hotel) {
    die("Hotel not found.");
}

// Calculate nights for display
$nights = 0;
$totalPrice = 0;
if ($checkIn && $checkOut) {
    try {
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        if ($checkOutDate > $checkInDate) {
            $nights = $checkInDate->diff($checkOutDate)->days;
            $totalPrice = $hotel['price'] * $nights;
        } else {
            $error = "Check-out date must be after check-in date.";
        }
    } catch (Exception $e) {
        $error = "Invalid date format.";
    }
} else {
    $error = "Please provide check-in and check-out dates.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $userId = $pdo->lastInsertId();

        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, hotel_id, check_in, check_out, total_price) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $hotelId, $checkIn, $checkOut, $totalPrice]);

        $message = "Booking confirmed! You'll receive a confirmation email soon.";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry error
            $error = "Email already registered. Please use a different email.";
        } else {
            $error = "An error occurred while processing your booking: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book <?= htmlspecialchars($hotel['name']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
        }
        header {
            background: #1a2a44;
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .booking-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .booking-container h2 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #1a2a44;
        }
        .booking-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .booking-container input {
            padding: 12px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .booking-container button {
            padding: 12px;
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .booking-container button:hover {
            background: #e55a50;
        }
        .message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
        @media (max-width: 600px) {
            .booking-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Book Your Stay</h1>
    </header>
    <div class="booking-container">
        <h2><?= htmlspecialchars($hotel['name']) ?></h2>
        <?php if (isset($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
            <button onclick="window.location.href='index.php'">Back to Home</button>
        <?php elseif (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <button onclick="window.location.href='hotels.php?destination=&checkIn=<?= urlencode($checkIn) ?>&checkOut=<?= urlencode($checkOut) ?>'">Back to Hotels</button>
        <?php else: ?>
            <form method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <p>Check-in: <?= htmlspecialchars($checkIn) ?></p>
                <p>Check-out: <?= htmlspecialchars($checkOut) ?></p>
                <p>Total Price: $<?= number_format($totalPrice, 2) ?> (<?= $nights ?> nights)</p>
                <button type="submit">Confirm Booking</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
