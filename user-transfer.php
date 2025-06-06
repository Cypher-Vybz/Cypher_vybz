<?php
include("user-mainbar.php");
include("user-transfer-style.php");
?>


                    <div class="col-12">
                      <?php
                        if(isset($_POST["transfer"])) {
                            $tfmtd=htmlspecialchars($_POST['tfmtd']);
                            $rcptn=htmlspecialchars($_POST['rcptn']);
                            $payid=htmlspecialchars($_POST['payid']);
                            $acc=htmlspecialchars($_POST['acc']);
                              $btyp=htmlspecialchars($_POST['btyp']);

                              $ammount=htmlspecialchars($_POST['ammount']);
                                
                              if($ammount > $_SESSION['totalbalance']) {


                                
                                    echo '<script>Swal.fire({
                             title: "oops!",
                            text: "Withdrawal error!",
                            icon: "error",
                            confirmButtonText: "OK"
                            }).then((result) => {
                            if (result.isConfirmed) {
                           window.location.href = window.location.href="Transfer";
                                    }
                                });</script>';
            
                                
                              }
                            
                              else {
                              
                              $bal =$_SESSION['totalbalance']-$ammount;
                              $sql = "INSERT INTO `admin_transactions`(tfmethod,recipient,payid,Recipient_acc,bank,amount)
                              VALUES ('$tfmtd', '$rcptn', '$payid','$acc','$btyp','$ammount')";
                                $result = mysqli_query($connect,$sql);

                                if($result){
                                    echo '<script>Swal.fire({
                     title: "Successful!",
                       text: "Transaction was  Successful!",
                        icon: "success",
                     confirmButtonText: "OK"
                    }).then((result) => {
                    if (result.isConfirmed) {
                    window.location.href = window.location.href="Dashboard";
                    }
                    });</script>';
                                }
                             

                                $sql1 = "UPDATE  `admin` SET  totalbalance = '$bal' WHERE `id` ='".$_SESSION['id']."'";
                                "SQL Query: " . $sql1 . "<br>"; 
                                $result = mysqli_query($connect,$sql1);
                              
            
                          
                        }

                    }


                       ?>
             
             <form class="ctp-send-money-online-form col-12" method="post">
    <div class="form-header">
        <h2 class="form-title">Transfer to Bank Account</h2>
    </div>
    <div class="form-content">
        <div class="form-group">
            <label>Transfer Method</label>
            <select name="tfmtd" class="form-control">
                <option>Bank Transfer</option>
                <option>Mobile Transfer</option>
            </select>
        </div>

        <div class="col">
            <h5>Recipient Fullname</h5>
            <input type="text" class="form-control" placeholder="Enter Fullname" name="rcptn" required>
        </div>

        <input type="hidden" class="form-control" value="<?php echo $_SESSION['id']; ?>" name="payid">                     

        <br>

        <div class="col">
            <h5>Recipient Account</h5>
            <input type="text" class="form-control" placeholder="Enter Account Number" name="acc" required>
        </div>
        <br>

        <div class="form-group">
            <label>Select Bank</label>
            <select name="btyp" class="form-control">
                <option>Citi Bank</option>
                <option>WestGate</option>
                <option>Bank Of America</option>
                <option>WellsFargo</option>
                <option>Chase Bank</option>
                <option>Wema</option>
                <option>Paystack</option>
            </select>
        </div>
                                  
        <div class="info">
            <div class="col">
                <h5>Amount</h5>
                <input type="text" class="form-control" placeholder="USD 0.0" name="ammount" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" name="transfer">Send Now</button>
    </div>
</form>

<h2 class="page-title">Recent Transaction</h2>

    <div class="col-xl-12 col-lg-12 col-md-12">
    <table id="example" class="table table-striped table-bordered" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Account Number</th>
            <th>Bank</th>
            <th>Amount</th>
            <th>Start date</th>
            
        </tr>
    </thead>
    <tbody>
    <?php

$stmt = $connect->prepare("SELECT * FROM `admin_transactions` WHERE `payid` = ?");
$stmt->bind_param("s",$_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc())
{
?>
        <tr>
            <td><?php echo $row['recipient'];?></td>
            <td><?php echo $row['Recipient_acc'];?></td>
            <td><?php echo $row['bank'];?></td>
            <td>$<?php echo $row['amount'];?></td>
            <td>2011-04-25</td>
        </tr>
    </tfoot>
    <?php
}
?>
</table>
<a href="Transfer-History">
    <span style="margin-left: 980px;">see more</span>
</a>
    </div>
  

</body>
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="vendor/chartjs/chartjs.js"></script>
<script src="js/plugins/chartjs-line-init.js"></script>
<script src="js/plugins/chartjs-donut.js"></script>
<script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="js/plugins/perfect-scrollbar-init.js"></script>
     <script src="js/scripts.js"></script>
      <script src="assets/js/meanmenu.js"></script>
        <script src="assets/js/nice-select.min.js"></script>
        <script src="assets/js/owl.carousel.min.js"></script>
        <script src="assets/js/main.js"></script>
       
</html>