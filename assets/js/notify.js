const lines = document.querySelectorAll('.content');
const buttons = document.querySelectorAll('.read-btn');

//set the lines font weight status
window.addEventListener("DOMContentLoaded", () => {
    lines.forEach((evt) => {
        let statusInput = evt.querySelector('.status'); //select status from each line
        let status = statusInput.value; //get the status value

        if (status == 0) { //if unread  
            evt.style.fontWeight = 'bold'; //set font weight to bold
        } else {
            evt.style.fontWeight = 'normal'; //set font weight to normal
        }
    });
});

//button functionality
buttons.forEach((button, index) => { //get a index to match line with button
    button.addEventListener('click', function () {
        let statusInput = lines[index].querySelector('.status'); //hidden input to store status
        let status = statusInput.value;

        //button.classList.toggle('read'); //like a on off switch

        if (status == 0) { //if unread 
            lines[index].style.fontWeight = 'normal';
            button.classList.add('read');
            // button.textContent = "Read";
            // button.disabled = true;
        }
    });
})

//dropdown apply
const sortDropdown = document.getElementById('sorting');
sortDropdown.addEventListener('change', () => {
    document.getElementById('searchForm').submit(); //auto submit form when change
})