//open the create post block
const create_post = document.getElementById('create-post');
const close_button = document.getElementById('close-button');
const form_submit = document.querySelector('.create-post-block');
const realForm = document.querySelector('.creatingForm');

const validationError = document.querySelector(".validation-error");
let hasValidationError = false; //check the validation error 

//The sessionStorage object stores data for only one session.
//(The data is deleted when the browser is closed).
const createInputs = document.querySelectorAll("input, textarea,select");
createInputs.forEach(input => {
    input.addEventListener("change", () => { //when input change, save it
        sessionStorage.setItem(input.name, input.value); //save the input name and value into the session storage object
    })
})

//if error exist, keep modal open
window.addEventListener("DOMContentLoaded", () => {
    if (validationError && validationError.textContent.trim() !== "") {
        showCreateEventBlock();
        hasValidationError = true;

        const createInputs = document.querySelectorAll("input, textarea,select");
        createInputs.forEach(input => {
            const saveValue = sessionStorage.getItem(input.name); //read data by using the key 
            if (saveValue !== null) {
                input.value = saveValue; //update the value
            }
        })
    }
});

const clearSession = () => {
    createInputs.forEach(input => {
        sessionStorage.removeItem(input.name);
    })
}

//open modal
const showCreateEventBlock = () => {
    form_submit.classList.add("open");
    document.body.style.overflow = "hidden";
}
//allow the close button to exit the form even have validation error
const closeCreateEventBlock = () => {
    if (validationError) {
        validationError.textContent = ""; //clear the content to allow exit
        hasValidationError = false;
    }

    form_submit.classList.remove("open");
    document.body.style.overflow = "visible";
    if (realForm) {
        realForm.reset();
        fileInputShow.textContent = "No file chosen";
    }
}

form_submit.addEventListener('click', e => {
    const create_post_box = document.querySelector('.create-post-box');

    if (create_post_box.contains(e.target)) { //if click the modal inside
        return;
    }

    if (e.target === form_submit) { //if click outside and has error
        if (hasValidationError) {
            //restart animation because css animation plays once unless reset
            create_post_box.classList.remove('splash');
            void create_post_box.offsetWidth; //reflow or reset
            create_post_box.classList.add('splash'); //add
            clearSession();
            return;
        }

        form_submit.classList.remove("open");
        document.body.style.overflow = "visible";

        if (realForm) {
            realForm.reset();
            fileInputShow.textContent = "No file chosen";
        }
    }
})

create_post.addEventListener('click', showCreateEventBlock);
close_button.addEventListener('click', () => {
    closeCreateEventBlock();
    clearSession();
});

//----------------------------------------------------------------------
//open the trading box
const tradeButton = document.querySelectorAll('.TRADE');
const closing_button = document.querySelectorAll('.CLOSE');
const openTrade = document.querySelector('.trade-post-block');
const trade_box = document.querySelector('.trading-box');
const realForm2 = document.querySelector('.tradingForm');
const timeShow = document.getElementById('time-showing'); //for showing +2 hours text

const trade_itemId = document.getElementById('trade-itemId');
const trade_sellerId = document.getElementById('trade-sellerId');
const trade_leaf = document.getElementById('trade-leaf');

const empty_error = document.querySelector('.empty_errors');
let hasEmpty_error = false;

window.addEventListener('DOMContentLoaded', () => {
    if (empty_error && empty_error.textContent.trim() !== "") {
        showTradingBox();
        hasEmpty_error = true;

        //read cookies by convert into js object
        //formEntries=dictionary --> { trade_title: "Book", trade_name: "Alice" }
        const cookies = Object.fromEntries(
            document.cookie.split('; ').map(c => c.split('='))
        );//trade_title=book; trade_name=lam --> ['trade_title','book','trade_name','lam']

        //restore the current box title and name
        if (cookies.trade_title) {
            trade_title.innerHTML = decodeURIComponent(cookies.trade_title);
        }
        if (cookies.trade_name) {
            trade_name.innerHTML = decodeURIComponent(cookies.trade_name);
        }

        // Restore hidden inputs 
        //the hidden input also need to restore 
        if (cookies.trade_itemId) {
            trade_itemId.value = decodeURIComponent(cookies.trade_itemId);
        }
        if (cookies.trade_sellerId) {
            trade_sellerId.value = decodeURIComponent(cookies.trade_sellerId);
        }
        if (cookies.trade_leaf) {
            trade_leaf.value = decodeURIComponent(cookies.trade_leaf);
        }

        console.log(document.cookies);
    }
})

const showTradingBox = () => {
    openTrade.classList.add("open");
    document.body.style.overflow = "hidden";
}
const closeTradingBox = () => {
    if (empty_error) {
        empty_error.textContent = ""; //clear the content to allow exit
        hasEmpty_error = false;
    }

    openTrade.classList.remove("open");
    document.body.style.overflow = "visible";
    if (realForm2) {
        realForm2.reset();
        timeShow.textContent = "";
    }
}

tradeButton.forEach(event => {
    event.addEventListener('click', function () {
        showTradingBox();

        const tradePost = this.closest('button.TRADE');

        var itemId = tradePost.querySelector('input[name="itemId"]').value;
        var sellerId = tradePost.querySelector('input[name="sellerId"]').value;
        var leaf = tradePost.querySelector('input[name="leaf"]').value;

        document.cookie = `trade_itemId=${encodeURIComponent(itemId)}; path=/`;
        document.cookie = `trade_sellerId=${encodeURIComponent(sellerId)}; path=/`;
        document.cookie = `trade_leaf=${encodeURIComponent(leaf)}; path=/`;

        trade_itemId.value = itemId;
        trade_sellerId.value = sellerId;
        trade_leaf.value = leaf;

    });
})
closing_button.forEach(evt => {
    evt.addEventListener('click', closeTradingBox);
})

