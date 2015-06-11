<?php

	defined('_JEXEC') or die('Restricted access');
	jimport('joomla.application.component.modellist');
	jimport('joomla.utilities.date');
	
	$prefix = "index.php?option=com_registrationpro";
	
	// MainMenu in Sidebar
	$mainMenu = array (
		array (
			'icon'   => 'icon-dashboard',
			'title'  => 'Dashboard',
			'link' => ''
		),
		array (
			'icon'  => 'icon-cog',
			'title' => 'Configuration',
			'subs'  => array ( // Title | view&controller&task
					'Main Settings   | view=settings&task=edit',
					'Config Emails   | view=emails',
					//'Add Sample Data | controller=commons&task=SampleData'
			)
		),
		array (
			'icon'  => 'icon-star',
			'title' => 'Events',
			'subs'  => array ( // Title | view&controller&task
					'Event Manager   | view=events',
					'Event Categories   | view=categories',
					'Registration Forms   | view=forms',
					'Location Manager | view=locations'
			)
		),
		array (
			'icon'  => 'icon-chart',
			'title' => 'Statistics',
			'subs'  => array ( // Title | view&controller&task
					'Event Reports   | view=stat_reports',
					'Charts | view=stat_charts'
			)
		),
		array (
			'icon'  => 'icon-archive',
			'title' => 'Archive Manager',
			'link' => 'view=archives'
		),
		array (
			'icon'  => 'icon-download',
			'title' => 'Discount Coupons',
			'link' => 'view=coupons'
		),
		array (
			'icon'  => 'icon-tags',
			'title' => 'Payment Plugins',
			'link' => 'view=plugins'
		),
		array (
			'icon'  => 'icon-vcard',
			'title' => 'Name Badges',
			'link' => 'view=badge'
		),
		array (
			'icon'  => 'icon-search',
			'title' => 'Search',
			'link' => 'view=search'
		),
		
		//array (
		//	'icon'  => 'icon-star',
		//	'title' => 'About',
		//	'link' => 'view=about'
		//)
	);
	
	$urlC = JRequest::getVar('controller', 'XXXXX');
	$urlV = JRequest::getVar('view', 'XXXXX');
	
	if($urlV == 'category') $urlV = 'categories';
	if($urlV != 'XXXXX') $urlC = 'XXXXX';
	
?>
<script>
jQuery(document).ready(function(){
	jQuery('#my-navbar').click(function(){
	
		if(jQuery('#my-collapse').hasClass('in'))
		{
			jQuery('#my-collapse').removeClass('in');
			jQuery('#my-collapse').css('height','0');
			console.log(jQuery(this));
			jQuery(this).addClass('colla');	
		}else if(jQuery('#my-navbar').hasClass('colla')){
			jQuery('#my-collapse').slideDown('slow');
			jQuery('#my-collapse').addClass('in');
		}
	});
});
</script>
<div id="sidebar"class="sidebar span2">
	<div class="navbar">
		<div class="navbar-inner dashboard-menu">
		  <a class="btn btn-navbar"id="my-navbar" data-toggle="collapse" data-target="#my-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		  </a>
			
				<div class="nav-collapse collapse" id="my-collapse">
					<ul class="nav-ace nav-list-ace">
						
						<?php 
							if(isset($mainMenu) && (is_array($mainMenu))) {
								foreach($mainMenu as $item) {
									if (is_array($item)){

										$currUrl = JRequest::getURI();
										$currUrl = substr($currUrl, strpos($currUrl, 'index.php?'));
									
										$cls = '';
										if(array_key_exists('subs', $item)) {
											$display = 'none';
											foreach($item['subs'] as $subitem) {
												list($sub_title, $sub_url) = explode('|', $subitem);
												$sub_title = trim($sub_title);
												
												$boo = false;
												if(strpos($sub_url,$urlV) === false){
													if(strpos($sub_url,$urlC) !== false) $boo = true;
												} else $boo = true;
												
												if($boo) {
													$display = 'block';
													break;
												}
											}
											
											echo "<li class=\"ddown\">\n";
											echo "  <a class=\"dropdown-toggle-ace\" href=\"#\">\n";
											echo "    <i class=\"".$item['icon']."\"></i>\n";
											echo "    <span class=\"menu-text\">".$item['title']."</span>\n";
											echo "    <b class=\"arrow js-icon-angle-down\"></b>\n";
											echo "  </a>\n";
											echo "  <ul class=\"submenu\" style=\"display:".$display."\">\n";
											
											foreach($item['subs'] as $subitem) {
												list($sub_title, $sub_url) = explode('|', $subitem);
												$sub_title = trim($sub_title);
												$sub_url = $prefix."&".trim($sub_url);
												
												$sub_cls = '';
												
												$boo = false;
												if(strpos($sub_url,$urlV) === false){
													if(strpos($sub_url,$urlC) !== false) $boo = true;
												} else $boo = true;
												
												if($boo) $sub_cls = 'class="active-item visible-desktop"';
																										
												echo "	<li ".$sub_cls.">\n";
												echo "  	<a href=\"".$sub_url."\">\n";
												echo "  	<i class=\"js-icon-double-angle-right\"></i>".$sub_title."\n";
												echo "      </a>\n";
												echo "  </li>\n";
											}
											
											echo "  </ul>\n";
											//echo "</li><li class=\"separator\"></li>\n";
											
										} else {
											$lnk = '';
											if($item['link'] != '') $lnk = "&".$item['link'];
											//if($currUrl == ($prefix.$lnk)) $cls = 'class="active-item"';
											
											$boo = false;
											if(strpos($item['link'],$urlV) === false){
												if(strpos($item['link'],$urlC) !== false) $boo = true;
											} else $boo = true;
											if($boo) $cls = 'class="active-item"';
											
											echo "<li ".$cls.">\n";
											echo "  <a href=\"".$prefix.$lnk."\">\n";
											echo "    <i class=\"".$item['icon']."\"></i>\n";
											echo "    <span class=\"menu-text\">".$item['title']."</span>\n";
											echo "  </a>\n";
											//echo "</li><li class=\"separator\"></li>\n";
										}
									}
								}
							}
						?>
						<li>
							<div id="sidebar-collapse" class="sidebar-collapse visible-desktop">
								<i class="js-icon-double-angle-left"></i>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>