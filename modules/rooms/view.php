<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

try {
    $rooms = $pdo->query("SELECT * FROM rooms ORDER BY room_number ASC")->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Rooms > All Rooms
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">Room deleted successfully.</div>
    <?php elseif ($_GET['msg'] == 'error'): ?>
        <div class="alert alert-danger">Error: <?php echo htmlspecialchars($_GET['err'] ?? 'Cannot delete room.'); ?></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>All Rooms</h3>
        <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New Room</a>
    </div>
    <div class="card-body" style="padding: 20px;">
        <div class="filter-bar">
            <input type="text" id="searchRoom" class="form-control search-input" placeholder="Search by Room Number...">
        </div>
        
        <div class="table-responsive">
            <table class="table" id="roomsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room Number</th>
                        <th>Type</th>
                        <th>Price/Night</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rooms as $r): ?>
                    <tr>
                        <td><?php echo $r['room_id']; ?></td>
                        <td><?php echo htmlspecialchars($r['room_number']); ?></td>
                        <td><?php echo htmlspecialchars($r['room_type']); ?></td>
                        <td>Rs. <?php echo number_format($r['price_per_night'], 2); ?></td>
                        <td>
                            <?php 
                            $badge = 'success';
                            if ($r['status'] == 'Occupied') $badge = 'danger';
                            elseif ($r['status'] == 'Maintenance') $badge = 'warning';
                            ?>
                            <span class="badge badge-<?php echo $badge; ?>"><?php echo $r['status']; ?></span>
                        </td>
                        <td><?php echo htmlspecialchars(substr($r['description'], 0, 30)) . '...'; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $r['room_id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?php echo $r['room_id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this room?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    filterTable("searchRoom", "roomsTable", 1); // 1 is the column index for Room Number
});
</script>

<?php include '../../includes/footer.php'; ?>
