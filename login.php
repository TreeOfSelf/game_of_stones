 <?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
$skipVerify = 1;
include('header.htm');
?>

 <div class="row"align='left' style="background-color:#00000096; border-radius:25px; padding:20px; margin:20px">
      <form role="form" action="verify.php" method="post">
        <div class="form-group form-group-sm">
          <label for="userid">First Name:</label>
          <input type="text" class="form-control gos-form" id="userid" name="userid" maxlength="15">
        </div>
        <div class="form-group form-group-sm">
          <label for="lastname">House Name:</label>
          <input type="text" class="form-control gos-form" id="lastname" name="lastname" maxlength="15">
        </div>        
        <div class="form-group form-group-sm">
          <label for="password">Password:</label>
          <input type="password" class="form-control gos-form" id="pswd" name="pswd" maxlength="20">
        </div>
        <div class="form-group form-group-sm">
          <label for="mode">Mode:</label>
          <select class="form-control gos-form" id="mode" name="mode">
            <option value='0'>Normal</option>
            <option value='1'>Lite</option>
          </select>
        </div>
        <button type="submit" class="btn btn-block btn-success">Enter World</button>
		<a href='reset.php' class='btn btn-block btn-danger'>Forgot Password?</a>
      </form>     
      <br/><br/>
      <center>Don't have a character? </center>
      <a href='create.php' class='btn btn-block btn-warning'>Create a Character</a>
    </div>
  </div>
<?php
include('footer.htm');
?>
