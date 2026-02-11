<?php
include_once("../topbar.php");
// get all rewards
$sql = "SELECT * FROM rewarditem ORDER BY rewardId DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoLeaf - Rewards Management</title>
    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- shared dashboard css -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/reward.css">
    <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>

    <!-- javascript for modal -->
    <script type="text/javascript" src="../assets/js/reward.js" defer></script>
</head>

<body>

    <div class="dashboard-container">

        <!-- header block -->
        <div class="page-header-block">
            <div class="header-text">
                <h1>Rewards Management</h1>
                <p>Create and manage reward items</p>
            </div>
            <!-- create button -->
            <button class="btn-create" onclick="openModal()">Create New Reward</button>
        </div>

        <!-- cards grid -->
        <div class="rewards-grid">

            <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>

            <form class="reward-card" method="POST" action="../backend/reward_update.php" enctype="multipart/form-data">

                <!-- hidden -->
                <input type="hidden" name="rewardId" value="<?= $row['rewardId'] ?>">
                <input type="hidden" name="adminId" value="<?= $row['adminId'] ?>">

                <!-- image section -->
                <div class="card-image-wrapper <?= $row['status'] === 'inactive' ? 'is-inactive' : '' ?>">
                    <span class="status-badge <?= $row['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>

                    <?php if (!empty($row['imageFile'])): ?>
                    <img src="../admin/image/<?= htmlspecialchars($row['imageFile']) ?>" alt="Reward Image">
                    <?php else: ?>
                    <img src="../assets/image/EcoLeaf.png" alt="Default Reward">
                    <?php endif; ?>
                </div>

                <!-- content -->
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="card-desc"><?= htmlspecialchars($row['description']) ?></p>

                    <!-- quick edit inputs -->
                    <div class="card-inputs-row">
                        <div class="input-group-small">
                            <label><i class="bx bxs-leaf"></i>Leaf</label>
                            <input type="number" name="pointsRequired" value="<?= $row['pointsRequired'] ?>">
                        </div>

                        <div class="input-group-small">
                            <label>Stock</label>
                            <input type="number" name="quantity" value="<?= $row['quantity'] ?>">
                        </div>
                    </div>

                    <!-- actions -->
                    <div class="card-actions">
                        <!-- update -->
                        <button type="submit" name="action" value="update" class="btn-save-card">Save Change</button>
                        <!-- delete -->
                        <button type="submit" name="action" value="delete" class="btn-delete-card"
                            onclick="return confirm('Delete this reward?');">Delete</button>
                    </div>
                </div>
            </form>

            <?php endwhile; ?>
            <?php else: ?>

            <p class="no-rewards">
                No rewards found.
            </p>

            <?php endif; ?>

        </div>
    </div>

    <!-- create new reward modal -->
    <div class="modal-overlay" id="createRewardModal">
        <div class="modal-card">

            <div class="modal-header">
                <div class="modal-title">
                    <h2>Create New Reward</h2>
                    <p>Add a new reward item to the shop</p>
                </div>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>

            <form class="modal-body" method="POST" action="../backend/reward_create.php" enctype="multipart/form-data">

                <!-- hidden inputs -->
                <input type="hidden" name="adminId" value="<?= $_SESSION['user']['adminId'] ?>">
                <input type="hidden" name="status" value="active">

                <!-- item name -->
                <div>
                    <label class="modal-label">Item Name</label>
                    <input type="text" name="name" class="modal-input" required>
                </div>

                <!-- description -->
                <div>
                    <label class="modal-label">Description</label>
                    <textarea name="description" class="modal-input"></textarea>
                </div>

                <!-- points and inventory row -->
                <div class="row-inputs">
                    <div class="row-input">
                        <label class="modal-label">Leaf for trade</label>
                        <input type="number" name="pointsRequired" class="modal-input" min=1 required>
                    </div>
                    <div class="row-input">
                        <label class="modal-label">Inventory</label>
                        <input type="number" name="quantity" class="modal-input" min=1 required>
                    </div>
                </div>

                <!-- image upload -->
                <div>
                    <label class="modal-label">Image</label>
                    <label class="upload-area">
                        <input type="file" name="imageFile" id="image" accept="image/*">
                        <span class="upload-text">Click to upload image</span>
                    </label>
                    <small class="hint-text">recommended image size: 800 x 600 px</small>
                    <small id="imgError" class="error-text"></small>
                    <small id="imgSuccessful" class="success-text"></small>
                </div>

                <!-- submit button -->
                <button type="submit" class="btn-create-modal">Create Reward</button>

            </form>
        </div>
    </div>

    <?php
$messages = [
    'error' => [
        'name_required'  => 'please enter name',
        'invalid_points' => 'invalid points value',
        'invalid_stock'  => 'invalid stock value',
        'missing_image'  => 'please upload an image',
        'db_error'       => 'database error, please try again',
    ],
    'success' => [
        'reward_created' => 'reward created successfully',
        'reward_updated' => 'reward updated successfully',
        'reward_deleted' => 'reward deleted successfully',
    ],
];

$msg = null;

if (isset($_GET['error']) && isset($messages['error'][$_GET['error']])) {
    $msg = $messages['error'][$_GET['error']];
}

if (isset($_GET['success']) && isset($messages['success'][$_GET['success']])) {
    $msg = $messages['success'][$_GET['success']];
}

if ($msg !== null):
?>
    <script>
    alert('<?php echo ($msg); ?>');
    </script>
    <?php endif; ?>
</body>

</html>