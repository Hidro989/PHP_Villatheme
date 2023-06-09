app();
function app(){
const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

const modal =  $('#modal');
const modalContent = $('#modalContent');

window.addEventListener('click', (e) => {
    if(e.target.id == 'btnOpenAddProduct'){
        modal.classList.add('active');
        let data = {};
        let url = 'index.php?controller=products&action=create';
        try{
            data = synLoadDoc(url);

        }catch(e){
            console.log(e);
        }

        modalContent.innerHTML = createHtmlProduct('Add', data.categories, data.tags);
        loadProductScrip();
    }

    if(e.target.closest('.editProduct') != null){
        modal.classList.add('active');
        let ancestor = e.target.closest('.editProduct');
        let data = {};
        const url = `index.php?controller=products&action=edit&id=${ancestor.dataset.id}`
        try{
            data = synLoadDoc(url);

        }catch(e){
            console.log(e);
        }
        let product = data.product;
        modalContent.innerHTML = createHtmlProduct('Edit', data.categories, data.tags, product.categories, product.tags, product.name, product.sku, product.price, product.image, product.gallery, product.id);
        loadProductScrip();
    }

    if(e.target.closest('.deleteProduct') != null){
        let ancestor = e.target.closest('.deleteProduct');
        const url = `index.php?controller=products&action=delete&id=${ancestor.dataset.id}`
        let check = confirm('Do you want to delete this product');

        if(check == true){
            let inform = synLoadDoc(url);
            alert(inform.success);
            loadContent('#myApp', function(response){
                document.open();
                document.write(response);
                document.close();
            });
        }
    }

    if(e.target.id == 'btnOpenAddProperty'){
        modal.classList.add('active');
        modalContent.innerHTML = createHtmlProperty();
        loadPropertyScript();
    }

    if(e.target.classList.contains('overlay') || (e.target.closest('.modal-close') != null)){
        modal.classList.remove('active');
    }

    if(e.target.closest('.pagItem') != null){
        let item = e.target.closest('.pagItem');
        let url = item.dataset.url;
        sendDataLoadWholePage(url);
    }

    if(e.target.id == 'btnSynData'){
        let records = synLoadDoc('index.php?controller=products&action=syndata').records;
        let recordLenth = records.length;
        let count = 0;

        $('#modalLoader').classList.add('active');

        // for(let i = 0; i < recordLenth; ++i){
        //     if(count == 5){
        //         break;
        //     }
        //     console.table(i, count);
        //     count++;
        // }
        
        SynDataFromVillaRecursion(records);
        // records.forEach(element => {
        //     let url = 'index.php?controller=products&action=addSyndata&url=' + element;
        //     const xhttp = new XMLHttpRequest();
        //     xhttp.open('GET', url, true);
        //     xhttp.onreadystatechange = function(){
        //         if(this.readyState == 4 && this.status == 200){
        //             count++;
        //             console.log(count);
        //             if(count == recordLenth){
        //                 $('#modalLoader').classList.remove('active');
        //                 alert("Load data successfully");
        //                 loadContent('#myApp', function(response){
        //                     document.open();
        //                     document.write(response);
        //                     document.close();
        //                 });
        //             }
        //         }
        //     }
        //     xhttp.send();
        // });

        
    }
} );


function SynDataFromVillaRecursion(records, startPoint = 0){
    let recordLenth = records.length;
    if(startPoint == recordLenth){
        return;
    }
    let formData = new FormData();
    let count = 0;
    while(startPoint < recordLenth){
        if(count == 5){
            break;
        }
        formData.append('urls[]', records[startPoint]);
        startPoint++;
        count++;
    }
    
    // for(const pair of formData.entries()){
    //     console.log(`${pair[0]} => ${pair[1]}`)
    // }
    // console.log('done');
    // Send data to server

    let url = 'index.php?controller=products&action=addSyndata';
    const xhttp = new XMLHttpRequest();
    xhttp.open('POST', url, false);
    xhttp.send(formData);

    if(startPoint == recordLenth){
        $('#modalLoader').classList.remove('active');
        alert("Load data successfully");
        loadContent('#myApp', function(response){
            document.open();
            document.write(response);
            document.close();
            });
        }

    SynDataFromVillaRecursion(records, startPoint);
}



function synLoadDoc(url, method = 'GET'){
    const xhttp = new XMLHttpRequest();
    xhttp.open(method, url, false);
    xhttp.send();
    return JSON.parse(xhttp.responseText);
}

function loadContent(url, callback){
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            callback(xhr.responseText);
        }
    };

    xhr.open('GET', url, true);
    xhr.send();
}



