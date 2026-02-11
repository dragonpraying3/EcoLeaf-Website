const optionMenu = document.querySelector(".select-menu"),
    selectButton = document.querySelector(".select-btn"),
    options = document.querySelectorAll(".option"),
    buttonText = document.querySelector(".btn-text"),
    hiddenInput = document.getElementById("iconKey"),
    hiddenColour = document.getElementById('iconColor');

selectButton.addEventListener("click", () => {
    optionMenu.classList.toggle("active");
});

options.forEach(option => {
    option.addEventListener("click", () => {
        const value = option.dataset.value; //get the data-value value
        const colour = option.dataset.colour;
        const iconHTML = option.innerHTML;

        // Update button text
        buttonText.innerHTML = iconHTML;

        hiddenInput.value = value;
        hiddenColour.value = colour;

        sessionStorage.setItem("icon_key", hiddenInput.value);
        sessionStorage.setItem("icon_colour", hiddenColour.value);

        // Close dropdown
        optionMenu.classList.remove("active");
    });
});

//----------------------------------------------------------------------------
//show colour of the icon
const iconClass = document.querySelectorAll('li .bxr');

window.addEventListener("DOMContentLoaded", () => {
    options.forEach((option, index) => {
        const colour = option.dataset.colour;
        iconClass[index].style.color = colour;
    })
});

//----------------------------------------------------------------------------
//icon preview section
const form = document.getElementById('form');
const badgeName = document.getElementById('name');
const badgeCriteria = document.getElementById('criteria');
const badgeValue = document.getElementById('points');

const iconPlace = document.querySelector('.image-badge');
const titlePlace = document.querySelector('.title-badge');
const valuePlace = document.getElementById('num');
const criteriaPlace = document.getElementById('text');
const place = [iconPlace, titlePlace, valuePlace, criteriaPlace];
function clear() { //loop through element
    for (const item of place) {
        item.innerHTML = "";
    }
}

//update each place 
options.forEach(option => {
    option.addEventListener("click", () => {
        const value = option.dataset.value;
        const colour = option.dataset.colour;
        iconPlace.innerHTML = `<i class='bxr ${value}'></i>`;
        iconPlace.style.color = colour;
    });
});

badgeCriteria.addEventListener('change', function () {
    let criteriaValue = this.options[this.selectedIndex];
    criteriaPlace.innerHTML = criteriaValue.text;
})

form.addEventListener('change', () => {
    titlePlace.innerHTML = badgeName.value;
    valuePlace.innerHTML = badgeValue.value;
})

//----------------------------------------------------------------------------
//open the hidden create badge box
const openButton = document.getElementById('create-badge');
const close_button = document.getElementById('close-button');
const form_submit = document.querySelector('.create-badge-block');
const realForm = document.querySelector('.creatingForm');

const validationError = document.querySelector('.empty_errors');
let hasValidationError = false; //check the validation error 

const createInputs = document.querySelectorAll("input,select,option");
createInputs.forEach(input => {
    input.addEventListener('change', () => {
        sessionStorage.setItem(input.name, input.value);
    })
})

const clearSession = () => {
    createInputs.forEach(input => {
        sessionStorage.removeItem(input.name);
    })
}
//if error exist, keep modal open
window.addEventListener("DOMContentLoaded", () => {
    if (validationError && validationError.textContent.trim() !== "") {
        showBadgeBox();
        hasValidationError = true;

        const createInputs = document.querySelectorAll("input, select,option");
        createInputs.forEach(input => {
            const saveValue = sessionStorage.getItem(input.name); //read data by using the key 
            if (saveValue !== null) {
                input.value = saveValue; //update the value
            }
        })

        const saveKey = sessionStorage.getItem("icon_key");
        const saveColor = sessionStorage.getItem("icon_colour");
        if (saveKey !== null && saveColor !== null) {
            hiddenInput.value = saveKey;
            hiddenColour.value = saveColor;

            buttonText.innerHTML = `<i class='bxr ${saveKey}' style="color:${saveColor}"></i>`;
        }
    }
});

//open modal
const showBadgeBox = () => {
    form_submit.classList.add("open");
    document.body.style.overflow = "hidden";
}
//allow the close button to exit the form even have validation error
const closeBadgeBox = () => {
    if (validationError) {
        validationError.textContent = ""; //clear the content to allow exit
        hasValidationError = false;
    }

    form_submit.classList.remove("open");
    document.body.style.overflow = "visible";

    if (realForm) {
        realForm.reset();
        resetIconSelect();
        optionMenu.classList.remove("active");
        clear();
    }
}

form_submit.addEventListener('click', e => {
    const create_post_box = document.querySelector('.create-box');

    if (create_post_box.contains(e.target)) { //if click the modal inside
        return;
    }

    if (e.target === form_submit) { //if click outside and has error
        if (hasValidationError) {
            //restart animation because css animation plays once unless reset
            create_post_box.classList.remove('splash');
            void create_post_box.offsetWidth; //reflow or reset
            create_post_box.classList.add('splash'); //add
            return;
        }

        form_submit.classList.remove("open");
        document.body.style.overflow = "visible";

        if (realForm) {
            realForm.reset();
            resetIconSelect();
            optionMenu.classList.remove("active");
            clear();
        }
    }
})

openButton.addEventListener('click', showBadgeBox);
close_button.addEventListener('click', () => {
    closeBadgeBox();
    clearSession();
});

//----------------------------------------------------------------------
function resetIconSelect() {
    buttonText.textContent = "Select Icon";

    // reset hidden inputs
    hiddenInput.value = "";
    hiddenColour.value = "";
}

//----------------------------------------------------------------------
//confirm box
function confirmDelete(badgeName) {
    return confirm("Are you sure you want to delete the badge '" + badgeName + "' ?");
}

//pagination
let linking = document.querySelectorAll('.page-numbers>a');
let bodyId = parseInt(document.body.id) - 1;

linking[bodyId].classList.add('active');

//----------------------------------------------------------------------
//testing
window.addEventListener('click', (e) => {
    console.log(e.target);
})