<?php
include_once("dbconnect.php");

if (isset($_POST['insert'])) {
    // Retrieve and sanitize form inputs
    $fname = htmlspecialchars($_POST["fname"] ?? '');
    $lname = htmlspecialchars($_POST["lname"] ?? '');
    $phn = htmlspecialchars($_POST["phn"] ?? '');
    $email = htmlspecialchars($_POST["email"] ?? '');
    $country = htmlspecialchars($_POST['country'] ?? '');
    $locate = htmlspecialchars($_POST["locate"] ?? '');
    $zip = htmlspecialchars($_POST['zip'] ?? '');
    $sexm = htmlspecialchars($_POST['sexm'] ?? '');                      
    $sexf = htmlspecialchars($_POST['sexf'] ?? '');
    $Pswd = htmlspecialchars($_POST['Pswd'] ?? '');

    // Check for email duplication
    $stmt = $connect->prepare("SELECT `email` FROM `client` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->fetch_assoc()) {
        echo '<script>
            Swal.fire({
                title: "Oops!",
                text: "Email is already taken.",
                icon: "error"
            });
        </script>';
    } elseif (strlen($zip) > 6) {
        echo '<div class="alert alert-danger">
            <strong>Invalid Zip!</strong>
        </div>';
    } elseif (strlen($Pswd) < 8) {
        echo '<div class="alert alert-danger">
            <strong>Password length is required to be at least 8 characters!</strong>
        </div>';
    } else {
        // Define upload directory
        $folderDir = "upload/";
        
        // Array to store filenames for uploaded files
        $uploadedFiles = [
            'passport' => '',
            'govtid' => '',
            'proof' => '',
            'ssn' => ''
        ];

        // Function to handle file upload
        function uploadFile($inputName, $folderDir) {
            if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
                $filename = basename($_FILES[$inputName]['name']);
                $filePath = $folderDir . $filename;
                if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath)) {
                    return $filename;
                }
            }
            return null;
        }

        // Upload each file and store the filenames
        $uploadedFiles['passport'] = uploadFile('passport', $folderDir);
        $uploadedFiles['govtid'] = uploadFile('govtid', $folderDir);
        $uploadedFiles['proof'] = uploadFile('proof', $folderDir);
        $uploadedFiles['ssn'] = uploadFile('ssn', $folderDir);

        // SQL query for inserting data into the database
        $sql = "INSERT INTO `client` (firstname, lastname, phone, email, country, locate, zip_code, dob, male, female, id_doc, add_doc, ssn_doc, password) 
                VALUES ('$fname', '$lname', '$phn', '$email', '$country', '$locate', '$zip', '', '$sexm', '$sexf', 
                '{$uploadedFiles['govtid']}', '{$uploadedFiles['proof']}', '{$uploadedFiles['ssn']}', '$Pswd')";

        // Execute query
        if ($connect->query($sql) === TRUE) {
            echo '<script>Swal.fire({
                title: "Account Successfully Created!",
                text: "Click Ok to proceed!",
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "signin";
                }
            });</script>';
        } else {
            echo "Error: " . $sql . "<br>" . $connect->error;
        }
    }
}
?>


<?php
include_once("dbconnect.php");

if (isset($_POST['insert'])) {
    $fname = htmlspecialchars($_POST["fname"] ?? '');
    $lname = htmlspecialchars($_POST["lname"] ?? '');
    $phn = htmlspecialchars($_POST["phn"] ?? '');
    $email = htmlspecialchars($_POST["email"] ?? '');
    $country = htmlspecialchars($_POST['country'] ?? '');
    $locate = htmlspecialchars($_POST["locate"] ?? '');
    $zip = htmlspecialchars($_POST['zip'] ?? '');
    $sexm = htmlspecialchars($_POST['sexm'] ?? '');
    $sexf = htmlspecialchars($_POST['sexf'] ?? '');
    $pswd = htmlspecialchars($_POST['pswd'] ?? '');  // Ensure this is lowercase here and in HTML form

    // Check for existing email
    $stmt = $connect->prepare("SELECT `email` FROM `client` WHERE `email` = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_email = $result->fetch_assoc();
    $stmt->close();

    // Validate input fields
    if ($existing_email) {
        echo '<script>
            Swal.fire({
                title: "Oops!",
                text: "Email already taken",
                icon: "error"
            });
        </script>';
    } elseif (strlen($zip) > 6) {
        echo '<div class="alert alert-danger">
            <strong>Invalid Zip!</strong>
        </div>';
    } elseif (strlen($pswd) < 8) {
        echo '<div class="alert alert-danger">
            <strong>Password length required!</strong>
        </div>';
    } else {
        // File upload
        $uploadDir = "upload/";
        $filename1 = basename($_FILES['passport']['name']);
        $folderPath1 = $uploadDir . $filename1;
        
        $filename2 = basename($_FILES['govtid']['name']);
        $folderPath2 = $uploadDir . $filename2;
        $filename3 = basename($_FILES['proof']['name']);
        $folderPath3 = $uploadDir . $filename3;
        $filename4 = basename($_FILES['ssn']['name']);
        $folderPath4 = $uploadDir . $filename4;

        // Move files
        move_uploaded_file($_FILES["passport"]["tmp_name"], $folderPath1);
        move_uploaded_file($_FILES["govtid"]["tmp_name"], $folderPath2);
        move_uploaded_file($_FILES["proof"]["tmp_name"], $folderPath3);
        move_uploaded_file($_FILES["ssn"]["tmp_name"], $folderPath4);

        // Secure password
        $hashedPassword = password_hash($pswd, PASSWORD_DEFAULT);

        // Insert data into the database
        $sql = "INSERT INTO `client` (firstname, lastname, phone, email, country, locate, zip_code, male, female, id_doc, add_doc, ssn_doc, password) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connect->prepare($sql);
        $stmt->bind_param("sssssssssssss", $fname, $lname, $phn, $email, $country, $locate, $zip, $sexm, $sexf, $filename2, $filename3, $filename4, $hashedPassword);
        
        if ($stmt->execute()) {
            echo '<script>Swal.fire({
                title: "Account Successfully Created!",
                text: "Click OK to proceed!",
                icon: "success",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "signin";
                }
            });</script>';
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

?>


<?php

if (isset($_POST['login'])) {
    $email = htmlspecialchars($_POST['email']);
    $upswd = htmlspecialchars($_POST['upswd']);

    // Prepare and execute the query
    $stmt = $connect->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows >= 1) {
        $row = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($upswd, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            header('location: ./dashboard/dashboard.html');
        } else {
            echo '<script>Swal.fire({
                title: "Invalid!",
                text: "Check Credentials And Try Again!",
                icon: "error",
                confirmButtonText: "OK"
            });</script>';
        }
    } else {
        echo '<script>Swal.fire({
            title: "Invalid!",
            text: "Check Credentials And Try Again!",
            icon: "error",
            confirmButtonText: "OK"
        });</script>';
    }
}
?>
