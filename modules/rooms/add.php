<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_number = trim($_POST['room_number'] ?? '');
    $room_type = $_POST['room_type'] ?? 'Single';
    $price_per_night = $_POST['price_per_night'] ?? 0;
    $status = $_POST['status'] ?? 'Available';
    $description = trim($_POST['description'] ?? '');

    if (empty($room_number)) {
        $error = "Room number is required.";
    } elseif ($price_per_night <= 0) {
        $error = "Price must be greater than 0.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ?");
            $stmt->execute([$room_number]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Room number already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, price_per_night, status, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$room_number, $room_type, $price_per_night, $status, $description]);
                $success = "Room added successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Rooms > Add Room
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Add New Room</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="add.php" onsubmit="return validateRoomForm()">
            <div class="form-group">
                <label for="room_number">Room Number *</label>
                <input type="text" id="room_number" name="room_number" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="room_type">Room Type *</label>
                <select id="room_type" name="room_type" class="form-control" required>
                    <option value="Single">Single</option>
                    <option value="Double">Double</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price_per_night">Price Per Night (Rs) *</label>
                <input type="number" step="0.01" id="price_per_night" name="price_per_night" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Available">Available</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Room</button>
        </form>
    </div>
</div>

<script>
function validateRoomForm() {
    var roomNum = document.getElementById('room_number').value.trim();
    var price = document.getElementById('price_per_night').value;
    
    if (roomNum === '') {
        alert('Room number cannot be empty.');
        return false;
    }
    
    if (!validatePrice(price)) {
        alert('Price must be numeric and greater than 0.');
        return false;
    }
    
    var confirmRoom = prompt("Please confirm the room number before saving:");
    if (confirmRoom !== roomNum) {
        alert('Room number confirmation failed. Save cancelled.');
        return false;
    }
    
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>
