<?php

    
    //Includeing the database connection file
    include 'includes/library.php';
    //connecting Database
    $pdo =connectDB();

    //running session to get results for display
    session_start();
    

    //If there is no action user (passed from session)- the user is redirected to the login page to login using an account
    if(empty($_SESSION['ActiveuserID'])){
        header("Location: login.php");
    }

    //declaring the active user
    $activeuser = $_SESSION['ActiveuserID'];


    //Getting all the Lists from the database name project List
    /* Database Table Name: Project_List
    entryID - autoItterating
    userID - this is specfic to the user and is taken from the user_Record
              it is used to filter out the lists which is specific to the user 
              this function helps for every user to have lists specific to the user

    listName - tells the List name - Varchar value 
    expiryDate - Takes in expiry date
    privateYN - save Y if the list is private - saves N if the list is public 
    listPassword - if the list is private then a password is saved else Null
    
    
    
    */

    // Retrieve the listID from the URL
    $entryID = $_GET['listID'] ?? null;

    // Check if the listID is valid for the logged-in user
    $checkListQuery = "SELECT * FROM Project_List WHERE entryID = ? AND userID = ?";
    $stmtCheckList = $pdo->prepare($checkListQuery);
    $stmtCheckList->execute([$entryID, $activeuser]);
    $validList = $stmtCheckList->fetch();

    if (!$validList) {
        
        header("Location: error.php"); // Create an error.php page for handling such errors
        exit();
    }

    $listnamequery="SELECT * FROM `Project_List` Where userID=?";
    $stmt = $pdo->prepare($listnamequery);
    $stmt->execute([$activeuser]);
    $ListNames=$stmt->fetchAll();



    //If there is no list created then we get a default list and make the insertion into the default list 

    if(empty($result)){
        $query3 = "SELECT * FROM `wishlistitems` WHERE entryID=?";
        $stmtZero = $pdo->prepare($query3);
        $result=$stmtZero->fetchAll();
    }
    


   
   //This entry id is used to make the list dynamic -- we declare this so wehn we click the list on left list menu the list item menu refreshes

    $entryID = $_GET['listID']?? 0;
   //ensuring entryID is not null
    if(!is_null($entryID)){

        //This for making the entry ID of the wish list items
       //Takes all the data from wishlistitems table on the database

       /*Database Table name: wishlistitems

       itemID - this is autoitterating and a new id is created on every insertion 
       entryID - this value is take from the active list entryID and then inserted - this is used to make the data relational
       ItemName - Stores the Item Name 
       description - stores item description 
       link - stores the link to the item 
       checked - stores if the item is already checked of not
       imagelink - stores the link to image here if it is not inserted then it will be null

       
       
       */

        $query1 = "SELECT * FROM `wishlistitems` WHERE entryID=?";
        $stmtCheck = $pdo->prepare($query1);
        $stmtCheck->execute([$entryID]);
        $result=$stmtCheck->fetchAll();

    }
    //Function to delete item on click trash button specifc to the item
    if(isset($_POST["deleteitem"])){
        echo "eiutgniuentrf";//testing
        echo $_POST["deleteitem"];//Testing
        $deleteitemid = $_POST["deleteitem"];//takes the value of the button of the trash button which saves the itemID
        $DeletespecificItem = "DELETE FROM wishlistitems WHERE itemID=?";  //selects the user filtered from the itemID
        $stmtDeleteitem = $pdo->prepare($DeletespecificItem);
        $stmtDeleteitem->execute([$deleteitemid]);
        $itemdeleted=$stmtDeleteitem->fetch();
        header('Location: '.$_SERVER['REQUEST_URI']);//Redirects to the same page ---- referenced from stackoverflow

    }
    // Modify the code to retrieve the default list's entry ID for the user
    $getDefaultListIDQuery = "SELECT entryID FROM Project_List WHERE userID = ? AND listName = 'plans (Default)'";
    $stmtDefaultListID = $pdo->prepare($getDefaultListIDQuery);
    $stmtDefaultListID->execute([$activeuser]);
    $defaultListIDResult = $stmtDefaultListID->fetch();
    $defaultListID = $defaultListIDResult['entryID'];

    if (isset($_POST['DeleteList'])){
        
        if($entryID != 0){
            $DeleteProject = "DELETE FROM Project_List WHERE entryID=?";
            $stmtDeleteProject = $pdo->prepare($DeleteProject);
            $stmtDeleteProject->execute([$entryID]);
            $ProjectDeleteResult = $stmtDeleteProject->fetch();
    
            $DeleteListItems = "DELETE FROM wishlistitems WHERE entryID=?";
            $stmtDeleteList = $pdo->prepare($DeleteListItems);
            $stmtDeleteList->execute([$entryID]);
            $ItemsDeleteResult = $stmtDeleteList->fetch();
            
            if(!$ProjectDeleteResult && !$ItemsDeleteResult){
                // Redirect to the user's default list
                header("Location: Toolpage.php?listID={$defaultListID}");
                exit();
            }  
        }
    }
    

    //Delete items delete all the items of a particular list

    if (isset($_POST['DeleteItems'])){
            $DeleteListItems = "DELETE FROM wishlistitems WHERE entryID=?";  //selects the user filtered from the username
            $stmtDeleteList = $pdo->prepare($DeleteListItems);
            $stmtDeleteList->execute([$entryID]);
            $ItemsDeleteResult=$stmtDeleteList->fetch();
            
            if(!$ProjectDeleteResult && !$ItemsDeleteResult){
                header("Location: Toolpage.php?listID={$entryID}");
                exit();
            }  
        
    }

    

    //In order to display the name of the list: which is opened.
    $getListName = "SELECT * FROM Project_List WHERE entryID=?";  
    $StmtListName = $pdo->prepare($getListName);
    $StmtListName->execute([$entryID]);
    $ListNameResult=$StmtListName->fetch();
    $nameofList = $ListNameResult['listName']??null;


    
    if (isset($_POST['makelist'])) {
        $itemname = $_POST['itemname'] ?? null;
        $checkvalue = $_POST['checked'] ?? null;
        $itemdescription = $_POST['itemdescription'] ?? null;
        
        // Check if itemdescription is empty, and if so, set it to "none"
        if (empty($itemdescription)) {
            $itemdescription = "none";
        }
        
        $checkvalue = "N";
    
        $queryInsert = "INSERT INTO wishlistitems VALUES (NULL,?,?,?,?)";
        $stmtInsert = $pdo->prepare($queryInsert);
        $stmtInsert->execute([$entryID, $itemname, $itemdescription, $checkvalue]);
    
        header("Location: Toolpage.php?listID={$entryID}");
        exit();
    }
    
    if (isset($_POST['make-new-list'])) {
        // POST - FORM NAMES
        /* listname ---- refers to the textbox with the list name
        expiry -- takes the date HTML5 date type data
        privateOrPublic --- refers to the checkbox, it tells if the list is public or private
        listpassword --- if the list is public or private ---- this needs to be added with JavaScript - on change trigger of the checkbox
        */
    
        // This section takes the values from the form
        // FORM- LIST
        // Contains
        $listName = $_POST['listname'] ?? null; // List Name
        $privateYN = $_POST['private'] ?? 'N';
        if ($privateYN == "Y") {
            $listPassword = $_POST['listpassword'] ?? null;
        } else {
            $listPassword = "N";
        }
    
        $activeUser = $_SESSION['ActiveuserID'] ?? null; // Takes in the active user when the session is started and the active user is set from the database
    
        // Convert the list name to lowercase
        $listNameLower = strtolower($listName);
    
        // Check if the list name already exists (case-insensitive) for the active user
        $checkListNameQuery = "SELECT * FROM Project_List WHERE userID = ? AND LOWER(listName) = ?";
        $stmtCheckListName = $pdo->prepare($checkListNameQuery);
        $stmtCheckListName->execute([$activeUser, $listNameLower]);
    
        if ($stmtCheckListName->rowCount() > 0) {
            // The list name already exists for this user
            // Display an alert box to notify the user
            echo '<script>alert("List name already exists. Please use a different name.");</script>';
        } else {
            // The list name is unique, so insert it into the database
            $queryInsert = "INSERT INTO Project_List VALUES (NULL,?,?,?,?)"; // entryId, userID, listName, expiryDate, Private, listPassword
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->execute([$activeUser, $listName, $privateYN, $listPassword]);
    
            // Get the last inserted listID
            $listID = $pdo->lastInsertId();
    
            // Redirect to Toolpage.php with the listID parameter
            header("Location: Toolpage.php?listID={$listID}");
        }
    }
    
    
    
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Your Plan-To-Do List</title>
        <!--Stylesheets-->
        <link rel="stylesheet" href="styles/toolpage.css" />
        <!-- to style top navigation -->
        <link rel="stylesheet" href="styles/nav.css" />  
        <link rel="stylesheet" href="styles/footer.css" /> 
        <link rel="stylesheet" href="styles/media-queries.css" /> 
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">       
        <script src="https://kit.fontawesome.com/1d089da2a3.js" crossorigin="anonymous"></script>
        <script src="script/main.js"  type="text/javascript"></script>

    </head>
    <body>
        <main>
            <!--Navigation only for logined in user-->
            <?php include "includes/UserNav.php"?>
            <!--Main page-->
            <div id="toolmainpage" class="toolmainpage"><!--this is the division for the whole page for seperating css-->
                <div class="projectmenu" id="projectmenu"><!--Projectmenu-->
                    <!-- link to add list to the specifc user account -->
                    <ol class="new-list">
                        <li><a href="#" id="showPopup">&#43; Add List</a></li>
                    </ol>

                    <div class="AccountDiv">
                        <form method="post" class="AccountForm popup-form">
                        <a href="#" id="closePopup"><i class="fa-solid fa-xmark"></i></a> <!-- Close icon -->
                            <h1>Add to list</h1>
                            <div>
                                <div class="AccountRecord">
                                    <input type="text" id="listname" name="listname" placeholder="Enter List Name..." required maxlength="20">
                                </div>
                            </div>
                            <div>
                                <div id="PrivateCheck">
                                    <input type="checkbox" class="checkbox-private" name="private" id="private" value="Y" />
                                    <label for="private">Private list?</label>
                                </div>
                                <div class="hidepass" id="listpassdiv">
                                    <input type="text" id="listpassword" name="listpassword" placeholder="Enter a Password..." maxlength="50" style="display: none;">
                                </div>
                            </div>
                            <div class="button-add-list">
                                <div>
                                    <button type="submit" class="button-confirm" name="make-new-list">Confirm</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <table>                    
                        <!-- Getting all list from the database specific to the database -->
                        <?php foreach ($ListNames as $row): ?>
                        <tr>
                        
                            <td class="List-name-delete"><a  href="Toolpage.php?listID=<?php echo $row['entryID'];?>" ><i class="fa-solid fa-angle-right"></i>  <?php    echo $row['listName']  ?></a>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                  
                    </table>
                </div>


                <div class="listcontenthead" id="listcontent"><!--Connecting the SearchAPI-->                
                <form method="post">
                    <h1>
                        <?php echo "List: " . $nameofList; ?>
                        <?php if ($nameofList !== "plans (Default)"): ?>
                            <button type="submit" class="Delete-list" name="DeleteList"><i class="fa-solid fa-trash"></i></button>
                        <?php endif; ?>
                    </h1>
                </form>
                    <div class="activities-container">
                        <div class="ActivityClass">
                            <button type="submit" role="button" class="button-85" id="ActivitySuggestion" name="getAPI" value="SearchAPI">Suggest Some Activity</button>
                            
                        </div>
                        <div id="PrintActivity"></div>
                    <!-- Share and Copy to Clipboard -->
                        <input type="text" class="link-list" value="https://loki.trentu.ca/~sidaksinghsra/To-Do-List/verifyshare.php?listID=<?php echo $entryID?>" id="myInput">
                        <div class="tooltip">
                            <button class="copybutton button-85"  role="button" id="copyButton">
                                Copy to Share Link
                            </button>
                            <span class="tooltiptext" id="tooltipText">Link Copied!</span>
                        </div>
                    </div>                   
                    <!-- Share and Copy to Clipboard -->
                    <div>
                    <form method="post" enctype="multipart/form-data" class="AccountForm">
                            <div>
                                <div class="input-row">
                                    <input type="text" id="listname"  name="itemname" placeholder="Enter Item Name" required maxlength="30">  <input type="text" id="listname"  name="itemdescription" placeholder="Any Description?" maxlength="200">
                                    <button type="submit" class="button-confirm" name="makelist">ADD</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class ="listcontent"> 
                        <form method ="post" class="list-table-buttons">
                            <div class="left-container">
                            <?php if (empty($result)): ?>
                                <p class="empty-table">The Items for this list is empty right now.</br><span class="text-highlight"> "Unleash Your Imagination: Create your to-do list and organize your work." </span></p>
                            <?php else: ?>
                                <table> 
                                    <!-- Table header for displaying list items -->
                                    <tr class="tableTop">
                                        <th>Item Name</th>
                                        <th>Description</th>
                                        <th style="
                                                width: 100px;
                                            "><button type="submit" class="button-24" name="DeleteItems">- Delete All</button></th>
                                        
                                    </tr>

                                    <!-- Displaying all the list items which have been checked - this would have three things: one checkbox, second label which is triggered, third trash button -->
                                    <?php foreach ($result as $row2): ?> 
                                        <tr class="Row">
                                            <!-- Just a checkbox specific to the item -->
                                            <td><label for="gift"><?php echo $row2['ItemName']; ?></label></td>
                                            <!-- Label for the item - tells the item name - also this is triggered with JavaScript -->
                                            <td>
                                                <label for="gift">
                                                    <?php
                                                    if (empty($row2['description'])) {
                                                        echo 'none';
                                                    } else {
                                                        echo $row2['description'];
                                                    }
                                                    ?>
                                                </label>
                                            </td>

                                            <!-- Trash button which when clicked will delete the item -->
                                            <td><button type="submit" class="trash-button" name="deleteitem" value="<?php echo $row2['itemID']; ?>"><i class="fa-solid fa-trash"></i></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    
                                </table>
                            <?php endif; ?>
                            </div>
                        </form>

                    </div> 
                   
                </div>
            </div>
        </main>
         <script>

            // JavaScript to toggle the visibility of the password input field
                const privateCheckbox = document.getElementById("private");
                const passwordField = document.getElementById("listpassword");
                const listForm = document.getElementById("listForm");

                privateCheckbox.addEventListener("click", function () {
                    if (privateCheckbox.checked) {
                        passwordField.style.display = "block";
                        passwordField.setAttribute("required", "required"); // Make password field required
                    } else {
                        passwordField.style.display = "none";
                        passwordField.removeAttribute("required"); // Remove required attribute
                    }
                });


                document.getElementById('showPopup').addEventListener('click', function () {
                const popupForm = document.querySelector('.popup-form');
                popupForm.classList.add('show');

                // Add an event listener for the close icon
                document.getElementById('closePopup').addEventListener('click', function (e) {
                    e.preventDefault(); // Prevent the default link behavior
                    popupForm.classList.remove('show'); // Hide the pop-up form
                });
                });

                document.querySelector('.popup-form button[name="makelist"]').addEventListener('click', function () {
                    const popupForm = document.querySelector('.popup-form');
                    popupForm.classList.remove('show');
                });

        </script>
    </body>
</html>