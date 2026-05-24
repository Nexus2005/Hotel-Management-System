<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$error = '';
$success = '';
$room_id = $_GET['id'] ?? null;

if (!$room_id) {
    header("Location: view.php");
    exit();
}

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
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ? AND room_id != ?");
            $stmt->execute([$room_number, $room_id]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Room number already exists.";
            } else {
                $stmt = $pdo->prepare("UPDATE rooms SET room_number = ?, room_type = ?, price_per_night = ?, status = ?, description = ? WHERE room_id = ?");
                $stmt->execute([$room_number, $room_type, $price_per_night, $status, $description, $room_id]);
                $success = "Room updated successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch();
    
    if (!$room) {
        die("Room not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > <a href="view.php">Rooms</a> > Edit Room
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Edit Room #<?php echo htmlspecialchars($room['room_number']); ?></h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?php echo $room_id; ?>" onsubmit="return validateRoomForm()">
            <div class="form-group">
                <label for="room_number">Room Number *</label>
                <input type="text" id="room_number" name="room_number" class="form-control" value="<?php echo htmlspecialchars($room['room_number']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="room_type">Room Type *</label>
                <select id="room_type" name="room_type" class="form-control" required>
                    <option value="Single" <?php echo $room['room_type'] == 'Single' ? 'selected' : ''; ?>>Single</option>
                    <option value="Double" <?php echo $room['room_type'] == 'Double' ? 'selected' : ''; ?>>Double</option>
                    <option value="Suite" <?php echo $room['room_type'] == 'Suite' ? 'selected' : ''; ?>>Suite</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="price_per_night">Price Per Night (Rs) *</label>
                <input type="number" step="0.01" id="price_per_night" name="price_per_night" class="form-control" value="<?php echo $room['price_per_night']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="Available" <?php echo $room['status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                    <option value="Occupied" <?php echo $room['status'] == 'Occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="Maintenance" <?php echo $room['status'] == 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($room['description']); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Room</button>
            <a href="view.php" class="btn btn-warning">Cancel</a>
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
    
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>
