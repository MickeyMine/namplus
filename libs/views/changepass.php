<?php
if(!isset($_SESSION))
{
	session_start();
}

if(isset($_SESSION['username']))
{
	/*
	$modCustomer = new mod_customers();
	$currCustomer = $modCustomer->GetCustomerByEmail($_SESSION['username']);
	*/
?>
	<div style="position: relative; background-color: #ccc; margin: 0px auto;">
		<div style="clear: both; height: 2px;">
				&nbsp;
		</div>
		<form id="frmChangePass" method="post" action="">
			<fieldset class="login-information" style="padding-left: 10px;">
                <h3 style="padding: 0px; margin-bottom: 10px; margin-top: 10px;">
                	CHANGE YOUR PASSWORD                	
                </h3>
                <ol>
                    <li>
                        <label for="OldPassword" style="padding-left: 0px;">Enter old password :</label>
                        <input autocomplete="off" class="" id="oldpassword" name="oldpassword" type="password" value="" />
                    </li>
                    <li>
                        <label for="NewPassword" style="padding-left: 0px;">Enter new password :</label>
                        <input autocomplete="off" class="" id="newpassword" name="newpassword" type="password" value="" />
                    </li>
                    <li>
                        <label for="ReNewPassword" style="padding-left: 0px;">Re-enter new password :</label>
                        <input autocomplete="off" class="" id="renewpassword" name="renewpassword" type="password" value="" />
                    </li>
                    <li style="text-align: center; padding-top: 10px; padding-bottom: 0px; border:">
                        <button class="dark styleButton" type="submit" id="buttonChangePass" >
                        <span></span>Change pass!
                        </button>
                    </li>
               	</ol>
      		</fieldset>
        </form>    
	</div>
<?php 
}
?>