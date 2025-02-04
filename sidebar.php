<?php
  include 'init.php'; // Include session handling and login check
  include 'db_connect.php';

  // Fetch user details
  $user_id = $_SESSION['user_id'];
  $query = "SELECT firstname, lastname, avatar FROM users WHERE user_id = ?";
  $stmt = $con->prepare($query);
  if (!$stmt) {
      die("Error preparing query: " . $con->error);
  }

  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  $firstname = $user['firstname'];
  $lastname = $user['lastname'];
  $avatar = $user['avatar'];

  // Generate initials if avatar is not available
  $initials = strtoupper($firstname[0] . $lastname[0]);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<link rel="stylesheet" href="sidebar.css">

<div class="main-sidebar">

    <!-- Back Button -->
    <div class="back-button">
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>


    <a href="#" class="brand-link">
        <div class="user-info">
            <?php if ($avatar): ?>
                <img src="uploads/avatars/<?php echo $avatar; ?>" alt="User Avatar" class="user-avatar">
            <?php else: ?>
                <div class="user-initials"><?php echo $initials; ?></div>
            <?php endif; ?>
            <span><b><?php echo $firstname . ' ' . $lastname; ?></b></span>
        </div>
    </a>

    <div class="sidebar">
       <ul class="nav">
            <li class="nav-item">
                <a href="dashboard.php?page=item_list" class="nav-link sidebar-link">Items</a>
            </li>
            <li class="nav-item">
                <a href="dashboard.php?page=borrow_list" class="nav-link sidebar-link">Borrowing List</a>
            </li>
            <li class="nav-item">
                <a href="dashboard.php?page=borrow_history" class="nav-link sidebar-link">Borrowers</a>
            </li>
            <li class="nav-item">
                <a href="dashboard.php?page=report" class="nav-link sidebar-link">Reports</a>
            </li>
        </ul>

    </div>
</div>

<script src="sidebar.js"></script>
</body>
</html>