const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

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

            
        }else if(ele.tagName == 'INPUT'){

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
    }
    arrParent.forEach(ele => {
        ele.classList.add('error');
    });

});


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
    }
});

searchInput.addEventListener('focus', () => {
    if(searchInput.classList.contains('error')){
        searchInput.classList.remove('error');
    }
});