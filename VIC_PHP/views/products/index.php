<div class="app">
    <h1>PHP1</h1>
    <header>
        <div class="top-bar">
            <div class="manage-bar">
                <a class="btn btn--primary" href="index.php?controller=products&action=create">Add product</a>
                <a class="btn" href="index.php?controller=properties&action=create">Add property</a>
                <a class="btn" href="">Sync from Villa Theme</a>
            </div>
            <div class="search-bar">
                <form action="index.php" method="GET">
                    <input type="hidden" name="controller" value="products">
                    <input type="hidden" name="action" value="index">
                    <input type="hidden" name="page" value="<?=$filter['currentPage']?>">
                    <input type="hidden" name="orderBy" value="<?=$filter['orderBy']?>">
                    <input type="hidden" name="sort" value="<?=$filter['sort']?>">
                    <input type="hidden" name="selectedCategory" value="<?=$filter['selectedCategory']?>">
                    <input type="hidden" name="selectedTag" value="<?=$filter['selectedTag']?>">
                    <input type="hidden" name="startDate" value="<?=$filter['startDate']?>">
                    <input type="hidden" name="endDate" value="<?=$filter['endDate']?>">
                    
                    <input id="search" type="search" name="search" placeholder="Search product..."
                        <?php 
                            if(!empty($filter['search'])){
                                echo 'value = "'.$filter['search'].'"';
                            }
                        ?>
                    >
                    <button type="submit" id="btnSearch"><i class='bx bx-search'></i></button>
                </form>
            </div>
        </div>
        <div class="sort-bar">
            <form action="index.php" method="GET">
                <input type="hidden" name="controller" value="products">
                <input type="hidden" name="action" value="index">
                <input type="hidden" name="page" value="<?=$filter['currentPage']?>">
                <input type="hidden" name="search" value="<?=$filter['search']?>">
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
            </form>
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

                        $gallery = explode(',', $item->gallery);
                        $galleryHtml = array();
                        foreach($gallery as $g){
                            $galleryHtml[] = '<img src="'. $g.'" alt="Gallery" height="30" widt="30">';
                        }
                        echo '<tr>';
                        echo '<td>'. $item->createdDate. '</td>';
                        echo '<td>'. $item->name. '</td>';
                        echo '<td>'. $item->sku. '</td>';
                        echo '<td>$'. $item->price. '</td>';
                        echo '<td><img src="'. $item->image. '" alt="Feature Image" height="30" widt="30"></td>';


                        echo '<td>'. implode(' ',$galleryHtml). '</td>';
                        echo '<td>'. $item->categories. '</td>';
                        echo '<td>'. $item->tags.'</td>';
                        echo '<td>';
                        echo '<a href="index.php?controller=products&action=edit&id='.$item->id.'"><i class=\'bx bxs-edit\'></i></a>';
                        echo '<a href="index.php?controller=products&action=delete&id='.$item->id.'" onclick="return confirm(\'Do you want to delete?\')"><i class=\'bx bxs-trash-alt\'></i></a>';
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