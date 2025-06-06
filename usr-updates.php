<?php
 // USER DETAILS UPDATE START
include_once('../dbconnect.php');

if (isset($_POST['update'])) {

    $fname = mysqli_real_escape_string($connect, $_POST['fname']);
    $lname = mysqli_real_escape_string($connect, $_POST['lname']);
    $phn1 = mysqli_real_escape_string($connect, $_POST['phn1']);
    $ucountry = mysqli_real_escape_string($connect, $_POST['ucountry']);
    $uaddrs = mysqli_real_escape_string($connect, $_POST['uaddrs']); 
    $usor = mysqli_real_escape_string($connect, $_POST['usor']); 
    $uzip = mysqli_real_escape_string($connect, $_POST['uzip']); 
    $dob = mysqli_real_escape_string($connect, $_POST['dob']);
    $sexm = isset($_POST['sexm']) ? mysqli_real_escape_string($connect, $_POST['sexm']) : null;
    $sexf = isset($_POST['sexf']) ? mysqli_real_escape_string($connect, $_POST['sexf']) : null;
  
    // User-inputted password for verification
    $input_password = mysqli_real_escape_string($connect, $_POST['password']);
    $user_id = $_SESSION['id'];

    // Fetch the stored password from the database
    $stmt = $connect->prepare("SELECT `password` FROM `client` WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stored_password = $row['password'];

    // Direct password comparison (assuming no hashing is used)
    if ($input_password === $stored_password) { 
        // Passwords match; proceed with asking user for confirmation via SweetAlert
        // Update the data in the database
        $update_stmt = $connect->prepare("UPDATE `client` SET firstname=?, lastname=?, phone=?, country=?, locate=?, u_state=?, zip_code=?, dob=?, male=?, female=? WHERE id=?");
        $update_stmt->bind_param("ssssssssssi", $fname, $lname, $phn1, $ucountry, $uaddrs, $usor, $uzip, $dob, $sexm, $sexf, $user_id);

        if ($update_stmt->execute()) {
            echo '<script>
            Swal.fire({
              title: "Confirmed!",
              text: "Your data has been updated.",
              icon: "success",
              confirmButtonColor: "#3085d6",
              confirmButtonText: "OK"
            }).then(() => {
              window.location.href = "profile";
            });
            </script>';
        } else {
            echo '<script>
            Swal.fire({
              title: "Error!",
              text: "Could not update your data.",
              icon: "error",
              confirmButtonColor: "#d33",
              confirmButtonText: "Try Again"
            });
            </script>';
        }
    } else {
        // Password does not match
        echo '<script>
        Swal.fire({
          title: "Incorrect Password",
          text: "The password you entered is incorrect. Please try again.",
          icon: "error",
          confirmButtonColor: "#d33",
          confirmButtonText: "Try Again"
        });
        </script>';
    }
}
 // USER DETAILS UPDATE END

 // USER IMAGE UPDATE START
if(isset($_POST["pupdt"])){

  $folderDir = "upload/";
  
  $filename = basename($_FILES['pass']['name']);
  
  $folderPath = $folderDir . $filename;
  
  $filetype = pathinfo($folderPath,PATHINFO_EXTENSION);
  
  $sql = "UPDATE `client` SET `passport` = '$filename' WHERE `id` = '".$_SESSION["id"]."'";
  $qu=mysqli_query($connect,$sql);
  
  if(move_uploaded_file($_FILES["pass"]["tmp_name"], $folderPath)){
  
    echo '<div class="alert alert-success">
  <strong>Success!</strong> Indicates a successful or positive action.
</div>';
  }
}
 // USER IMAGE UPDATE END
?>

<?php
// USER WITHDRAWAL

// Define a withdrawal fee
$withdrawalFee = 5.00; // Fixed fee for each withdrawal

// Check if the form has been submitted
if (isset($_POST['transact'])) {
    
    $_SESSION['r_name'] = htmlspecialchars($_POST['receiver_name']);
    $_SESSION['r_bank'] = htmlspecialchars($_POST['receiver_bank']);
    $_SESSION['r_acc'] = htmlspecialchars($_POST['receiver_acc']);
    $_SESSION['amount'] = (float) htmlspecialchars($_POST['amount']); // Ensure amount is treated as a float
    $_SESSION['date_time'] = htmlspecialchars($_POST['date']);
    $_SESSION['type'] = htmlspecialchars($_POST['wstatus']); 

    $payid = $_SESSION['id']; // User ID from the session

    // Retrieve the password input from the form
    $password = htmlspecialchars($_POST['password']); // New addition to capture the password

    // Validate amount
    if ($_SESSION['amount'] <= 0) {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Invalid Amount",
                    text: "Please enter a valid withdrawal amount."
                });
              </script>';
        exit;
    }

    // Calculate the total deduction (withdrawal amount + fee)
    $totalDeduction = $_SESSION['amount'] + $withdrawalFee;

    // Retrieve the stored password and current balance for the user from the database
    $query = "SELECT password, u_balance FROM client WHERE id = '$payid'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['password'];  // The stored plain-text password
        $currentBalance = (float) $row['u_balance']; // Ensure balance is treated as a float

        // Compare the entered password with the stored one
        if ($password === $storedPassword) {
            // Check if the total deduction is greater than the user's balance
            if ($totalDeduction > $currentBalance) {
                echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Insufficient Funds",
                            text: "You do not have enough funds to complete this transaction, including the $5 fee."
                        });
                      </script>';
            } else {
                // Update balance after withdrawal and fee deduction
                $newBalance = $currentBalance - $totalDeduction;

                // Insert transaction record
                $sql = "INSERT INTO `transaction` (r_name, r_bank, r_acc, amount, payid, date_time,type) 
                        VALUES ('".$_SESSION['r_name']."', '".$_SESSION['r_bank']."',
                        '".$_SESSION['r_acc']."', '".$_SESSION['amount']."', '$payid', '".$_SESSION['date_time']."','".$_SESSION['type']."')";
                $transactionResult = mysqli_query($connect, $sql);

                if ($transactionResult) {
                    echo '<script>
                          Swal.fire({
                              icon: "success",
                              title: "Withdrawal Successful",
                              text: "Your withdrawal has been successfully processed, including the $5 fee."
                          }).then((result) => {
                              if (result.isConfirmed) {
                                  window.location.href = "confirm.php"; 
                              }
                          });
                          </script>';
                } else {
                    echo '<script>
                            Swal.fire({
                                icon: "error",
                                title: "Withdrawal Error",
                                text: "There was an error processing your withdrawal."
                            });
                          </script>';
                }

                // Update the user's balance in the database
                $sql1 = "UPDATE `client` SET u_balance = '$newBalance' WHERE id = '$payid'";
                mysqli_query($connect, $sql1);
            }
        } else {
            // Password is incorrect
            echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Incorrect Password",
                        text: "The password you entered is incorrect. Please try again."
                    });
                  </script>';
        }
    } else {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "There was an error retrieving your data. Please try again later."
                });
              </script>';
    }
}

