<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$error = '';
$success = '';

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
            // Check unique email and mobile
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM guests WHERE email = ? OR mobile = ?");
            $stmt->execute([$email, $mobile]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Guest with this email or mobile number already exists.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO guests (name, mobile, email, address, id_proof) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $mobile, $email, $address, $id_proof]);
                $success = "Guest registered successfully.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

include '../../includes/header.php';
include '../../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Guests > Add Guest
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>Register New Guest</h3>
    </div>
    <div class="card-body" style="padding: 20px;">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="add.php" onsubmit="return validateGuestForm()">
            <div class="form-group">
                <label for="name">Full Name *</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="mobile">Mobile Number *</label>
                <input type="text" id="mobile" name="mobile" class="form-control" required maxlength="10">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="id_proof">ID Proof Number (Aadhar/PAN/Passport) *</label>
                <input type="text" id="id_proof" name="id_proof" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="address">Full Address *</label>
                <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Register Guest</button>
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
    
    var confirmName = prompt("Please confirm the guest's name before registering:");
    if (confirmName !== name.trim()) {
        alert('Name confirmation failed. Registration cancelled.');
        return false;
    }
    
    return true;
}
</script>

<?php include '../../includes/footer.php'; ?>