function sendFormData(url, data, method = 'GET', event = null) {
    const xhttp = new XMLHttpRequest();
    xhttp.open(method, url, true);
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            let inform = JSON.parse(this.responseText);

            if(inform.success != null && inform.success != ''){
                alert(inform.success);
                loadContent('#myApp', function(response){
                    document.open();
                    document.write(response);
                    document.close();
                });
            }
            showErrorFromServer(inform.errors, event);

        }
    }
    xhttp.send(data);
}

function sendDataLoadWholePage(url, method = 'GET'){
    const xhttp = new XMLHttpRequest();
    xhttp.open(method, url, true);
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            document.open();
            document.write(this.responseText);
            document.close();
        }
    }
    xhttp.send();
}

function showErrorFromServer(errors, e){
    let errorsEle = $('.errors');
    if(errors == null && errors == undefined){
        errorsEle.innerHTML = '';
        return;
    }
    let htmlErr = [];
    errors.forEach(ele => {
        html = `<p>${ele}</p>`;
        htmlErr.push(html);
    });
    errorsEle.innerHTML = htmlErr.join('');
    e.preventDefault();
}

function createHtmlProduct(title= '', categories = [], tags = [], selectedCategory = '', selectedTag = '', name = '', sku = '', price = '', image= '', gallery = '', id = ''){

    let htmlId = id != '' ? `<input type="hidden" class="form-control" name="id" value="${id}">` : '';
    let htmlImage = image != '' ? `<img height="30" width="30" src="${image}" alt="Feature Image" />` : '';
    
    let htmlGallery = [];
    if(gallery.length > 0){
        gallery = gallery.split(',');
        gallery.forEach(ele => {
            let galry = `<img height="30" width="30" src="${ele}" alt="Gallery" />`;
            htmlGallery.push(galry);
        });
    } 

    let htmlCategory = [];
    if(categories.length > 0){
        let productCategory = selectedCategory.split(',');
        
        categories.forEach(ele => {
            let selected = productCategory.includes(ele.name) ? 'selected' : '';
            let option = `<option value="${ele.id}" ${selected}>${ele.name}</option>`;
            htmlCategory.push(option);
        });
    }

    let htmlTag = [];
    if(tags.length > 0){
        let tagProduct = selectedTag.split(',');

        tags.forEach(ele => {
            let selected = tagProduct.includes(ele.name) ? 'selected' : '';
            let option = `<option value="${ele.id}" ${selected}>${ele.name}</option>`;
            htmlTag.push(option);
        });
    }



    return `
    <span class="modal-close"><i class='bx bx-x'></i></span>
        <h3>${title} Product</h3>
        <div class="errors"></div>
        <div class="wrap-form">
        <div>
            <div class="form-group">
                ${htmlId}
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="${name}">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="sku">SKU:</label>
                <input type="text" class="form-control" id="sku" name="sku" placeholder="Enter SKU" value="${sku}">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" class="form-control" min="0" id="price" name="price" placeholder="Enter price" value="${price}">
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" class="form-control" id="image" name="image" accept=".jpg,.png">
                ${htmlImage}
                <p class="error"></p>
            </div>
            <div class="form-group">
                <label for="gallery">Gallery:</label>
                <input type="file" class="form-control" id="gallery" name="gallery[]"  multiple accept=".jpg,.png">
                ${htmlGallery.length > 0 ? htmlGallery.join(''): ''}
                <p class="error"></p>
            </div>
            <div class="form-group">
                    <label for="categories">Categories:</label>
                    <select name="categories[]" id="categories" class="form-control" multiple>
                    ${htmlCategory.length > 0 ? htmlCategory.join(''): ''}
                    </select>
                    <p class="error"></p>
                </div>
                <div class="form-group">
                    <label for="tags">Tags:</label>
                    <select name="tags[]" id="tags" class="form-control" multiple>
                    ${htmlTag.length > 0 ? htmlTag.join(''): ''}
                    </select>
                    <p class="error"></p>
                </div>

            <div class="form-group center">
                <button class="btn btn--primary" id="btn${title}">Submit</button>
            </div>
        </div>
         
        </div>`;
}


function createHtmlProperty(){
    return `<span class="modal-close"><i class='bx bx-x'></i></span>
    <h3>Add Property</h3>
    <div class="wrap-form">
        <div>
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
               
            <div class="form-group center">
                <button type="submit" class="btn btn--primary" id="btnSubmit">Submit</button>
            </div>
        </div>
         
    </div>`;
}

LoadIndexScript();

