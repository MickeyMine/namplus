<?php
if(!isset($_SESSION))
{
	session_start();
}
	
include ('../config.php');
include ('../libs/modules/class.common.php');
include ('../libs/modules/clsDB.php');
include ('../libs/modules/mod_offers.php');
include ('../libs/modules/mod_offer_questions.php');
include ('../libs/modules/mod_offer_answers.php');

echo '<div class="pageoffer">';

if(isset($_POST['offerid'])){
	$offer_id = trim($_POST['offerid']);
	
	$clsCommon = new SBD_Common();
	
	$modOffer = new mod_offers();
	$currOffer = $modOffer->GetDataTable('offer_id = ' . $offer_id, null);
	$modOffer->closeConnect();
	
	if(count($currOffer) == 1)
	{
		$link = BASE_NAME . 'offerpage/' . $clsCommon->text_rewrite($currOffer[0]['offer_title']) . '-' . $currOffer[0]['offer_id'] . '/';
?>
	<script type="text/javascript">
	$(document).ready(function(){
		/*
		* Email validator
		*/
		function IsEmail(email) {
		  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		  return regex.test(email);
		}

		/*
		* Redirect with method 
		*/
		function url_redirect(options)
		{
			var $form = $('<form />');

			$form.attr('action', options.url);
			$form.attr('method', options.method);

			for(var data in options.data)
			{
				$form.append('<input type="hidden" name="' + data + '" value="' + options.data[data] + '" />');
			}

			$('body').append($form);
			$form.submit();
		}
		
		/*
		 * Join offer 
		 */
		$('#getjoinoffer').click(function(e){
			e.preventDefault();
			/*
			$('.buttonleftcontent input:checkbox:checked').each(function(){
				alert($(this).attr('name').split('][')[1]);
			});*/

			var question = [];
			$('.buttonleftcontent input:checkbox[name^="multians"]:checked').each(function(){
				var strArr = $(this).attr('name').split('][');
				//alert(strArr[0]);
				var stt = strArr[0].split('[')[1];

				if(question[stt] == null)
				{
					question[stt] = strArr[1].split(']')[0];
				}
				else
				{
					question[stt] += ';' + strArr[1].split(']')[0];
				}
			});

			$('.buttonleftcontent input:text[name^="singleans"]').each(function(){
				var stt = $(this).attr('name').split('[')[1].split(']')[0];

				question[stt] = $(this).val();

			});

			var flag = true;
			for(var i = 0; i < question.length; i++)
			{
				if(question[i] == '' || question[i] == null)
				{
					flag = false;
				}
			}	

			if(flag == true)
			{
				var questionStr = '';
				for(var i = 0; i < question.length; i++)
				{
					questionStr += question[i];
					if(i<question.length - 1)
					{
						questionStr += "#";
					}
				}
				
				var email = $('.buttonleftcontent #email').val();
				if(email == '')
				{
					alert('Please enter email!!');
				}
				else
				{
					if(IsEmail(email))
					{
						if(confirm('Are you ready to get offer ?'))
						{
							//alert('Send mail');
							url_redirect({
								url: 'ajax/proccessoffer.php',
								method: 'post',
								data: {
									"offerid" : <?php echo $offer_id;?>,
									"answer" : questionStr
								}
							});
						}
					}
					else
					{
						alert('Please enter correct email');
						$('.buttonleftcontent #email').val('');
						$('.buttonleftcontent #email').focus();
					}
				}					
			}
			else
			{
				alert('Please answer all question!!');
			}		
			
		});
	});
	</script>
	<form id="frmgetquestion">
		<fieldset style="background-color: transparent;">
			<div class="buttonleftcontent" style="padding: 0px; overflow: auto;">	
				<ol>
					<li>	 
		            	<p>
		             		<a class="buttonstyledarkjoin <?php echo $currOffer[0]['offer_type'] == 0? 'joinsizeoffer': 'joinsizecompetition';?>" style="margin: 0px auto;">
		                    	<span><?php echo $currOffer[0]['offer_type'] == 0? 'offer': 'competition';?></span>
		                    </a>
		            	</p>
            		</li>
	            	<li>
		            	<label style="padding-bottom: 10px; padding-left: 8px; line-height: 16px; background-color: transparent;" >
		                    <?php echo $currOffer[0]['offer_question_content']; ?>
		                </label>
	                </li>
           	<?php 
           	if($currOffer[0]['offer_type'] == 1)
           	{
           		$modQuestion = new mod_offer_questions();
           		$listQuestion = $modQuestion->GetQuestionByOfferId($offer_id);
           		$modQuestion->closeConnect();           		
           		
           		if(count($listQuestion) > 0)
           		{
           	?>
           			<?php 
           			$stt = 0;
           			foreach ($listQuestion as $question)
           			{
           				$questionTitle = 'Question ' . ($stt + 1) . ' : ';
           			?>
           				<li>
           					<label for="<?php echo 'singleans['. $stt .']';?>" style="padding-left: 0px; background-color: transparent;">
               					 <?php echo $questionTitle . $question['question_content'];?>
				            </label>
           			<?php 	
           				if($question['question_type'] == 0)
           				{
           			?>           					
               				
                        	<input style="width:70%" autocomplete="off" class="" id="<?php echo 'singleans['. $stt .']';?>" name="<?php echo 'singleans['. $stt .']';?>" type="text" value="" />
           			<?php 	
           				}
           				else 
           				{
           					$modAnswer = new mod_offer_answers();
           					$listAnswer = $modAnswer->GetAnswerByQuestionID($question['question_id']);
           					$modAnswer->closeConnect();
           						
           					if(count($listAnswer) > 0)
           					{
           						$sttAns = 1;
           						foreach ($listAnswer as $answer)
           						{
           			?>
           						<input type='checkbox' name="<?php echo 'multians['. $stt .'][' . $sttAns . ']';?>" value='<?php echo $answer['answer_content'];?>' /><?php echo $answer['answer_content'];?> <br />
           			<?php 
           							$sttAns ++;
           						}
           					}
           					else 
           					{
           						echo 'Can not get answer for this question !!';
           					}
           				}
           			?>
           				</li>
           			<?php 	
           				$stt++;
           			}
           			?>
           			
           	<?php 
           		}
           		else 
           		{
           			echo 'Can not get question of this offer !!';
           		}
           	}
           	?>           		
           			<li>
                       <label for="email" style="margin: 0px auto; padding-left: 0px; text-align: left; background-color: transparent;">Email</label>
                       <input type="text" value="<?php echo $_SESSION['username']; ?>" name="email" class="email" id="email" style="margin: 0px auto; padding-left: 0px; width: 70%; text-align: left;" />
                    </li>            
                    <li style="margin-top: 10px;">
	                    <p>
		             		<a id="getjoinoffer" class="buttonstyledarkjoin <?php echo $currOffer[0]['offer_type'] == 0? 'joinsizeoffer': 'joinsizecompetition';?>" style="margin: 0px auto;">
		                    	<span><?php echo $currOffer[0]['offer_type'] == 0? 'get offer': 'join competition';?></span>
		                    </a>
		            	</p>
                    </li>        
           		</ol>
            </div>
		</fieldset>
	</form>
<?php 
		
	}
	else {
		echo 'Can not get offer content !!';
	}	
}
else 
{
	echo 'false';
}


echo '</div>';
?>