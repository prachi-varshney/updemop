<?php 
include 'database.php';
class Client{

    function tableList($db,$data){

        $condition = "1=1 ";
        if (!empty($data['name'])) {
            $condition .= " AND name LIKE '%" . $data['name'] . "%' ";
        }
        if (!empty($data['email'])) {
            $condition .= " AND email = '" . $data['email'] . "' ";
            // $condition .= " AND email LIKE '%" . $data['email'] . "%' ";
        }
        if (!empty($data['phone'])) {
            $condition .= " AND phone = '" . $data['phone'] . "' ";
            // $condition .= " AND phone LIKE '%" . $data['phone'] . "%' ";
        }
        if (!empty($data['address'])) {
            // $condition .= " AND address = '" . $data['address'] . "' ";
            $condition .= " AND address LIKE '%" . $data['address'] . "%' ";
        }

        $sorting = '';
        if (!empty($data['sort_order'] && !empty($data['sort_column']))) {
            $sorting = " ORDER BY " . $data['sort_column'] . " " . $data['sort_order'];
        }


      
        $page = isset($data['page']) ? $data['page'] : 1;     
        $records_per_page = isset($data['records_per_page']) ? $data['records_per_page'] : 5;
        $start_from = ($page - 1) * $records_per_page;

        
        $start_from = ($data['page'] - 1) * $records_per_page;



        $query = "SELECT id,name,phone,email,CONCAT(t1.address,', ',t2.state_name, ', ',t3.district_name, ', ',pincode) AS address 
        FROM client_master as t1 
        LEFT JOIN ms_state_master AS t2 ON t1.state = t2.state_id 
        LEFT JOIN ms_district_master as t3 ON t1.city = t3.district_id where $condition $sorting LIMIT $start_from, $records_per_page";
        echo $query;die;

        $query_total = "SELECT COUNT(*) AS total FROM client_master WHERE $condition";
       
        $recordData = $db->getData($query_total);
        $rec_total = (int) $recordData['data'][0]['total'];
        $result = $db->getData($query);
        $total_pages = ceil($rec_total / $records_per_page);

        $result = $db->getData($query);
        
        $html = '';  
    
        if($result['success'] == true){
            $sno =0;
            foreach($result['data'] as $value){
                $sno++;
                $html .= '<tr>';
                $html .= '<td>' . $sno . '</td>';
                $html .= '<td>' . $value['id'] . '</td>';
                $html .= '<td>' . $value['name'] . '</td>';
                $html .= '<td>' . $value['email'] . '</td>';
                $html .= '<td>' . $value['phone'] . '</td>';
                $html .= '<td>' . $value['address'] . '</td>';
                $html .= '<td>
                             <button class="btn btn-sm btn-dark" onclick="getFromData(' . $value['id'] . ')">edit</button>
                             <button class="btn btn-sm btn-danger delete-btn" onclick="deleteRecords(' . $value['id'] . ')">delete</button>
                          </td>';
                $html .= '</tr>';
            }
        }else {
            $html .= '<tr align="center">';
            $html .= '<td style="color:red" align="center" colspan="7">' . "No data found" . '</td>';
            $html .= '</tr>';
        }
    
        $response = [
            'html' => $html,
            'total_pages' => $total_pages,
        ];
        return $response;
        // return $html;
    }
    function addUpdate($db,$data){

        $formData = [];
        foreach ($data as $key => $value) {
            $formData[$key] = isset($value) ? $value : '';
        }
        $name = $formData['name'];
        $email = $formData['email'];
        $phone = $formData['phone'];
        $state = $formData['state'];
        $city = $formData['city'];
        $pincode = $formData['pincode'];
        $address = $formData['address'];
        $id = !empty($formData['id'])?$formData['id']:0;

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

        if (empty($pincode)) {
            $err[] = "PinCode number cannot be empty.";
        }

        
       
        if (!empty($err)) {
            foreach ($err as $message) {
                return array("success"=>false,"message"=>$message);
                break;
            }
        }

        $query =  "";
        if (empty($id)) {
            $query = "INSERT INTO client_master (name, email, phone, state, city, pincode, address) 
            VALUES ('$name', '$email', '$phone', '$state', '$city', '$pincode', '$address')";
          
        } else {
            $query = "UPDATE client_master 
            SET name = '$name', email = '$email', phone = '$phone',state = '$state', city = '$city', pincode = '$pincode', address = '$address'
            WHERE id = '$id'";
        }

        $db_resp = $db->runQuery($query);
        if($db_resp){
            return array("success"=>true,"message"=>"Client saved successfully");
        }else{
            return array("success"=>false,"message"=>"Failed to save");
        }


    }

    function getFormDetails($db,$id){
        $query = "SELECT * FROM client_master WHERE id = ".$id;
        $data  = $db->getEditRecord($query);
        if($data['success'] == true ){
            return array("success"=>true,"data" =>$data['data']);

        }else{
            return array("success"=>true,"data" =>array());
        }
    }

    function deleteClient($db,$id){
        $query = " DELETE FROM client_master WHERE id = ".$id;
        $data  = $db->runQuery($query);

        if($data){
            return array("success"=>true,"message" =>"Record deleted successfully");
        }else{
            return array("success"=>false,"message" => "Failed to delete");
        }
    }


    function stateList($db){
        $query = "SELECT state_id,state_name FROM ms_state_master";
        $result = $db->getData($query);
        
        $selectOption = '';  
    
        if($result['success'] == true){
            foreach($result['data'] as $value){
                $selectOption .= '<option value="' . $value['state_id'] . '">' . $value['state_name'] . '</option>';
            }
        }
    
        return $selectOption;
    }

    function cityList($db,$id){
        $query = "SELECT district_id,district_name FROM ms_district_master WHERE state_id = ".$id;
        $result = $db->getData($query);
        
        $selectOption = '';  
    
        if($result['success'] == true){
            foreach($result['data'] as $value){
                $selectOption .= '<option value="' . $value['district_id'] . '">' . $value['district_name'] . '</option>';
            }
        }
    
        return $selectOption;
    }

}


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['type'])) {
        $type = strtoupper($_POST['type']);
        $Client = new Client();
        $db = new Database();
        unset($_POST['type']);

        if(!empty($type) && $type == 'LIST'){
            echo  json_encode($Client->tableList($db,$_POST));
        }else if($type == "ADD_EDIT"){
            $response = $Client->addUpdate($db,$_POST);
            echo json_encode($response);
        }else if($type == "GET_RECORD"){
            $response = $Client->getFormDetails($db,$_POST['id']);
            echo json_encode($response); 
        }else if($type == "DELETE_RECORD"){
            $response = $Client->deleteClient($db,$_POST['id']);
            echo json_encode($response);
        }else if($type == "STATE_LIST"){
            echo  $Client->stateList($db);
        }
        else if($type == "CITY_LIST"){
            echo  $Client->cityList($db,$_POST['state_id']);
        }
    } else {
        // Handle the case where the "type" key is not present in the $_POST array
        echo "Error: Missing 'type' parameter in the request.";
    }
}
?>