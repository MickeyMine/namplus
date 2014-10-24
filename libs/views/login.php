<?php
session_start();
include ('../../config.php');

try{
	require_once 'facebooklogin.php';
}
catch (Exception $e)
{
	error_log($e);
}
?>
<script type="text/javascript">
	$(document).ready(function(e){
		var url = $(location).attr('href');
		$.fn.exists = function(){
			return this.val().length !== 0;
		};

		function IsEmail(email) {
		  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		  return regex.test(email);
		}

		function addErrorClass()
		{
			$("#loginform").addClass("error");
			$("#email").addClass("error");
			$("#password").addClass("error");
		}

		function removeErrorClass()
		{
			$("#loginform").removeClass("error");
			$("#email").removeClass("error");
			$("#password").removeClass("error");
		}

		$('.forgotpass').click(function(e){
			e.preventDefault();
			
			alert('Forgot pass');
		});

		function hideControl()
		{
			$('#email').attr('disabled', 'disabled');
			$('#password').attr('disabled', 'disabled');
			$('.login-button').attr('disabled', 'disabled');
		}

		function showControl()
		{
			$('#email').removeAttr('disabled');
			$('#password').removeAttr('disabled');
			$('.login-button').removeAttr('disabled');
		}
		
		$('.login-button').click(function(e){
			e.preventDefault();

			flag = false;
			if($("#email").exists())
			{
				if($("#password").exists())
				{
					if(IsEmail($("#email").val()) == false)
					{
						addErrorClass();
					}
					else
					{
						removeErrorClass();

						flag = true;
					}
				}
				else
				{
					addErrorClass();
				}
			}
			else
			{
				addErrorClass();
			}

			if(flag)
			{
				$.ajax({
					type: "POST",
					url: "<?php echo BASE_NAME?>ajax/processlogin.php",
					data: $('#loginform').serialize(),
					beforeSend: function(){
						hideControl();
					},
					success: function(result){
						//alert(result + "-" + url);
						if(result == 'true')
						{
							alert('Login Success');
							window.location = url;
						}
						else if(result == 'block')
						{
							alert('Your account is blocking !!');
						}
						else
						{
							alert('Verify your email / password !');
						}
						showControl();
					}
				});
			}
			else
			{
				alert('Please enter your email / password !');
			}
		});
	});
</script>

<div class="login-container login-container-login">	
	<div class="login-form">
    	<div class="login-title">
        	<span style="padding-right: 3px;">LOG IN</span>
        </div>
        <div class="login-content">
            <form id="loginform">
                <ol>
                    <li>
                        <label for="email">Email</label>
                        <input type="text" name="email" class="email" id="email" />
                    </li>
                    <li>
                        <label for="password">Password</label>
                        <input type="password" name="password" class="password" id="password" />
                    </li>                    
                </ol>   
                <div style="float: left; line-height: 12px;">                	
	                <a class="forgot-pass facebook" style="color: #fff;" href="<?php echo $login_url;?>">
	                	Login by facebook ? <img src="<?php echo BASE_NAME.'images/facebook.png'; ?>" alt="Login facebook" style="width: 12px; height: 12px; margin-top: 3px;" />
	                </a><br />
	                <a class="forgot-pass forgotpass" style="color: #fff;" href="forgotpassword.php">Forgot your password ?</a>
                </div>
                <div style="float:left">
                	<input class="login-button dark" id="login-form-submit-button" type="submit" value="Log in" class="button login-button dark" />
                </div>         
            </form>
        </div>
        <div id="popupLogin">
            <div class="wrapper-map">  
            	<div id="map"></div>              
            </div>
        </div>
    </div>
</div>