<?php
// Initialize the session
require_once "config.php";
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
  header("location: index.html");
  exit;
}
$msg = "";
$property_id = $_GET['property_id'];

// If upload button is clicked ...
if (isset($_POST['upload'])) {
  // Get image name
  $document = $_FILES['document']['name'];
  // Get text
  $document_text = mysqli_real_escape_string($link, $_POST['document_text']);


  // image file directory
  $target = "documents/";

  $sql = "INSERT INTO document (document, document_text,property_id) VALUES ('$document', '$document_text','$property_id')";
  // execute query
  mysqli_query($link, $sql);

  if (move_uploaded_file($_FILES['document']['tmp_name'], $target)) {
    $msg = "document uploaded successfully";
  }else{
    $msg = "Failed to upload document";
  }
}
$result = mysqli_query($link, "SELECT document, document_text FROM document WHERE property_id = $property_id ");
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" href="ico2.ico" type="image/x-icon">
  <meta charset="utf-8">
  <title>Landlord Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  <link rel="stylesheet" href="css/myStyle.css">
  <link href='css/navStyle.css' rel='stylesheet' type='text/css'>
  <script src="js/script.js"></script>
  <link rel="css/stylesheet" href="index-style.css">
</head>

<body>
  <header class="stickyNav">
      <div class="mainHeader background-box background-color ", style="background-color:#B71C1C">
      <div class="mainHeader-grid fs">

        <div class="grid-column-33-per content-align-left">
          <div class="menuNavButton">
            <span onclick="toggleNav()">
              <img class="menuNavImg" width="25" height="25" src="MenuNav.png" alt="Menu Navigation Button">
            </span>
          </div>

          <nav id="navigationBar" class="sideNav background-color">
            <div class="navContainer">
              <ul class="navMenu">

                <li class="navItems">
                  <a href="landlord-home.php">Home</a>
                </li>

                <li class="navItems">
                  <a href="chat.php">Chat</a>
                </li>

                <li class="navItems">
                  <a>Acount</a>
                </li>

                <li class="navItems">
                  <a href ="property-main.php"> Property Management</a>
                </li>

                <li class="navItems">
                  <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
                </li>

                <li class="navItems">
                  <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
                </li>

              </ul>

              <footer>
                <p>something</p>
              </footer>

            </div>
          </nav>
        </div>

        <div class="grid-column-33-per content-align-center">
          <div class="headerLogo">
            <p>logo</p>
          </div>
        </div>

        <div class="grid-column-33-per content-align-right">
          <div class="socialLayout">
            <ul>
              <li>
                <a href="https://twitter.com/leoclarke_" target="_blank">
                  <img height="20" width="20" src="socials/twitter.png" alt="Twitter Icon">
                </a>
              </li>
              <li>
                <a href="https://www.instagram.com/leoclarke_/" target="_blank">
                  <img height="20" width="20" src="socials/instagramPNG.png" alt="Instagram Icon">
                </a>
              </li>
              <li>
                <a href="https://www.linkedin.com/in/leo-clarke-663315157/" target="_blank">
                  <img height="20" width="20" src="socials/linkedinPNG.png" alt="Linkedin Icon">
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>

  <div id="main" class="main">
    <h1><?php echo htmlspecialchars($_GET["property_name"]); ?></h1>



    <button onclick="document.getElementById('id01').style.display='block'" style="width:auto;">Add tenant</button>

    <div id="id01" class="modal">

      <form method="post" action="add-property.php">
        <div class="input-group">
          <?php
          $sql = "SELECT * FROM tenant ";
          $result = $link->query($sql);

          if ($result->num_rows > 0) {
            ?><ul><?php
            while($fc_user = $result->fetch_assoc()) {
              ?>
              <li>
                <a href="profile.php?fname=<?php echo $fc_user['fname']; ?>">
                  <?php echo $fc_user['fname']; ?>
                </a>
                <a href="link.php?uid=<?php echo $fc_user['tenant_id']; ?> & property_id=<?php echo $_GET['property_id']; ?>">[add]</a>
              </li>
              <?php
            }
            ?></ul><?php
          } else {
            ?>
            <p class="text-center">No users to add!</p>
            <?php
          }
          ?>
        </div>
        <div class="container" style="background-color:#f1f1f1">
  <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
  <span class="psw">Forgot <a href="#">password?</a></span>
</div>
      </form>
    </div>

    <?php
    $prop_id = $_GET["property_id"];
    $sql = "SELECT occupant.occupant_id, tenant.fname, tenant.lname, tenant.tenant_id FROM occupant INNER JOIN tenant ON occupant.tenant_id = tenant.tenant_id  where property_id = '$prop_id' ";
    $result = $link->query($sql);

    if ($result->num_rows > 0) {
      ?><ul><?php
      while($fc_user = $result->fetch_assoc()) {
        ?>
        <li>
          <a href="tenant-profile.php?tenant_id=<?php echo $fc_user['tenant_id']; ?>">
            <?php echo $fc_user['fname']; ?> <?php echo $fc_user['lname']; ?>
          </a>
          <a href="remove-tenant.php?uid=<?php echo $fc_user['tenant_id']; ?>">[Remove]</a>
        </li>
        <?php
      }
      ?></ul><?php
    } else {
      ?>
      <p class="text-center">No tenants currenly live here</p>
      <?php
    }
    ?>
        <h1>document upload</h1>
    <?php
    while ($row = mysqli_fetch_array($result)) {
      echo "<div id='img_div'>";
      echo "<img src='images/".$row['document']."' >";
      echo "<p>".$row['document_text']."</p>";
      echo "</div>";
    }
    ?>
    <form method="POST"  enctype="multipart/form-data">
      <input type="hidden" name="size" value="1000000">
      <div>
        <input type="file" name="document">
      </div>
      <div>
        <textarea
        id="text"
        cols="40"
        rows="4"
        name="document_text"
        placeholder="Say something about this image..."></textarea>
      </div>
      <div>
        <button type="submit" name="upload">POST</button>
      </div>
    </form>
        <h1>View pictures</h1>
    <?php
    $property_id = $_GET['property_id'];
    $result = mysqli_query($link, "SELECT image, image_text FROM image WHERE property_id = $property_id ");
    ?>

    <?php
    while ($row = mysqli_fetch_array($result)) {
      echo "<div id='img_div'>";
      echo "<img src='images/".$row['image']."' >";
      echo "<p>".$row['image_text']."</p>";
      echo "</div>";
    }
    ?>
  </div>

</body>
</html>
