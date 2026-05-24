<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$error = '';
$success = '';
$guest_id = $_GET['id'] ?? null;

if (!$guest_id) {
    header("Location: view.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $id_proof = trim($_POST['id_proof'] ?? '');

    if (empty($name) || empty($mobile) || empty($email) || empty($address) || empty($id_proof)) {
        $error = "All fields are required.";
    } else {
        try {
            // Check unique email and mobile (exclude current guest)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM guests WHERE (email = ? OR mobile = ?) AND guest_id != ?");
            $stmt->execute([$email, $mobile, $guest_id]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Another guest with this email or mobile number already exists.";
            } else {
                $stmt = $pdo->prepare("UPDATE guests SET name = ?, mobile = ?, email = ?, address = ?, id_proof = ? WHERE guest_id = ?");
                $stmt->execute([$name, $mobile, $email, $address, $id_proof, $guest_id]);
                $success = "Guest updated successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM guests WHERE guest_id = ?");
    $stmt->execute([$guest_id]);
    $guest = $stmt->fetch();
    
    if (!$guest) {
        die("Guest not found.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > <a href="view.php">Guests</a> > Edit Guest
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Edit Guest: <?php echo htmlspecialchars($guest['name']); ?></h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="edit.php?id=<?php echo $guest_id; ?>" onsubmit="return validateGuestForm()">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($guest['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="mobile">Mobile Number *</label>
                <input type="text" id="mobile" name="mobile" class="form-control" value="<?php echo htmlspecialchars($guest['mobile']); ?>" required maxlength="10">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($guest['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="id_proof">ID Proof Number *</label>
                <input type="text" id="id_proof" name="id_proof" class="form-control" value="<?php echo htmlspecialchars($guest['id_proof']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="address">Full Address *</label>
                <textarea id="address" name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($guest['address']); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Guest</button>
            <a href="view.php" class="btn btn-warning">Cancel</a>
        </form>
    </div>
</div>

<script>
function validateGuestForm() {
    var name = document.getElementById('name').value;
    var mobile = document.getElementById('mobile').value;
    var email = document.getElementById('email').value;
    
    if (!validateName(name)) {
        alert('Please enter a valid name (letters and spaces only, min 2 chars).');
        return false;
    }
    
    if (!validateMobile(mobile)) {
        alert('Please enter a valid 10-digit Indian mobile number.');
        return false;
    }
    
    if (!validateEmail(email)) {
        alert('Please enter a valid email address.');
        return false;
    }
    
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>