//click outside
openTrade.addEventListener('click', e => {

    if (trade_box.contains(e.target)) { //if click the modal inside
        return;
    }

    if (e.target === openTrade) {
        if (hasEmpty_error) {
            trade_box.classList.remove('splash');
            void trade_box.offsetWidth; //reflow or reset
            trade_box.classList.add('splash'); //add
            return;
        }

        openTrade.classList.remove("open");
        document.body.style.overflow = "visible";

        if (realForm2) {
            realForm2.reset();
            timeShow.textContent = "";
        }
    }

})

//----------------------------------------------------------------------
//showing pop window of the trading box
const trade_name = document.querySelector('.name'); //the display place
const trade_title = document.querySelector('.name-display'); //the display place

tradeButton.forEach(btn => {
    btn.addEventListener("click", function () {
        var postDiv = this.closest('div.post-word');

        var title = postDiv.querySelector('.POSTtitle').textContent.trim();
        // console.log(title);
        var owner = postDiv.querySelector('.post-owner').textContent.trim();
        // console.log(owner);

        trade_title.innerHTML = title;
        trade_name.innerHTML = owner;

        //save to cookie ,by default of browser is closed, by default path is current page
        document.cookie = `trade_title=${encodeURIComponent(title)}; path=/`;
        document.cookie = `trade_name=${encodeURIComponent(owner)}; path=/`;

        //console.log(this.closest('div').parentElement);
    })
})


//----------------------------------------------------------------------
//display file name when upload image
const fileInput = document.getElementById('image');
const fileInputShow = document.querySelector('.picture-name');

const displayFileName = () => {
    if (fileInput.files.length > 0) {
        fileInputShow.innerHTML = fileInput.files[0].name; //display the name of the first selected file
    } else {
        fileInputShow.innerHTML = "No file chosen";
    }
}

fileInput.addEventListener('input', displayFileName);

//----------------------------------------------------------------------
//limit the description length by only max 30
document.addEventListener('DOMContentLoaded', () => {
    const description = document.getElementById('description');

    description.addEventListener('input', function () {
        let words = description.value.trim().split(/\s+/).filter(w => w.length > 0); //split by reges [one or more whitespaces]
        //filter outany empty strings that might result from spli() 

        if (words.length > 30) {
            description.value = words.slice(0, 30).join(' '); //take only first 30 and join back with white space
        }
    })

    description.addEventListener('keydown', function (e) {
        let words = description.value.trim().split(/\s+/).filter(w => w.length > 0);

        //not allow users to enter any key space
        if (words.length >= 30 && e.key !== 'Backspace' && e.key !== 'Delete' && !e.ctrlKey) {
            e.preventDefault();
        }
    })
})

//----------------------------------------------------------------------
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

        var rejectPost = this.closest('div.rejectSection');

        var itemId = rejectPost.querySelector('input[name="itemId"]').value;

        rejectItemInput.value = itemId;

        showReject();
    });
})

cancel_button.forEach(event => {
    event.addEventListener('click', hideReject);
})

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
//set the end time and button disabled
const startDate = document.getElementById('trade-datetimestart');
const endDate = document.getElementById('trade-datetimeend');

startDate.addEventListener('change', () => {
    const startingDate = new Date(startDate.value);

    //adjust UTC time and local time 
    startingDate.setHours(startingDate.getHours() - startingDate.getTimezoneOffset() / 60);

    //set to +2 hours
    startingDate.setHours(startingDate.getHours() + 2);

    endDate.value = startingDate.toISOString().slice(0, 16); //extract until minutes only "YYYY-MM-DDTHH:mm:ss.sssZ"

    timeShow.innerHTML = "*Trading must be done within 2 hours";
})

//restrict date to choose future date
const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);

const tomorrowStr = tomorrow.toISOString().slice(0, 10); //get until date

document.querySelectorAll('input[type="datetime-local"]').forEach(evt => {
    evt.min = tomorrowStr + "T00:00";
})

//----------------------------------------------------------------------
window.addEventListener('click', (e) => {
    console.log(e.target);
})
//----------------------------------------------------------------------
//show the time left 
const timeElements = document.querySelectorAll('.time');
const timeElements2 = document.querySelectorAll('.timing');

function startCountdown(selector, parentSelector, durationMs) {
    const elements = document.querySelectorAll(selector);

    elements.forEach((evt) => {
        const requestAtStr = evt.textContent.trim();
        if (!requestAtStr) return;

        const requestAt = new Date(requestAtStr);
        const expiryTime = new Date(requestAt.getTime() + durationMs);

        function timerFunc() {
            const now = new Date();
            let diffSeconds = Math.floor((expiryTime - now) / 1000);

            if (diffSeconds > 0) {
                const hours = Math.floor(diffSeconds / 3600);
                const minutes = Math.floor((diffSeconds % 3600) / 60);
                const seconds = diffSeconds % 60;

                evt.textContent = `Visible again in ${hours}h ${minutes}m ${seconds}s`;
            } else {
                evt.textContent = "Expired";
                clearInterval(timerId);

                const post = evt.closest(parentSelector);
                if (post) post.style.display = "none";
            }
        }

        const timerId = setInterval(timerFunc, 1000);
        timerFunc();
    });
}
//startCountdown('.time', '.trade-pending-post', 5 * 60 * 1000); // 5 mins 
startCountdown('.time', '.trade-pending-post', 24 * 60 * 60 * 1000); //--- 24 hours
startCountdown('.timing', '.trade-approve-post', 2 * 60 * 60 * 1000); // 2 hours

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

