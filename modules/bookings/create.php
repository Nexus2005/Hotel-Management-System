<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guest_id = $_POST['guest_id'] ?? '';
    $room_id = $_POST['room_id'] ?? '';
    $check_in = $_POST['check_in_date'] ?? '';
    $check_out = $_POST['check_out_date'] ?? '';
    $total_nights = $_POST['total_nights'] ?? 0;
    $total_amount = $_POST['total_amount'] ?? 0;

    if (empty($guest_id) || empty($room_id) || empty($check_in) || empty($check_out)) {
        $error = "All fields are required.";
    } elseif (strtotime($check_out) <= strtotime($check_in)) {
        $error = "Check-out date must be after check-in date.";
    } else {
        try {
            // Verify room is still available
            $stmt = $pdo->prepare("SELECT status FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $room_status = $stmt->fetchColumn();
            
            if ($room_status != 'Available') {
                $error = "Sorry, this room is no longer available.";
            } else {
                $pdo->beginTransaction();
                
                // Insert booking
                $stmt = $pdo->prepare("INSERT INTO bookings (room_id, guest_id, check_in_date, check_out_date, total_nights, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'Booked')");
                $stmt->execute([$room_id, $guest_id, $check_in, $check_out, $total_nights, $total_amount]);
                
                // Update room status
                $stmt = $pdo->prepare("UPDATE rooms SET status = 'Occupied' WHERE room_id = ?");
                $stmt->execute([$room_id]);
                
                $pdo->commit();
                $success = "Booking created successfully.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}

try {
    $guests = $pdo->query("SELECT guest_id, name, mobile FROM guests ORDER BY name ASC")->fetchAll();
    $rooms = $pdo->query("SELECT room_id, room_number, room_type, price_per_night FROM rooms WHERE status = 'Available' ORDER BY room_number ASC")->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Bookings > New Booking
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Create New Booking</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="create.php" onsubmit="return validateBookingForm()">
            <div class="form-group">
                <label for="guest_id">Select Guest *</label>
                <select id="guest_id" name="guest_id" class="form-control" required>
                    <option value="">-- Select Guest --</option>
                    <?php foreach ($guests as $g): ?>
                        <option value="<?php echo $g['guest_id']; ?>"><?php echo htmlspecialchars($g['name'] . ' (' . $g['mobile'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="room_select">Select Room *</label>
                <select id="room_select" name="room_id" class="form-control" onchange="calculateBooking()" required>
                    <option value="" data-price="0">-- Select Available Room --</option>
                    <?php foreach ($rooms as $r): ?>
                        <option value="<?php echo $r['room_id']; ?>" data-price="<?php echo $r['price_per_night']; ?>">
                            <?php echo htmlspecialchars($r['room_number'] . ' - ' . $r['room_type'] . ' (Rs.' . $r['price_per_night'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="check_in">Check-in Date *</label>
                <input type="date" id="check_in" name="check_in_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" onchange="calculateBooking()" required>
            </div>
            
            <div class="form-group">
                <label for="check_out">Check-out Date *</label>
                <input type="date" id="check_out" name="check_out_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" onchange="calculateBooking()" required>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px;">
                <h4>Booking Summary</h4>
                <p id="nights_display">0 nights</p>
                <h3 id="amount_display" style="color: var(--btn-primary);">Total: Rs.0.00</h3>
            </div>
            
            <input type="hidden" id="total_nights_hidden" name="total_nights" value="0">
            <input type="hidden" id="total_amount_hidden" name="total_amount" value="0">
            
            <button type="submit" class="btn btn-primary">Confirm Booking</button>
        </form>
    </div>
</div>

<script>
function calculateBooking() {
    var checkInVal = document.getElementById('check_in').value;
    var checkOutVal = document.getElementById('check_out').value;
    var roomSelect = document.getElementById('room_select');
    
    if (!checkInVal || !checkOutVal || roomSelect.selectedIndex <= 0) {
        return;
    }
    
    var checkIn = new Date(checkInVal);
    var checkOut = new Date(checkOutVal);
    var pricePerNight = parseFloat(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price'));

    var timeDiff = checkOut - checkIn;
    var nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

    if (nights <= 0) {
        alert('Check-out must be after check-in date');
        document.getElementById('nights_display').innerText = '0 nights';
        document.getElementById('amount_display').innerText = 'Total: Rs.0.00';
        document.getElementById('total_nights_hidden').value = 0;
        document.getElementById('total_amount_hidden').value = 0;
        return;
    }

    var total = nights * pricePerNight;

    document.getElementById('nights_display').innerText = nights + ' nights';
    document.getElementById('amount_display').innerText = 'Total: Rs.' + total.toFixed(2);
    document.getElementById('total_nights_hidden').value = nights;
    document.getElementById('total_amount_hidden').value = total.toFixed(2);
}

function validateBookingForm() {
    var nights = document.getElementById('total_nights_hidden').value;
    if (nights <= 0) {
        alert('Invalid dates. Check-out must be after check-in.');
        return false;
    }
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>
