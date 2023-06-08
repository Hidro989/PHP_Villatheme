<?php
    class Pagination{
        protected $_config = array(
            'current_page'  => 1, // Trang hiện tại
            'total_record'  => 1, // Tổng số record
            'total_page'    => 1, // Tổng số trang
            'limit'         => 10,// limit
            'start'         => 0, // start
            'link_full'     => '',// Link full có dạng như sau: domain/com/page/{page}
            'link_first'    => '',// Link trang đầu tiên
        );

        function init($config = array()) {

            foreach ($config as $key => $val) {
                if (isset($this->_config[$key])){
                    $this->_config[$key] = $val;
                }
            }

            if($this->_config['limit'] < 0){
                $this->_config['limit'] = 0;
            }

            $this->_config['total_page'] = ceil($this->_config['total_record'] / $this->_config['limit']);

            if(!$this->_config['total_page']){
                $this->_config['total_page'] = 1;
            }


            if($this->_config['current_page'] < 1){
                $this->_config['current_page'] = 1;
            }

            if($this->_config['current_page'] > $this->_config['total_page']){
                $this->_config['current_page'] = $this->_config['total_page'];
            }

            $this->_config['start'] = ($this->_config['current_page'] - 1) * $this->_config['limit'];           

        }

        private function __link($page)
        {   
            // old condition $page < 1 && $this->_config['link_first']
            if($page < 1 && $this->_config['link_first']){
                return $this->_config['link_first'];
            }

            return str_replace('{page}', $page, $this->_config['link_full']);
        }

        function html(){

            $p = '';

            if($this->_config['total_record'] > $this->_config['limit']){

                $p .= '<ul>';

                if($this->_config['current_page'] > 1){
                    $p .= '<li><div class="pagItem" data-url="'.$this->__link($this->_config['current_page']-1).'"><i class=\'bx bx-left-arrow-alt\'></i></div></li>';
                }
    
                for($i = 1; $i <= $this->_config['total_page']; $i++){
    
                    if($this->_config['current_page'] == $i){
                        $p .= '<li><span class="active">'.$i.'</span></li>';
                    }else{
                        $p .= '<li><div class="pagItem" data-url="'.$this->__link($i).'">'.$i.'</div></li>';
                    }
    
                }
    
                if($this->_config['current_page'] < $this->_config['total_page']){
                    $p .= '<li><div class="pagItem" data-url="'.$this->__link($this->_config['current_page'] + 1).'"><i class=\'bx bx-right-arrow-alt\'></i></div></li>';
                }
    
                $p .= '</ul>';
            }

            return $p;
            
        }
    }


?>