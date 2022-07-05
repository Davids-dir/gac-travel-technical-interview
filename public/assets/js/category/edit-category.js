const categoryInput = document.getElementById('edit_category_name');
const buttonSubmit = document.getElementById('edit_category_submit');
const inputBorderError = document.getElementById('input-category-block');
const helpErrorText = document.getElementById('text-input-error');
const helpErrorChilds = helpErrorText.children;

// When document load
// Disabled submit button
buttonSubmit.classList.add('disabled');
categoryInput.addEventListener('change', function () {
    checkLength(this);
});

// Function to check length of input value, cant submit if input value is blank
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