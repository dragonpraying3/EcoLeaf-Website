document.addEventListener("DOMContentLoaded", function () {
    console.log("Create Event script loaded!"); 

    const desc = document.getElementById("event_description");
    const descCount = document.getElementById("descCount");
    const dateInput = document.getElementById("event_date");
    const startTimeInput = document.querySelector("[name='event_start']");
    const endTimeInput = document.querySelector("[name='event_end']");

   
    if (desc && descCount) {
       
        desc.addEventListener("input", function() {
            const currentLength = desc.value.length;
            descCount.textContent = currentLength;
            
      
            if (currentLength > 300) {
                descCount.style.color = "red";
            } else {
                descCount.style.color = "gray";
            }
        });
    }

   
    const now = new Date();
  
    const today = now.getFullYear() + '-' + 
                  String(now.getMonth() + 1).padStart(2, '0') + '-' + 
                  String(now.getDate()).padStart(2, '0');
    
    dateInput.setAttribute("min", today);

    
    function validateDateTime() {
        const selectedDate = dateInput.value;
        const currentTime = new Date();
        const currentHHMM = String(currentTime.getHours()).padStart(2, '0') + ":" + 
                            String(currentTime.getMinutes()).padStart(2, '0');

       
        if (selectedDate === today) {
            if (startTimeInput.value && startTimeInput.value < currentHHMM) {
                alert("The start time cannot be earlier than the current time for today's event.");
                startTimeInput.value = ""; 
            }
        }


        if (startTimeInput.value && endTimeInput.value) {
            if (endTimeInput.value <= startTimeInput.value) {
                alert("End time must be later than the start time.");
                endTimeInput.value = "";
            }
        }
    }


    dateInput.addEventListener("change", validateDateTime);
    startTimeInput.addEventListener("change", validateDateTime);
    endTimeInput.addEventListener("change", validateDateTime);

    document.getElementById("eventForm").addEventListener("submit", function (e) {

        const requiredFields = ["event_title", "event_description", "event_date", "event_start", "event_end", "event_venue", "event_category", "event_capacity"];
        for (let name of requiredFields) {
            let field = document.querySelector(`[name='${name}']`);
            if (!field || field.value.trim() === "") {
                alert("Please fill in all required fields.");
                e.preventDefault();
                return;
            }
        }

        const imageField = document.querySelector("[name='event_image']");
        if (!imageField || imageField.files.length === 0) {
            alert("Error：Please submit event picture");
            e.preventDefault();
            return;
        }

        if (dateInput.value < today) {
            alert("Invalid date selection.");
            e.preventDefault();
            return;
        }
    });
});