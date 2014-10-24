<?php
include (PATH_CLASS_MODEL  . 'mod_subscriptions.php');
include (PATH_CLASS_MODEL  . 'mod_payment_type.php');
include (PATH_CLASS_MODEL  . 'mod_register_form.php');

if(isset($_SESSION['username']))
{
	echo ('<span style="font-size: 12pt; font-weight: bolder; padding-top: 10px;">You are already become our member !! <a href="' . BASE_NAME . '">TO CONTINUE, CLICK HERE !!</a><span>');	
	//header('Location:' . BASE_NAME);
}
else 
{
	$modRegisterForm = new mod_register_form();
	$registerMember = $modRegisterForm->GetRegisterMember();
	$registerProfession = $modRegisterForm->GetRegisterProfession();
?>
<div style="clear: both; height: 2px;">
			&nbsp;
</div>
<div style="position: relative; display: table-row; background-color: #ccc; margin: 0px auto;">
    <div class="register-left">
        <form id="registrationFormSubscription" method="post" action="">
            <fieldset class="login-information">
                <h3 style="padding: 10px 0px 0px 8px; margin-bottom: 8px;">
                	<?php echo $registerMember[0]['register_title'];?>
                </h3>
                <label style="padding-bottom: 10px; padding-left: 8px; line-height: 16px;">
                    <?php echo $registerMember[0]['register_description'];?>
                </label>
                <ol>
                    <li>
                        <label for="FirstName" style="padding-left: 0px;">First name :</label>
                        <input autocomplete="off" class="" id="firstname" name="firstname" type="text" value="" />
                    </li>
                    <li>
                        <label for="LastName" style="padding-left: 0px;">Last name :</label>
                        <input autocomplete="off" class="" id="lastname" name="lastname" type="text" value="" />
                    </li>
                    <li>
                        <label for="Email" style="padding-left: 0px;">Email :</label>
                        <input autocomplete="off" class="" id="Email" name="Email" type="text" value="" />
                    </li>
                    <li>
                        <label for="FacebookUrl" style="padding-left: 0px;">Facebook URL : </label>
                        <input autocomplete="off" class="" id="facebook" name="facebook" type="text" value="" />
                    </li>
                    <li>
                        <label for="Phone" style="padding-left: 0px;">Phone number :</label>
                        <input autocomplete="off" class="" id="phone" name="phone" type="text" value="" />
                    </li>
                    <li>
                        <label for="Address" style="padding-left: 0px;">Address :</label>
                        <input autocomplete="off" class="" id="address" name="address" type="text" value="" />
                    </li>
                    <?php 
                    $mod_subscriptions = new mod_subscriptions();
                    $listSubscription = $mod_subscriptions->GetAllSubscriptions();
                    
                    if(count($listSubscription) > 0)
                    {
                    ?>
                    
                    <li>
                        <label for="Subscriptions" style="padding-left: 0px;">Subscriptions :</label>                     
                        <select id="Subscriptions" name="Subscriptions">
                        <?php
                        $isFirst = true;
                        foreach ($listSubscription as $subscription)
                        { 
                        ?>
			                <option <?php if($isFirst) echo 'selected="selected"'; ?> value='<?php echo trim($subscription['subscription_id']);?>'><?php echo trim($subscription['subscription_type']);?></option>
			            <?php 
			            	$isFirst = false;
                        }
                        ?>
		                </select>
                    </li>
            		<?php 
                    }
                    
                    $modPaymentType = new mod_payment_type();
                    $listPaymentType = $modPaymentType->GetAllPaymentType();
                    
                    if(count($listPaymentType) > 0)
                    {
            		?>
            		<li>
                        <label for="Payments" style="padding-left: 0px;">Payments :</label>                     
                        <select id="Payments" name="Payments">
                        <?php 
                        $isFirst = true;
                        foreach ($listPaymentType as $paymentType)
                        {
                        ?>
                        	<option <?php if($isFirst) echo 'selected="selected"'; ?> value='<?php echo $paymentType['payment_id'];?>'><?php echo $paymentType['payment_type'];?></option>
                        <?php 
                        	$isFirst = false;
                        }
                        ?>
		                </select>
                    </li>
                    <?php 
                    }
                    ?>  
                    <li style="text-align: center; padding-top: 10px; padding-bottom: 0px; border:">
                        <button class="dark styleButton" type="submit" id="buttonRegisterSub" >
                        <span></span>Register now!
                        </button>
                    </li>  
                            
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="register-right">
        <form id="registrationFormProfession" method="post" action="">
            <fieldset class="login-information">
                <h3 style="padding: 10px 0px 0px 8px; margin-bottom: 8px;">
                	<?php echo $registerProfession[0]['register_title'];?>
                </h3>
                <label style="padding-bottom: 10px; padding-left: 8px; line-height: 16px;">
                    <?php echo $registerProfession[0]['register_description'];?>
                </label>
                <ol>
                    <li>
                        <label for="FirstName" style="padding-left: 0px;">First name : </label>
                        <input autocomplete="off" class="" id="firstnamepro" name="firstnamepro" type="text" value="" />
                    </li>
                    <li>
                        <label for="Lastname" style="padding-left: 0px;">Last name : </label>
                        <input autocomplete="off" class="" id="lastnamepro" name="lastnamepro" type="text" value="" />
                    </li>
                    <li>
                        <label for="EmailPro" style="padding-left: 0px;">Email :</label>
                        <input autocomplete="off" class="" id="EmailPro" name="EmailPro" type="text" value="" />
                    </li>
                    <li>
                        <label for="FacebookUrl" style="padding-left: 0px;">Facebook URL : </label>
                        <input autocomplete="off" class="" id="facebookpro" name="facebookpro" type="text" value="" />
                    </li>
                    <li>
                        <label for="Phone" style="padding-left: 0px;">Phone number : </label>
                        <input autocomplete="off" class="" id="phonepro" name="phonepro" type="text" value="" />
                    </li>
                    <li>
                        <label for="Profession" style="padding-left: 0px;">Profession : </label>
                        <input autocomplete="off" class="" id="profession" name="profession" type="text" value="" />
                    </li>
                    
                    <li style="text-align: center; padding-top: 10px; padding-bottom: 0px;">
                        <button class="dark styleButton" type="submit" id="buttonRegisterPro" >
                        	<span></span>Register now!
                        </button>
                    </li>            
                </ol>
            </fieldset>
        </form>
    </div>
    <div class="line-register desktop-only">
    	&nbsp;
    </div>
</div>
<?php 
	$modPaymentType->closeConnect();
	$modRegisterForm->closeConnect();
}
?>