function LoadIndexScript(){

const btnFilter = $('#btnFilter');
const formControl = $$('.form-input-control');
const orderBy = $('#orderBy');
const sort = $('#sort');
const selectedCategory = $('#selectedCategory');
const selectedTag = $('#selectedTag');
const startDate = $('#startDate');
const endDate = $('#endDate');
const orderByOptions = convertValueToArr(orderBy.options);
const sortOptions = convertValueToArr(sort.options);
const selectedCategoryOptions = convertValueToArr(selectedCategory.options);
const selectedTagOptions = convertValueToArr(selectedTag.options);
const searchInput = $('#search');
const btnSearch = $('#btnSearch');

const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
const partternText = /^[0-9a-z\s_-]*$/i;



function convertValueToArr(objects){
    const arr = [];
    for(let i = 0; i < objects.length; i++){
        arr.push(objects[i].value);
    }
    return arr;
}




function getFilterQueryString(){
    
    let data = {};
    formControl.forEach(ele => {
        data[ele.name] = ele.value;
    });

    let queryString = Object.keys(data).map(function(key){
        return encodeURIComponent(key) + '=' + encodeURIComponent(data[key]);
    }).join('&');

    return queryString;
}


function validateSelect(selectedIndex, defaultOptions){
    let check = false;
    if(defaultOptions.includes(selectedIndex.value) == true){
        check = true;
    }
    return check;
}

btnSearch.addEventListener('click', (e) => {
    let searchValue = searchInput.value.trim();
    if((!partternText.test(searchValue)) && (searchValue.length > 0)){
        searchInput.classList.add('error');
        alert("Please enter a valid search");
        e.preventDefault();
    }else{
        let url = `index.php?controller=products&action=index&search=${encodeURIComponent(searchValue)}&`+ getFilterQueryString();
        sendDataLoadWholePage(url);
    }
});

searchInput.addEventListener('focus', () => {
    if(searchInput.classList.contains('error')){
        searchInput.classList.remove('error');
    }
});

btnFilter.addEventListener('click', (e) => { 
    let arrParent = [];
    formControl.forEach(ele => {
        let parent = ele.parentElement;
        if(parent.classList.contains('error') == true){
            parent.classList.remove('error');
        }
        if(ele.tagName == 'SELECT') {
            switch(ele.name){
                case 'orderBy': {
                    if(validateSelect(ele.options[ele.selectedIndex], orderByOptions) == false){
                        arrParent.push(parent);
                    }
                }
                break;
                case 'sort': {
                    
                    if(validateSelect(ele.options[ele.selectedIndex], sortOptions) == false){
                        arrParent.push(parent);
                    }
                }
                break;
                case 'selectedCategory': {
                    if(validateSelect(ele.options[ele.selectedIndex], selectedCategoryOptions) == false){
                        arrParent.push(parent);
                    }
                }
                break;
                case 'selectedTag': {
                    if(validateSelect(ele.options[ele.selectedIndex], selectedTagOptions) == false){
                        arrParent.push(parent);
                    }
                }
                break;
            }

            
        }else if(ele.tagName == 'INPUT' && ele.type != 'hidden'){

            if((!dateRegex.test(ele.value)) && (ele.value.trim().length > 0)){
                arrParent.push(parent);
            }
            
        }
    });

    if(startDate.value.length > 0 && endDate.value.length > 0){
        if(dateRegex.test(startDate.value) && dateRegex.test(endDate.value)){
            if(startDate.value > endDate.value){
                alert('START DATE must smaller END DATE');
                e.preventDefault();
            }
        }
    }
    
    if(arrParent.length > 0){
        e.preventDefault();
        alert('Pleae check FILTER');

        arrParent.forEach(ele => {
            ele.classList.add('error');
        });
    }else{
        let searchValue = searchInput.value.trim();
        let url = `index.php?controller=products&action=index&search=${encodeURIComponent(searchValue)}&`+ getFilterQueryString();
        sendDataLoadWholePage(url);
    }

});



}





