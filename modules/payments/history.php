<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

try {
    $mode_filter = $_GET['mode'] ?? 'All';
    $status_filter = $_GET['status'] ?? 'All';
    
    $query = "
        SELECT p.payment_id, b.booking_id, g.name as guest_name, r.room_number, 
               p.amount_paid, p.payment_mode, p.payment_date, p.status
        FROM payments p
        JOIN bookings b ON p.booking_id = b.booking_id
        JOIN guests g ON b.guest_id = g.guest_id
        JOIN rooms r ON b.room_id = r.room_id
        WHERE 1=1
    ";
    
    $params = [];
    if ($mode_filter != 'All') {
        $query .= " AND p.payment_mode = ?";
        $params[] = $mode_filter;
    }
    if ($status_filter != 'All') {
        $query .= " AND p.status = ?";
        $params[] = $status_filter;
    }
    
    $query .= " ORDER BY p.payment_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $payments = $stmt->fetchAll();
    
    // Calculate total revenue from paid payments shown in current view
    $total_revenue = 0;
    foreach($payments as $p) {
        if($p['status'] == 'Paid' || $p['status'] == 'Partial') {
            $total_revenue += $p['amount_paid'];
        }
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Payments > Payment History
</div>

<div class="card">
    <div class="card-header">
        <h3>Payment History</h3>
        <a href="record.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Record Payment</a>
    </div>
    <div class="card-body" style="padding: 20px;">
        <div class="filter-bar">
            <form method="GET" action="history.php" style="display: flex; gap: 10px; align-items: center;">
                <label>Mode: </label>
                <select name="mode" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                    <option value="All" <?php echo $mode_filter == 'All' ? 'selected' : ''; ?>>All Modes</option>
                    <option value="Cash" <?php echo $mode_filter == 'Cash' ? 'selected' : ''; ?>>Cash</option>
                    <option value="Card" <?php echo $mode_filter == 'Card' ? 'selected' : ''; ?>>Card</option>
                    <option value="UPI" <?php echo $mode_filter == 'UPI' ? 'selected' : ''; ?>>UPI</option>
                </select>
                
                <label>Status: </label>
                <select name="status" class="form-control" style="width: 150px;" onchange="this.form.submit()">
                    <option value="All" <?php echo $status_filter == 'All' ? 'selected' : ''; ?>>All Status</option>
                    <option value="Paid" <?php echo $status_filter == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="Partial" <?php echo $status_filter == 'Partial' ? 'selected' : ''; ?>>Partial</option>
                    <option value="Pending" <?php echo $status_filter == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                </select>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Booking ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Amount Paid</th>
                        <th>Mode</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($payments as $p): ?>
                    <tr class="<?php echo $p['status'] == 'Pending' ? 'row-pending' : ''; ?>">
                        <td><?php echo $p['payment_id']; ?></td>
                        <td>#<?php echo $p['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($p['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($p['room_number']); ?></td>
                        <td>Rs. <?php echo number_format($p['amount_paid'], 2); ?></td>
                        <td><?php echo $p['payment_mode']; ?></td>
                        <td><?php echo date('d-M-Y H:i', strtotime($p['payment_date'])); ?></td>
                        <td>
                            <?php 
                            $badge = 'success';
                            if($p['status'] == 'Pending') $badge = 'warning';
                            elseif($p['status'] == 'Partial') $badge = 'info';
                            ?>
                            <span class="badge badge-<?php echo $badge; ?>"><?php echo $p['status']; ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($payments)): ?>
                    <tr><td colspan="8" style="text-align:center;">No payment records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: var(--btn-primary); color: white; border-radius: 4px; text-align: right;">
            <h3 style="margin:0;">Total Revenue: Rs. <?php echo number_format($total_revenue, 2); ?></h3>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
