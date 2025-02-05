<?php

include 'database.php';
class invoice
{

    function tableList($db, $data)
    {
        $condition = "1=1 ";
        if (!empty($data['invoice_no.'])) {
            $condition .= " AND invoice_no. LIKE '%" . $data['invoice_no.'] . "%' ";
        }
        if (!empty($data['invoice_date'])) {
            $condition .= " AND invoice_date LIKE '%" . $data['invoice_date'] . "%' ";
        }
        if (!empty($data['name'])) {
            $condition .= " AND name LIKE '%" . $data['name'] . "%' ";
        }
        if (!empty($data['email'])) {
            // $condition .= " AND email = '" . $data['email'] . "' ";
            $condition .= " AND email LIKE '%" . $data['email'] . "%' ";
        }
        if (!empty($data['phone'])) {
            // $condition .= " AND phone = '" . $data['phone'] . "' ";
            $condition .= " AND phone LIKE '%" . $data['phone'] . "%' ";
        }
        $query = "SELECT 
    i.invoice_id, 
    i.invoice_no, 
    i.invoice_date, 
    c.address, 
    i.client_id,
    c.name, 
    c.email, 
    c.phone, 
    i.total 
FROM 
    invoice_master i 
INNER JOIN 
    client_master c 
ON 
    i.client_id = c.id ";
    // -- WHERE 
    // --     $condition";
        // print_r($query);die;
        $result = $db->getData($query);
        // print_r($result);die;
        $html = '';
        if ($result['success'] == true) {

            $sno = 0;
            foreach ($result['data'] as $value) {
                $sno++;
                $html .= '<tr>';
                $html .= '<td>' . $sno . '</td>';
                $html .= '<td>' . $value['invoice_id'] . '</td>';
                $html .= '<td>' . $value['invoice_no'] . '</td>';
                $html .= '<td>' . $value['invoice_date'] . '</td>';
                $html .= '<td>' . $value['name'] . '</td>';
                $html .= '<td>' . $value['address'] . '</td>';
                $html .= '<td>' . $value['email'] . '</td>';
                $html .= '<td>' . $value['phone'] . '</td>';
                $html .= '<td>' . $value['total'] . '</td>';
                $html .= '<td>
             <button class="btn btn-sm btn-primary" onclick="generatePdf(' . $value['invoice_id'] . ')">PDF</button>
          </td>';
$html .= '<td>
             <button class="btn btn-sm btn-success" onclick="sendMail(' . $value['invoice_id'] . ')">Mail</button>
          </td>';
          $html .= '<td>
          <button class="btn btn-sm btn-dark" onclick="getFromData(' . $value['invoice_id'] . ')">Edit</button>
          <button class="btn btn-sm btn-danger delete-btn" onclick="deleteRecords(' . $value['invoice_id'] . ')">Delete</button>
      </td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr align="center">';
            $html .= '<td style="color:red" align="center" colspan="6">' . "No data found" . '</td>';
            $html .= '</tr>';
        }
        // print_r($html);die;
        return $html;
    }
    function addUpdate($db, $data)
    {

        $formData = [];
        foreach ($data as $key => $value) {
            $formData[$key] = isset($value) ? $value : '';
        }
        $name = $formData['invoice_no'];
        $email = $formData['invoice_date'];
        $phone = $formData['client_id'];
        $password = $formData['total'];
        $id = !empty($formData['id']) ? $formData['id'] : 0;

        $err = array();
        if (empty($name)) {
            $err[] = "Name cannot be empty.";
        }

        if (empty($email)) {
            $err[] = "Email cannot be empty.";
        }

        if (empty($phone)) {
            $err[] = "Phone number cannot be empty.";
        }


        if (empty($password)) {
            $err[] = "Password number cannot be empty.";
        }
        if (!empty($err)) {
            foreach ($err as $message) {
                return array("success" => false, "message" => $message);
                break;
            }
        }

        $query = "";
        if (empty($id)) {
            $query = "INSERT INTO invoice_master (name, email, phone, password)
            VALUES ('$name', '$email', '$phone', '$password')";
        } else {
            $query = "UPDATE invoice_master 
            SET name = '$name', email = '$email', phone = '$phone', password = '$password' 
            WHERE id = '$id'";

            
        }
       
    
        $db_resp = $db->runQuery($query);
        if ($db_resp) {
            return array("success" => true, "message" => "invoice saved successfully");
        } else {
            return array("success" => false, "message" => "Failed to save");
        }


    }

    function getFormDetails($db, $id)
    {
        $query = "SELECT * FROM invoice_master WHERE id = " . $id;
        $data = $db->getEditRecord($query);
        if ($data['success'] == true) {
            return array("success" => true, "data" => $data['data']);

        } else {
            return array("success" => true, "data" => array());
        }
    }

    function deleteRecords($db, $id)
    {
        $query = " DELETE FROM invoice_master WHERE id = " . $id;
        try {
            $db_resp = $db->runQuery($query);
            if ($db_resp) {
                return array("success" => true, "message" => "Record deleted successfully");
            } else {
                return array("success" => false, "message" => "Failed to delete");
            }
        } catch (Exception $e) {
            return array("success" => false, "message" => "Error deleting record: " . $e->getMessage());
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type'])) {
        $type = strtoupper($_POST['type']);
        $invoice = new Invoice();
        $db = new Database();
        unset($_POST['type']);

        if ($type == "AUTOCOMPLETE") {
            $term = $_POST['term'];
            $query = "SELECT name, address, email, phone FROM client_master WHERE name LIKE '%$term%'";
            $result = $db->getData($query);
            $data = array();
            foreach ($result['data'] as $row) {
                $data[] = array(
                    'value' => $row['name'],
                    'address' => $row['address'],
                    'email' => $row['email'],
                    'phone' => $row['phone']
                );
            }
            echo json_encode($data);
        } else if (!empty($type) && $type == 'LIST') {
            echo $invoice->tableList($db, $_POST);
        } else if ($type == "ADD_EDIT") {
            $response = $invoice->addUpdate($db, $_POST);
            echo json_encode($response);
        } else if ($type == "GET_RECORD") {
            $response = $invoice->getFormDetails($db, $_POST['id']);
            echo json_encode($response);
        } else if ($type == "DELETE_RECORD") {
            $response = $invoice->deleteRecords($db, $_POST['id']); // Ensure this matches the function name
            echo json_encode($response);
        } else if ($type == "EDIT_RECORD") {
            $response = $invoice->getFormDetails($db, $_POST['id']);
            echo json_encode($response);
        }
    } else {
        // Handle the case where the "type" key is not present in the $_POST array
        echo "Error: Missing 'type' parameter in the request.";
    }
}
?>