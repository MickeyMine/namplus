<?php
if(!isset($_SESSION))
{
	session_start();
}
include ('../../../../../config.php');
include ('../../../../modules/clsDB.php');
include ('../../../../modules/mod_customers.php');
include ('../../../../modules/mod_subscriptions.php');
include ('../../../../modules/mod_payment_type.php');

if(isset($_POST['cusid']))
{
	$cusid = $_POST['cusid'];
	$modCustomers = new mod_customers();
	
	if(get_magic_quotes_gpc()==false)
	{
		$cusid = mysqli_real_escape_string($modCustomers->clsDb->link, $cusid);
	}
		
	$currCus = $modCustomers->GetCustomerByID($cusid);
	$modCustomers->closeConnect();
	
	if(count($currCus) == 1)
	{
?>
<script type="text/javascript">
	$(document).ready(function(){
		var url = $(location).attr('href');
		
		$('#managerAccountUpdate').validate({
			debug: true,
			rules: {
				firstname:{
					required: true,
				},
				lastname: {
					required: true,
				},
				Email:{
					required: true,
					email: true,
				},
				facebook: {
					required: true,
					url: true
				}
			},
			messages:{
				firstname:{
					required: '*',
				},
				lastname: {
					required: '*',
				},
				Email: {
					required: '*',
					email: '*',
				},
				facebook: {
					required: '*',
					url: '*'
				}
			},
		});
		
		$('#btnUpdateAccount').click(function(e){
			e.preventDefault();

			if($('#managerAccountUpdate').valid() == true)
			{
				//alert($('#Status').val());
				$.ajax({
					type: "POST",
					url: "ajax/updatecustomer.php",
					data: $('#managerAccountUpdate').serialize(),
					beforeSend : function() {
					},
					success: function(result){
						if(result == 'true')
						{
							alert('Update success !!');
							window.location = url;
						}
						else
						{
							alert(result);
						}
					}
				});
			}
			else
			{
				alert('Verify your information !!');
			}
		});
	});
</script>
	<div class="page" style="min-width: 500px;">	
	    <div class="content-page order-details marginv-auto">
		    <form id="managerAccountUpdate" method="post" action="">
	            <fieldset class="login-information" >
	                <h3 style="padding: 10px 0px 0px 8px; margin-bottom: 8px;">ACCOUNT INFORMATION</h3>
	                <ol>
	                    <li>
	                        <label for="FirstName" style="padding-left: 0px;">First name :</label>
	                        <input autocomplete="off" class="" id="firstname" name="firstname" type="text" value="<?php echo $currCus[0]['customer_first_name'];?>" />
	                    </li>
	                    <li>
	                        <label for="LastName" style="padding-left: 0px;">Last name :</label>
	                        <input autocomplete="off" class="" id="lastname" name="lastname" type="text" value="<?php echo $currCus[0]['customer_last_name'];?>" />
	                    </li>
	                    <li>
	                        <label for="Email" style="padding-left: 0px;">Email :</label>
	                        <input autocomplete="off" class="" id="Email" name="Email" type="text" value="<?php echo $currCus[0]['customer_email'];?>" />
	                    </li>
	                    <li>
	                        <label for="FacebookUrl" style="padding-left: 0px;">Facebook URL : </label>
	                        <input autocomplete="off" class="" id="facebook" name="facebook" type="text" value="<?php echo $currCus[0]['customer_facebook'];?>" />
	                    </li>
	                    <li>
	                        <label for="Phone" style="padding-left: 0px;">Phone number :</label>
	                        <input autocomplete="off" class="" id="phone" name="phone" type="text" value="<?php echo $currCus[0]['customer_phone'];?>" />
	                    </li>		                   
	    				<?php 
	    				if(!isset($currCus[0]['customer_profession']) || trim($currCus[0]['customer_profession']) == '')
	    				{
	    				?>
	    				<li>
	                        <label for="Address" style="padding-left: 0px;">Address :</label>
	                        <input autocomplete="off" class="" id="address" name="address" type="text" value="<?php echo $currCus[0]['customer_address'];?>" />
	                    </li>	
	    				<?php	
		    				$mod_subscriptions = new mod_subscriptions();
		    				$listSubscription = $mod_subscriptions->GetAllSubscriptions();
		    				$mod_subscriptions->closeConnect();
		    				
		    				if(count($listSubscription) > 0)
		                    {
	                    ?>
		                    
		                    <li>
		                        <label for="Subscriptions" style="padding-left: 0px;">Subscriptions :</label>                     
		                        <select id="Subscriptions" name="Subscriptions">
		                        <?php
		                        
		                        foreach ($listSubscription as $subscription)
		                        { 
		                        	$isFirst = false;
		                        	if($subscription['supscription_id'] == $currCus[0]['supsctiption_id'])
		                        	{
		                        		$isFirst = true;
		                        	}
		                        ?>
					                <option <?php if($isFirst) echo 'selected="selected"'; ?> value='<?php echo trim($subscription['supscription_id']);?>'><?php echo trim($subscription['supscription_type']);?></option>
					            <?php						            	
		                        }
		                        ?>
				                </select>
		                    </li>
	            		<?php 
			    			}
			    			
			    			$modPaymentType = new mod_payment_type();
			    			$listPaymentType = $modPaymentType->GetAllPaymentType();
			    			$modPaymentType->closeConnect();
			    			
			    			if(count($listPaymentType) > 0)
			    			{
	    				?>
    			            		<li>
    			                        <label for="Payments" style="padding-left: 0px;">Payments :</label>                     
    			                        <select id="Payments" name="Payments">
    			                        <?php 
    			                        
    			                        foreach ($listPaymentType as $paymentType)
    			                        {    			                        	
    			                        	$isFirst = false;
    			                        	if($paymentType['payment_id'] == $currCus[0]['customer_payment_type'])
    			                        	{
    			                        		$isFirst = true;
    			                        	}
    			                        ?>
    			                        	<option <?php if($isFirst) echo 'selected="selected"'; ?> value='<?php echo $paymentType['payment_id'];?>'><?php echo $paymentType['payment_name'];?></option>
    			                        <?php 
    			                        }
    			                        ?>
    					                </select>
    			                    </li>
    			            <?php 
    			                }
		    				}
		    				else
		    				{
	    					?>
    					 <li>
	                        <label for="Profession" style="padding-left: 0px;">Profession : </label>
	                        <input autocomplete="off" class="" id="profession" name="profession" type="text" value="<?php echo $currCus[0]['customer_profession'];?>" />
	                    </li>	
    					<?php 
	    				}  
						?>		
						<li>
							<label for="Status" style="padding-left: 0px;">Status :</label>                     
    			            <select id="Status" name="Status">
    			            <?php 
    			            for($stt = -1; $stt <= 1; $stt ++)
    			            {
    			            ?>
    			            	<option <?php if($currCus[0]['customer_status'] == $stt) echo 'selected="selected"';?> value='<?php echo $stt;?>'>
    			            	<?php 
    			            	switch ($stt)
    			            	{
    			            		case -1:
    			            			echo 'Pending';
    			            			break;
    			            		case 0:
    			            			echo 'Active';
    			            			break;
    			            		case 1:
    			            			echo 'Block';
    			            			break;
    			            		default:
    			            			echo 'Pending';
    			            			break;
    			            	}
    			            	?>
    			            	</option>
    			            <?php 
    			            }
    			            ?>
    			            </select>    			                        
						</li>                    		                            
	                </ol>
	            </fieldset>
	            <ol>
	            	<li style="text-align: center; padding-top: 0px; padding-bottom: 10px;">
                        <button class="dark styleButton" type="submit" rel="<?php echo $currCus[0]['customer_id'];?>" id="btnUpdateAccount" >
                       		<span></span>Update Account
                        </button>
                    </li>    		            	
	            </ol>
	        </form>
		</div>
	</div>
	
<?php 
	}
	else 
	{
		echo 'Can not get customer information!';
	}
}
else 
{
	echo 'Verify your information !!!';
}
?>

