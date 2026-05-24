<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$room_id = $_GET['id'] ?? null;

if ($room_id) {
    try {
        // Check if room has active bookings
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ? AND status IN ('Booked', 'Checked In')");
        $stmt->execute([$room_id]);
        $active_bookings = $stmt->fetchColumn();
        
        if ($active_bookings > 0) {
            header("Location: view.php?msg=error&err=" . urlencode("Cannot delete room with active bookings."));
            exit();
        } else {
            // Delete room
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
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
