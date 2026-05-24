<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
<html>
<head>
    <title>Hotel Guest Directory</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background-color: #f4f6f9;
            color: #333;
            margin: 40px;
        }
        h2 {
            color: #1a1f36;
            text-align: center;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #1a1f36;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .badge {
            background: #27ae60;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>FitStay Hotel Guest Directory</h2>
    <table>
        <tr bgcolor="#1a1f36">
            <th>Guest ID</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Address</th>
            <th>ID Proof</th>
            <th>Total Bookings</th>
        </tr>
        <xsl:for-each select="hotel_guests/guest">
        <tr>
            <td><xsl:value-of select="@id"/></td>
            <td><xsl:value-of select="name"/></td>
            <td><xsl:value-of select="mobile"/></td>
            <td><xsl:value-of select="email"/></td>
            <td><xsl:value-of select="address"/></td>
            <td><xsl:value-of select="id_proof"/></td>
            <td><span class="badge"><xsl:value-of select="total_bookings"/></span></td>
        </tr>
        </xsl:for-each>
    </table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
