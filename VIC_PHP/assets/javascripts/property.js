const $ = document.querySelector.bind(document);
const $$ = document.querySelectorAll.bind(document);

const propertyName = $('#name');
const type = $('#type');
const btnSubmit = $('#btnSubmit');


btnSubmit.addEventListener('click', (e) => {
    propertyName.parentElement.lastElementChild.innerHTML = "";
    type.parentElement.lastElementChild.innerHTML = "";

    let parttern = /^[0-9a-z\s_-]*$/i;
    const defaultValue = ['category', 'tag'];
    let options = Array.from(type.options);

    if(propertyName.value.trim().length <= 0) {
        showError(propertyName, 'Please enter a property name');
        e.preventDefault();
    }else if(parttern.test(propertyName.value.trim()) == false){
        showError(propertyName, 'Please do not enter special characters');
        e.preventDefault();
    }

    options.forEach(ele => {  
        if(defaultValue.includes(ele.value) == false || defaultValue.includes(ele.innerHTML.trim().toLowerCase()) == false) {
            showError(type, 'The data format has been changed');
            e.preventDefault();
            return;
        }
    });

});

function showError(item, message){
    item.parentElement.lastElementChild.innerHTML = message;
}