
function image_resize(newsImage, alsoLike)
{
	$(newsImage).imgLiquid();
	$(alsoLike).imgLiquid();
}

function image_also_resize(alsoLike)
{
	$(alsoLike).imgLiquid();
}

$(document).ready(function(){
	//Current url
	var url = $(location).attr('href');
	
	/*
	 * Add function not equal for validate plugin
	 */
	jQuery.validator.addMethod("notEqualTo", function(value, element, param){
		return this.optional(element) || value != $(param).val();
	}, "Please specify a different value");
	
	/*
	* Menu mobile
	*/
		
	$('.device-menu').click(function(e){
		e.preventDefault();
		
		$('html').toggleClass('nav-visible');
	});
	/*
	$('#content-next').click(function(e){
		e.preventDefault();
		
		//alert('Come here');
		window.location = $(this).attr('rel');
	});
	*/
	
	//Content of news
	var news_content = $('#content-page');
	
	$('.tabs-line-div .div-content-next a').click(function(e){
		e.preventDefault();
		$('.tabs-line-div .div-content-next a').removeClass('selected');
		$(this).addClass('selected');
		
		var nextElement = $(this).parent().parent().parent().parent().next().children().children().children().children().attr('href');
					
		if($(this).attr('href') == url)
		{
			$arr = $(this).attr('href').split('/');
						
			$subArr = $arr[$arr.length - 2].split('-');
			$id = $subArr[$subArr.length - 1];
			
			$('.content-next-news .content-next-box-main').html('start with #' + ($('.tabs-line-div .div-content-next a').length - 1));
		}
		else
		{
			$arr = $(this).attr('href').split('_');
			
			//alert($arr[$arr.length-1].slice(0, -1));
			$id = $arr[$arr.length-1].slice(0, -1);
			
			if(nextElement == null)
			{
				$('.content-next-news .content-next-box-main').html('end page');
			}
			else
			{
				$('.content-next-news .content-next-box-main').html('next page');
			}
		}
		
		$.ajax({
			type: 'POST',
			data: {
				newsid: $id,
			},
			url: 'ajax/loadnewscontent.php',
			success: function(result){
				news_content.html(result);
			},
		});
		
	});
		
	$('.content-next-news .content-next-box-main').click(function(e){
		e.preventDefault();
		
		var selectTop = $('.tabs-line-div .div-content-next .selected');
		var nextElement = selectTop.parent().parent().parent().parent().next().children().children().children().children();
		
		//alert(selectTop.attr('href') + "-" + nextElement.attr('href'));
		
		if(nextElement == null)
		{
			alert('End page');
		}
		else
		{
			$arr = nextElement.attr('href').split('_');
			
			//alert($arr[$arr.length-1].slice(0, -1));
			$id = $arr[$arr.length-1].slice(0, -1);
			
			$.ajax({
				type: 'POST',
				data: {
					newsid: $id,
				},
				url: 'ajax/loadnewscontent.php',
				success: function(result){
					news_content.html(result);
				},
			});
			
			$('.tabs-line-div .div-content-next a').removeClass('selected');
			$.each($('.tabs-line-div .div-content-next a'), function(val){
				if($(this).attr('href') == nextElement.attr('href'))
				{
					$(this).addClass('selected');
				}
			});
			
			var subNextElement = nextElement.parent().parent().parent().parent().next().children().children().children().children();
			
			if(subNextElement.attr('href') == null)
			{
				$(this).html('end page');
			}
			else
			{
				$(this).html('next page');
			}
			
		}
	});
	
	/*
	* Login action
	*/
	$('.nav .login, .login-offer-details').click(function(e){
		e.preventDefault();
		
		$.ajax({
			type : "POST",
			url : $(this).attr("rel"),
			data : "",
			success : function(result) {				
				$('#my_popup').popup('hide');
				$('#my_popup').popup({
					autoopen: true,
					closeelement: '.b-close',
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html(result);
					},
				});
			},
			beforeSend : function() {
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html('Loading.....');
					},
				});
			}
		});
	});
	
	/*
	 * Log out
	 */
	$('#btnLogout, #btnLogoutAdmin').click(function(e){
		e.preventDefault();
		
		if(confirm('Are you want to log out ?'))
		{
			$.ajax({
				type: "POST",
				url: "ajax/logout.php",
				beforeSend: function(){
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.wrapper-map').html('Loading.....');
						},
					});
				},
				success: function(result){
					$('#my_popup').popup('hide');
					
					if(result == "true")
					{
						alert('Logout successed !');
						window.location = "/";
					}
					else
					{
						alert('Logout error !!');
					}
				}
			});
		}
	});
	
	/*
	 * Register 
	 */
	$('.register-offer-details').click(function(e){
		e.preventDefault();
		
		window.location = $(this).attr('rel');
	});
	
	$('#registrationFormSubscription').validate({
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
				remote: {
					url: 'ajax/getcustomer.php',
					type: 'post',
					data: {
						email: function(){
							return $('#Email').val();
						},
					},
				},
			},
			facebook:{
				required: true,
				url: true,
				remote: {
					url: 'ajax/getprofessional.php',
					type: 'post',
					data: {
						facebook: function(){
							return $('#facebook').val();
						},
					},
				},
			},
			phone:{
				required: true,
				number: true,
				maxlength: 12,
			},
			address:{
				required: true,
			}
		},
		messages:{
			firstname:{
				required: '*',
			},
			lastname: {
				required: '*',
			},
			Email:{
				required: '*',
				email: '*',
				remote: jQuery.format('{0} is already in use'),
			},
			facebook:{
				required: '*',
				url: '*',
				remote: jQuery.format('{0} is already in use'),
			},
			phone:{
				required: '*',
				number: '*',
				maxlength: '*',
			},
			address:{
				required: '*',
			},
		},
	});
	
	$('#buttonRegisterSub').click(function(e){
		e.preventDefault();
		
		$loadimg = $(this).find('span');
		//$loadimg.css('display', 'block');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 12px;" />');
				
		$('#my_popup').popup({
			autoopen: true,
			pagecontainer: '.wrapper-map',
			onopen: function(){
				$('.my_popup_close').css('display', 'block');
				//$('.wrapper-map').html(result);
				if($('#registrationFormSubscription').valid() == true)
				{
					$.ajax({
						type: 'POST',
						url: 'ajax/processCustomers.php',
						data: $('#registrationFormSubscription').serialize() + "&action=insert",
						beforeSend : function() {
							$('#my_popup').popup('hide');
							$('#my_popup').popup({
								autoopen: true,
								pagecontainer: '.wrapper-map',
								onopen: function(){
									$('.wrapper-map').html('Loading.....');
								},
							});
						},
						success: function(result){
							$('#my_popup').popup('hide');
							if(result=='true')
							{
								$('#my_popup').popup({
									autoopen: true,
									pagecontainer: '.wrapper-map',
									onopen: function(){
										$('.wrapper-map').html('<span class="message-popup">Register successed!!</span>');
									},
									onclose: function(){
										window.location = '/';
									}
								});								
							}
						}
					});					
				}
				else
				{
					$('.wrapper-map').html('<span class="message-popup">Please verify your information!</span>');
				}
			},
			onclose: function(){
				$loadimg.html('');
			}
		});			
	});
	
	$('#registrationFormProfession').validate({
		debug: true,
		rules: {
			firstnamepro:{
				required: true,
			},
			lastnamepro: {
				required: true,
			},
			profession:{
				required: true,				
			},
			phonepro:{
				required: true,
				number: true,
				maxlength: 12,
			},
			EmailPro:{
				required: true,
				email: true,
				remote: {
					url: 'ajax/getcustomer.php',
					type: 'post',
					data: {
						email: function(){
							return $('#EmailPro').val();
						},
					},
				},
			},
			facebookpro:{
				required: true,
				url: true,
				remote: {
					url: 'ajax/getprofessional.php',
					type: 'post',
					data: {
						facebook: function(){
							return $('#facebookpro').val();
						},
					},
				},
			}
		},
		messages:{
			firstnamepro:{
				required: '*',
			},
			lastnamepro: {
				required: '*',
			},
			profession:{
				required: '*',
			},
			phonepro:{
				required: '*',
				number: '*',
				maxlength: '*',
			},
			EmailPro: {
				required: '*',
				email: '*',
				remote: jQuery.format('{0} is already in use'),
			},
			facebookpro:{
				required: '*',
				url: '*',
				remote: jQuery.format('{0} is already in use'),
			},
		},
	});
	
	$('#buttonRegisterPro').click(function(e){
		e.preventDefault();
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 12px;" />');
		
		$('#my_popup').popup({
			autoopen: true,
			pagecontainer: '.wrapper-map',
			onopen: function(){
				$('.my_popup_close').css('display', 'block');
				//$('.wrapper-map').html(result);
				if($('#registrationFormProfession').valid() == true)
				{
					$.ajax({
						type: 'POST',
						url: 'ajax/processCustomers.php',
						data: $('#registrationFormProfession').serialize() + "&action=insert",
						beforeSend : function() {
							$('#my_popup').popup('hide');
							$('#my_popup').popup({
								autoopen: true,
								pagecontainer: '.wrapper-map',
								onopen: function(){
									$('.wrapper-map').html('Loading.....');
								},
							});
						},
						success: function(result){
							$('#my_popup').popup('hide');
							if(result=='true')
							{
								$('#my_popup').popup({
									autoopen: true,
									pagecontainer: '.wrapper-map',
									onopen: function(){
										$('.wrapper-map').html('<span class="message-popup">Register successed!!</span>');
									},
									onclose: function(){
										window.location = '/';
									}
								});								
							}
							else
							{
								$('#my_popup').popup({
									autoopen: true,
									pagecontainer: '.wrapper-map',
									onopen: function(){
										$('.wrapper-map').html('<span class="message-popup">' + result + '</span>');
									}
								});
							}
						}
					});	
				}
				else
				{
					$('.wrapper-map').html('<span class="message-popup">Please verify your information!</span>');
				}
			},
			onclose: function(){
				$loadimg.html('');
			}
		});
	});
	
	$('#login-admin-button').click(function(e){
		e.preventDefault();
		
		
		if($('#login-admin-form').valid() == true)
		{
			$loadimg = $(this).find('span');
			$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 5px;" />');
			
			$.ajax({
				type: "POST",
				url: "ajax/managerlogin.php",
				data: $('#login-admin-form').serialize(),
				beforeSend: function(){
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.wrapper-map').html('Loading.....');
						},
					});
				},
				success: function(result){
					$('#my_popup').popup('hide');
					
					if(result == 'true')
					{
						alert('Login successed !');
						
						window.location = url;
					}
					else
					{
						alert(result);
					}
				}
			});
			$loadimg.html('');
		}
		else
		{
			alert('Verify your information !!');
		}
	});
	
	/*
	 * Change pass
	 */
	//Validate form change passe
	$('#frmChangePass').validate({
		debug: true,
		debug: true,
		 rules:{
			 oldpassword:{
				 required: true,
				 remote: {
						url: 'ajax/getpass.php',
						type: 'post',
						data: {
							passlogin: function(){
								return $('#oldpassword').val();
							}
						},
					},
			 },
			 newpassword:{
				 required: true,
				 notEqualTo: '#oldpassword',
			 },
			 renewpassword:{
				 equalTo: '#newpassword',
			 }
		 },
		 messages:{
			 oldpassword:{
				 required: '*',
				 remote: '*'
			 },
			 newpassword:{
				 required: '*',
				 notEqualTo: '*',
			 },
			 renewpassword:{
				 equalTo: '*',
			 }
		 }
	});
	
	$('#buttonChangePass').click(function(e){
		e.preventDefault();
		
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 5px;" />');
				
		if($('#frmChangePass').valid() == true)
		{
			$.ajax({
				type: "POST",
				url: "ajax/updatecustomer.php",
				data: "newpass=" + $('#newpassword').val() + "&oldpass=" + $('#oldpassword').val(),
				beforeSend: function(){
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.wrapper-map').html('Loading.....');
						},
					});
				},
				success: function(result){
					$('#my_popup').popup('hide');
					
					if(result == 'true')
					{
						alert('Update successed !');
						
						window.location = "/";
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
			alert('Please verify your infomation !!');
		}
		
		$loadimg.html('');
	});
	
	/*
	 * Delete account
	 */
	$('.delete-account').click(function(e){
		e.preventDefault();
		
		//alert('Delete ' + $(this).attr('rel'));
		
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 5px;" />');
		
		//alert($(this).attr('rel'));
		if(confirm('Are you want to delete your account ? '))
		{
			$.ajax({
				type: "POST",
				url: "ajax/deletecustomer.php",
				data: "id=" + $(this).attr('rel'),
				beforeSend: function(){
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.wrapper-map').html('Loading.....');
						},
					});
				},
				success: function(result){
					$('#my_popup').popup('hide');
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.my_popup_close').css('display', 'block');
							
							if(result == 'true')
							{
								window.location = url;
							}
							else
							{
								alert(result);
							}
						},
						onclose: function(){
							$('.my_popup_close').css('display', 'none');
						}
					});
				},
			});			
		}
		$loadimg.html('');
	});
	
	/*
	 * Update account 
	 */
	$('.update-account').click(function(e){
		e.preventDefault();
		
		//alert('Update ' + $(this).attr('rel'));
		
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 5px;" />');
		
		//alert($(this).attr('rel'));
		$.ajax({
			type: "POST",
			url: "libs/views/manager/libs/views/loadmanageraccount.php",
			data: "cusid=" + $(this).attr('rel'),
			beforeSend: function(){
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html('Loading.....');
					},
				});
			},
			success: function(result){
				$('#my_popup').popup('hide');
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html(result);
					},
					onclose: function(){
						$('.my_popup_close').css('display', 'none');
					}
				});
			},
		});	
		$loadimg.html('');
	});
	
	$('.j-magazine a').click(function(e){
		e.preventDefault();
		
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 5px;" />');
		
		//alert($(this).attr('rel'));
		$.ajax({
			type: "POST",
			url: "ajax/loadmagazine.php",
			data: "magazineid=" + $(this).attr('rel'),
			beforeSend: function(){
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html('Loading.....');
					},
				});
			},
			success: function(result){
				$('#my_popup').popup('hide');
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html(result);
					},
					onclose: function(){
						$('.my_popup_close').css('display', 'none');
						//$('.popup_background').css('display', 'none');
						$('.popup_wrapper').css('display', 'none');
					}
				});
			},
		});	
		$loadimg.html('');
	});
	
	/*
	 * Get offer
	 */
	$('#joinoffer').click(function(e){
		e.preventDefault();
		
		var $offerid = $(this).attr('rel');
		
		$.ajax({
			type: "POST",
			url: "ajax/checkoffervouchers.php",
			data: "offerid=" + $offerid,
			beforeSend: function(){
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html('Loading.....');
					},
				});
			},
			success: function(result){
				$('#my_popup').popup('hide');
				if(result == 'true')
				{
					$.ajax({
						type: "POST",
						url: "ajax/getquestion.php",
						data: "offerid=" + $offerid,
						beforeSend: function(){
							$('#my_popup').popup({
								autoopen: true,
								pagecontainer: '.wrapper-map',
								onopen: function(){
									$('.wrapper-map').html('Loading.....');
								},
							});
						},
						success: function(result){
							$('#my_popup').popup('hide');
							$('#my_popup').popup({
								autoopen: true,
								pagecontainer: '.wrapper-map',
								onopen: function(){
									$('.my_popup_close').css('display', 'block');
									$('.wrapper-map').html(result);
								},
								onclose: function(){
									$('.my_popup_close').css('display', 'none');
								}
							});
						}
					});
				}
				else
				{
					//alert('You are already get this offer !!');
					alert(result);
				}
			},
		});
	});
	
	/*
	 * Offer rules
	 */
	$('#offer_rules').click(function(e){
		e.preventDefault();
		
		//alert($(this).attr('rel'));
		
		$.ajax({
			type: 'POST',
			url: 'ajax/loadrules.php',
			data: 'offerid=' + $(this).attr('rel'),
			beforeSend: function(){
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html('Loading.....');
					},
				});
			},
			success: function(result){	
				$('#my_popup').popup('hide');
				
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.my_popup_close').css('display', 'block');
						$('.wrapper-map').html(result);
					},
					onclose: function(){
						$('.my_popup_close').css('display', 'none');
					}					
				});
			},
		});
	});	
	
	//Invite friend
	$('#buttonInvite').click(function(e){
		e.preventDefault();
		//Just redirect to invite friend link
		window.location = $(this).attr('rel');
	});
	
	$('#myFormInvite').validate({
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
				remote: {
					url: 'ajax/getcustomer.php',
					type: 'post',
					data: {
						email: function(){
							return $('#Email').val();
						},
					},
				},
			},
			facebookurl: {
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
				/*remote: jQuery.format('{0} is already in use'),*/
				remote: '*'
			},
			facebookurl: {
				required: '*',
				url: '*'
			}
		},
	});
	
	$('#buttonInviteFriend').click(function(e){
		e.preventDefault();
		
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 12px;" />');
		
		$('#my_popup').popup({
			autoopen: true,
			pagecontainer: '.wrapper-map',
			onopen: function(){
				$('.my_popup_close').css('display', 'block');
				//$('.wrapper-map').html(result);
				if($('#myFormInvite').valid() == true)
				{
					$.ajax({
						type: 'POST',
						url: 'ajax/processinvite.php',
						data: $('#myFormInvite').serialize(),
						beforeSend : function() {
							$('#my_popup').popup('hide');
							$('#my_popup').popup({
								autoopen: true,
								pagecontainer: '.wrapper-map',
								onopen: function(){
									$('.wrapper-map').html('Loading.....');
								},
							});
						},
						success: function(result){
							$('#my_popup').popup('hide');
							if(result=='true')
							{
								$('#my_popup').popup({
									autoopen: true,
									pagecontainer: '.wrapper-map',
									onopen: function(){
										$('.wrapper-map').html('<span class="message-popup">Register successed!!</span>');
									},
									onclose: function(){
										window.location = '/';
									}
								});								
							}
							else
							{
								$('#my_popup').popup({
									autoopen: true,
									pagecontainer: '.wrapper-map',
									onopen: function(){
										$('.wrapper-map').html('<span class="message-popup">' + result + '</span>');
									}
								});
							}
						}
					});	
				}
				else
				{
					$('.wrapper-map').html('<span class="message-popup">Please verify your information!</span>');
				}
			},
			onclose: function(){
				$loadimg.html('');
			}
		});
	});
	
	/*
	 * Send mail contact
	 */
	function saveEditorTrigger()
	{
		for ( instance in CKEDITOR.instances ) CKEDITOR.instances[instance].updateElement();
	}
	
	$('#buttonSendMail').click(function(e){
		e.preventDefault();
		
		$loadimg = $(this).find('span');
		$loadimg.html('<img src="images/loading.gif" style="position: relative; width:15px; height:15px; margin-right: 5px;" />');
				
		if($('#frmcontact').valid() == true)
		{
			saveEditorTrigger();
			
			var str = $('#frmcontact').serialize();
			
			$.ajax({
				type: "POST",
				url: "ajax/contactus.php",
				data: str,
				beforeSend: function(){
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.wrapper-map').html('Loading.....');
						},
					});
				},
				success: function(result){
					$('#my_popup').popup('hide');
					$('#my_popup').popup({
						autoopen: true,
						pagecontainer: '.wrapper-map',
						onopen: function(){
							$('.my_popup_close').css('display', 'block');
							
							var arr = result.split('\n');

		   					 if($.trim(arr[arr.length - 1]) == 'verify')
		   					 {
		   						 $('#contactcaptcha').addClass('error');
		   					 }
		   					 else if($.trim(arr[arr.length - 1]) == 'true')
		   					 {
		   						 $('#contactcaptcha').removeClass('error');
		   						 alert('Send mail success');
		   						 window.location = url;
		   					 }
		   					 else
		   					 {
		   						 alert('Send mail fail : ' + result);
		   					 }
						},
						onclose: function(){
							$('.my_popup_close').css('display', 'none');
						}
					});
				},
			});
		}
		else
		{
			alert('Please verify your information !!');
		}
		
		$loadimg.html('');
	});
	
	/*
	 * Offer rules
	 */
	$('#offer_rules').click(function(e){
		e.preventDefault();
		
		//alert($(this).attr('rel'));
		
		$.ajax({
			type: 'POST',
			url: 'ajax/loadrules.php',
			data: 'offerid=' + $(this).attr('rel'),
			beforeSend: function(){
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.wrapper-map').html('Loading.....');
					},
				});
			},
			success: function(result){	
				$('#my_popup').popup('hide');
				
				$('#my_popup').popup({
					autoopen: true,
					pagecontainer: '.wrapper-map',
					onopen: function(){
						$('.my_popup_close').css('display', 'block');
						$('.wrapper-map').html(result);
					},
					onclose: function(){
						$('.my_popup_close').css('display', 'none');
					}					
				});
			},
		});
	});
});