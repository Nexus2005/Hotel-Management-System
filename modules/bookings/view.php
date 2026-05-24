<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

try {
    $filter = $_GET['filter'] ?? 'All';
    $query = "
        SELECT b.booking_id, g.name as guest_name, r.room_number, b.check_in_date, 
               b.check_out_date, b.total_nights, b.total_amount, b.status 
        FROM bookings b
        JOIN guests g ON b.guest_id = g.guest_id
        JOIN rooms r ON b.room_id = r.room_id
    ";
    
    if ($filter != 'All') {
        $stmt = $pdo->prepare($query . " WHERE b.status = ? ORDER BY b.created_at DESC");
        $stmt->execute([$filter]);
        $bookings = $stmt->fetchAll();
    } else {
        $bookings = $pdo->query($query . " ORDER BY b.created_at DESC")->fetchAll();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Bookings > All Bookings
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'checkedin'): ?>
        <div class="alert alert-success">Guest Checked In successfully!</div>
    <?php elseif ($_GET['msg'] == 'error'): ?>
        <div class="alert alert-danger">Error: <?php echo htmlspecialchars($_GET['err']); ?></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>All Bookings</h3>
        <a href="create.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Booking</a>
    </div>
    <div class="card-body" style="padding: 20px;">
        <div class="filter-bar">
            <form method="GET" action="view.php" style="display: flex; gap: 10px; align-items: center;">
                <label>Filter Status: </label>
                <select name="filter" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                    <option value="All" <?php echo $filter == 'All' ? 'selected' : ''; ?>>All</option>
                    <option value="Booked" <?php echo $filter == 'Booked' ? 'selected' : ''; ?>>Booked</option>
                    <option value="Checked In" <?php echo $filter == 'Checked In' ? 'selected' : ''; ?>>Checked In</option>
                    <option value="Checked Out" <?php echo $filter == 'Checked Out' ? 'selected' : ''; ?>>Checked Out</option>
                    <option value="Cancelled" <?php echo $filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </form>
            <input type="text" id="searchBooking" class="form-control search-input" placeholder="Search by Guest Name...">
        </div>
        
        <div class="table-responsive">
            <table class="table" id="bookingsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Nights</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td><?php echo $b['booking_id']; ?></td>
                        <td><?php echo htmlspecialchars($b['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($b['room_number']); ?></td>
                        <td><?php echo date('d-M', strtotime($b['check_in_date'])) . ' to ' . date('d-M', strtotime($b['check_out_date'])); ?></td>
                        <td><?php echo $b['total_nights']; ?></td>
                        <td>Rs. <?php echo number_format($b['total_amount'], 2); ?></td>
                        <td>
                            <?php 
                            $badge = 'info';
                            if($b['status'] == 'Booked') $badge = 'primary';
                            elseif($b['status'] == 'Checked In') $badge = 'warning';
                            elseif($b['status'] == 'Checked Out') $badge = 'success';
                            elseif($b['status'] == 'Cancelled') $badge = 'danger';
                            ?>
                            <span class="badge badge-<?php echo $badge; ?>"><?php echo $b['status']; ?></span>
                        </td>
                        <td>
                            <?php if ($b['status'] == 'Booked'): ?>
                                <a href="checkin.php?id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Check in this guest?');">Check In</a>
                            <?php elseif ($b['status'] == 'Checked In'): ?>
                                <a href="checkout.php?id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-success">Check Out</a>
                            <?php endif; ?>
                            <a href="/hotel/modules/payments/record.php?booking_id=<?php echo $b['booking_id']; ?>" class="btn btn-sm btn-info" title="Payment"><i class="fas fa-rupee-sign"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($bookings)): ?>
                    <tr><td colspan="8" style="text-align:center;">No bookings found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    filterTable("searchBooking", "bookingsTable", 1); // 1 is the column index for Guest Name
});
</script>

<?php include '../../includes/footer.php'; ?>
