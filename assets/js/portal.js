
// Find the Login and Register tab buttons on the current page
var loginBtn = document.querySelector('.tab-btn[data-type="login"]');
var registerBtn = document.querySelector('.tab-btn[data-type="register"]');

// Find the form container, heading text, and submit button
var portalForm = document.querySelector('.portal-form');
var heading = document.getElementById('form-heading');
var submitButton = document.getElementById('portal-submit');

// password input + register pattern
var passwordInput = document.getElementById('passwordInput');
// same rule as register
var registerPwdPattern = "^(?=.*[0-9])(?=.*[!@#$%^&*(),.?\\\":{}|<>])[A-Za-z0-9!@#$%^&*(),.?\\\":{}|<>]{8,}$";

var portalCard = document.querySelector('.portal-card');
var extraGrid = document.querySelector('.extra-grid');
var registerNotes = document.querySelectorAll('.register-note');

var rememberRow = document.querySelector('.remember-row');

// Find extra fields (only used in register mode)
var extraFields = document.querySelectorAll('.extra-grid input, .extra-grid select');


var registerDraftPrefix = "portal_register_draft_";

// Read role name from HTML header (Student / Organizer / Admin)
if (heading && heading.getAttribute("data-role")) {
    roleName = heading.getAttribute("data-role");
}
// portal_register_draft_role_xxx
registerDraftPrefix = registerDraftPrefix + roleName.toLowerCase() + "_";

function saveRegisterDraft() {
    if (!portalForm) return;
    var modeField = document.getElementById("form-mode");
    // if form mode not register will return
    if (!modeField || modeField.value !== "register") return;

    
    portalForm.querySelectorAll('input, select').forEach(function(field) {
        if (!field.name || field.type === "hidden") return;
        if (field.type === "radio") {
            // portal_registe_draft_student_username
            if (field.checked) localStorage.setItem(registerDraftPrefix + field.name, field.value);
            return;
        }
        if (field.type === "checkbox") {
            if (field.checked) {
                localStorage.setItem(registerDraftPrefix + field.name, field.value);
            } else {
                localStorage.removeItem(registerDraftPrefix + field.name);
            }
            return;
        }
        localStorage.setItem(registerDraftPrefix + field.name, field.value);
    });
}

function loadRegisterDraft() {
    if (!portalForm) return;
    portalForm.querySelectorAll('input, select').forEach(function(field) {
        if (!field.name || field.type === "hidden") return;
        var saved = localStorage.getItem(registerDraftPrefix + field.name);
        if (saved === null) return;
        if (field.type === "radio") {
            field.checked = (saved === field.value);
            return;
        }
        if (field.type === "checkbox") {
            field.checked = (saved === field.value);
            return;
        }
        field.value = saved;
    });
}

function clearRegisterDraft() {
    var keysToRemove = [];
    for (var i = 0; i < localStorage.length; i++) {
        var key = localStorage.key(i);
        if (key && key.indexOf(registerDraftPrefix) === 0) {
            keysToRemove.push(key);
        }
    }
    keysToRemove.forEach(function(key) {
        localStorage.removeItem(key);
    });
}

// Switch to login mode
function switchToLogin() {

    // clear inputs only when explicitly requested
    const shouldClear = arguments.length === 0 ? true : arguments[0];
    if (shouldClear) {
        clearFormFields();
    }

    document.getElementById("form-mode").value = "login";

    extraFields.forEach(function (field) {
        field.disabled = true;
    });

    //highlight Login tab and un-highlight Register tab
    if (loginBtn && registerBtn) {
        loginBtn.classList.add('active');
        registerBtn.classList.remove('active');
    }

    /*//hide extra fields (show only base login fields)
    if (portalForm) {
        portalForm.classList.remove('is-register');
    }*/

    //.extra-grid { display: none })
    if (extraGrid) {
        extraGrid.classList.remove('active');
    
    }

    //.portal-card.register-mode)
    if (portalCard) {
        portalCard.classList.remove('register-mode');
    }

    //"No spaces allowed")
    registerNotes.forEach(function(note) {
        note.style.display = 'none';
    });

    //change heading text (e.g. Student Login)
    if (heading) {
        heading.textContent = roleName + " Login";
    }

    //change submit button text
    if (submitButton) {
        submitButton.textContent = "Login";
    }
    
    if(rememberRow) rememberRow.style.display = 'flex';
    applyPasswordValidationByMode();
}

