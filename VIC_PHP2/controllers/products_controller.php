<?php
    require_once('controllers/base_controller.php');
    require_once('models/product.php');
    require_once('models/pagination.php');
    require_once('models/property.php');
    
    class ProductsController extends BaseController{
        private $indexScript = '<script src="./assets/javascripts/index.js"></script>';
        public function __construct()
        {   
            $this->folder = 'products';
        }

        public function index(){
            $data = Product::getAll();
            $this->render('index', "Products", $data, $this->indexScript);
        }

        public function create(){
            $data = array(
                'categories' => Property::getCategories(),
                'tags' => Property::getTags()
            );
            $this->renderJson($data);
        }

        public function stored(){            
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                header('Location: index.php?controller=pages&action=error');
            }
            $request = $_REQUEST;

            if(empty($request['sku'] || $request['name'])){
                array_push($this->errors, "Please enter this field");
            }
            

            if(!empty($request['price'])){
                if(filter_var($request['price'], FILTER_VALIDATE_FLOAT) == false) {
                    array_push($this->errors, "Please enter number for field price");
                }
            }


            if(count($this->errors) > 0){
                $this->renderJson(array('errors' => $this->errors));
                return;
            }


            // Get Value from client
            $name = trim($request['name']);
            $sku = trim($request['sku']);
            $price = trim($request['price']) == '' ? 0 : trim($request['price']);

            // Create floder for product
            $targetDirectory  = "./assets/images/";
            $product = new Product(0, $name, $sku, $price, '', '', null);
            $id = Product::insert($product);

            mkdir($targetDirectory.'product'.$id);
            $targetDirectory = $targetDirectory.'product'.$id.'/';
            mkdir($targetDirectory.'image');
            mkdir($targetDirectory.'gallery');
            $imageFilePath = '';
            if(!empty($_FILES['image']['name'])){
                $imageDirectory = $targetDirectory.'image/';
                // Save Feature Image
                $imageFileName = $_FILES['image']['name'];
                $imageFilePath = $imageDirectory . basename($imageFileName);
                move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath);
            }
            
            
            $gallery = '';
            if(!empty($_FILES['gallery']['name'][0])){
                // Save Gallery 
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
            }


            $product = new Product($id, $name, $sku, $price, $imageFilePath, $gallery, null);


            $success = 'Insert product successfully';
            try{
                Product::update($product);
                // Add Category/Tag for newest product
                if($request['tags'] != null && $request['categories'] !=  null){
                    Product::addCategoryDetail($product->id, array_merge($request['categories'], $request['tags']));
                }
                
            }catch(Exception $e){
                $success = $e->getMessage();
            }

            
            $this->renderJson(array('success' => $success));
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

            $this->renderJson($data);
        }

        public function update(){
            if($_SERVER['REQUEST_METHOD'] == 'GET'){
                header('Location: index.php?controller=pages&action=error');
            }

            $request = $_REQUEST;
             if(empty($request['sku'] || $request['name'])){
                array_push($this->errors, "Please enter this field");
            }
            

            if(!empty($request['price'])){
                if(filter_var($request['price'], FILTER_VALIDATE_FLOAT) == false) {
                    array_push($this->errors, "Please enter number for field price");
                }
            }


            if(count($this->errors) > 0){
                $this->renderJson(array('errors' => $this->errors));
                return;
            }

            $targetDirectory  = './assets/images/';

            $product = Product::getByID($request['id']);
            $targetDirectory .= "product$product->id";
            

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
            $success = 'Update product successfully';
            try{
                Product::update($product);
                if($request['tags'] != null && $request['categories'] !=  null){
                    Product::updateProperty($request['id'], array_merge($request['categories'], $request['tags']));
                }
            }catch(Exception $e){
                $success = $e->getMessage();
            }
            $this->renderJson(array('success' => $success));

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

            $success = 'Delete product successfully';
            try{
                Product::delete($_REQUEST['id']);
            }catch(Exception $e){
                $success = $e->getMessage();
            }

            $this->renderJson(array('success' => $success));

        }

        public function syndata(){
            $url = 'https://villatheme.com/extensions/';
            $html = file_get_contents($url);
            $pattern = '/<h2 class="woocommerce-loop-product__title"><a href="(.+)">(.+)<\/a><\/h2>/i';
            $j = 0;
            $count = 0;

            preg_match_all($pattern, $html, $urlmatches);

            //$totalRecords = count($urlmatches[1]);
            $this->renderJson(array('records' => $urlmatches[1]));
            
        }

        public function addSyndata(){
            $urls = $_REQUEST['urls'];
            // $this->pr($urls);
            foreach ($urls as $url) {
                $item = $this->getDatafromVilla($url);
                // $this->pr($item);
                // die;
                if(Product::checkProductExists($item['name']) == true) {
                    Product::insertByArray($item);
                }else{
                    Product::updateByArray($item);
                }
            }
        }

        private function getDatafromVilla($url){
            $html = file_get_contents($url);
            $patterns = array(
                'name' => '/<h1 class="product_title entry-title">(.*?)<\/h1>/',
                'price' => '/<p class="price"><del aria-hidden="true">.*?<bdi><span class="woocommerce-Price-currencySymbol">&#36;<\/span>(.*?)<\/bdi>.*?<\/del>.*?<\/p>/s',
                'salePrice' => '/<p class="price">.*?<ins>.*?<bdi><span class="woocommerce-Price-currencySymbol">&#36;<\/span>(.*?)<\/bdi>.*?<\/ins><\/p>/s',
                'sku' => '/<span class="sku">(.*?)<\/span>/',
                'featureImage' => '/<figure .*?>.*?<a href="(.*?)">.*?<\/a>.*?<\/figure>/s',
                'gallery' => '/<div[^>]*>\s*<a[^>]+>\s*<img[^>]+src="([^">]+)/i',
                'category' => '/<a href="https:\/\/villatheme.com\/product-cate\/.*?\/" rel="tag">(.*?)<\/a>/',
                'tag' => '/<a href="https:\/\/villatheme.com\/product-tag\/.*?\/" rel="tag">(.*?)<\/a>/s',
                'description' => '/<div class="panel entry-content" .*?>(.*?)<\/div>/sm'
            );

            $info = array();
            foreach($patterns as $key => $pattern){
                
                if($key == 'tag' || $key == 'category'){
                    $property = array();
                    preg_match('/<div class="product_meta">(.*?)<\/div>/s', $html, $metaMathces);
                    preg_match_all($pattern, $metaMathces[1], $tagMathces);
                    
                    foreach($tagMathces[1] as $tag){
                        array_push($property, $tag);
                    }

                    $info[$key] = $property;

                }else if($key == 'description')
                {
                    preg_match($pattern, $html, $matches);
                    $info['description'] = htmlspecialchars($matches[1] ?? '');
                }
                else if($key == 'gallery'){
                    $property = array();
                    preg_match_all($pattern, $html, $galleryMatches);

                    foreach($galleryMatches[1] as $gallery){
                        array_push($property, $gallery);
                    }

                    $info[$key] = $property;

                }else{
                    preg_match($pattern, $html, $matches);
                    $info[$key] = $matches[1] ?? '';
                }
                
            }

            return $info;

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

        private function pr($data)
        {
            echo "<pre>";
            print_r($data); // or var_dump($data);
            echo "</pre>";
        }

    }
?>