<?php
require_once 'includes/session_check.php';
require_once 'config/db.php';

// Auto checkout past bookings
try {
    $pdo->exec("
        UPDATE bookings
        SET status = 'Checked Out'
        WHERE check_out_date < CURDATE()
        AND status = 'Checked In'
    ");
    
    $pdo->exec("
        UPDATE rooms r
        JOIN bookings b ON r.room_id = b.room_id
        SET r.status = 'Available'
        WHERE b.status = 'Checked Out'
        AND r.status = 'Occupied'
    ");
} catch(PDOException $e) {
    // Ignore error silently for dashboard load
}

// Fetch Stats
$stats = [
    'total_rooms' => 0,
    'available_rooms' => 0,
    'occupied_rooms' => 0,
    'maintenance_rooms' => 0,
    'total_guests' => 0,
    'today_revenue' => 0,
    'pending_payments' => 0,
    'total_bookings' => 0
];

try {
    // Rooms stats
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM rooms GROUP BY status");
    while ($row = $stmt->fetch()) {
        $stats['total_rooms'] += $row['count'];
        if ($row['status'] == 'Available') $stats['available_rooms'] = $row['count'];
        if ($row['status'] == 'Occupied') $stats['occupied_rooms'] = $row['count'];
        if ($row['status'] == 'Maintenance') $stats['maintenance_rooms'] = $row['count'];
    }
    
    // Guests count
    $stats['total_guests'] = $pdo->query("SELECT COUNT(*) FROM guests")->fetchColumn();
    
    // Bookings count
    $stats['total_bookings'] = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    
    // Today's revenue
    $stmt = $pdo->query("SELECT SUM(amount_paid) FROM payments WHERE DATE(payment_date) = CURDATE() AND status = 'Paid'");
    $stats['today_revenue'] = $stmt->fetchColumn() ?: 0;
    
    // Pending payments count
    $stats['pending_payments'] = $pdo->query("SELECT COUNT(*) FROM payments WHERE status = 'Pending'")->fetchColumn();
    
    // Recent Bookings
    $recentBookings = $pdo->query("
        SELECT b.booking_id, g.name as guest_name, r.room_number, b.check_in_date, b.check_out_date, b.status
        FROM bookings b
        JOIN guests g ON b.guest_id = g.guest_id
        JOIN rooms r ON b.room_id = r.room_id
        ORDER BY b.created_at DESC LIMIT 5
    ")->fetchAll();
    
    // Available Rooms
    $availableRooms = $pdo->query("
        SELECT room_number, room_type, price_per_night 
        FROM rooms 
        WHERE status = 'Available'
        ORDER BY room_number ASC
    ")->fetchAll();
    
} catch (PDOException $e) {
    die("Error fetching dashboard data: " . $e->getMessage());
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="breadcrumb">
    Dashboard
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary"><i class="fas fa-door-open"></i></div>
        <div class="stat-info">
            <h4>Total Rooms</h4>
            <h2><?php echo $stats['total_rooms']; ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h4>Available</h4>
            <h2><?php echo $stats['available_rooms']; ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-danger"><i class="fas fa-bed"></i></div>
        <div class="stat-info">
            <h4>Occupied</h4>
            <h2><?php echo $stats['occupied_rooms']; ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-tools"></i></div>
        <div class="stat-info">
            <h4>Maintenance</h4>
            <h2><?php echo $stats['maintenance_rooms']; ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-info"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h4>Total Guests</h4>
            <h2><?php echo $stats['total_guests']; ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-success"><i class="fas fa-rupee-sign"></i></div>
        <div class="stat-info">
            <h4>Today's Revenue</h4>
            <h2>Rs. <?php echo number_format($stats['today_revenue'], 2); ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-warning"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-info">
            <h4>Pending Payments</h4>
            <h2><?php echo $stats['pending_payments']; ?></h2>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon bg-dark"><i class="fas fa-book"></i></div>
        <div class="stat-info">
            <h4>Total Bookings</h4>
            <h2><?php echo $stats['total_bookings']; ?></h2>
        </div>
    </div>
</div>

<div style="display: flex; gap: 20px; flex-wrap: wrap;">
    <!-- Recent Bookings Table -->
    <div class="card" style="flex: 2; min-width: 400px;">
        <div class="card-header">
            <h3>Recent Bookings</h3>
            <a href="/hotel/modules/bookings/view.php" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Room</th>
                        <th>Dates</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recentBookings as $b): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($b['guest_name']); ?></td>
                        <td><?php echo htmlspecialchars($b['room_number']); ?></td>
                        <td><?php echo $b['check_in_date'] . ' to ' . $b['check_out_date']; ?></td>
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
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($recentBookings)): ?>
                    <tr><td colspan="4" style="text-align:center;">No recent bookings</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Available Rooms Table -->
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header">
            <h3>Available Rooms</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Type</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($availableRooms as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($r['room_type']); ?></td>
                        <td>Rs. <?php echo number_format($r['price_per_night'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($availableRooms)): ?>
                    <tr><td colspan="3" style="text-align:center;">No rooms available</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
