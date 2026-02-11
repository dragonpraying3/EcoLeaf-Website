const statusSelect = document.getElementById('statusSelect');

function updateStatusColor() {
    if (statusSelect.value === 'inactive') {
        // swtich red
        statusSelect.classList.remove('status-active');
        statusSelect.classList.add('status-inactive');
    } else {
        // swtich green
        statusSelect.classList.remove('status-inactive');
        statusSelect.classList.add('status-active');
    }
}

// execute logic if the element exists
if (statusSelect) {
    // add event listener for changes
    statusSelect.addEventListener('change', updateStatusColor);
    // run function
    updateStatusColor();
}

// toast message 
function showToast(msg, isError) {
    const toast = document.getElementById("toast");

    toast.textContent = msg;

    // reset
    toast.classList.remove('error');

    if (isError) {
        toast.classList.add('error');
    }

    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

document.addEventListener("DOMContentLoaded", () => {

    // status select logic
    if (statusSelect) {
        statusSelect.addEventListener('change', updateStatusColor);
        updateStatusColor();
    }

    // ===== Toast trigger from PHP (data attribute) =====
    const toastType = document.body.dataset.toast;
    const errorTypes = ['tp_exists', 'duplicate_user'];
    if (!toastType) return;

    const isError = errorTypes.includes(toastType);
    let message = '';

    switch (toastType) {
        case 'updated':
            message = 'User updated successfully!';
            break;
        case 'reset':
            message = 'Password reset to default!';
            break;
        case 'deleted':
            message = 'User deleted successfully!';
            break;
        case 'tp_exists':
            message = 'TP Number already exists.';
            break;
        case 'duplicate_user':
            message = 'Email or phone number already exists.';
            break;
    }

    if (message) {
        showToast(message,isError);
    }

});

// disable all field for inactive user
// data-xxx-yyy="value" / data-user-status
// dataset.xxxYyy / userStatus
const userStatus = document.body.dataset.userStatus;

if (userStatus === 'inactive') {
    // disable all inputs/selects/textareas
    document.querySelectorAll(
        'input:not([type="hidden"]), select, textarea')
        .forEach(i => {i.disabled = true;});

    // disable save and delete buttons
    document.querySelectorAll('.btn-save, .btn-delete')
    .forEach(btn => {
        btn.disabled = true;
        btn.classList.add('disabled');});
}

