const modal = document.getElementById('createRewardModal');
// create reward form
const form = modal.querySelector('form');
const fileInput = document.getElementById('image');
const uploadText = document.querySelector('.upload-text');
const imgError = modal.querySelector('#imgError');
const imgSuccess = modal.querySelector('#imgSuccessful');

const resetModalForm = () => {
    if (!form) return;
    form.reset(); // clears all inputs including the file picker

    if (uploadText) {
        uploadText.textContent = 'Click to upload image';
    }

    if (imgError) {
        imgError.textContent = '';
        imgError.classList.remove('show');
    }

    if (imgSuccess) {
        imgSuccess.textContent = '';
        imgSuccess.classList.remove('show');
    }
};

function openModal() {
    modal.style.display = 'flex';
}

function closeModal() {
    modal.style.display = 'none';
    resetModalForm();
}

// close if clicked outside the card
window.onclick = function(event) {
    if (event.target === modal) {
        closeModal();
    }
};

if (fileInput && uploadText) {
    const displayFileName = () => {
        if (fileInput.files && fileInput.files.length > 0) {
            uploadText.textContent = fileInput.files[0].name;
        } else {
            uploadText.textContent = 'Click to upload image';
        }
    };

    fileInput.addEventListener('change', displayFileName);
}

form.addEventListener('submit', (e) => {
  if (!fileInput.files || fileInput.files.length === 0) {
    e.preventDefault();

    imgSuccess.classList.remove('show');
    imgSuccess.textContent = '';

    imgError.textContent = 'please upload an image before creating reward';
    imgError.classList.add('show');
  }
});