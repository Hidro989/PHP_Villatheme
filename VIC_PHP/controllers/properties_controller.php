<?php
    require_once('controllers/base_controller.php');
    require_once('models/property.php');
    require_once('models/product.php');
    require_once('models/pagination.php');
    class PropertiesController extends BaseController{

        private $script = '<script src="./assets/javascripts/property.js"></script>';
        public function __construct()
        {
            $this->folder = 'properties';
        }


        public function create(){
            $this->render('create', 'Create Property', array('errors' => $this->errors), $this->script);
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

            if(count($this->errors) > 0){
                $this->create();
                return;
            }

            $property = new Property(0, $_POST['name'], $_POST['type']);
            Property::insert($property);
            
            header('Location: index.php?controller=products');
        }

    }
?>