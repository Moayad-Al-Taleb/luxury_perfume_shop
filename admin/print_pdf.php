<?php

// Include the MPDF library
require '../vendor/autoload.php';

// Create a new instance of MPDF
$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);

// Enable auto scripting and language detection
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

// Read the CSS file
$stylesheet = file_get_contents('style.css');

// Write the CSS to the PDF
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

// Get the invoice ID from the request parameters
$id = intval($_GET['id']);
$invoice_id = $id;

// Fetch the invoice details from the database
require '../connect.php';

$sql = "SELECT invoices.*, addresses.city, addresses.street_name, addresses.phone, shipments.method, customers.first_name, customers.last_name, customers.user_name, customers.phone FROM invoices, addresses, shipments, customers WHERE invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id AND invoices.id = '$invoice_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$user_name = $row['user_name'];
$user_name .= "." . $row['delivery_date'];

$conn->close();

// Generate the HTML markup for the invoice details
$html = '
    <h1>View invoice details</h1>

    <u>Reservation Date: ' . $row['reservation_date'] . '</u> <br>
    <u>Status: An invoice has been paid</u> <br>

    <u>City: ' . $row['city'] . '</u> <br>
    <u>Street Name: ' . $row['street_name'] . '</u> <br>
    <u>Method: ' . $row['method'] . '</u> <br>

    <u>Customer: ' . $row['first_name'] . ' ' . $row['last_name'] . '</u> <br>
    <u>User Name: ' . $row['user_name'] . '</u> <br>

    <u>Delivery Date: ' . $row['delivery_date'] . '</u>
    <hr>

    <h4>Invoice Items: </h4>';

// Fetch the invoice items from the database
require '../connect.php';

$sql = "SELECT customers_products.*, products.* FROM customers_products, products WHERE customers_products.product_id = products.id AND customers_products.invoice_id = '$invoice_id';";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $price_1 = 0;

    $html .= '
        <center>
            <table border="1" style="width: 100%; table-layout: fixed;">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 20%;">Details</th>
                    <th style="width: 10%;">Code</th>
                    <th style="width: 10%;">Scent</th>
                    <th style="width: 10%;">Size</th>
                    <th style="width: 10%;">Expiration Date</th>
                    <th style="width: 10%;">Unit Price</th>
                    <th style="width: 10%;">Quantity</th>
                    <th style="width: 10%;">Price</th>
                </tr>';

    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $price_2 = 0;

        if (empty($row['milliliter']) && !empty($row['price'])) {
            $price_2 = ($row['price'] * $row['quantity']);
        } elseif (!empty($row['milliliter']) && empty($row['price'])) {
            $price_2 = ((intval($row['size']) * $row['milliliter']) * $row['quantity']);
        }

        $price_1 += $price_2;

        $html .= '
            <tr>
                <td>' . $counter . '</td>
                <td>' . $row['name'] . '</td>
                <td>' . $row['details'] . '</td>
                <td>' . $row['code'] . '</td>
                <td>' . $row['scent'] . '</td>
                <td>' . $row['size'] . '</td>
                <td>' . $row['expiration_date'] . '</td>
                <td>';

        if (empty($row['milliliter']) && !empty($row['price'])) {
            $html .= $row['price'] . ' widget';
        } elseif (!empty($row['milliliter']) && empty($row['price'])) {
            $html .= $row['milliliter'] . ' milliliter';
        }

        $html .= '
                </td>
                <td>' . $row['quantity'] . '</td>
                <td>' . $price_2 . '</td>
            </tr>';

        $counter++;
    }

    $html .= '
            </table>
        </center>

        <h4>Total price: ' . $price_1 . '</h4>';
} else {
    $html .= '0 results';
}

$conn->close();

// Write the invoice details HTML to the PDF
$mpdf->WriteHTML($html);

// Output the PDF inline or force download
// $mpdf->Output("myPDF.pdf", "I"); // Inline display
$mpdf->Output("$user_name.pdf", "D"); // Force download
