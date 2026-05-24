<?php
require_once '../includes/session_check.php';
require_once '../config/db.php';

try {
    // Fetch all guests with their total booking count
    $stmt = $pdo->query("
        SELECT g.*, 
               (SELECT COUNT(*) FROM bookings b WHERE b.guest_id = g.guest_id) as total_bookings
        FROM guests g
        ORDER BY g.name ASC
    ");
    $guests = $stmt->fetchAll();

    // Create XML Document
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;

    // Add Stylesheet PI
    $xslt = $xml->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="guests.xsl"');
    $xml->appendChild($xslt);

    // Add DTD reference (DOMImplementation handles DOCTYPE)
    $imp = new DOMImplementation();
    $dtd = $imp->createDocumentType('hotel_guests', '', 'guests.dtd');
    $xml->appendChild($dtd);

    // Root element
    $root = $xml->createElement('hotel_guests');
    $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $root->setAttribute('xsi:noNamespaceSchemaLocation', 'guests.xsd');
    $xml->appendChild($root);

    foreach ($guests as $g) {
        $guestNode = $xml->createElement('guest');
        $guestNode->setAttribute('id', 'G' . $g['guest_id']);

        $guestNode->appendChild($xml->createElement('name', htmlspecialchars($g['name'])));
        $guestNode->appendChild($xml->createElement('mobile', htmlspecialchars($g['mobile'])));
        $guestNode->appendChild($xml->createElement('email', htmlspecialchars($g['email'])));
        $guestNode->appendChild($xml->createElement('address', htmlspecialchars($g['address'])));
        $guestNode->appendChild($xml->createElement('id_proof', htmlspecialchars($g['id_proof'])));
        $guestNode->appendChild($xml->createElement('total_bookings', $g['total_bookings']));

        $root->appendChild($guestNode);
    }

    // Save XML file
    $xmlFilePath = 'guests.xml';
    $xml->save($xmlFilePath);

    $success = "XML Export generated successfully.";
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $error = "XML Generation error: " . $e->getMessage();
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="breadcrumb">
    <a href="/hotel/dashboard.php">Dashboard</a> > Reports > XML Export
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h3>XML Export</h3>
    </div>
    <div class="card-body" style="padding: 20px; text-align: center;">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            
            <p>The XML file has been generated and styled using XSLT.</p>
            <br>
            <a href="guests.xml" target="_blank" class="btn btn-primary btn-lg">View Generated XML (Styled)</a>
            <br><br>
            <div style="text-align: left; background: #f8f9fa; padding: 15px; border-radius: 5px;">
                <strong>Files involved:</strong>
                <ul>
                    <li><code>guests.xml</code> - Generated data</li>
                    <li><code>guests.dtd</code> - Document Type Definition</li>
                    <li><code>guests.xsd</code> - XML Schema</li>
                    <li><code>guests.xsl</code> - XSL Transformation</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
