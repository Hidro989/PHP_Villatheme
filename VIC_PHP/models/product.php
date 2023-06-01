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

        function __construct($id, $name, $sku, $price, $image, $gallery, $createdDate, $categories = '', $tags = '') 
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

        }

        static function getAll() {
            $db = DB::getInstance();

            $filter = self::getValueForFilter();

            $sql = self::createCountQuery($filter);
            
            $total_record = self::getTotalRecord($sql);

            $linkfull = self::createLinkFull($filter);
            $config = array(
                'current_page'  => isset($_GET['page']) ? $_GET['page'] : 1, // Trang hiện tại
                'total_record'  => $total_record, // Tổng số record
                'limit'         => 2,// limit
                'link_full'     =>  $linkfull,// Link full có dạng như sau: domain/com/page/{page}
                'link_first'    => 'index.php?controller=products',// Link trang đầu tiên
            );

            $paging = new Pagination();
            $paging->init($config);

            $sql = self::createSelectQuery($filter);
            $list = [];
            $offset = ($config['current_page'] - 1) * $config['limit'];
            $perPage = $config['limit'];

            $sql .= " LIMIT $offset , $perPage";

            
            $req = $db->query($sql);

            if($req->num_rows > 0){

                while($item = $req->fetch_assoc()) {
                    $list[] = new Product($item['ProductID'], $item['Title'], $item['SKU'], $item['Price'], $item['FeaturedImage'], $item['Gallery'], $item['CreatedDate']);
                }

            }

            $list = self::addPropertyForProduct($list);
            
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
            );
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

        private static function createCountQuery($filter){
            $sql = "SELECT COUNT(*) AS total FROM products";

            if(!empty($filter['selectedCategory']) && !empty($filter['selectedTag'])){

                $sql = 'SELECT COUNT(*) as total
                FROM (SELECT products.ProductID, COUNT(*) AS tt 
                FROM products INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID
                WHERE categorydetail.CategoryID = '.$filter['selectedCategory'].' OR categorydetail.CategoryID = '.$filter['selectedTag'];
                
                if(!empty($filter['search'])){
                    $sql.= ' AND products.Title LIKE \'%'.$filter['search'].'%\'';
                }

                if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                    $sql.= " AND products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
                }
                
                $sql.= ' GROUP BY products.ProductID HAVING tt > 1)
                AS subquery;';
            }
            else if(!empty($filter['selectedCategory']) && empty($filter['selectedTag'])){

                $sql .= " INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"
                        ."WHERE categorydetail.CategoryID = ".$filter['selectedCategory'];

                if(!empty($filter['search'])){
                    $sql.= ' AND products.Title LIKE \'%'.$filter['search'].'%\'';
                }

                if(!empty($filter['startDate']) && !empty($filter['endDate']) && ($filter['startDate'] <= $filter['endDate'])){
                    $sql.= " AND products.CreatedDate BETWEEN '".$filter['startDate']."' AND '".$filter['endDate']."'";
                }

            }else if(!empty($filter['selectedTag']) && empty($filter['selectedCategory'])){

                $sql .= " INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"
                        ."WHERE categorydetail.CategoryID = ".$filter['selectedTag'];
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

        private static function createSelectQuery($filter){
            $sql = "SELECT * FROM products";

            if(!empty($filter['selectedCategory']) && !empty($filter['selectedTag'])){
                $sql .= " INNER JOIN categorydetail ON categorydetail.ProductID = products.ProductID\n"
                ." INNER JOIN category ON category.CategoryID = categorydetail.CategoryID\n"
                ." WHERE (categorydetail.CategoryID = ".$filter['selectedCategory']." OR categorydetail.CategoryID = ".$filter['selectedTag'].")\n";
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

            if(!empty($filter['orderBy'])){
                $sql .= " ORDER BY " . $filter['orderBy'];
            }
            
            if(!empty($filter['sort'])){
                $sql .= ' '. $filter['sort'];
            }

            return $sql;
        }

        private static function getValueForFilter(){            $filter = array(
                'currentPage'  =>  1,
                'search' => isset($_GET['search']) ? trim($_GET['search']) : null,
                'orderBy' => isset($_GET['orderBy']) ? $_GET['orderBy'] : 'CreatedDate',
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

        private static function getTotalRecord($sql){
            $db = DB::getInstance();
            $result = $db->query($sql);
            $total_record = 0;
            if($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $total_record = $row['total'];
            }

            return $total_record;
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
            $sql = "INSERT INTO products (SKU, Title, Price, FeaturedImage, Gallery) VALUES(?,?,?,?,?);";
            $req = $db->prepare($sql);
            $req->bind_param('sssss', $product->sku, $product->name, $product->price, $product->image, $product->gallery);
            $req->execute();
        }

        static function addPropertyForNewestProduct($properties){
            $id = self::getLastID();
            self::addCategoryDetail($id, $properties);
        }

        static function getLastID(){
            $db = Db::getInstance();
            $sql = "SELECT ProductId FROM products ORDER BY ProductId DESC LIMIT 1";
            $req = $db->query($sql);
            $id = 0;

            if($req->num_rows > 0){
                $row = $req->fetch_assoc();
                $id = $row['ProductId'];
            }

            return $id;
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
            $sql = "UPDATE products SET SKU = ?, Title = ?, Price = ?, FeaturedImage = ?, Gallery = ? WHERE ProductID = ?";
            $req = $db->prepare($sql);
            $req->bind_param('ssssss', $product->sku, $product->name, $product->price, $product->image, $product->gallery, $product->id);
            $req->execute();
        }

        static function updateProperty($id, $properties){
            $db = Db::getInstance();
            $sql = "DELETE FROM categorydetail WHERE ProductID = $id";
            $req = $db->prepare($sql);
            $req->execute();

            self::addCategoryDetail($id, $properties);
        }

        private static function addCategoryDetail($id, $properties){
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
    }


?>