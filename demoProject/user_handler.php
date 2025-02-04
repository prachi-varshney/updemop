<?php
include 'database.php';
class User
{
    function tableList($db, $data)
    {
        $condition = "1=1 ";
        if (!empty($data['name'])) {
            $condition .= " AND name LIKE '%" . $data['name'] . "%' ";
        }
        if (!empty($data['email'])) {
            $condition .= " AND email LIKE '%" . $data['email'] . "%' ";
        }
        if (!empty($data['phone'])) {
            $condition .= " AND phone LIKE '%" . $data['phone'] . "%' ";
        }

        $order = '';
        if (!empty($data['order_by'])) {
            $order = " ORDER BY ";
            if ($data['order_by'] == 1) {
                $order .= "id";
            } elseif ($data['order_by'] == 2) {
                $order .= "name";
            } elseif ($data['order_by'] == 3) {
                $order .= "email";
            } elseif ($data['order_by'] == 4) {
                $order .= "phone";
            }
            $order .= " " . $data['order'];
        }

        $query = "SELECT * FROM user_master WHERE $condition $order";
        $result = $db->getData($query);
        $html = '';
        if ($result['success'] == true) {
            $sno = 0;
            foreach ($result['data'] as $value) {
                $sno++;
                $html .= '<tr>';
                $html .= '<td>' . $sno . '</td>';
                $html .= '<td>' . $value['id'] . '</td>';
                $html .= '<td>' . $value['name'] . '</td>';
                $html .= '<td>' . $value['email'] . '</td>';
                $html .= '<td>' . $value['phone'] . '</td>';
                $html .= '<td>
                     <button class="btn btn-sm btn-dark" onclick="getFromData(' . $value['id'] . ')">edit</button>
                     <button class="btn btn-sm btn-danger delete-btn" onclick="deleteRecords(' . $value['id'] . ')">delete</button>
                    
                     </td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr align="center">';
            $html .= '<td style="color:red" align="center" colspan="6">' . "No data found" . '</td>';
            $html .= '</tr>';
        }
        return $html;
    }


    function addUpdate($db, $data) {
        $formData = [];
        foreach ($data as $key => $value) {
            $formData[$key] = isset($value) ? $value : '';
        }
        $name = $formData['name'];
        $email = $formData['email'];
        $phone = $formData['phone'];
        $password = $formData['password'];
        $id = !empty($formData['id']) ? $formData['id'] : 0;
    
        $err = array();
    
        if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
            $err['name'] = "Name can only contain letters and spaces.";
        }
    
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $err['email'] = "Invalid email format.";
        }
    
        if (!preg_match('/^[0-9]{10}$/', $phone)) {
            $err['phone'] = "Phone number must be 10 digits long.";
        }
    
        if (empty($id) && empty($password)) {
            $err['password'] = "Password is required.";
        } elseif (!empty($password)) {
            if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
                $err['password'] = "Password must be at least 8 characters long and contain at least one lowercase letter, one uppercase letter, one digit, and one special character.";
            }
        }
    
        if (!empty($err)) {
            return array("success" => false, "message" => implode("<br>", $err));
        }
    
        if (empty($id)) {
            $query = "INSERT INTO user_master (name, email, phone, password)
            VALUES ('$name', '$email', '$phone', '$password')";
        } else {
            if (!empty($password)) {
                $query = "UPDATE user_master 
                SET name = '$name', email = '$email', phone = '$phone', password = '$password' 
                WHERE id = '$id'";
            } else {
                $query = "UPDATE user_master 
                SET name = '$name', email = '$email', phone = '$phone' 
                WHERE id = '$id'";
            }
        }
    
     $db_resp = $db->runQuery($query);

    if ($db_resp) {
        if (empty($id)) {
            return array("success" => true, "message" => "User   created successfully");
        } else {
            return array("success" => true, "message" => "User   updated successfully");
        }
    } else {
        return array("success" => false, "message" => "Failed to save");
    }
}

    function getFormDetails($db, $id)
    {
        $query = "SELECT * FROM user_master WHERE id = " . $id;
        $data = $db->getEditRecord($query);
        if ($data['success'] == true) {
            return array("success" => true, "data" => $data['data']);

        } else {
            return array("success" => true, "data" => array());
        }
    }

    // function deleteUser($db, $id)
    // {
    //     $query = " DELETE FROM user_master WHERE id = " . $id;
    //     $data = $db->runQuery($query);

    //     if ($data) {
    //         return array("success" => true, "message" => "Record deleted successfully");
    //     } else {
    //         return array("success" => false, "message" => "Failed to delete");
    //     }
    // }




    function deleteUser($db, $id)
    {
        session_start();
        if ($_SESSION["email"] == $this->getEmail($db, $id)) {
            return array("success" => false, "message" => "You cannot delete your own account");
        }
    
        $query = " DELETE FROM user_master WHERE id = " . $id;
        $data = $db->runQuery($query);
    
        if ($data) {
            return array("success" => true, "message" => "Record deleted successfully");
        } else {
            return array("success" => false, "message" => "Failed to delete");
        }
    }
    
    function getEmail($db, $id)
    {
        $query = "SELECT email FROM user_master WHERE id = " . $id;
        $result = $db->getEditRecord($query);
        if ($result['success'] == true) {
            return $result['data']['email'];
        } else {
            return false;
        }
    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type'])) {
        $type = strtoupper($_POST['type']);
        $user = new User();
        $db = new Database();
        unset($_POST['type']);

        if (!empty($type) && $type == 'LIST') {
            echo $user->tableList($db, $_POST);
        } else if ($type == "ADD_EDIT") {
            $response = $user->addUpdate($db, $_POST);
            echo json_encode($response);
        } else if ($type == "GET_RECORD") {
            $response = $user->getFormDetails($db, $_POST['id']);
            echo json_encode($response);
        } else if ($type == "DELETE_RECORD") {
            $response = $user->deleteUser($db, $_POST['id']);
            echo json_encode($response);
        }
    } else {
        // Handle the case where the "type" key is not present in the $_POST array
        echo "Error: Missing 'type' parameter in the request.";
    }
}
?>