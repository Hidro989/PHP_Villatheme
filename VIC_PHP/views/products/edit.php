<div class="app">
    <h1>Edit Product</h1>
    <div class="wrap-form">
        <?php
            if(count($errors) > 0){
                foreach($errors as $er){
                    echo "<p style='color:red;'>$er</p>";
                }
            }
        ?>
        <form action="index.php?controller=products&action=update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="controller" value="products">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?=$product->id?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="<?=$product->name?>">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="sku">SKU:</label>
                <input type="text" class="form-control" id="sku" name="sku" placeholder="Enter SKU" value="<?=$product->sku?>">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" min="0" id="price" name="price" placeholder="Enter price" value="<?=$product->price?>">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control" id="image" name="image">
                <?php
                   echo '<img src="'.$product->image.'" alt="Feature Image" height="40" width="40">'; 
                ?>
            
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="gallery">Gallery:</label>
                <input type="file" class="form-control" id="gallery" name="gallery[]" multiple>
                <?php
                    $galleries = explode(",", $product->gallery);
                    foreach($galleries as $gallery){
                        echo '<img src="'.$gallery.'" alt="Gallery" height="40" width="40">'; 
                    }
                ?>
                <p class="error"></p>
            </div>
            <div class="form-group">
                    <label for="categories">Categories:</label>
                    <select name="categories[]" id="categories" class="form-control" multiple>
                        <?php
                            if(count($categories) > 0){
                                $productCategory = explode(',',$product->categories);
                                foreach ($categories as $category) {
                                    if(in_array($category->name, $productCategory)){
                                        echo "<option value='$category->id' selected>$category->name</option>";
                                    }else{
                                        echo "<option value='$category->id'>$category->name</option>";
                                    }
                                    
                                }
                            }
                        ?>
                    </select>
                    <p class="error"></p>
                </div>
                <div class="form-group">
                    <label for="tags">Tags:</label>
                    <select name="tags[]" id="tags" class="form-control" multiple>
                    <?php
                            if(count($tags) > 0){
                                $productTag = explode(',',$product->tags);
                                foreach ($tags as $tag) {
                                    if(in_array($tag->name, $productTag)){
                                        echo "<option value='$tag->id' selected>$tag->name</option>";
                                    }else{
                                        echo "<option value='$tag->id'>$tag->name</option>";
                                    }
                                    
                                }
                            }
                    ?>
                    </select>
                    <p class="error"></p>
                </div>

            <div class="form-group">
                <button type="submit" class="btn btn--primary" id="btnEdit" >Submit</button>
            </div>
        </form>
         
    </div>
    <a class="btn btn--back" href="index.php?controller=products">Back to list</a>
</div>