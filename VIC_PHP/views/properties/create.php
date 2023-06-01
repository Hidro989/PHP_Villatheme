<div class="app">
    <h1>Add Property</h1>
    <div class="wrap-form">
        <?php
            if(count($errors) > 0){
                foreach($errors as $er){
                    echo "<p style='color:red;'>$er</p>";
                }
            }
        ?>
        <form action="index.php?controller=properties&action=stored" method="post">
            <input type="hidden" name="controller" value="properties">
            <input type="hidden" name="action" value="stored">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name">
                <p class="error"></p>
            </div>
            <div class="form-group">
                    <label for="type">Type:</label>
                    <select name="type" id="type" class="form-control">
                        <option value="category">Category</option>
                        <option value="tag">Tag</option>
                    </select>
                    <p class="error"></p>
            </div>
               
            <div class="form-group">
                <button type="submit" class="btn btn--primary" id="btnSubmit">Submit</button>
            </div>
        </form>
         
    </div>
    <a class="btn btn--back" href="index.php?controller=products">Back to list</a>
</div>