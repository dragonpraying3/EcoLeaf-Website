<?php 
include_once '../topbar.php'; 
// -------------------------------------------------------------------
// Information (FAQ) Page
// Displays categorized FAQs with collapsible answers
// use js to open and hide the block
$faqSections = [
    'General' => [
        ['What is this platform used for?', 
        'This platform tracks students’ sustainability activities on APU campus, rewards participation with points and badges, and visualizes environmental impact through measurable data.'],
        ['Is the location limited to APU campus?', 
        'Yes. All activities, rewards, and events are fixed within the APU campus to ensure accurate tracking and verification.'],
    ],
    'Points, Rewards & Badges' => [
        ['How do I earn green points?', 
        'You earn points by participating in events and completing sustainability-related activities.'],
        ['What happens when I redeem a reward?', 
        'If your leaf are available, it will be deducts follow the leaf of reward item.'],
        ['Why is the reward button disabled?', 
        'The reward may be out of stock or temporarily unavailable.'],
        ['How do badges work?', 
        'Badges are unlocked permanently when you reach specific point milestones.'],
        ['Can I lose a badge if my points decrease?', 
        'No. Once unlocked, badges are permanently stay and will not be removed even if your point balance changes.'],
    ],
    'Event Attendance' => [
        ['How do I mark my attendance for an event?', 
        'After your event request is approved, the event will appear on your My Event page. Click SIGN ATTENDANCE and enter the OTP code provided by the organizer.'],
        ['Where does the OTP code come from?', 
        'The organizer generates a real-time OTP after event ended.'],
        ['What happens after attendance is verified?', 
        'If the OTP are correct: Your attendance will be marked; points will be awarded.'],
    ],
    'Organizer Post-Event Summary' => [
        ['What is the post-event summary?', 
        'After completing an event, organizers must submit a summary including: green plants planted; waste collected; recycled items; total participants.'],
        ['Where is the summary data shown?', 
        'The data will appear on organizer dashboard and admin dashboard. This contributes to overall environmental impact statistics.'],
    ],
    'Dashboards & Measurable Data' => [
        ['What data can students see on their dashboard?', 
        'Students can view: green points; CO₂ saved; badges earned; environmental impact summary; leaderboard ranking; event participation rate (%).'],
        ['What data can organizers see?', 
        'Organizers can view statistics related to their own events.'],
        ['What data is available to admins?', 
        'Admins can see overall system-wide data.'],
    ],
    'DIY Hub Trading' => [
        ['How does the DIY Hub trading work?', 
        'Buyers can TRADE with other students. The seller can ACCEPT or REJECT the request.'],
        ['What if the seller does not respond?', 
        'If the seller does not respond within 24 hours, the request expires and the post becomes visible again.'],
        ['How is a trade completed?', 
        'Both buyer and seller must click COMPLETE. Once both confirm, the trade is marked successful and the post becomes invisible.'],
        ['What happens if the trade is not completed on time?', 
        'If the trade is not completed within the allowed time range: the post automatically becomes visible again.'],
    ],
    'Carbon Footprint Calculator' => [
        ['How does the carbon footprint calculator work?', 
        'Users input their daily activity data . The system calculates carbon emissions and provides personalized advice.'],
        ['How is the carbon footprint calculated?', 
        'Your net carbon footprint is calculated by adding fuel, transport, electricity, and waste emissions using the formula.'],
    ],
    'Leaderboard' => [
        ['How is the leaderboard ranked?',
         'The leaderboard ranks students based on total points earned.'],
        ['Who can view the leaderboard?', 
        'The leaderboard is available to students only.'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information</title>
    <link rel="stylesheet" href="/EcoLeaf/assets/css/carbonccl.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/EcoLeaf/assets/css/information.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.faq-toggle').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var item = btn.closest('.faq-item');
                var open = item.classList.toggle('open');
                var icon = btn.querySelector('i');
                icon.className = open ? 'bx bx-chevron-up' : 'bx bx-chevron-down';
            });
        });
    });
    </script>
</head>

<body>
    <div class="zone-intro body-container">
        <div id="wrapper">
            <div class="title-box">
                <div class="title">
                    <div id="title-bold">Frequently Asked Questions (FAQ)</div>
                    Click the arrow to reveal the answer
                </div>
            </div>
            <div class="faq-wrapper">
                <?php foreach($faqSections as $section => $items): ?>
                <div class="faq-section-title"><?php echo htmlspecialchars($section); ?></div>
                <div class="faq-card">
                    <?php foreach($items as $f): ?>
                    <div class="faq-item">
                        <div class="faq-head">
                            <div class="faq-question"><?php echo htmlspecialchars($f[0]); ?></div>
                            <button class="faq-toggle" type="button" aria-label="Toggle answer"><i
                                    class='bx bx-chevron-down'></i></button>
                        </div>
                        <div class="faq-answer"><?php echo htmlspecialchars($f[1]); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>