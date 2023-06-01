<div class="app">
    <h1>Add Product</h1>
    <div class="wrap-form">
        <?php
            if(count($errors) > 0){
                foreach($errors as $er){
                    echo "<p style='color:red;'>$er</p>";
                }
            }
        ?>
        <form action="index.php?controller=products&action=stored" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="controller" value="products">
            <input type="hidden" name="action" value="stored">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="sku">SKU:</label>
                <input type="text" class="form-control" id="sku" name="sku" placeholder="Enter SKU">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" min="0" id="price" name="price" placeholder="Enter price">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control" id="image" name="image">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="gallery">Gallery:</label>
                <input type="file" class="form-control" id="gallery" name="gallery[]" multiple>
                <p class="error"></p>
            </div>
            <div class="form-group">
                    <label for="categories">Categories:</label>
                    <select name="categories[]" id="categories" class="form-control" multiple>
                        <?php
                            if(count($categories) > 0){
                                foreach ($categories as $category) {
                                    echo "<option value='$category->id'>$category->name</option>";
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
                                foreach ($tags as $tag) {
                                    echo "<option value='$tag->id'>$tag->name</option>";
                                }
                            }
                    ?>
                    </select>
                    <p class="error"></p>
                </div>

            <div class="form-group">
                <button type="submit" class="btn btn--primary" id="btnAdd">Submit</button>
            </div>
        </form>
         
    </div>
    <a class="btn btn--back" href="index.php?controller=products">Back to list</a>
</div>