// USER WITHDRAWAL END
?>

<?php
if (isset($_POST['deposit'])) {
    $_SESSION['amount'] = (float) htmlspecialchars($_POST['damount']);  // Ensure amount is treated as a float
    $_SESSION['p_mthd'] = htmlspecialchars($_POST['pmethod']);
    $_SESSION['card_num'] = htmlspecialchars($_POST['cardnum']);

    $_SESSION['card_expdate'] = htmlspecialchars($_POST['expdate']);
    $_SESSION['cvv_num'] =  htmlspecialchars($_POST['cvvnum']);
    $_SESSION['r_name'] =  htmlspecialchars($_POST['dptname']);  

    $_SESSION['r_bank'] =  htmlspecialchars($_POST['dptbank']);  
    $_SESSION['r_acc'] =  htmlspecialchars($_POST['dptacc']);  

    $_SESSION['date_time'] = htmlspecialchars($_POST['depodate']);
    
    $_SESSION['pin'] = htmlspecialchars($_POST['dptpin']);
    $_SESSION['type'] = htmlspecialchars($_POST['status']); 

    $depositid = $_SESSION['id']; // User ID from the session

    // Retrieve the password input from the form
    $password = htmlspecialchars($_POST['password']); // Password from form input

    // Validate amount
    if ($_SESSION['amount'] <= 0) {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Invalid Amount",
                    text: "Please enter a valid withdrawal amount."
                });
              </script>';
        exit;
    }
    elseif (strlen($_SESSION['cvv_num']) > 3) {
        echo '<div class="alert alert-danger">
            <strong>Invalid Cvv Number!</strong>
        </div>';
    } 
     if (strlen($_SESSION['cvv_num'])< 3) {
        echo '<div class="alert alert-danger">
            <strong>Invalid Cvv Number!</strong>
        </div>';
    } 
    // Retrieve the stored password and current balance for the user from the database
    $query = "SELECT password, u_balance FROM client WHERE id = '$depositid'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $storedPassword = $row['password'];  // The stored plain-text password
        $currentBalance = $row['u_balance'];  // Current balance

        // Compare the entered password with the stored one
        if ($password === $storedPassword) {

            // Insert transaction record
            $sql2 = "INSERT INTO `transaction` (amount, p_mthd, card_num, card_expdate , cvv_num, r_name ,r_bank, r_acc, date_time, pin, payid,type) 
                    VALUES ('".$_SESSION['amount']."', '".$_SESSION['p_mthd']."', '".$_SESSION['card_num']."', '".$_SESSION['card_expdate']."',
                     '".$_SESSION['cvv_num']."', '".$_SESSION['r_name']."', '".$_SESSION['r_bank']."', '".$_SESSION['r_acc']."', '".$_SESSION['date_time']."',  '".$_SESSION['pin']."', '$depositid', '".$_SESSION['type']."')";
            $depositResult = mysqli_query($connect, $sql2);

            if ($depositResult) {
                echo '<script>
                      Swal.fire({
                          icon: "success",
                          title: "Deposit Successful",
                          text: "Your Deposit has been successfully processed."
                      }).then((result) => {
                          if (result.isConfirmed) {
                              window.location.href = "confirm.php"; 
                          }
                      });
                      </script>';

                // Update the user's balance in the database
                $newBalance = $currentBalance + $_SESSION['amount'];  // Calculate new balance
                $sql3 = "UPDATE `client` SET u_balance = '$newBalance' WHERE id = '$depositid'";
                mysqli_query($connect, $sql3);

            } else {
                echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Deposit Error",
                            text: "There was an error processing your deposit."
                        });
                      </script>';
            }
        } else {
            // Password is incorrect
            echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "Incorrect Password",
                        text: "The password you entered is incorrect. Please try again."
                    });
                  </script>';
        }
    } else {
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "There was an error retrieving your data. Please try again later."
                });
              </script>';
    }
}
?>


