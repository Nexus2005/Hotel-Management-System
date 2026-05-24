<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$guest_id = $_GET['id'] ?? null;

if ($guest_id) {
    try {
        // Check if guest has active bookings
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE guest_id = ? AND status IN ('Booked', 'Checked In')");
        $stmt->execute([$guest_id]);
        $active_bookings = $stmt->fetchColumn();
        
        if ($active_bookings > 0) {
            header("Location: view.php?msg=error&err=" . urlencode("Cannot delete guest with active bookings."));
            exit();
        } else {
            // Delete guest
            $stmt = $pdo->prepare("DELETE FROM guests WHERE guest_id = ?");
            $stmt->execute([$guest_id]);
            header("Location: view.php?msg=deleted");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: view.php?msg=error&err=" . urlencode("Database error: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: view.php");
    exit();
}
?>
