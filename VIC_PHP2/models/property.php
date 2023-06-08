<?php
    class Property{
        public $id;
        public $name;
        public $type;

        function __construct($id, $name, $type){
            $this->id = $id;
            $this->name = $name;
            $this->type = $type;
        }

        static function insert($property){
            $db = DB::getInstance();
            $req = $db->prepare('INSERT INTO category (name, type) VALUES(?,?)');
            $req->bind_param("ss", $property->name, $property->type);
            $req->execute();
            return $req->insert_id;
        }

        static function getCategories(){
            $db = DB::getInstance();
            $sql = "SELECT * FROM category WHERE Type = 'category'";
            $req = $db->query($sql);
            $list = array();
            if($req->num_rows > 0){
                while($item = $req->fetch_assoc()) {
                    $list[] = new Property($item['CategoryID'], $item['Name'], $item['Type']);
                }
            }
            return $list;
        }

        static function getTags(){
            $db = DB::getInstance();
            $sql = "SELECT * FROM category WHERE Type = 'tag'";
            $req = $db->query($sql);
            $list = array();
            if($req->num_rows > 0){
                while($item = $req->fetch_assoc()) {
                    $list[] = new Property($item['CategoryID'], $item['Name'], $item['Type']);
                }
            }
            return $list;
        }

        static function checkPropertyExists($propertyName){
            $db = DB::getInstance();
            $sql = "SELECT COUNT(*) AS result FROM category WHERE Name = '$propertyName'";
            $req = $db->query($sql);

            $result = 0;
            if($req->num_rows > 0){
                $row = $req->fetch_assoc();
                $result = $row['result'];
            }
            return $result > 0 ? true : false;
        }

        static function getPropertyByName($propertyName){
            $db = DB::getInstance();
            $sql = "SELECT CategoryID FROM category WHERE Name = '$propertyName'";
            $req = $db->query($sql);

            $result = -1;
            if($req->num_rows > 0){
                $row = $req->fetch_assoc();
                $result = $row['CategoryID'];
            }

            return $result;
        }
    }

?>