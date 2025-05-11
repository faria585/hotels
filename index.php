<?php
require_once 'db.php';
$hotels = $pdo->query("SELECT * FROM hotels LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels - Book Your Stay</title>
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
        header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .search-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            margin: 20px auto;
            max-width: 800px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .search-container form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        .search-container input {
            padding: 12px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 8px;
            flex: 1;
            min-width: 150px;
        }
        .search-container button {
            padding: 12px 30px;
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-container button:hover {
            background: #e55a50;
        }
        .featured {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .featured h2 {
            font-size: 2em;
            margin-bottom: 20px;
            text-align: center;
            color: #1a2a44;
        }
        .hotel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .hotel-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .hotel-card:hover {
            transform: translateY(-5px);
        }
        .hotel-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .hotel-card h3 {
            font-size: 1.5em;
            padding: 15px;
            color: #1a2a44;
        }
        .hotel-card p {
            padding: 0 15px 15px;
            color: #666;
        }
        .hotel-card .price {
            font-weight: bold;
            color: #ff6f61;
        }
        @media (max-width: 600px) {
            .search-container form {
                flex-direction: column;
            }
            .search-container input, .search-container button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Hotels</h1>
        <p>Find Your Perfect Stay</p>
    </header>
    <div class="search-container">
        <form id="searchForm">
            <input type="text" id="destination" placeholder="Destination" required>
            <input type="date" id="checkIn" required>
            <input type="date" id="checkOut" required>
            <button type="submit">Search</button>
        </form>
    </div>
    <section class="featured">
        <h2>Featured Hotels</h2>
        <div class="hotel-grid">
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card">
                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
                    <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                    <p><?= htmlspecialchars($hotel['location']) ?></p>
                    <p class="price">$<?= number_format($hotel['price'], 2) ?>/night</p>
                    <p>Rating: <?= htmlspecialchars($hotel['rating']) ?> ★</p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <script>
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const destination = document.getElementById('destination').value;
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;
            window.location.href = `hotels.php?destination=${encodeURIComponent(destination)}&checkIn=${checkIn}&checkOut=${checkOut}`;
        });
    </script>
</body>
</html>