function loadProductScrip(){

    const formControl = $$('.form-control');
    const btnAdd = $('#btnAdd');
    const btnEdit = $('#btnEdit');
    const categories = $('#categories');
    const tags = $('#tags');
    const catOptions = convertValueToArr(categories.options);
    const tagsOptions = convertValueToArr(tags.options);
    //const partternText = /([a-z]|[0-9]|_|-)/igm; oldPartternText 
    const partternText = /^[0-9a-z\s_-]*$/i;
    const partternNumber = /([0-9])/;

    function convertValueToArr(objects){
        const arr = [];
        for(let i = 0; i < objects.length; i++){
            arr.push(objects[i].value);
        }
        return arr;
    }


    if(btnAdd != null){
        btnAdd.addEventListener('click', (e) => {
            if(validateField(e) == true){
                url = 'index.php?controller=products&action=stored';
                sendFormData(url, getFormData(), 'POST', e);
                modal.classList.remove('active');
            }
            
        });
    }

    if(btnEdit!= null){
        btnEdit.addEventListener('click', (e) => {
            if(validateField(e) == true){
                url = 'index.php?controller=products&action=update';
                sendFormData(url, getFormData(), 'POST', e);
                modal.classList.remove('active');
            }
        });
    }


    function getFormData(){
        let formData = new FormData();

        formControl.forEach(ele => {
            if(ele.name == 'tags[]' || ele.name == 'categories[]'){
                let selectedOptions = ele.selectedOptions;

                for(let i = 0; i < selectedOptions.length; i++){
                    formData.append(ele.name, selectedOptions[i].value);
                }
            }else if(ele.type == 'file'){
                let files = ele.files;
                let fileLength = files.length;
                for(let i = 0; i  < fileLength; i++){
                    formData.append(ele.name, files[i]);
                }
            }
            else{
                formData.append(ele.name, ele.value);
            }
        });

        return formData;
    }


    function validateField(e){
        let checkIncrement = 0;
        formControl.forEach(ele => {
            const errorEle = ele.parentElement.lastElementChild;
            errorEle.innerHTML = "";
            if(ele.tagName ==='INPUT'){
                let eleValue = ele.value.trim();
                
                if(ele.value == ''&& ele.type != 'file' && ele.id != 'price'){
                    errorEle.innerHTML = "Please enter this field";
                    checkIncrement++;
                }

                if(ele.id == 'price' && eleValue.length > 0){
                    if(partternNumber.test(eleValue) == false){
                        errorEle.innerHTML = "Please  enter number";
                        checkIncrement++;
                    }
                }else if(ele.type == 'text' && eleValue.length > 0){
                    if(partternText.test(eleValue) == false){
                        errorEle.innerHTML = "Please do not enter special characters";
                        checkIncrement++;
                    }
                }else if(ele.id == 'image' || ele.id == 'gallery'){
                    if(ele.files.length > 0){
                        const file = ele.files[0];
                        if (!file) {
                            errorEle.innerHTML = "File not found";
                        } 
                    }
                }
            }else if(ele.tagName == "SELECT"){

                const selectedOptionLength = ele.selectedOptions.length;

                if(selectedOptionLength > 0){
                    if(ele == categories){
                        let check = [];
                        const selectedOption = ele.selectedOptions;

                        for(let i = 0; i < selectedOptionLength; i++){
                            if(catOptions.includes(selectedOption[i].value)){
                                check[i] = true;
                            }else{
                                check[i] = false;
                            }
                        }

                        if(check.includes(false)){
                            errorEle.innerHTML = "Invalid data option";
                            checkIncrement++;
                        }
                        
                    }
                    if(ele == tags){
                        let check = [];
                        const selectedOption = ele.selectedOptions;

                        for(let i = 0; i < selectedOptionLength; i++){
                            if(tagsOptions.includes(selectedOption[i].value)){
                                check[i] = true;
                            }else{
                                check[i] = false;
                            }
                        }

                        if(check.includes(false)){
                            errorEle.innerHTML = "Invalid data option";
                            checkIncrement++;
                        }
                    } 
                }
            }       

        });
        if(checkIncrement != 0){
            e.preventDefault();
        }

        return checkIncrement != 0 ? false : true;
    }


}


function loadPropertyScript(){

    const propertyName = $('#name');
    const type = $('#type');
    const btnSubmit = $('#btnSubmit');


    if(btnSubmit != null){
        btnSubmit.addEventListener('click', (e) => {
            if(validateField(e) == true){
                let formData = new FormData();
                formData.append('name', propertyName.value);
                formData.append('type', type.options[type.selectedIndex].value);
                const url = "index.php?controller=properties&action=stored";
                sendFormData(url, formData, 'POST', e);
                modal.classList.remove('active');
            }
        });
    }

    function validateField(e){
        propertyName.parentElement.lastElementChild.innerHTML = "";
        type.parentElement.lastElementChild.innerHTML = "";
        
        let parttern = /^[0-9a-z\s_-]*$/i;
        const defaultValue = ['category', 'tag'];
        let typeSelected = type.options[type.selectedIndex];
        let checkIncrement = 0;

        if(propertyName.value.trim().length <= 0) {
            showError(propertyName, 'Please enter a property name');
            checkIncrement++;
        }else if(parttern.test(propertyName.value.trim()) == false){
            showError(propertyName, 'Please do not enter special characters');
            checkIncrement++;
        }

        if(defaultValue.includes(typeSelected.value) == false || defaultValue.includes(typeSelected.innerHTML.trim().toLowerCase()) == false) {
            showError(type, 'The data format has been changed');
            checkIncrement++;
        }

        if(checkIncrement != 0){
            e.preventDefault();
        }

        return checkIncrement != 0 ? false : true;
    }

    function showError(item, message){
        item.parentElement.lastElementChild.innerHTML = message;
    }

}
}


