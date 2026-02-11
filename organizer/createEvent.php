<?php
include_once '../topbar.php'; 

include_once '../backend/database.php';


if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'organizer') {
    header("Location: /EcoLeaf/index.php");
    exit();
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>


    <link rel="stylesheet" href="/EcoLeaf/assets/css/topbarDesign.css">

    <link rel="stylesheet" href="../assets/css/createEvent.css">

</head>

<body>




    <div class="create-container">

        <h2>Create New Event</h2>
        <p class="subtitle">
            Create a sustainability event for APU students. Events require admin approval before publishing.
        </p>


        <form id="eventForm" class="event-form" method="POST" action="../backend/createEvent_process.php"
            enctype="multipart/form-data">
            <div class="form-grid">

                <div class="area-title">
                    <label>Event Title</label>
                    <input type="text" name="event_title" placeholder="Enter event title">
                </div>

                <div class="area-description">
                    <label>Description</label>
                    <textarea name="event_description" id="event_description"
                        placeholder="Enter event description"></textarea>
                    <div style="font-size: 12px; color: gray;">
                        <span id="descCount">0</span>/300 characters
                    </div>
                </div>

                <div class="area-image">
                    <label>Image</label>
                    <input type="file" name="event_image" accept="image/*">
                </div>

                <div class="area-leaf">
                    <label>Leaf</label>
                    <input type="number" name="event_leaf" id="event_leaf" min="0" placeholder="Enter leaf amount">
                </div>

                <div class="area-date">
                    <label>Date</label>
                    <input type="date" name="event_date" id="event_date">
                </div>

                <div class="area-start">
                    <label>Start Time</label>
                    <input type="time" name="event_start">
                </div>

                <div class="area-end">
                    <label>End Time</label>
                    <input type="time" name="event_end">
                </div>

                <div class="area-venue">
                    <label>Venue</label>
                    <select name="event_venue">
                        <option value="">Select event location</option>
                        <option value="APU Lecture Hall 1">APU Lecture Hall 1</option>
                        <option value="APU Block A Courtyar">APU Block A Courtyar</option>
                        <option value="APU Swimming Pool">APU Swimming Pool</option>
                        <option value="Outdoor Carpark Area">Outdoor Carpark Area</option>
                        <option value="APU Main Entrance">APU Main Entrance</option>
                    </select>
                </div>

                <div class="area-category">
                    <label>Category</label>
                    <select name="event_category">
                        <option value="">Select event category</option>
                        <option value="Lecture Promotion">Lecture Promotion</option>
                        <option value="Tree Planting">Tree Planting</option>
                        <option value="Recycle">Recycle</option>
                    </select>
                </div>

                <div class="area-capacity">
                    <label>Participant Capacity</label>
                    <input type="number" name="event_capacity" min="1" placeholder="Enter capacity">
                </div>

            </div>

            <button type="submit" class="submit-btn">Submit Proposal</button>
        </form>


    </div>


    <script src="../assets/js/createEvents.js"></script>


</body>

</html>