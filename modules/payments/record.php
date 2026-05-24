<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$error = '';
$success = '';
$prefill_booking_id = $_GET['booking_id'] ?? '';
$prefill_amount = $_GET['amount'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = $_POST['booking_id'] ?? '';
    $amount_paid = $_POST['amount_paid'] ?? 0;
    $payment_mode = $_POST['payment_mode'] ?? 'Cash';
    $status = $_POST['status'] ?? 'Paid';
    $payment_date = date('Y-m-d H:i:s'); // Today

    if (empty($booking_id) || $amount_paid <= 0) {
        $error = "Valid booking and amount greater than 0 are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO payments (booking_id, amount_paid, payment_date, payment_mode, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$booking_id, $amount_paid, $payment_date, $payment_mode, $status]);
            $success = "Payment recorded successfully.";
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

try {
    // Fetch bookings that might need payment (Booked or Checked In)
    $bookings = $pdo->query("
        SELECT b.booking_id, g.name, r.room_number, b.total_amount 
        FROM bookings b 
        JOIN guests g ON b.guest_id = g.guest_id 
        JOIN rooms r ON b.room_id = r.room_id 
        WHERE b.status IN ('Booked', 'Checked In', 'Checked Out')
        ORDER BY b.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Payments > Record Payment
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Record Payment</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="record.php" onsubmit="return validatePaymentForm()">
            <div class="form-group">
                <label for="booking_id">Select Booking *</label>
                <select id="booking_id" name="booking_id" class="form-control" required>
                    <option value="">-- Select Booking --</option>
                    <?php foreach ($bookings as $b): ?>
                        <option value="<?php echo $b['booking_id']; ?>" <?php echo ($prefill_booking_id == $b['booking_id']) ? 'selected' : ''; ?>>
                            ID: <?php echo $b['booking_id']; ?> - <?php echo htmlspecialchars($b['name']); ?> (Room <?php echo $b['room_number']; ?>) - Total: Rs.<?php echo $b['total_amount']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="amount_paid">Amount Paid (Rs) *</label>
                <input type="number" step="0.01" id="amount_paid" name="amount_paid" class="form-control" value="<?php echo htmlspecialchars($prefill_amount); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="payment_mode">Payment Mode *</label>
                <select id="payment_mode" name="payment_mode" class="form-control" required>
                    <option value="Cash">Cash</option>
                    <option value="Card">Card</option>
                    <option value="UPI">UPI</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="status">Status *</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="Paid">Paid</option>
                    <option value="Partial">Partial</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Save Payment</button>
        </form>
    </div>
</div>

<script>
function validatePaymentForm() {
    var amount = document.getElementById('amount_paid').value;
    
    if (!validatePrice(amount)) {
        alert('Amount must be numeric and greater than 0.');
        return false;
    }
    
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>
