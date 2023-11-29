<?php

require '../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);

// Enable auto scripting and language detection
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;

// Read the CSS file
$stylesheet = file_get_contents('style.css');

// Write the CSS to the PDF
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

// Generate the HTML markup for the invoice details
$html = '
    <h1>View invoice details</h1>
';

require '../connect.php';

$id = intval($_GET['id']);
$invoice_id = $id;

$sql = "SELECT invoices.*, addresses.city, addresses.street_name, addresses.phone, shipments.method, customers.first_name, customers.last_name, customers.user_name, customers.phone FROM invoices, addresses, shipments, customers WHERE invoices.address_id = addresses.id AND invoices.shipment_id = shipments.id AND invoices.customer_id = customers.id AND invoices.id = '$invoice_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

$user_name = $row['user_name'];
$user_name .= "." . $row['delivery_date'];

$conn->close();

$html .= '
    <u>Reservation Date: ' . $row['reservation_date'] . '</u><br>
    <u>Status: An invoice has been paid</u><br>

    <u>City: ' . $row['city'] . '</u><br>
    <u>Street Name: ' . $row['street_name'] . '</u><br>
    <u>Method: ' . $row['method'] . '</u><br>

    <u>Customer: ' . $row['first_name'] . ' ' . $row['last_name'] . '</u><br>
    <u>User Name: ' . $row['user_name'] . '</u><br>

    <u>Delivery Date: ' . $row['delivery_date'] . '</u>
    <hr>

    <h4>Invoice Items: </h4>
';

require '../connect.php';

$sql = "SELECT invoices_fragrances.*, fragrances.id AS ID, fragrances.name FROM invoices_fragrances, fragrances WHERE invoices_fragrances.fragrance_id = fragrances.id AND invoices_fragrances.invoice_id = '$invoice_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $price_1 = 0;

    $html .= '
        <center>
            <table border="1" style="width: 100%;">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">Name</th>
                    <th style="width: 20%;">Quantity</th>
                    <th style="width: 10%;">Price</th>
                </tr>
    ';

    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $fragrance_id = $row['ID'];

        $sql2 = "SELECT perfume_compositions.*, products.milliliter, (perfume_compositions.volume * products.milliliter) AS 'X' FROM perfume_compositions, products WHERE perfume_compositions.product_id = products.id AND perfume_compositions.fragrance_id = '$fragrance_id'";
        $result2 = $conn->query($sql2);

        $price_2 = 0;

        while ($row2 = $result2->fetch_assoc()) {
            $price_2 += $row2['X'];
        }

        $price_2 = ($row['quantity'] * $price_2);

        $price_1 += $price_2;

        $html .= '
            <tr>
                <td style="width: 10%;">' . $counter . '</td>
                <td style="width: 20%;">' . $row['name'] . '</td>
                <td style="width: 20%;">' . $row['quantity'] . '</td>
                <td style="width: 10%;">' . $price_2 . '</td>
            </tr>
        ';

        $counter++;
    }

    $html .= '
            </table>
        </center>

        <h4>Total price: ' . $price_1 . '</h4>
    ';
} else {
    $html .= '0 results';
}

$conn->close();

// Output the PDF as a downloadable file
$mpdf->WriteHTML($html);
$mpdf->Output("$user_name.pdf", "D");
