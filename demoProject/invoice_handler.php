<?php

include 'database.php';

class Invoice{
    function tableList($db, $data)
{
    $condition = "1=1 ";
    if (!empty($data['invoice_no.'])) {
        $condition .= " AND i.invoice_no LIKE '%" . $data['invoice_no.'] . "%' ";
    }
    if (!empty($data['invoice_date'])) {
        $condition .= " AND i.invoice_date LIKE '%" . $data['invoice_date'] . "%' ";
    }
    if (!empty($data['name'])) {
        $condition .= " AND c.name LIKE '%" . $data['name'] . "%' ";
    }
    if (!empty($data['email'])) {
        $condition .= " AND c.email LIKE '%" . $data['email'] . "%' ";
    }
    if (!empty($data['phone'])) {
        $condition .= " AND c.phone LIKE '%" . $data['phone'] . "%' ";
    }

    $query = "SELECT 
                i.invoice_id, 
                i.invoice_no, 
                i.invoice_date, 
                c.client_id, 
                c.name, 
                c.email, 
                c.phone, 
                i.total 
            FROM 
                invoice_master i 
            INNER JOIN 
                client_master c 
            ON 
                i.client_id = c.id 
            WHERE 
                $condition";

    $result = $db->getData($query);

    if ($result['success'] == false) {
        return $result['message'];
    }

    $html = '';

    $sno = 0;
    foreach ($result['data'] as $value) {
        $sno++;
        $html .= '<tr>';
        $html .= '<td>' . $sno . '</td>';
        $html .= '<td>' . $value['invoice_id'] . '</td>';
        $html .= '<td>' . $value['invoice_no'] . '</td>';
        $html .= '<td>' . $value['invoice_date'] . '</td>';
        $html .= '<td>' . $value['client_id'] . '</td>';
        $html .= '<td>' . $value['name'] . '</td>';
        $html .= '<td>' . $value['email'] . '</td>';
        $html .= '<td>' . $value['phone'] . '</td>';
        $html .= '<td>' . $value['total'] . '</td>';
        $html .= '<td>
                         <button class="btn btn-sm btn-dark" onclick="getFromData(' . $value['invoice_id'] . ')">edit</button>
                         <button class="btn btn-sm btn-danger delete-btn" onclick="deleteRecords(' . $value['invoice_id'] . ')">delete</button>
                      </td>';
        $html .= '</tr>';
    }

    return $html;
}
public function addUpdate($db, $data)
{
    $formData = [];
    foreach ($data as $key => $value) {
        $formData[$key] = isset($value) ? $value : '';
    }
    $invoice_no = $formData['invoice_no'];
    $invoice_date = $formData['invoice_date'];
    $client_id = $formData['client_id'];
    $total = $formData['total'];
    $id = !empty($formData['id']) ? $formData['id'] : 0;

    $err = array();
    if (empty($invoice_no)) {
        $err[] = "Invoice No cannot be empty.";
    }

    if (empty($invoice_date)) {
        $err[] = "Invoice Date cannot be empty.";
    }

    if (empty($client_id)) {
        $err[] = "Client ID cannot be empty.";
    }

    if (empty($total)) {
        $err[] = "Total cannot be empty.";
    }
    if (!empty($err)) {
        foreach ($err as $message) {
            return array("success" => false, "message" => $message);
            break;
        }
    }

    $query = "";
    if (empty($id)) {
        $query = "INSERT INTO invoice_master (invoice_no, invoice_date, client_id, total)
        VALUES ('$invoice_no', '$invoice_date', '$client_id', '$total')";
    } else {
        $query = "UPDATE invoice_master 
        SET invoice_no = '$invoice_no', invoice_date = '$invoice_date', client_id = '$client_id', total = '$total' 
        WHERE id = '$id'";
    }

    $db_resp = $db->runQuery($query);
    if ($db_resp) {
        return array("success" => true, "message" => "invoice saved successfully");
    } else {
        return array("success" => false, "message" => "Failed to save");
    }
}

public function getFormDetails($db, $id)
{
    $query = "SELECT * FROM invoice_master WHERE id = " . $id;
    $data = $db->getEditRecord($query);
    if ($data['success'] == true) {
        return array("success" => true, "data" => $data['data']);

    } else {
        return array("success" => true, "data" => array());
    }
}

public function deleteInvoice($db, $id)
{
    $query = " DELETE FROM invoice_master WHERE id = " . $id;
    $data = $db->runQuery($query);

    if ($data) {
        return array("success" => true, "message" => "Record deleted successfully");
    } else {
        return array("success" => false, "message" => "Failed to delete");
    }
}

}
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['type'])) {
            $type = strtoupper($_POST['type']);
            $invoice = new Invoice();
            $db = new Database();
            $invoice_handler = new InvoiceHandler($db);
            unset($_POST['type']);
    
            if (!empty($type) && $type == 'CREATE_INVOICE') {
                $invoice_data = array(
                    'invoice_no' => 'INV001',
                    'invoice_date' => '2022-01-01',
                    'client_id' => 1,
                    'total' => 100.00
                );
                $invoice_id = $invoice_handler->createInvoice($invoice_data);
    
                $invoice_item_data = array(
                    'invoice_id' => $invoice_id,
                    'item_name' => 'Item 1',
                    'quantity' => 2,
                    'price' => 50.00,
                    'total' => 100.00
                );
                $invoice_handler->addInvoiceItem($invoice_item_data);
    
                $invoice_details = $invoice_handler->getInvoiceDetails($invoice_id);
                $invoice_items = $invoice_handler->getInvoiceItems($invoice_id);
    
                $invoice_data = array(
                    'id' => $invoice_id,
                    'invoice_no' => 'INV002',
                    'invoice_date' => '2022-01-02',
                    'client_id' => 2,
                    'total' => 200.00
                );
                $invoice_handler->updateInvoice($invoice_data);
    
                $invoice_handler->deleteInvoice($invoice_id);
            } else if (!empty($type) && $type == 'LIST') {
                echo $invoice->tableList($db, $_POST);
            } else if ($type == "ADD_EDIT") {
                $response = $invoice->addUpdate($db, $_POST);
                echo json_encode($response);
            } else if ($type == "GET_RECORD") {
                $response = $invoice->getFormDetails($db, $_POST['id']);
                echo json_encode($response);
            } else if ($type == "DELETE_RECORD") {
                $response = $invoice->deleteInvoice($db, $_POST['id']);
                echo json_encode($response);
            }
        } else {
            // Handle the case where the "type" key is not present in the $_POST array
            echo "Error: Missing 'type' parameter in the request.";
        }
    }

?>