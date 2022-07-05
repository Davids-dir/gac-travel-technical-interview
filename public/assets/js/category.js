const categoryInput = document.getElementById('category_name');
const buttonSubmit = document.getElementById('category_submit');
const inputBorderError = document.getElementById('input-category-block');
const helpErrorText = document.getElementById('text-input-error');
const helpErrorChilds = helpErrorText.children;


buttonSubmit.classList.add('disabled');
categoryInput.addEventListener('change', function () {
    checkLength(this);
});

function checkLength (element) {
    if (element.value.length === 0) {
        inputBorderError.classList.add('has-error');
        helpErrorChilds[0].style.display = "block";
        helpErrorText.classList.add('with-errors');
        buttonSubmit.classList.add('disabled');
    } else {
        inputBorderError.classList.remove('has-error');
        helpErrorChilds[0].style.display = "none";
        helpErrorText.classList.remove('with-errors');
        buttonSubmit.classList.remove('disabled');
    }
}