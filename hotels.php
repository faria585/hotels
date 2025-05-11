<?php
require_once 'db.php';
$destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$checkIn = isset($_GET['checkIn']) ? $_GET['checkIn'] : '';
$checkOut = isset($_GET['checkOut']) ? $_GET['checkOut'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';
$priceRange = isset($_GET['price']) ? $_GET['price'] : '';
$rating = isset($_GET['rating']) ? $_GET['rating'] : '';

$query = "SELECT * FROM hotels WHERE location LIKE :destination";
$params = [':destination' => "%$destination%"];

if ($priceRange) {
    $query .= " AND price <= :price";
    $params[':price'] = $priceRange;
}
if ($rating) {
    $query .= " AND rating >= :rating";
    $params[':rating'] = $rating;
}

if ($sort === 'price_asc') {
    $query .= " ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $query .= " ORDER BY price DESC";
} elseif ($sort === 'rating_desc') {
    $query .= " ORDER BY rating DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
іт

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Listings</title>
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
        .filters {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .filters select, .filters button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
        }
        .filters button {
            background: #ff6f61;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        .filters button:hover {
            background: #e55a50;
        }
        .hotel-list {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .hotel-item {
            display: flex;
            background: white;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }
        .hotel-item:hover {
            transform: translateY(-5px);
        }
        .hotel-item img {
            width: 300px;
            height: 200px;
            object-fit: cover;
        }
        .hotel-info {
            padding: 20px;
            flex: 1;
        }
        .hotel-info h3 {
            font-size: 1.8em;
            color: #1a2a44;
        }
        .hotel-info p {
            margin: 10px 0;
            color: #666;
        }
        .hotel-info .price {
            font-weight: bold;
            color: #ff6f61;
        }
        .hotel-info button {
            padding: 10px 20px;
            background: #ff6f61;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .hotel-info button:hover {
            background: #e55a50;
        }
        @media (max-width: 800px) {
            .hotel-item {
                flex-direction: column;
            }
            .hotel-item img {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Hotel Listings</h1>
    </header>
    <div class="filters">
        <select id="sort" onchange="applyFilters()">
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="rating_desc" <?= $sort === 'rating_desc' ? 'selected' : '' ?>>Best - Best Rated</option>
        </select>
        <select id="price" onchange="applyFilters()">
            <option value="">All Prices</option>
            <option value="100" <?= $priceRange === '100' ? 'selected' : '' ?>>Up to $100</option>
            <option value="200" <?= $priceRange === '200' ? 'selected' : '' ?>>Up to $200</option>
            <option value="300" <?= $priceRange === '300' ? 'selected' : '' ?>>Up to $300</option>
        </select>
        <select id="rating" onchange="applyFilters()">
            <option value="">All Ratings</option>
            <option value="4" <?= $rating === '4' ? 'selected' : '' ?>>4+ Stars</option>
            <option value="3" <?= $rating === '3' ? 'selected' : '' ?>>3+ Stars</option>
        </select>
        <button onclick="applyFilters()">Apply Filters</button>
    </div>
    <section class="hotel-list">
        <?php if (empty($hotels)): ?>
            <p>No hotels found.</p>
        <?php else: ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-item">
                    <img src="<?= htmlspecialchars($hotel['image']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
                    <div class="hotel-info">
                        <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                        <p><?= htmlspecialchars($hotel['location']) ?></p>
                        <p class="price">$<?= number_format($hotel['price'], 2) ?>/night</p>
                        <p>Rating: <?= htmlspecialchars($hotel['rating']) ?> ★</p>
                        <p><?= htmlspecialchars($hotel['description']) ?></p>
                        <button onclick="bookHotel(<?= $hotel['id'] ?>, '<?= $checkIn ?>', '<?= $checkOut ?>')">Book Now</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <script>
        function applyFilters() {
            const destination = '<?= addslashes($destination) ?>';
            const checkIn = '<?= addslashes($checkIn) ?>';
            const checkOut = '<?= addslashes($checkOut) ?>';
            const sort = document.getElementById('sort').value;
            const price = document.getElementById('price').value;
            const rating = document.getElementById('rating').value;
            window.location.href = `hotels.php?destination=${encodeURIComponent(destination)}&checkIn=${checkIn}&checkOut=${checkOut}&sort=${sort}&price=${price}&rating=${rating}`;
        }
        function bookHotel(hotelId, checkIn, checkOut) {
            window.location.href = `booking.php?hotelId=${hotelId}&checkIn=${checkIn}&checkOut=${checkOut}`;
        }
    </script>
</body>
</html>
