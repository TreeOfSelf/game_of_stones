 <?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$skipVerify = 1;
include('header.htm');
?>

 <div class="row"align='left' style="background-color:#00000096; border-radius:25px; padding:20px; margin:20px">
      <form role="form" action="verifyReset.php" method="post">
        <div class="form-group form-group-sm">
          <label for="userid">First Name:</label>
          <input type="text" class="form-control gos-form" id="userid" name="userid" maxlength="15">
        </div>
        <div class="form-group form-group-sm">
          <label for="lastname">House Name:</label>
          <input type="text" class="form-control gos-form" id="lastname" name="lastname" maxlength="15">
        </div>        
        <div class="form-group form-group-sm">
          <label for="password">Email:</label>
          <input type="text" class="form-control gos-form" id="pswd" name="pswd" maxlength="30">
        </div>
        <button type="submit" class="btn btn-block btn-danger">Reset Password</button>
      </form>     
      <br/><br/>
      <center>Don't have a character? </center>
      <a href='create.php' class='btn btn-block btn-warning'>Create a Character</a>
    </div>
  </div>

<?php


include('footer.htm');
?>
