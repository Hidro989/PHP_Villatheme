<?php
    require_once('controllers/base_controller.php');
    require_once('models/product.php');
    require_once('models/pagination.php');
    require_once('models/property.php');
    
    class ProductsController extends BaseController{

        private $script = '<script src="./assets/javascripts/pro_edit_create.js"></script>';
        private $indexScript = '<script src="./assets/javascripts/index.js"></script>';
        public function __construct()
        {   
            $this->folder = 'products';
        }

        public function index(){
            $data = Product::getAll();
            $data['orderList'] = [
                ['key'=> 'CreatedDate', 'value'=> 'Date'], 
                ['key'=>'Title', 'value' => 'Product Name'], 
                ['key' => 'Price', 'value' =>'Price']];
            $data['sortList'] = ['ASC', 'DESC'];
            $this->render('index', "Products", $data, $this->indexScript);
        }

        public function create(){
            $data = array(
                'categories' => Property::getCategories(),
                'tags' => Property::getTags(),
                'errors' => $this->errors
            );
            $this->render('create', 'Create Product', $data, $this->script);
        }

        public function stored(){            
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                header('Location: index.php?controller=pages&action=error');
            }
            $request = $_REQUEST;
            // Check categories / tags
            if(!isset($request['categories'])){
                array_push($this->errors, 'Please select Categories');
            }


            if(!isset($request['tags'])){
                array_push($this->errors, 'Please select Tags');
            }

            // Check image / gallery
            if(empty($_FILES['image']['name'])) {
                array_push($this->errors, 'Please select Feature Image');
            }

            if(empty($_FILES['gallery']['name'][0])){
                array_push($this->errors, 'Please select Gallery');
            }
            
            // Check all value is submitted
            foreach($request as $key => $value) {
                if(empty($value)){
                    array_push($this->errors, "Please enter field $key");
                }else if($key == 'price'){
                    if(filter_var($value, FILTER_VALIDATE_INT) == false) {
                        array_push($this->errors, "Please enter number for field $key");
                    }
                }
            }
            if(count($this->errors) > 0){
                $this->create();
                return;
            }

            // Get Value from client
            $name = trim($request['name']);
            $sku = trim($request['sku']);
            $price = trim($request['price']);

            // Create floder for product
            $targetDirectory  = "./assets/images/";
            $id = Product::getLastID() + 1;

            mkdir($targetDirectory.'product'.$id);
            $targetDirectory = $targetDirectory.'product'.$id.'/';
            mkdir($targetDirectory.'image');

            $imageDirectory = $targetDirectory.'image/';
            // Save Feature Image
            $imageFileName = $_FILES['image']['name'];
            $imageFilePath = $imageDirectory . basename($imageFileName);
            move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath);


            // Save Gallery 
            mkdir($targetDirectory.'gallery');
            $galleryDirectory = $targetDirectory.'gallery/';
            $galleryFilePath = array();
            $galleryNames = $_FILES['gallery']['name'];

            foreach($galleryNames as $key => $value){
                $galleryFilePath[] = $galleryDirectory . basename($value);
            }

            $galleryTmpNames = $_FILES['gallery']['tmp_name'];

            for($i = 0; $i < count($galleryTmpNames); $i++){
                move_uploaded_file($galleryTmpNames[$i] , $galleryFilePath[$i]);
            }
            
            // Add product
            $gallery = implode(',', $galleryFilePath);

            $product = new Product(0, $name, $sku, $price, $imageFilePath, $gallery, null);

            Product::insert($product);

            // Add Category/Tag for newest product

            Product::addPropertyForNewestProduct(array_merge($request['categories'], $request['tags']));
            
            header('Location: index.php?controller=products');
        }


        public function edit(){
            if(!isset($_REQUEST['id'])){
                header('Location: index.php?controller=pages&action=error');
            }

            $product = Product::getByID($_REQUEST['id']);

            if(!isset($product)){
                header('Location: index.php?controller=pages&action=error');
            }

            $data = array(
                'product' => $product,
                'categories' => Property::getCategories(),
                'tags' => Property::getTags(),
                'errors' => $this->errors
            );
            $this->render('edit', "Edit Product", $data, $this->script);
        }

        public function update(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                header('Location: index.php?controller=pages&action=error');
            }

            $request = $_REQUEST;
            // Check categories / tags
            if(!isset($request['categories'])){
                array_push($this->errors, 'Please select Categories');
            }
            
            if(!isset($request['tags'])){
                array_push($this->errors, 'Please select Tags');
            }
            
            // Check all value is submitted
            foreach($request as $key => $value) {
                if(empty($value)){
                    array_push($this->errors, "Please enter field $key");
                }else if($key == 'price'){
                    if(filter_var($value, FILTER_VALIDATE_INT) == false) {
                        array_push($this->errors, "Please enter number for field $key");
                    }
                }
            }

            if(count($this->errors) > 0){
                $this->edit();
                return;
            }

            $targetDirectory  = './assets/images/';

            $product = Product::getByID($request['id']);
            $imageDirectory = explode('/', $product->image)[3];
            $targetDirectory .=$imageDirectory;
            

            // upload image
            $imageDirectory = $targetDirectory.'/'.'image/';
            if(!empty($_FILES['image']['name'])){

                if(file_exists($product->image)){
                    unlink($product->image);
                }
                 // Save Feature Image
                $imageFileName = $_FILES['image']['name'];
                $imageFilePath = $imageDirectory . basename($imageFileName);
                move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath);
                $product->image = $imageFilePath;
            }
            
            // upload gallery
            $galleryDirectory = $targetDirectory.'/'.'gallery/';
            if(!empty($_FILES['gallery']['name'][0])){
                $galleries = explode(',', $product->gallery);
                //delete gallery file
                foreach($galleries as $gallery){
                    if(file_exists($gallery)){
                        unlink($gallery);
                    }
                }

                $galleryFilePath = array();
                $galleryNames = $_FILES['gallery']['name'];

                foreach($galleryNames as $key => $value){
                    $galleryFilePath[] = $galleryDirectory . basename($value);
                }

                $galleryTmpNames = $_FILES['gallery']['tmp_name'];

                for($i = 0; $i < count($galleryTmpNames); $i++){
                    move_uploaded_file($galleryTmpNames[$i] , $galleryFilePath[$i]);
                }
                $product->gallery = implode(',', $galleryFilePath);
            }

            $product->name = trim($request['name']);
            $product->sku = trim($request['sku']);
            $product->price = trim($request['price']);
            Product::update($product);
            Product::updateProperty($request['id'], array_merge($request['categories'], $request['tags']));

            header('Location: index.php?controller=products');
        }

        public function delete(){
            if(!isset($_REQUEST['id'])){
                header('Location: index.php?controller=pages&action=error');
            }

            $product = Product::getByID($_REQUEST['id']);

            if(!isset($product)){
                header('Location: index.php?controller=pages&action=error');
            }
            // fix delete folder
            $targetDirectory  = './assets/images/';
            $imageDirectory = explode('/', $product->image)[3];


            if (is_dir($targetDirectory.$imageDirectory)) {
                $this->deleteDir($targetDirectory.$imageDirectory);
            }

            Product::delete($_REQUEST['id']);

            header('Location: index.php?controller=products');
        }

        private function deleteDir($dirPath){
            if(! is_dir($dirPath)){
                throw new InvalidArgumentException("$dirPath must be a directory");
            }
            if(substr($dirPath, strlen($dirPath) - 1, 1) != '/'){
                $dirPath.= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);

            foreach($files as $file){
                if(is_dir($file)){
                    $this->deleteDir($file);
                }else{
                    unlink($file);
                }
            }
            rmdir($dirPath);
        }
    }
?>