<?php
if (! isset ( $_SESSION )) {
	session_start ();
}

$clsCommon = new SBD_Common ();

?>
<ul class="nav">
	<li>
		<form id="searchForm" method="post">
			<div>
				<input type="text" placeholder="Searching ..." style="width: 100%;" />
				<input type="submit" value="" id="btnSearch" />
			</div>
		</form>
	</li>
	<li><a href="<?php echo BASE_NAME;?>" style="margin-top: 5px;">Home</a></li>
		<?php
		$modCategories = new mod_categories ();
		$listCatParent = $modCategories->GetParentCategories ();
		
		if (count ( $listCatParent ) > 0) {
			foreach ( $listCatParent as $catParent ) {
				?>
      		<li class="parent-cat">
      			<?php echo $catParent['cat_name'];?>
      		</li>
      	<?php
				$listCatChild = $modCategories->GetCategoriesParentId ( $catParent ['cat_id'] );
				
				if (count ( $listCatChild ) > 0) {
					foreach ( $listCatChild as $catChild ) {
						$href = BASE_NAME;
						if ($catChild ['cat_is_offer'] == 0 && $catChild ['cat_is_competition'] == 0) {
							$href .= 'news/';
						} else {
							$href .= 'offers/';
						}
						$href .= $clsCommon->text_rewrite ( $catChild ['cat_name'] ) . '-' . $catChild ['cat_id'];
						?>
      				<li><a href="<?php echo $href ;?>/"><?php echo $catChild['cat_name'];?></a>
      					<?php 
      					$listCatSubChild = $modCategories->GetCategoriesParentId($catChild['cat_id']);
      					if(count($listCatSubChild) > 0)
      					{ 
      					?>
      					<ul>
						
						<?php 
							foreach ($listCatSubChild as $catSub)
							{
								$hrefCatSub = BASE_NAME;
								if ($catSub ['cat_is_offer'] == 0 && $catSub ['cat_is_competition'] == 0) {
									$hrefCatSub .= 'news/';
								} else {
									$hrefCatSub .= 'offers/';
								}
								$hrefCatSub .= $clsCommon->text_rewrite ( $catSub ['cat_name'] ) . '-' . $catSub ['cat_id'];
							
						?>					
		                    <li>
		                    	<a href="<?php echo $hrefCatSub;?>"><?php echo $catSub['cat_name'];?></a>
		                    </li>
		                    <?php 
							}
		                    ?>
		                </ul>
		                <?php 
      					}
		                ?>
					</li>
      	<?php
					}
				}
			}
		}
		
		$modCategories->closeConnect ();
		?>
      	
      	<li style="margin-top: 8px; margin-bottom: 8px;">
      		<a href="<?php echo BASE_NAME ; ?>nam-archive/">Nam Archive</a>
      	</li>
      	<?php
		if (isset ( $_SESSION ['username'] )) {
								?>
        <li>
        	<a href='<?php echo BASE_NAME ; ?>my-account/' rel='<?php echo BASE_NAME.PATH_CLASS_VIEW ; ?>myaccount.php'>My Account</a>
        </li>
		<li>       	
        <?php
			if (isset ( $user_profile )) {
				echo '<a href="' . $logout_url . '">Logout</a>';
			} else {
				echo '<a href="' . BASE_NAME . 'logout/" id="btnLogout">Logout</a>';
			}
			
		?>
        </li>  
        <?php
			} 
			else 
			{
		?>           
        <li>
        	<a class="login" href="<?php echo BASE_NAME ; ?>login/"	rel="<?php echo BASE_NAME . PATH_CLASS_VIEW ; ?>login.php">login</a>
        </li>
		<li>
			<a href="<?php echo BASE_NAME ; ?>register/">register</a>
		</li>            
        <?php
		}
		?>
      	<li>
      		<a href="<?php echo BASE_NAME ; ?>contact-us/">Contact us</a>
		</li>
		<?php 
		if(isset($_SESSION['useradmin']))
		{
		?>			
			<li class="parent-cat">
      			manager
      		</li>
			<li>
      			<a href="<?php echo BASE_NAME ; ?>manager/accounts/">Accounts</a>
			</li>
			<li>
      			<a href="<?php echo BASE_NAME ; ?>manager/vouchers/">Vouchers</a>
			</li>
			<li>
      			<a href="<?php echo BASE_NAME ; ?>manager/logout/" id="btnLogoutAdmin">Log out</a>
			</li>
		<?php 
		}
		?>
		
</ul>