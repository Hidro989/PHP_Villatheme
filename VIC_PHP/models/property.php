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
    }

?>