<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

try {
    $guests = $pdo->query("SELECT * FROM guests ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Guests > All Guests
</div>

<?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success">Guest deleted successfully.</div>
    <?php elseif ($_GET['msg'] == 'error'): ?>
        <div class="alert alert-danger">Error: <?php echo htmlspecialchars($_GET['err'] ?? 'Cannot delete guest.'); ?></div>
    <?php endif; ?>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>All Guests</h3>
        <a href="add.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Register New Guest</a>
    </div>
    <div class="card-body" style="padding: 20px;">
        <div class="filter-bar">
            <input type="text" id="searchGuest" class="form-control search-input" placeholder="Search by Name...">
        </div>
        
        <div class="table-responsive">
            <table class="table" id="guestsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>ID Proof</th>
                        <th>Registered Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($guests as $g): ?>
                    <tr>
                        <td><?php echo $g['guest_id']; ?></td>
                        <td><?php echo htmlspecialchars($g['name']); ?></td>
                        <td><?php echo htmlspecialchars($g['mobile']); ?></td>
                        <td><?php echo htmlspecialchars($g['email']); ?></td>
                        <td><?php echo htmlspecialchars($g['id_proof']); ?></td>
                        <td><?php echo date('d-M-Y', strtotime($g['created_at'])); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $g['guest_id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete.php?id=<?php echo $g['guest_id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this guest?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($guests)): ?>
                    <tr><td colspan="7" style="text-align:center;">No guests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    filterTable("searchGuest", "guestsTable", 1); // 1 is the column index for Name
});
</script>

<?php include '../../includes/footer.php'; ?>
