<?php
if(isset($_POST['save_bill']) && $_POST['save_bill'] != '')
{
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
?>

<?php
// Database Connection
// $conn = new mysqli("localhost", "root", "", "inventory_db");

// if (isset($_POST['save_bill'])) {
//     $invoice_no = $_POST['invoice_no'];
//     $party_invoice_no = $_POST['party_invoice_no'];
//     $party_id = $_POST['party_id'];
//     $company_id = $_POST['company_id'];

//     $conn->begin_transaction();

//     try {
//         foreach ($_POST['item_desc'] as $i => $item_id) {
//             if (empty($item_id) || empty($_POST['qty'][$i])) {
//                 continue;
//             }
//             $qty = $_POST['qty'][$i];
//             $rate = $_POST['rate'][$i];
//             $total = $qty * $rate;
//             $sql = "INSERT INTO bills (invoice_no, party_invoice_no, party_id, company_id, item_id, qty, rate, total) 
//                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
//             $stmt = $conn->prepare($sql);
//             $stmt->bind_param("ssiiiidd", 
//                 $invoice_no, 
//                 $party_invoice_no, 
//                 $party_id, 
//                 $company_id, 
//                 $item_id, 
//                 $qty, 
//                 $rate, 
//                 $total
//             );
//             $stmt->execute();
//             $updateSql = "UPDATE inventory SET stock = stock - ? WHERE id = ?";
//             $updStmt = $conn->prepare($updateSql);
//             $updStmt->bind_param("ii", $qty, $item_id);
//             $updStmt->execute();
//         }
//         $conn->commit();
//         echo "Bill saved and inventory updated successfully!";
//     } catch (Exception $e) {
//         $conn->rollback();
//         echo "Error saving bill: " . $e->getMessage();
//     }
// }
?>