<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$booking_id = $_GET['id'] ?? null;
$error = '';
$success = '';

if (!$booking_id) {
    header("Location: view.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_checkout'])) {
    try {
        $pdo->beginTransaction();
        
        // Update booking status
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'Checked Out' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        
        // Update room status
        $stmt = $pdo->prepare("UPDATE rooms r JOIN bookings b ON r.room_id = b.room_id SET r.status = 'Available' WHERE b.booking_id = ?");
        $stmt->execute([$booking_id]);
        
        $pdo->commit();
        $success = "Checkout completed successfully.";
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $error = "Database error: " . $e->getMessage();
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT b.*, g.name as guest_name, r.room_number 
        FROM bookings b
        JOIN guests g ON b.guest_id = g.guest_id
        JOIN rooms r ON b.room_id = r.room_id
        WHERE b.booking_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    
    if (!$booking) die("Booking not found");
    
    // Calculate total paid
    $stmt = $pdo->prepare("SELECT SUM(amount_paid) FROM payments WHERE booking_id = ? AND status = 'Paid'");
    $stmt->execute([$booking_id]);
    $total_paid = $stmt->fetchColumn() ?: 0;
    
    $balance_due = $booking['total_amount'] - $total_paid;
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > <a href="view.php">Bookings</a> > Checkout
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Checkout Guest</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <a href="view.php" class="btn btn-primary">Return to Bookings</a>
        <?php else: ?>
        
            <table class="table" style="margin-bottom: 20px;">
                <tr>
                    <th>Guest Name:</th>
                    <td><?php echo htmlspecialchars($booking['guest_name']); ?></td>
                </tr>
                <tr>
                    <th>Room:</th>
                    <td><?php echo htmlspecialchars($booking['room_number']); ?></td>
                </tr>
                <tr>
                    <th>Total Amount:</th>
                    <td>Rs. <?php echo number_format($booking['total_amount'], 2); ?></td>
                </tr>
                <tr>
                    <th>Amount Paid:</th>
                    <td style="color: green;">Rs. <?php echo number_format($total_paid, 2); ?></td>
                </tr>
                <tr>
                    <th>Balance Due:</th>
                    <td style="color: red; font-weight: bold;">Rs. <?php echo number_format($balance_due, 2); ?></td>
                </tr>
            </table>
            
            <?php if ($balance_due > 0): ?>
                <div class="alert alert-warning">
                    <strong>Notice:</strong> There is a pending balance of Rs. <?php echo number_format($balance_due, 2); ?>.
                </div>
                <a href="/hotel/modules/payments/record.php?booking_id=<?php echo $booking_id; ?>&amount=<?php echo $balance_due; ?>" class="btn btn-success">Pay Now</a>
            <?php else: ?>
                <div class="alert alert-success">
                    All dues are cleared.
                </div>
            <?php endif; ?>
            
            <form method="POST" style="margin-top: 20px;">
                <button type="submit" name="confirm_checkout" class="btn btn-danger" onclick="return confirm('Are you sure you want to process checkout? The room will become Available.');">Confirm Checkout</button>
                <a href="view.php" class="btn btn-primary">Cancel</a>
            </form>
            
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
