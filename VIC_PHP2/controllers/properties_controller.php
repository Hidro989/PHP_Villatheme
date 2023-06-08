<?php
    require_once('controllers/base_controller.php');
    require_once('models/property.php');
    require_once('models/product.php');
    require_once('models/pagination.php');
    class PropertiesController extends BaseController{

        public function __construct()
        {
            $this->folder = 'properties';
        }


        public function stored(){

            if($_SERVER["REQUEST_METHOD"] == 'GET'){
                header('Location: index.php?controller=pages&action=error');
            }

            if(empty($_POST['name'] || empty($_POST['type'])) ){
                array_push($this->errors, 'Vui lòng nhập đầy đủ các trường');
            }

            if($_POST['type'] != 'category' && $_POST['type'] != 'tag'){
                array_push($this->errors, 'Trường Type sai định dạng dữ liệu');
            }

            $property = new Property(0, $_POST['name'], $_POST['type']);
            
            $success = 'Insert property successfully';
            try{
                Property::insert($property);
            }catch(Exception $e){
                $success = $e->getMessage();
            }
            
            $data = array('success' => $success, 'errors' => $this->errors);
            $this->renderJson($data);
        }

    }
?>