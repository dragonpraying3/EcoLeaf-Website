//modal of reject box
const reject = document.querySelectorAll('.reject-trading');
const cancel_button = document.querySelectorAll('.canceling');
const reject_block = document.querySelector('.reject-block');
const form = document.querySelector('.reject-reason-form');
const rejectItemInput = document.getElementById('reject-itemId');

const showReject = () => {
    reject_block.classList.add("open");
    document.body.style.overflow = "hidden";
}
const hideReject = () => {
    reject_block.classList.remove("open");
    document.body.style.overflow = "visible";
    if (form) form.reset();
}

reject.forEach(evt => {
    evt.addEventListener('click', function (e) {
        e.preventDefault();

        //copy the $itemId from 'reject' button to 'send' button
        //从这个reject button开始向上找 找到 名字为rejectSection 的DOM 然后选择里面的一个class
        var rejectPost = this.closest('div.rejectSection');

        var itemId = rejectPost.querySelector('input[name="itemId"]').value;

        rejectItemInput.value = itemId;
        console.log(itemId);

        showReject();
    });
})

cancel_button.forEach(event => {
    event.addEventListener('click', hideReject);
})

//----------------------------------------------------------------------
//control button panel
const rejectButton = document.getElementById('reject');
const buttonON = () => {
    rejectButton.disabled = false;
    rejectButton.style.backgroundColor = '#e15957';
}
const buttonOFF = () => {
    rejectButton.disabled = true;
    rejectButton.style.backgroundColor = '#ebe9e9';
}

/*if form changes*/
form.addEventListener('change', buttonON);

document.addEventListener('click', e => {
    // console.log(e.target);
    if (e.target === reject_block) {
        reject_block.classList.remove("open");
        document.body.style.overflow = "visible";
        if (form) {
            buttonOFF();
            form.reset();
        }
    }
})

//----------------------------------------------------------------------
//admin -- diyModerate.php
const form_of_reason = document.querySelector('.reason-content');
const stableTitle = document.querySelector('.title-panel');
const stableButton = document.querySelector('.reject-button-zone');

//hide the box-shadow when scroll until top 
form_of_reason.addEventListener('scroll', () => {
    const scrollTop = form_of_reason.scrollTop;
    const maxScroll = 20; // the scroll height after which shadow is max
    const shadowIntensity = Math.min(scrollTop / maxScroll, 1) * 0.2; // max 0.2 opacity

    const shadow = `0px 3px 12px 0px rgba(0,0,0,${shadowIntensity})`;
    stableTitle.style.boxShadow = shadow;
    stableButton.style.boxShadow = shadow;
});

//----------------------------------------------------------------------
//notification copy value of div
const radioButtons = document.querySelectorAll('input[name="reject"]');
const descript = document.getElementById('descript');

radioButtons.forEach(radio => {
    radio.addEventListener('change', function () {
        let selectedReason = this.closest('div.reject-item');

        let description = selectedReason.querySelector('.reason-desc').textContent.trim();

        descript.value = description; //copying value to hidden input
        //console.log(descript.value);
    });
});



