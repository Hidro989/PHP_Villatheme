<?php
    class Product{
        public $id;
        public $name;
        public $sku;
        public $price;
        public $image;
        public $gallery;
        public $categories;
        public $tags;
        public $createdDate;
        public $salePrice;
        public $description;

        function __construct($id, $name, $sku, $price, $image, $gallery, $createdDate, $salePrice = 0,$description ='' , $categories = '', $tags = '') 
        {
            $this->id = $id;
            $this->name = $name;
            $this->sku = $sku;
            $this->price = $price;
            $this->image = $image;
            $this->gallery = $gallery;
            $this->categories = $categories;
            $this->tags = $tags;
            $this->createdDate = $createdDate;
            $this->salePrice = $salePrice;
            $this->description = $description;

        }

        static function getAll() {
            $db = DB::getInstance();

            $filter = self::getValueForFilter();

            $orderList = [
                ['key'=> 'createdDate', 'value'=> 'Date'], 
                ['key'=>'name', 'value' => 'Product Name'], 
                ['key' => 'price', 'value' =>'Price']];
            $sortList = ['ASC', 'DESC'];
            $list = [];
            $sql = self::createSelectQuery($filter);
            $req = $db->query($sql);

            if($req->num_rows > 0){

                while($item = $req->fetch_assoc()) {
                    $list[] = new Product($item['ProductID'], $item['Title'], $item['SKU'], $item['Price'], $item['FeaturedImage'], $item['Gallery'], $item['CreatedDate']);
                }

            }

            $list = self::addPropertyForProduct($list);

            $total_record = count($list);

            $linkfull = self::createLinkFull($filter);

            $config = array(
                'current_page'  => isset($_GET['page']) ? $_GET['page'] : 1, // Trang hiện tại
                'total_record'  => $total_record, // Tổng số record
                'limit'         => 3,// limit
                'link_full'     =>  $linkfull,// Link full có dạng như sau: domain/com/page/{page}
                'link_first'    => 'index.php?controller=products',// Link trang đầu tiên
            );

            $paging = new Pagination();
            $paging->init($config);

            
            $offset = ($config['current_page'] - 1) * $config['limit'];
            $perPage = $config['limit'];

            $list = self::quickSort($list, $filter['orderBy'], $filter['sort']);
            $list = self::getLimitList($list, $offset, $perPage);

            // Get list of category
            $categories = Property::getCategories();         
            // Get list of tag        
            $tags = Property::getTags();

            return array(
                'list' => $list,
                'paging' => $paging->html(),
                'categories' => $categories,
                'tags' => $tags,
                'filter' => $filter,
                'orderList' => $orderList,
                'sortList' => $sortList
            );
        }

        private static function getLimitList($list , $offset, $perPage ){
            $limitList = array();
            $i = $offset;
            $j = 0;
            while(($j < $perPage) && (count($list) > $i)){
                array_push($limitList, $list[$i]);
                $i++;
                $j++;
            }

            return $limitList;
        }


        private static function quickSort($array, $orderBy, $sort) {
            $length = count($array);
            

            if ($length <= 1) {
                return $array;
            }
            

            $pivot = $array[floor($length / 2)];
            
            $left = $right = array();
            $equal = array();
            

            for ($i = 0; $i < $length; $i++) {
                if($sort == 'ASC'){
                    if ($array[$i]->$orderBy < $pivot->$orderBy) {
                        $left[] = $array[$i];
                    } elseif ($array[$i]->$orderBy > $pivot->$orderBy) {
                        $right[] = $array[$i];
                    }else{
                        $equal[] = $array[$i];
                    }
                }else{
                    if ($array[$i]->$orderBy > $pivot->$orderBy) {
                        $left[] = $array[$i];
                    } elseif ($array[$i]->$orderBy < $pivot->$orderBy) {
                        $right[] = $array[$i];
                    }else{
                        $equal[] = $array[$i];
                    }
                }
            }
            

            return array_merge(self::quickSort($left, $orderBy, $sort), $equal, self::quickSort($right, $orderBy, $sort));
        }

        private static function createLinkFull($filter){
            $link = 'index.php?controller=products&page={page}';
            unset($filter['currentPage']);
            foreach($filter as $key => $value){
                if($key == 'search'){
                    $str = str_replace(' ', '+', $value);
                    $link .= '&search='.$str;
                }else{
                    if(!empty($value)){
                        $link .= '&'.$key.'='.$value;
                    }
                }
            }
            return $link;
        }

        private static function createSelectQuery($filter){
            $sql = "SELECT * FROM products";

            if(!empty($filter['selectedCategory']) && !empty($filter['selectedTag'])){
                $sql .= " INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"
                ." INNER JOIN category ON category.CategoryID = categorydetail.CategoryID\n"
                ." WHERE (categorydetail.CategoryID IN (".$filter['selectedCategory'].",".$filter['selectedTag']."))\n";
                if(!empty($filter['search'])){
                    $sql.= ' AND products.Title LIKE \'%'.$filter['search'].'%\'';
                }

                if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                    $sql.= " AND products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
                }

                $sql.=" GROUP BY products.ProductID ";
            }
            else if(!empty($filter['selectedCategory']) && empty($filter['selectedTag'])){
                $sql .= " INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"
                ." INNER JOIN category ON category.CategoryID = categorydetail.CategoryID\n"
                ." WHERE category.CategoryID = ".$filter['selectedCategory'];

                if(!empty($filter['search'])){
                    $sql.= ' AND products.Title LIKE \'%'.$filter['search'].'%\'';
                }

                if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                    $sql.= " AND products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
                }

            }else if(!empty($filter['selectedTag']) && empty($filter['selectedCategory'])){
                $sql .= " INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"
                ." INNER JOIN category ON category.CategoryID = categorydetail.CategoryID\n"
                ." WHERE category.CategoryID = ".$filter['selectedTag'];

                if(!empty($filter['search'])){
                    $sql.= ' AND products.Title LIKE \'%'.$filter['search'].'%\'';
                }

                if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                    $sql.= " AND products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
                }
            }else if(!empty($filter['search'])){
                $sql.= ' WHERE products.Title LIKE \'%'.$filter['search'].'%\'';
                if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                    $sql.= " AND products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
                }
            }else if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                $sql.= " WHERE products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
            }

            return $sql;
        }

        private static function getValueForFilter(){            
            $filter = array(
                'currentPage'  =>  1,
                'search' => isset($_GET['search']) ? trim($_GET['search']) : null,
                'orderBy' => isset($_GET['orderBy']) ? $_GET['orderBy'] : 'createdDate',
                'sort' => isset($_GET['sort']) ? $_GET['sort'] : 'ASC',
                'selectedCategory' => '',
                'selectedTag' => '',
                'startDate' => isset($_GET['startDate']) ? $_GET['startDate'] : '',
                'endDate' => isset($_GET['endDate']) ? $_GET['endDate'] : '',
            );

            if(isset($_GET['selectedCategory']) && $_GET['selectedCategory'] != 'no'){
                $filter['selectedCategory'] = $_GET['selectedCategory'];
            }

            if(isset($_GET['selectedTag']) && $_GET['selectedTag'] != 'no'){
                $filter['selectedTag'] = $_GET['selectedTag'];
            }
            return $filter;
        }

        private static function addPropertyForProduct($list){
            $db = DB::getInstance();
            for($i = 0; $i < count($list); $i++){
                $item = $list[$i];
                $sql = "SELECT products.ProductID, category.Name, category.Type FROM products\n"

                . "INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"

                . "INNER JOIN category ON category.CategoryID = categorydetail.CategoryID\n"

                . "WHERE products.ProductID = $item->id;";
                $req = $db->query($sql);
                
                $categories = array();
                $tags = array();

                if($req->num_rows > 0){
                    while($row = $req->fetch_assoc()) {
                        if($row['Type'] == 'category'){
                            $categories[] = $row['Name'];
                        }else{
                            $tags[] = $row['Name'];
                        }
                    }
                }

                $item->categories = implode(",", $categories);
                $item->tags = implode(",", $tags);
            }

             return $list;
        } 

        static function insert($product){
            $db = Db::getInstance();
            $sql = "INSERT INTO products (SKU, Title, Price, SalePrice, FeaturedImage, Gallery, Description) VALUES(?,?,?,?,?,?,?);";
            $req = $db->prepare($sql);
            $req->bind_param('ssddsss', $product->sku, $product->name, $product->price, $product->salePrice, $product->image, $product->gallery, $product->description);
            $req->execute();
            return $req->insert_id;
        }

        static function getByID($id){
            $db = Db::getInstance();
            $sql = "SELECT * FROM products WHERE ProductID = $id";
            $req = $db->query($sql);

            $product = null;
            if($req->num_rows > 0){
                $item = $req->fetch_assoc();
                $product = new Product($item['ProductID'], $item['Title'], $item['SKU'], $item['Price'], $item['FeaturedImage'], $item['Gallery'], $item['CreatedDate']);
            }

            $list = self::addPropertyForProduct(array($product));

            return $list[0];
        }

        static function update($product){
            $db = Db::getInstance();
            $sql = "UPDATE products SET SKU = ?, Title = ?, Price = ?, SalePrice = ?, FeaturedImage = ?, Gallery = ?, Description = ? WHERE ProductID = ?";
            $req = $db->prepare($sql);
            $req->bind_param('ssddssss', $product->sku, $product->name, $product->price, $product->salePrice, $product->image, $product->gallery, $product->description, $product->id);
            $req->execute();
        }

        static function updateProperty($id, $properties){
            $db = Db::getInstance();
            $sql = "DELETE FROM categorydetail WHERE ProductID = $id";
            $req = $db->prepare($sql);
            $req->execute();

            self::addCategoryDetail($id, $properties);
        }

        static function addCategoryDetail($id, $properties){
            $db = Db::getInstance();
            foreach($properties as $categoryID){
                $sql = "INSERT INTO categorydetail VALUES(?,?)";
                $req = $db->prepare($sql);
                $req->bind_param('ss', $id, $categoryID);
                $req->execute();
            }
        }

        static function delete($id){
            $db = Db::getInstance();

            $sql = "DELETE FROM categorydetail WHERE ProductID = $id";
            $req = $db->prepare($sql);
            $req->execute();

            $sql = "DELETE FROM products WHERE ProductID = $id";
            $req = $db->prepare($sql);
            $req->execute();

            
        }

        static function checkProductExists($name){
            $db = Db::getInstance();
            $sql = "SELECT COUNT(*) AS result FROM products WHERE Title = '$name'";
            $req = $db->query($sql);
            $result = 0;
            if($req->num_rows > 0){
                $row = $req->fetch_assoc();
                $result = $row['result'];
            }
            return $result > 0 ? false : true;
        }

        static function getIdByName($name){
            $db = Db::getInstance();
            $sql = "SELECT ProductID FROM products WHERE Title = '$name'";
            $req = $db->query($sql);
            $result = 0;
            if ($req->num_rows > 0){
                $row = $req->fetch_assoc();
                $result = $row['ProductID'];
            }
            return $result;
        }


        static function insertByArray($item){
            $targetDirectory  = "./assets/images/";
            $product = new Product(0, $item['name'], $item['sku'], floatval($item['price']), '', '', null, floatval($item['salePrice']), $item['description']);
            $id = Product::insert($product);

            mkdir($targetDirectory.'product'.$id);
            $targetDirectory = $targetDirectory.'product'.$id.'/';


            // Thêm Feature Image vào vào thư mục
            mkdir($targetDirectory.'image');
            $imageDirectory = $targetDirectory.'image/';
            $imageFeature = self::downloadImageToFolder($imageDirectory, $item['featureImage']);
            
            // Add Gallery to folder
            mkdir($targetDirectory.'gallery');
            $gallerydirectory = $targetDirectory.'gallery/';
            
            $gallery = self::downloadGalleryToFolder($gallerydirectory, $item['gallery']);

            // Handle tag/category

            $tags = self::updateTagVsCategory('tag', $item['tag']);
            $categories = self::updateTagVsCategory('category', $item['category']);
            
            $product = new Product($id, $item['name'], $item['sku'], floatval($item['price']), $imageFeature, $gallery, null, floatval($item['salePrice']), $item['description']);
            Product::update($product);
            Product::addCategoryDetail($product->id, array_merge($tags, $categories));
            
        }

        static function updateByArray($item){
            $id = self::getIdByName($item['name']);
            $product = self::getByID($id);
            $productGallery = explode(',', $product->gallery);

            $targetDirectory  = "./assets/images/";
            $targetDirectory = $targetDirectory.'product'.$id.'/';

            $imageDirectory = $targetDirectory.'image/';
            $gallerydirectory = $targetDirectory.'gallery/';

        
            // Handle featureImage
            if(file_exists($imageDirectory.basename($item['featureImage'])) == false){
                $imageFile = self::downloadImageToFolder($imageDirectory, $item['featureImage']);
                unlink($product->image);
                $product->image = $imageFile;
            }

            // Handle Gallery

            $listGalleryOld = array();
            foreach ($productGallery as $value){
                $listGalleryOld[basename($value)] = $value;
            }
            

            $listGalleryNew = array();
            foreach($item['gallery'] as $newGallery) {
                array_push($listGalleryNew, basename($newGallery));
            }
            

            foreach($listGalleryOld as $key => $value) {
                if(in_array($key, $listGalleryNew) == false){
                    unlink($value);
                    if(($index = array_search($value, $productGallery)) !== false){
                        unset($productGallery[$index]);
                    }
                }
            }
            
            foreach($item['gallery'] as $gallery){
                if(file_exists($gallerydirectory.basename($gallery)) == false){
                    $productGallery[] = self::downloadImageToFolder($gallerydirectory, $gallery);
                }
            }

            $tags = self::updateTagVsCategory('tag', $item['tag']);
            $categories = self::updateTagVsCategory('category', $item['category']);


            $product->gallery = implode(',', $productGallery);
            $product->sku = $item['sku'];
            $product->price = floatval($item['price']);
            $product->salePrice = floatval($item['salePrice']);
            $product->description = $item['description'];
            self::update($product);
            self::updateProperty($product->id, array_merge($tags, $categories));
            
        }

        private static function downloadImageToFolder($targetDirectory, $url){
            $imageFile = $targetDirectory.basename($url);
            $imageData = file_get_contents($url);
            file_put_contents($imageFile, $imageData);
            return $imageFile;
        }

        private static function downloadGalleryToFolder($targetDirectory, $urls){
            $galleryPaths = [];
            foreach($urls as $url){
                array_push($galleryPaths, self::downloadImageToFolder($targetDirectory, $url));
            }

            return implode(',', $galleryPaths);
        }

        private static function updateTagVsCategory($type, $properties){
            $listId = array();
            foreach($properties as $propertyName){
                $rs = Property::checkPropertyExists($propertyName);
                if($rs == false){
                    $property = new Property(0, $propertyName, $type);
                    $listId[] = Property::insert($property);
                }else{
                    $listId[] = Property::getPropertyByName($propertyName);
                }
            }
            return $listId;
        }



    }
