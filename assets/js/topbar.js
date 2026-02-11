const button_event = document.querySelector('.event');
const event_dropdown = document.querySelector('.event-dropdown');
const button_diy = document.querySelector('.DIY');
const diy_dropdown = document.querySelector('.DIY-dropdown');

const student_navbar = document.querySelector('.nav-student');
const organizer_navbar = document.querySelector('.nav-organizer');
const admin_navbar = document.querySelector('.nav-admin');

//show student navigation bar if user role is student
if (user_role === 'student') {
    student_navbar.style.display = 'block';
    organizer_navbar.style.display = 'none';
    admin_navbar.style.display = 'none';
} else if (user_role === 'organizer') {
    student_navbar.style.display = 'none';
    organizer_navbar.style.display = 'block';
    admin_navbar.style.display = 'none';
} else if (user_role === 'admin') {
    student_navbar.style.display = 'none';
    organizer_navbar.style.display = 'none';
    admin_navbar.style.display = 'block';
}

//dropdown option for student EVENT navigation 
let dropDownVisibleEvent = false;
//define function using variable =arrow function
const showDropDownEvent = () => {
    const rect = button_event.getBoundingClientRect(); //get the top, left, bottom, right position of the button

    event_dropdown.style.left = rect.left + window.scrollX + (-15) + "px";   // aligned to event
    event_dropdown.style.visibility = "visible";
    event_dropdown.style.opacity = "1";
    event_dropdown.style.transition = "all 0.5s";
    dropDownVisibleEvent = true;
}

const hideDropdownEvent = () => {
    event_dropdown.style.visibility = "hidden";
    event_dropdown.style.opacity = "0";
    event_dropdown.style.transition = "all 0.5s";
    dropDownVisibleEvent = false;
}

//PC hover
button_event.addEventListener('mouseenter', showDropDownEvent);
event_dropdown.addEventListener('mouseenter', showDropDownEvent);
button_event.addEventListener('mouseleave', hideDropdownEvent);
event_dropdown.addEventListener('mouseleave', hideDropdownEvent);

//dropdown option for student DIY navigation 
let dropDownVisibleDIY = false;
//define function using variable =arrow function
const showDropDownDIY = () => {
    const rect = button_diy.getBoundingClientRect(); //get the top, left, bottom, right position of the button

    diy_dropdown.style.left = rect.left + window.scrollX + (-15) + "px";   // aligned to event
    diy_dropdown.style.visibility = "visible";
    diy_dropdown.style.opacity = "1";
    diy_dropdown.style.transition = "all 0.5s";
    dropDownVisibleDIY = true;
}

const hideDropdownDIY = () => {
    diy_dropdown.style.visibility = "hidden";
    diy_dropdown.style.opacity = "0";
    diy_dropdown.style.transition = "all 0.5s";
    dropDownVisibleDIY = false;
}

//PC hover
button_diy.addEventListener('mouseenter', showDropDownDIY);
diy_dropdown.addEventListener('mouseenter', showDropDownDIY);
button_diy.addEventListener('mouseleave', hideDropdownDIY);
diy_dropdown.addEventListener('mouseleave', hideDropdownDIY);

//phone click
['click', 'touchstart'].forEach(evt => {
    button_event.addEventListener(evt, (event) => { //click is slow 300ms
        event.stopPropagation(); //not bubbling the document itself 

        if (!dropDownVisibleEvent) {
            showDropDownEvent();
        } else {
            hideDropdownEvent();
        }
    });

    button_diy.addEventListener(evt, (event) => { //click is slow 300ms
        event.stopPropagation(); //not bubbling the document itself 

        if (!dropDownVisibleDIY) {
            showDropDownDIY();
        } else {
            hideDropdownDIY();
        }
    });
})

document.addEventListener('touchstact', () => {
    if (dropDownVisibleEvent) hideDropdownEvent();
    if (dropDownVisibleDIY) hideDropdownDIY();
});

//active class
const links = document.querySelectorAll('.navbar ul li a');
links.forEach(link => {
    if (link.href === window.location.href) { //check if on the current link page 
        link.classList.add('active');
    }
});
//active icon
const icons = document.querySelectorAll('.small-icon a');
icons.forEach(icon => {
    if (icon.href === window.location.href) {
        icon.classList.add('active');
    }
})