//wwitch to REGISTER mode
function switchToRegister() {

    // clear inputs only when explicitly requested
    const shouldClear = arguments.length === 0 ? true : arguments[0];
    if (shouldClear) {
        clearFormFields();
    }

    document.getElementById("form-mode").value = "register";

    extraFields.forEach(function (field) {
        field.disabled = false;
    });

    // Highlight Register tab and un-highlight Login tab
    if (loginBtn && registerBtn) {
        registerBtn.classList.add('active');
        loginBtn.classList.remove('active');
    }

    //.extra-grid.active { display: grid })
    if (extraGrid) {
        extraGrid.classList.add('active');
    }

    //.portal-card.register-mode)
    if (portalCard) {
        portalCard.classList.add('register-mode');
    }

    //
    registerNotes.forEach(function(note) {
        note.style.display = 'block';
    });

    // Change heading text (e.g. Student Registration)
    if (heading) {
        heading.textContent = roleName + " Registration";
    }

    // Change submit button text (e.g. Create Student Account)
    if (submitButton) {
        submitButton.textContent = "Create " + roleName + " Account";
    }

    if(rememberRow) rememberRow.style.display = 'none';
    applyPasswordValidationByMode();
}

function clearFormFields() {
    const activeForm = document.querySelector('.portal-form');

    if (!activeForm) return;

    clearRegisterDraft();
    activeForm.querySelectorAll('input, select').forEach(field => {
        if (field.type === "radio" || field.type === "checkbox") {
            field.checked = false;
        } else if (field.type !== "hidden") {
            field.value = "";
        }
    });
}

//bind button click events
//login tab click
if (loginBtn) {
    loginBtn.addEventListener('click', function () {
        switchToLogin();
    });
}

//register tab click
if (registerBtn) {
    registerBtn.addEventListener('click', function () {
        switchToRegister();
    });
}

document.addEventListener("DOMContentLoaded", ()=>{
    // allow alerts to be closed
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.parentElement.remove();
        });
    });

    // restore saved register fields if any
    loadRegisterDraft();

    // save register draft on changes
    if (portalForm) {
        portalForm.addEventListener('input', saveRegisterDraft);
        portalForm.addEventListener('change', saveRegisterDraft);
    }

    // choose tab based on url mode
    const url = new URL(window.location.href);
    const hasError = url.searchParams.has("error");
    const hasRegistered = url.searchParams.has("registered");
    const rememberCheckbox = document.querySelector('input[name="remember_me"]');
    const shouldClear = !hasError && !(rememberCheckbox && rememberCheckbox.checked);
    const forceMode = url.searchParams.get("mode");

    // clear saved draft after successful register redirect
    if (hasRegistered) {
        clearRegisterDraft();
    }

    if (forceMode === "register") {
        switchToRegister(shouldClear);
        applyPasswordValidationByMode();
    } else {
        switchToLogin(shouldClear);
        applyPasswordValidationByMode();
    }

    // clean url once handled
    const urlParams = ["registered", "error", "mode"];
    let cleaned  = false;

    urlParams.forEach(param => {
        if (url.searchParams.has(param)) {
            url.searchParams.delete(param);
            cleaned = true;
        }
    });

    if (cleaned) {
        window.history.replaceState({},document.title,url.pathname);
    }
});

function applyPasswordValidationByMode() {
    if (!passwordInput) return;

    var modeField = document.getElementById("form-mode");
    var mode = modeField ? modeField.value : "login";

    if (mode === "register") {
        passwordInput.setAttribute("pattern", registerPwdPattern);
        passwordInput.setAttribute("minlength", "8");
        passwordInput.setAttribute("title", "At least 8 chars, include a number and a special symbol.");
    } else {
        // login remove password rule
        passwordInput.removeAttribute("pattern");
        passwordInput.removeAttribute("minlength");
        passwordInput.removeAttribute("title");
    }
}
