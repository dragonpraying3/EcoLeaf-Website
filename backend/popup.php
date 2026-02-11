<!--connect to boxicon and css-->
<link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free.min.css">
<link rel="stylesheet" href="/EcoLeaf/assets/css/popup.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<?php 
//use multi-dimensional Associative Array
$popups = [
    'post-approve' => [
        'colour' => 'green',
        'icon' => 'fa-check-circle',
        'title' => 'Post Approve',
        'message' => 'The post has been successfully approved and is now visible to students.'
    ],

    'post-reject' => [
        'colour' => 'red',
        'icon' => 'fa-times-circle',
        'title' => 'Post Reject. Reason Sent',
        'message' => 'The post has been rejected. The reason has been sent to the user.'
    ],

    'post-request-sent'=> [
        'colour' => 'yellow',
        'icon' => 'fa-check-circle',
        'title' => 'Post Request Sent',
        'message' => 'Your post has been successfully submitted for approval.'
    ],

    'trade-request-sent'=>[
        'colour' => 'yellow',
        'icon' => 'fa-check-circle',
        'title' => 'Trade Request Sent',
        'message' => 'Your trade request has been sent to the seller. Please wait for their response.'
    ],
    
    'trade-reject'=>[
        'colour' => 'red',
        'icon' => 'fa-times-circle',
        'title' => 'Trade Request Sent',
        'message' => 'Your trade request has been rejected. Please check the reason provided.'
    ],
    
    'trade-approve'=> [
        'colour' => 'green',
        'icon' => 'fa-check-circle',
        'title' => 'Trade Approve Sent',
        'message' => 'Your trade request has been approved. You may proceed with the transaction.'
    ],

    'trade-complete' => [
        'colour' => 'green',
        'icon' => 'fa-check-circle',
        'title' => 'Trade Complete',
        'message' => 'The trade has been successfully completed. Thank you!'
    ],

    'mark-complete' => [
        'colour' => 'yellow',
        'icon' => 'fa-check-circle',
        'title' => 'Mark Completion',
        'message' => 'The trade has been marked as completed.'
    ],

    'badge-complete' => [
        'colour' => 'green',
        'icon' => 'fa-check-circle',
        'title' => 'Badge Created',
        'message' => 'The badge now is available to the public. Thank you!'
    ],

    'badge-hidden' => [
        'colour' => 'green',
        'icon' => 'fa-check-circle',
        'title' => 'Badge Hidden',
        'message' => 'The badge has been successfully hidden.'
    ],

    'badge-unhidden' => [
        'colour' => 'green',
        'icon' => 'fa-check-circle',
        'title' => 'Badge Unhidden',
        'message' => 'The badge is now visible to the public.'
    ],

    'badge-delete' => [
        'colour' => 'red',
        'icon' => 'fa-times-circle',
        'title' => 'Badge Deleted',
        'message' => 'The badge has been successfully deleted.'
    ],

    'badge-gain' => [
        'colour' => 'green',
        'icon' => 'fa-solid fa-medal',
        'title' => 'New Badge Earned',
        'message' => 'You have unlocked a new badge!'
    ]
];

?>

<?php foreach($popups as $id => $content): ?>
<!--$id is key, $content is value-->
<div class="notify <?php echo $content['colour'];?> hidden" id="<?php echo $id;?>">
    <div class="room">
        <i class="fas <?php echo $content['icon'];?> pop"></i>
    </div>
    <div class="content">
        <span class="Bigtitle"><?php echo $content['title'];?></span>
        <div class="mssg"><?php echo $content['message'];?></div>
    </div>
    <div class="close-btn">
        <i class="fas fa-times off"></i>
    </div>
</div>
<?php endforeach; ?>


<script>
window.addEventListener('click', (e) => {
    console.log(e.target);
})

const closeButton = document.querySelectorAll('.close-btn');

closeButton.forEach(evt => {
    evt.addEventListener('click', function(e) {
        const block = e.currentTarget.closest('div.notify');
        block.classList.add('hide');

        setTimeout(function() {
            block.style.display = " none";
        }, 4000);
    });
})

function showRelevantPop(id, duration = 4000) {
    const popup = document.getElementById(id);
    if (!popup) return;

    //ensure the pop is display
    popup.style.display = "flex";
    popup.classList.remove('hide');
    popup.classList.add('show');

    //autoHide
    setTimeout(() => {
        popup.classList.remove('show');
        popup.classList.add('hide');
        setTimeout(() => {
            popup.style.display = "none";
        }, 500);
    }, duration);
}
</script>