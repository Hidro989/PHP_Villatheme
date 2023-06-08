<div class="app" id="myApp">
    <h1>PHP1</h1>
    <header>
        <div class="top-bar">
            <div class="manage-bar">
                <div class="btn btn--primary" id="btnOpenAddProduct">Add product</div>
                <div class="btn" id="btnOpenAddProperty">Add property</div>
                <div class="btn" id="btnSynData">Sync from Villa Theme</div>
            </div>
            <div class="search-bar">
                <div>
                    <input id="search" type="search" name="search" placeholder="Search product..."
                        <?php 
                            echo 'value = "'.$filter['search'].'"';
                        ?>
                    >
                    <button type="submit" id="btnSearch"><i class='bx bx-search'></i></button>
                </div>
            </div>
        </div>
        <div class="sort-bar">
            <div>
                <input type="hidden"  name="page" class="form-input-control" value="<?=$filter['currentPage']?>">
                <div class="form-input">
                    <select name="orderBy" class="form-input-control" id="orderBy">
                        <?php
                            if(count($orderList) > 0){
                                foreach($orderList as $order){
                                    $select = $filter['orderBy'] == $order['key'] ? 'selected' : '';
                                    echo '<option value="'.$order['key'].'"'.$select.'>'.$order['value'].'</option>';
                                }
                            }
                        ?>
                        
                    </select>
                </div>
                <div class="form-input">
                    <select name="sort" class="form-input-control" id="sort">
                        <?php
                            if(count($sortList) > 0){
                                foreach($sortList as $sort){
                                    $select = $filter['sort'] == $sort ? 'selected' : '';
                                    echo '<option value="'.$sort.'" '.$select.'>'.$sort.'</option>';
                                }
                            }
                        ?>
                        
                    </select>
                </div>
                <div class="form-input">
                    <select name="selectedCategory" class="form-input-control" id="selectedCategory">
                        <option value="no">Select category</option>
                        <?php
                            if(count($categories) > 0){
                                foreach ($categories as $category) {
                                    $select = ($filter['selectedCategory'] == $category->id) ? 'selected' : '';
                                    echo '<option value="'.$category->id.'" '.$select.'>'.$category->name.'</option>';
                                    
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-input">
                    <select name="selectedTag" class="form-input-control" id="selectedTag">
                        <option value="no">Select tag</option>
                        <?php
                            if(count($tags) > 0){
                                foreach ($tags as $tag) {
                                    $select = $filter['selectedTag'] == $tag->id ? 'selected' : '';
                                    echo '<option value="'.$tag->id.'" '.$select.'>'.$tag->name.'</option>';
                                }
                            }
                        ?>
                    </select>
                </div>
                <div class="form-input">
                    <?php
                     echo '<input type="date" value="'.$filter['startDate'].'" name="startDate" class="form-input-control" id="startDate">';
                    ?>
                </div>
                <div class="form-input">
                <?php
                    echo '<input type="date" value="'.$filter['endDate'].'" name="endDate" class="form-input-control" id="endDate">';
                    ?>
                </div>
                
                <div class="form-input">
                    <input type="submit" value="Filter" id="btnFilter">
                </div>
            </div>
        </div>
    </header>
    <main>
        <table class="table">
            <tr>
                <th>Date</th>
                <th>Product name</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Feature Image</th>
                <th>Gallery</th>
                <th>Categories</th>
                <th>Tags</th>
                <th>Action</th>
            </tr>
            <?php
                if(count($list) > 0){
                    foreach($list as $item){
                        $galleryHtml = array();
                        if(!empty($item->gallery)){
                            $gallery = explode(',', $item->gallery);
                            foreach($gallery as $g){
                                $galleryHtml[] = '<img src="'. $g.'" alt="Gallery" height="30" widt="30">';
                            }
                        }

                        $imageHtml = '';
                        if(!empty($item->image)){
                            $imageHtml = '<img src="'. $item->image. '" alt="Feature Image" height="30" widt="30">';
                        }


                        echo '<tr>';
                        echo '<td>'. $item->createdDate. '</td>';
                        echo '<td>'. $item->name. '</td>';
                        echo '<td>'. $item->sku. '</td>';
                        echo '<td>$'. $item->price. '</td>';
                        echo '<td>'.$imageHtml.'</td>';


                        echo '<td>'. implode(' ',$galleryHtml). '</td>';
                        echo '<td>'. $item->categories. '</td>';
                        echo '<td>'. $item->tags.'</td>';
                        echo '<td>';
                        echo '<span class="editProduct" data-id="'.$item->id.'"><i class=\'bx bxs-edit\'></i></span>';
                        echo '<span class="deleteProduct" data-id="'.$item->id.'"><i class=\'bx bxs-trash-alt\'></i></span>';
                        echo '</td>';
                    }
                }
            ?>
            <!-- <tr>
                <td>06/04/2021</td>
                <td>Product 1</td>
                <td>sku1</td>
                <td>$100</td>
                <td></td>
                <td></td>
                <td>Category1, category2</td>
                <td>Tag1, tag2</td>
                <td>
                    <a href=""><i class='bx bxs-edit'></i></a>
                    <a href="">
                        <i class='bx bxs-trash-alt' ></i>
                    </a>
                </td>
            </tr> -->

            
        </table>

        <div class="pagination">
            <!-- <ul>
                <li><a href=""><i class='bx bx-left-arrow-alt'></i></a></li>
                <li><a href="">1</a></li>
                <li> <span class="active">2</span></li>
                <li><a href="">3</a></li>
                <li><a href=""><i class='bx bx-right-arrow-alt'></i></a></li>
            </ul> -->
            <?=$paging?>
        </div>
    </main>
</div>
<div class="modal" id="modal">
    <div class="overlay">
    </div>
    <div class="modal-body" id="modalContent">
         
        </div>
    </div>
</div>

<div class="modal" id="modalLoader">
    <div class="loader-overlay">
    </div>
    <div class="loader"></div>
</div>
