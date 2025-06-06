<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, shrink-to-fit=no">
<link href="images/favicon.png" rel="icon">
<title>Payyed - Money Transfer and Online Payments HTML Template</title>
<meta name="description" content="This professional design html template is for build a Money Transfer and online payments website.">
<meta name="author" content="harnishdesign.net">

<!-- Web Fonts
============================================= -->
<link rel="stylesheet" href="../../../css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap">

<!-- Stylesheet
============================================= -->
<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="vendor/font-awesome/css/all.min.css">
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<!-- sweet alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="sweetalert2.all.min.js"></script>
</head>
<body>
<!-- Preloader -->
<div id="preloader">
  <div data-loader="dual-ring"></div>
</div>
<!-- Preloader End -->

<div id="main-wrapper">
  <div class="container-fluid px-0">
    <div class="row g-0 min-vh-100">
      <div class="col-md-6"> 
        <!-- Get Verified! Text
        ============================================= -->
        <div class="hero-wrap d-flex align-items-center h-100">
          <div class="hero-mask opacity-8 bg-primary"></div>
          <div class="hero-bg hero-bg-scroll" style="background-image:url('images/bg/image-3.jpg');"></div>
          <div class="hero-content mx-auto w-100 h-100 d-flex flex-column">
            <div class="row g-0">
              <div class="col-10 col-lg-9 mx-auto">
                <div class="logo mt-5 mb-5 mb-md-0"> <a class="d-flex" href="#"><img src="images/logo-light.png" alt="capital one"></a> </div>
              </div>
            </div>
            <div class="row g-0 my-auto">
              <div class="col-10 col-lg-9 mx-auto">
                <h1 class="text-11 text-white mb-4">Sign Up!</h1>
                <p class="text-4 text-white lh-base mb-5">Every day, Capital One makes thousands of customers happy.</p>
              </div>
            </div>
          </div>
        </div>
        <!-- Get Verified! Text End --> 
      </div>
      <div class="col-md-6 d-flex align-items-center"> 
        <!-- SignUp Form
        ============================================= -->
        <div class="container my-4">
          <div class="row g-0">
            <div class="col-11 col-lg-9 col-xl-8 mx-auto">
              <h3 class="fw-400 mb-4">Personal Details</h3>
              <?php
              include_once("dbconnect.php");
              if(isset($_POST['insert'])){
                $fname=htmlspecialchars($_POST["fname"] ?? '');
                $lname=htmlspecialchars($_POST["lname"] ?? '');
                $email=htmlspecialchars($_POST["email"] ?? '');
                $phn=htmlspecialchars($_POST["phn"] ?? '');
                $ssn=htmlspecialchars($_POST['ssn'] ?? '');
                $locate=htmlspecialchars($_POST["locate"] ?? '');
                $bday=htmlspecialchars($_POST['bday'] ?? '');
                $Pswd=htmlspecialchars($_POST['Pswd'] ?? '');
                $gender=htmlspecialchars($_POST['gender'] ?? '');
                $pop=NULL;

                $stmt = $connect->prepare("SELECT `email` FROM `user` WHERE `email` = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $pop = $row['email'];
                }

                if(($fname=="")|| ($lname=="")|| ($email== "") || ($phn== "")  || ($ssn== "") || ($bday== "") || ($Pswd== "") || ($gender== "")){
                  echo '<script>
                  Swal.fire({
                     title:"Opps!",
                     text:"Fill Out All Forms",
                     icon: "error"
                   });
                  </script>';
                 }

                 elseif($email==$pop)
                 {
                  echo '<script>
                 Swal.fire({
                    title:"Opps!",
                    text:"Email taken",
                    icon: "error"
                  });
                 </script>';
                 }

                 elseif(strlen($ssn) >9)
                 {
                     echo '<div class="alert alert-danger">
                     <strong>SSN not valid</strong>
                     </div>
                     
                     <script>
                     setTimeout(function (){
                         $(".alert-danger").hide();
                     },10000);
                     </script>';
                 }

                 elseif(strlen($Pswd) <6)
                                {
                                    echo '<div class="alert alert-danger">
                                    <strong>Pasword lenght required!</strong>
                                    </div>
                                    
                                    <script>
                                    setTimeout(function (){
                                        $(".alert-danger").hide();
                                    },10000);
                                    </script>';
                                }
                 else{
                  $fname=htmlspecialchars($_POST["fname"]);
                  $lname=htmlspecialchars($_POST["lname"]);
                  $email=htmlspecialchars($_POST["email"]);
                  $phn=htmlspecialchars($_POST["phn"]);
                  $ssn=htmlspecialchars($_POST['ssn']);
                  $locate=htmlspecialchars($_POST["locate"]);
                  $bday=htmlspecialchars($_POST['bday']);
                  $Pswd=htmlspecialchars($_POST['Pswd']);
                  $gender=htmlspecialchars($_POST['gender']);
                  $folderDir = "upload/";
                  $filename1 = basename($_FILES['passport']['name']);
                  $folderPath1 = $folderDir . $filename1;
                  $filetype1 = pathinfo($folderPath1,PATHINFO_EXTENSION);

                  $folderDir = "upload/";
  
                  $filename2 = basename($_FILES['docpass']['name']);
              
                  $folderPath2 = $folderDir . $filename2;
              
                  $filetype1 = pathinfo($folderPath1,PATHINFO_EXTENSION);
              
                  $sql="INSERT INTO `user`(firstname,lastname,email,phone,ssn,address,dob,img_pass,doc_upload,password,gender)
                           VALUES('$fname','$lname','$email','$phn','$ssn','$locate','$bday','$filename1','$filename2','$Pswd','$gender')";

            if ((move_uploaded_file($_FILES["passport"]["tmp_name"], $folderPath1))){  
              }

          if ((move_uploaded_file($_FILES["docpass"]["tmp_name"], $folderPath2))){  
              }


                           if ($connect->query($sql) === TRUE) {
                            echo '<script>Swal.fire({
                        title: "Account Successfuly Created!",
                        text: "Click Ok To Proceed!",
                        icon: "success",
                        confirmButtonText: "OK"
                        }).then((result) => {
                        if (result.isConfirmed) {
                        window.location.href = window.location.href="signin";
                        }
                        });</script>';
                           }

                           else {
                            echo "Error: " . $sql . "<br>" . $connect->error;
                           }
                 }

              }
              ?>
              <form id="loginForm" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="firstName" class="form-label"><strong>First Name</strong></label><br>
                  <?php
                   if(isset($_POST["insert"])){ 
                    if($fname==""){
                    echo '<span style="color: red;">First Name Cannot Be Empty</span>';
                            }
                        }
                  ?>
                  <input name="fname" type="text" class="form-control" placeholder="Enter Your First Name" value="<?php  if(isset($_POST['insert'])){ echo  $fname;}?>">
                </div>

                <div class="mb-3">
                  <label for="lastName" class="form-label"><strong>Last Name</strong></label><br>
                  <?php
                   if(isset($_POST["insert"])){ 
                    if($lname==""){
                    echo '<span style="color: red;">First Name Cannot Be Empty</span>';
                            }
                        }
                  ?>
                  <input name="lname" type="text" class="form-control" placeholder="Enter Your Last Name" value="<?php  if(isset($_POST['insert'])){ echo  $lname;}?>">
                </div>

                <div class="mb-3">
                  <label for="emailAddress" class="form-label"><strong>Email Address</strong></label><br>
                  <?php
                   if(isset($_POST["insert"])){ 
                    if($email==$pop){
                    echo '<span style="color: red;">This Email Is Taken</span>';
                            }
                        }
                  ?>
                  <input name="email" type="email" class="form-control" required="" placeholder="example@gmail.com" value="<?php  if(isset($_POST['insert'])){ echo  $email;}?>">
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Phone Number</strong></label>
                  <input name="phn" type="number" class="form-control" require placeholder="Enter Your Phone Numbber" value="<?php  if(isset($_POST['insert'])){ echo  $phn;}?>">
                  <small>country code required eg.(+1)</small>
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>SSN Number</strong></label>
                  <input name="ssn" type="number" class="form-control" required placeholder="Enter Your SSN Number">
                </div>

                <div class="mb-3">
                  <label class="form-label"><strong>Address</strong></label>
                  <input name="locate" type="text" class="form-control" required placeholder="san fransisco, carlifornia..USA" value="<?php  if(isset($_POST['insert'])){ echo  $locate;}?>">
                </div>

            <div class="mb-3">
              <label class="form-label"><strong>Date Of Birth</strong></label>
              <input name="bday" type="date" class="form-control" required value="<?php if(isset($_POST['insert'])){ echo  $bday;} ?>">
            </div>

            <div class="mb-3">
              <label class="form-label"><strong>Image upload</strong></label>
              <input name="passport" type="file" class="custom-file-input form-control" required accept=".png, .jpg, .jpeg">
              </div>
              
              <div class="mb-3">
              <label class="form-label"><strong>ID Document upload *</strong></label>
              <input name="docpass" type="file" class="custom-file-input form-control" required accept=".png, .jpg, .jpeg">
              <small>Required For Verification Of Account</small>
              </div>

              <div class="mb-3">
                  <label class="form-label"><strong>Password *</strong></label>
                  <input name="Pswd" type="text" class="form-control" required placeholder="Create a Login Password">
                  <small>Password Must Be at least 7 characters</small>
                </div>

            <div class="mb-3">
              <label for="gender" class="form-label"><strong>Gender</strong></label><br>
               <select name="gender" style="width: 100%;">
                <option>Male</option>
                <option>Female</option>
               </select>
              </div>

                <div class="d-grid mt-4 mb-3"><button class="btn btn-primary" name="insert" type="submit">SignUp</button></div>
              </form>
              <p class="text-3 text-center text-muted">Already have an account? <a class="btn-link" href="signin">Log In</a></p>
            </div>
          </div>  
        <!-- SignUp Form End --> 
      </div>
    </div>
  </div>
</div>


<!-- Script --> 
<script src="vendor/jquery/jquery.min.js"></script> 
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script> 
<!-- Style Switcher --> 
<script src="js/switcher.min.js"></script> 
<script src="js/theme.js"></script>
</body>
</html>