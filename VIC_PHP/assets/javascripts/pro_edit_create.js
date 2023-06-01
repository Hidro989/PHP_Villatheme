const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

const formControl = $$('.form-control');
const btnAdd = $('#btnAdd');
const btnEdit = $('#btnEdit');
const categories = $('#categories');
const tags = $('#tags');
const catOptions = convertValueToArr(categories.options);
const tagsOptions = convertValueToArr(tags.options);
//const partternText = /([a-z]|[0-9]|_|-)/igm; oldPartternText 
const partternText = /^[0-9a-z\s_-]*$/i;
const partternNumber = /([0-9])/gm;

function convertValueToArr(objects){
    const arr = [];
    for(let i = 0; i < objects.length; i++){
        arr.push(objects[i].value);
    }
    return arr;
}


if(btnAdd != null){
    btnAdd.addEventListener('click', (e) => {
        validateField('add', e);
    });
}

if(btnEdit!= null){
    btnEdit.addEventListener('click', (e) => {
        validateField('edit', e);
    });
}




function validateField(nameBtn, e){
    let checkIncrement = 0;
    formControl.forEach(ele => {
        const errorEle = ele.parentElement.lastElementChild;
        errorEle.innerHTML = "";
        if(ele.tagName ==='INPUT'){
            let eleValue = ele.value.trim();
            if(nameBtn == 'edit'){
                if(ele.value == '' && ele.type != 'file'){
                    errorEle.innerHTML = "Please enter this field";
                    checkIncrement++;
                }
            }else{
                if(ele.value == ''){
                    errorEle.innerHTML = "Please enter this field";
                    checkIncrement++;
                }
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
            }else{
                errorEle.innerHTML = "Please enter this field";
                checkIncrement++;
            }
        }       

    });
    if(checkIncrement != 0){
        e.preventDefault();
    }
}