<div class="new-main-content">
	<div class="new-content">	
	<?php 
		if(!isset($_SESSION['useradmin']))
		{
			require_once (PATH_CLASS_VIEW . 'managerlogin.php');
			//header('Location: ' . BASE_NAME . 'manager/');
		}
		else 
		{
			if(isset($_GET['pSub']))
			{
				$page = $_GET['pSub'];
				if(strtolower(trim($page)) == 'vouchers')
				{
					require (PATH_CLASS_VIEW . 'managervouchers.php');
				}
				else if(strtolower(trim($page)) == 'answers')
				{
					require (PATH_CLASS_VIEW . 'manageranswers.php');
				}
				else 
				{
					require (PATH_CLASS_VIEW . 'manageraccounts.php');
				}
			}
			else 
			{
				require (PATH_CLASS_VIEW . 'manageraccounts.php');
			}
		}
	?>
	</div>
	<div style="clear: both; height: 2px;">
			&nbsp;
	</div>
</div>