const productInput = document.getElementById('edit_product_name');
const buttonSubmit = document.getElementById('edit_product_submit');
const inputBorderError = document.getElementById('input-product-block');
const helpErrorText = document.getElementById('text-input-error');
const helpErrorChilds = helpErrorText.children;

// When document load
productInput.addEventListener('change', function () {
    checkLength(this);
});

// Function to check length of input value, can't submit if input value is blank
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