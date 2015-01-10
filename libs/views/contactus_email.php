<?php
	
?>
	<script type="text/javascript" src="<?php echo BASE_NAME;?>ckeditor/ckeditor.js"></script>
	<div style="clear: both; height: 2px;">
			&nbsp;
	</div>
	<div class="content-page order-details marginv-auto">
	    <form id="frmcontact" name="frmcontact" method="post">
	    	<fieldset class="login-information">
			        <ol>
			            <li>
			                <label for="contactemail" style="padding-left: 0px;">Email</label>
			                <input autocomplete="off" class="" id="contactemail" maxlength="150" name="contactemail" type="text" value="<?php if(isset($_POST['contactemail'])) echo $_POST['contactemail'];?>" />
			            </li>
			            <li>
			                <label for="contactname" style="padding-left: 0px;">Name</label>
			                <input autocomplete="off" class="" id="contactname" maxlength="100" name="contactname" type="text"  value="<?php if(isset($_POST['contacttitle'])) echo $_POST['contacttitle'];?>"/>
							
			            </li>

			            <li>
			                <label for="contacttitle" style="padding-left: 0px;">Title</label>
			                <input autocomplete="off" class="" id="contacttitle" maxlength="100" name="contacttitle" type="text"  value="<?php if(isset($_POST['contacttitle'])) echo $_POST['contacttitle'];?>"/>
							
			            </li>

			            <li style="padding-right: 7px;">
			                <label for="contactcontent" style="padding-left: 0px;">Content</label>
			                <textarea name="contactcontent" id="contactcontent" rows="10" cols="80" >
			                <?php if(isset($_POST['contactcontent'])) echo $_POST['contactcontent'];?>
			                </textarea>
			                <script type="text/javascript">
							var editor = CKEDITOR.replace('contactcontent', {
								toolbar: [
									  		{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
									  		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],			// Defines toolbar group without name.
									  		'/',																					// Line break - next group will be placed in new line.
									  		{ name: 'basicstyles', items: [ 'Bold', 'Italic' ] }
									  	]
									  });
			                </script>
			            </li>
			            <li>
			                <label for="contactcaptcha" style="padding-left: 0px;">Captcha</label>
			                <img border="0" src="<?php echo BASE_NAME;?>libs/views/verify.php" />
							<img src="<?php echo BASE_NAME;?>images/refresh.png" style="width:24px; height:24px;"  onclick="document.frmcontact.submit();" />
							<br />
			                <input class="" id="contactcaptcha" maxlength="20" size="20" name="contactcaptcha" type="text" style="width: 50px;" />
							
			            </li>
			        </ol>
		    	</fieldset>
		    	<ol>
	            	<li style="text-align: center; padding-top: 0px; padding-bottom: 10px;">
                        <button class="dark styleButton" type="submit" id="buttonSendMail" style="width: 100px;" >
                       		<span></span>Send mail
                        </button>
                    </li>    		            	
	            </ol>
	            <br/>
	            &nbsp;
		</form>
	</div>