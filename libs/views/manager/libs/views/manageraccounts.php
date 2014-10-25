<?php
//include (PATH_CLASS_MODEL . 'mod_customers.php');
include (PATH_CLASS_MODEL . 'mod_payment_type.php');
include (PATH_CLASS_MODEL . 'mod_subscriptions.php');
//include ('../../../modules/paginator.class.php');

$page = 1;
if(isset($_GET['page'])){
	$page = $_GET['page'];
}

$maxRecord = 10;

$modCustomers = new mod_customers();
$listCus = $modCustomers->GetAllCustomers();
//$modCustomers->closeConnect();

$totalPage = ceil(count($listCus)/$maxRecord);

if($page < 1)
{
	$page = 1;
}
else if($page > $totalPage)
{
	$page = $totalPage;
}

if(count($listCus) > 0)
{
	$modPaymentType = new mod_payment_type();
	$modSubscriptions = new mod_subscriptions();	
	
?>	
	<ol style="margin-left: 5px;">
		<li>
			<form id="searchManagerAccountForm" method="post" style=" position: relative; ">
	      		<input id="searchAccountInput" name="searchAccountInput" type="text" placeholder="Searching ..." style="width: 30%"/>
	      		<a id="searchManagerAccount" href="<?php echo BASE_NAME?>search-page/" style="padding: 2px 10px; color: white; background-color: #777;" >Search</a>
	      	</form>
		</li>
		<li style="overflow-x: auto">	
			<table border="1" id="manageraccounts" >
				<caption style="font-size: 15pt; font-weight: bolder; text-align: left;">DANH SACH TÀI KHOẢN</caption>
				<thead>
					<tr>
						<th>
							&nbsp;
						</th>
					<?php 
					
					$noCols = count(array_keys($listCus[0]));
					
					foreach (array_keys($listCus[0]) as $key) {
					?>					
						<th>
							<?php 
							$arr = array("customer", "_");
							echo trim(strtoupper(str_replace($arr, ' ', $key)));
							?>
						</th>
					<?php 
					}
					?>
					</tr>
				</thead>
				<tbody id="list-account-body">
<?php 
		if($page <= $totalPage)
		{
			$from = ($page - 1) * $maxRecord;
		
			$listCus = $modCustomers->GetDataTableLimit(null, 'customer_status', $from, $maxRecord);
		
			if(count($listCus) > 0)
			{
				foreach ($listCus as $cus)
				{
					?>
						<tr>
							<td>
								<a class="delete-account" href="<?php echo BASE_NAME;?>manager/deleteaccount/" rel="<?php echo $cus['customer_id'];?>">Delete</a>
								<a class="update-account" href="<?php echo BASE_NAME;?>manager/updateaccount/" rel="<?php echo $cus['customer_id'];?>">Update</a>														
							</td>
							<td>
								<?php echo $cus['customer_id'];?>
							</td>
							<td>
								<?php echo $cus['customer_code'];?>
							</td>
							<td>
								<?php echo $cus['customer_email'];?>
							</td>
							<td>
								<?php echo $cus['customer_pass'];?>
							</td>
							<td>
								<?php echo $cus['customer_first_name'];?>
							</td>
							<td>
								<?php echo $cus['customer_last_name'];?>
							</td>
							<td>
								<?php echo $cus['customer_profession'];?>
							</td>
							<td>
								<?php echo $cus['customer_phone'];?>
							</td>
							<td>
								<?php echo $cus['customer_address'];?>
							</td>
							<td>
								<?php 
								//echo $cus['supsctiption_id'];
								if($cus['subscription_id'] == 0)
								{
									echo 'None';
								}
								else 
								{
									$currSubscription = $modSubscriptions->GetSubscriptionsById($cus['subscription_id']);
									if(count($currSubscription) == 1)
									{
										echo $currSubscription[0]['subscription_type'];
									}
									else 
									{
										echo 'Fail !';
									}
								}
								?>
							</td>
							<td>
								<?php echo $cus['customer_facebook'];?>
							</td>
							<td>
								<?php echo $cus['customer_author_uid'];?>
							</td>
							<td>
								<?php echo $cus['customer_provider'];?>
							</td>
							<td>
								<?php 
								if($cus['customer_payment_type'] == 0)
								{
									echo 'None';
								}
								else 
								{
									$currPaymentType = $modPaymentType->GetPaymentTypeById($cus['customer_payment_type']);
									if(count($currPaymentType) == 1)
									{
										echo $currPaymentType[0]['payment_type'];
									}
									else 
									{
										echo 'Failed';										
									}
								}
								?>
							</td>
							<td>
								<?php 
								if($cus['customer_status'] == -1)
								{
									echo 'Pending';									
								}
								elseif($cus['customer_status'] == 0)
								{
									echo 'Active';									
								} 
								elseif($cus['customer_status'] == 1)
								{
									echo 'Blocking';									
								} 
								else 
								{
									echo 'None';
								}
								?>
							</td>
							<td>
								<?php echo $cus['customer_first_login'] == 0 ? 'Yes': 'No';?>
							</td>
						</tr>
					<?php 
					}
				}
				else 
				{
					echo 'Can not get account list !!';
				}
		}
?>					
				</tbody>
	<?php 
		if($totalPage > 1)
		{
	?>
				<tfoot>
					<tr>
						<td colspan='<?php echo $noCols + 1;?>'>
							<?php echo $paging->display_paging();?>
						</td>
					</tr>
				</tfoot>
	<?php 
		}
	?>
			</table>
		</li>
	</ol>
<?php 
$modCustomers->closeConnect();
$modPaymentType->closeConnect();
$modSubscriptions->closeConnect();
}
else 
{
	echo 'Can not get account !!';
}
?>