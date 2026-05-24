<?php
require_once '../../includes/session_check.php';
require_once '../../config/db.php';

$booking_id = $_GET['id'] ?? null;

if ($booking_id) {
    try {
        $pdo->beginTransaction();
        
        // Update booking status
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'Checked In' WHERE booking_id = ? AND status = 'Booked'");
        $stmt->execute([$booking_id]);
        
        // Get room id
        $stmt = $pdo->prepare("SELECT room_id FROM bookings WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $room_id = $stmt->fetchColumn();
        
        // Update room status
        $stmt = $pdo->prepare("UPDATE rooms SET status = 'Occupied' WHERE room_id = ?");
        $stmt->execute([$room_id]);
        
        $pdo->commit();
        header("Location: view.php?msg=checkedin");
        exit();
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        header("Location: view.php?msg=error&err=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: view.php");
    exit();
}
?>
