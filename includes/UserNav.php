<?php 

// In UserNav.php and other relevant files
require_once 'library.php'; // Use require_once instead of include
$pdo = connectDB();
// Modify the code to retrieve the default list's entry ID for the user
$getDefaultListIDQuery = "SELECT entryID FROM Project_List WHERE userID = ? AND listName = 'plans (Default)'";
$stmtDefaultListID = $pdo->prepare($getDefaultListIDQuery);
$stmtDefaultListID->execute([$activeuser]);
$defaultListIDResult = $stmtDefaultListID->fetch();
$defaultListID = $defaultListIDResult['entryID'];

?>
<header>
    <nav class="userNav">
    <div><a class="logo underlineHoverEffect" href="Toolpage.php?listID=<?php echo $defaultListID; ?>"><img src="images/logo.png" width="150px" height="30px"></a></div>
            <div class="navigation-right">
                <ul>
                    
                    <li><a href="logout.php" class="underlineHoverEffect">Logout</a></li>
                    <li>
                        <div class="dropdown">
                            <button class="dropbtn underlineHoverEffect"><i class="fa-solid fa-user"></i>
                            </button>
                            <div class="dropdown-content">
                            <a href="UserProfile.php">My Profile</a>
                                <a href="ManagePassword.php">Change Password</a>
                                <a href="AccountDelete.php">Delete Account</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
    </nav>
</header>