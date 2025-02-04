<?php 
include 'database.php';
class item{

    function tableList($db,$data)
    {

        $condition = "1=1 ";
        if (!empty($data['item_name'])) {
            $condition .= " AND item_name LIKE '%" . $data['item_name'] . "%' ";
        }

        $query = "SELECT * FROM item_master where $condition";
        $result = $db->getData($query);
        
        $html = '';  
    
        if($result['success'] == true){
            $sno =0;
            foreach($result['data'] as $value){
                $sno++;
                $html .= '<tr>';
                $html .= '<td>' . $sno . '</td>';
                $html .= '<td>' . $value['id'] . '</td>';
                $html .= '<td>' . $value['item_name'] . '</td>';
                $html .= '<td>' . $value['item_description'] . '</td>';
                $html .= '<td>' . $value['price'] . '</td>';
                $html .= '<td><img src="' . $value['path'] . '" alt="Item Image" style="width: 40px; height: auto;"></td>';
                $html .= '<td>
                             <button class="btn btn-sm btn-dark" onclick="getFromData(' . $value['id'] . ')">edit</button>
                             <button class="btn btn-sm btn-danger delete-btn" onclick="deleteRecords(' . $value['id'] . ')">delete</button>
                          </td>';
                $html .= '</tr>';
            }
        }else {
            $html .= '<tr align="center">';
            $html .= '<td style="color:red" align="center" colspan="6">' . "No data found" . '</td>';
            $html .= '</tr>';
        }
    
    
        return $html;
    }
    function addUpdate($db,$data){

        $formData = [];
        foreach ($data as $key => $value) {
            $formData[$key] = isset($value) ? $value : '';
        }
        $item_name = $formData['item_name'];
        $item_description = $formData['item_description'];
        $price = $formData['price'];

        // $path = isset($formData['itemimage']) ? $formData['itemimage'] : '';
        $id = !empty($formData['id'])?$formData['id']:0;

        $path = '';
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] == 0) {
            $file = $_FILES['item_image'];
            $path = $this->uploadFile($file);
        }
        else {
            $query = "SELECT path FROM item_master WHERE id = '$id'";
            $result = $db->getData($query);
            $path = $result['data'][0]['path'];
        }



        $err = array();
        if (empty($item_name)) {
            $err[] = "item_name cannot be empty.";
        }

        if (empty($item_description)) {
            $err[] = "item_description cannot be empty.";
        }

        if (empty($price)) {
            $err[] = "price number cannot be empty.";
        }

        if (!empty($err)) {
            foreach ($err as $message) {
                return array("success"=>false,"message"=>$message);
                break;
            }
        }

        $query =  "";
        if (empty($id)) {
            $query = "INSERT INTO item_master (item_name, item_description, price, path)
            VALUES ('$item_name', '$item_description', '$price', '$path')";
        } else {
            $query = "UPDATE item_master 
            SET item_name = '$item_name', item_description = '$item_description', price = '$price', path = '$path' 
            WHERE id = '$id'";
        }

        $db_resp = $db->runQuery($query);
        if($db_resp){
            return array("success"=>true,"message"=>"item saved successfully");
        }else{
            return array("success"=>false,"message"=>"Failed to save");
        }


    }


    function uploadFile($file) {
        $target_dir = 'uploads/'; // Ensure this directory exists and is writable
        $target_file = $target_dir . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $target_file;
        } else {
            return '';
        }
    }


    function getFormDetails($db,$id){
        $query = "SELECT * FROM item_master WHERE id = ".$id;
        $data  = $db->getEditRecord($query);
        if($data['success'] == true ){
            $imagePath = 'uploads/' . $data['data']['path'];
        $data['data']['path'] = $imagePath;
            return array("success"=>true,"data" =>$data['data']);

        }else{
            return array("success"=>true,"data" =>array());
        }
    }

      function deleteItem($db,$id)
      {
        $query = "DELETE FROM item_master WHERE id = ".$id;
        $data  = $db->runQuery($query);
    
        if($data){
            return array("success"=>true,"message" =>"Record deleted successfully");
        }else{
            return array("success"=>false,"message" => "Failed to delete");
        }
    }

}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['type'])) {
        $type = strtoupper($_POST['type']);
        $item = new Item();
        $db = new Database();
        unset($_POST['type']);

       if(!empty($type) && $type == 'LIST'){
            echo  $item->tableList($db,$_POST);
        }else if($type == "ADD_EDIT"){
            $response = $item->addUpdate($db,$_POST);
            echo json_encode($response);
        }else if($type == "GET_RECORD"){
            $response = $item->getFormDetails($db,$_POST['id']);
            echo json_encode($response); 
        }else if($type == "DELETE_RECORD"){
            $response = $item->deleteItem($db,$_POST['id']);
            echo json_encode($response);
        }
    } else {
        // Handle the case where the "type" key is not present in the $_POST array
        echo "Error: Missing 'type' parameter in the request.";
    }
}
?>

