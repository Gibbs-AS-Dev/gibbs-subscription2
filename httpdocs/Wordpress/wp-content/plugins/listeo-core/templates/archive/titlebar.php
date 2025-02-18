<!-- Titlebar
================================================== -->


	<div id="titlebar" class="gradient">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<?php
					$title = get_option('listeo_listings_archive_title');
					if(!empty($title)){ ?>
 						<h1  class="page-title"><?php echo esc_html($title); ?></h1>
					<?php } else {
						the_archive_title( '<h1 class="page-title">', '</h1>' );	
					}
					
					$subtitle = get_option('listeo_listings_archive_subtitle');

					if(isset($_GET['keyword_search'])) {
						?>
						<span>
						<?php
						
						$count = $GLOBALS['wp_query']->found_posts;
						printf(_n(  'We\'ve found <em class="count_listings">%s</em> <em class="count_text">listing</em> for you', 'We\'ve found <em class="count_listings">%s</em> <em class="count_text">listings</em> for you' , $count, 'listeo_core' ), $count); 
						?>
						</span>
						<?php
					} else {
						if($subtitle) {
							echo '<span>'.$subtitle.'</span>';
						}
					}
					
					echo term_description();
				?>
					
					<!-- Breadcrumbs -->
					<?php if(function_exists('bcn_display')) { ?>
			        <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
						<ul>
				        	<?php bcn_display_list(); ?>
				        </ul>
					</nav>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
