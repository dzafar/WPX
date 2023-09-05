<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php if ( is_front_page() && is_home() ) : ?>
<?php get_template_part( 'global-templates/hero' ); ?>
<?php endif; ?>

<div class="wrapper" id="index-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<?php
			// Do the left sidebar check and open div#primary.
			get_template_part( 'global-templates/left-sidebar-check' );
			?>

			<main class="site-main" id="main">

				<div>
					<?php

					$args = array(
						'post_type'      => array('post', 'houses' , 'cities'),
						'post_status'    => 'publish',    
						'orderby'          => 'post_date',
						'order'            => 'DESC',
						'posts_per_page' => - 1,
					);

					$query = new WP_Query( $args );
					if ( $query->have_posts() ) {
						$my_id = 0;
						while ( $query->have_posts() ) {
							$query->the_post();

							if ($my_id == 0) {
								$my_id = get_the_ID();
							}


						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'loop-templates/content', get_post_format() );
					}
				} else {
					get_template_part( 'loop-templates/content', 'none' );
				}
				?>

			</main>

			<?php
			// Display the pagination component.
			understrap_pagination();

			// Do the right sidebar check and close div#primary.
			get_template_part( 'global-templates/right-sidebar-check' );
			?>
		</div><!-- .row -->

		<div>
			<br><br>
			<h3>добавить свою недвижимость</h3>

			<form method="POST" id="add_house_form" enctype="multipart/form-data">
				<p>
					<label><input type="text" name="post_title" id="post_title" /> заголовок </label>
				</p>
				<p class="post_Sub_title804">
					<label><input type="text" name="post_Sub_title" id="post_Sub_title" /> ПОдзаголовок </label>
				</p>
				<p>
					<label><input type="text" name="post_content" id="post_content" /> описание </label>
				</p>
				<p>
					<label><input type="number" name="Square" id="Square" /> Площадь </label>
				</p>
				<p>
					<label><input type="number" name="Price" id="Price"  /> Стоимость</label>
				</p>
				<p>
					<label><input type="text" name="Address" id="Address" /> Адрес </label>
				</p>
				<p>
					<label><input type="number" name="Living_space" id="Living_space" /> Жилая площадь </label>
				</p>
				<p>
					<label><input type="number" name="Floor" id="Floor"/> Этаж </label>
				</p>
				<p>
					<label><input type="file" name="hous_img" id="hous_img" multiple="false"/> Картинка </label>
				</p>
				<?php wp_nonce_field( 'hous_img', 'hous_img_nonce' ); ?>

				<div id="post_parent">
					<?php 
					$cities = get_posts(array( 'post_type'=>'cities', 'posts_per_page'=>999, 'orderby'=>'post_title', 'order'=>'ASC' ));

					foreach( $cities as $city ){

						echo '<label><input type="radio" name="post_parent" value="'. $city->ID .'" '. checked($city->ID, $post->post_parent, 0) .' >
						'. esc_html($city->post_title) .'
						</label>';
					}
					?>
				</div>

				<br><br>
				<span name="add_houses" style="border: 1px solid black; padding: 5px;" id="add_houses_button" onclick="add_hous()">отправить</span>
			</form>

			<h3 id="result">
				результаты тут
			</h3>  

			<script type="text/javascript">

				var data = {
					"post_title": document.getElementById('post_title'),
					"post_Sub_title": document.getElementById('post_Sub_title'),
					"post_content": document.getElementById('post_content'),
					"Square": document.getElementById('Square'),
					"Price": document.getElementById('Price'),
					"Address": document.getElementById('Address'),
					"Living_space": document.getElementById('Living_space'),
					"Floor": document.getElementById('Floor'),
					"hous_img": document.getElementById('hous_img'),
				};
				var post_parent_wrapper = document.getElementById('post_parent');
				var add_houses_button = document.getElementById('add_houses_button');

				function validation() {	
					for (const pp of document.querySelectorAll('input[name="post_parent"]')) {
						if (pp.checked) {
							post_parent = pp;
						}
					}

					for (let x in data){
						if (data[x].value !== "") {
							data[x].style.border = "1px solid green";
						}
					}

					if (post_parent.value !== undefined) {
						post_parent_wrapper.style.border = "0";
					}
				}
				setInterval(validation, 500);

				function add_hous(){
					console.log("-");
					var i = 2;
					add_houses_button.style.pointerEvents = "none";
					add_houses_button.style.border = "2px solid red";

					housData = new FormData();

					for (const pp of document.querySelectorAll('input[name="post_parent"]')) {
						if (pp.checked) {
							post_parent = pp;
						}
					}

					for (let x in data){
						if (data[x].value == "") {
							data[x].style.border = "2px solid red";
							i--;
						}
					}

					if (post_parent.value == undefined) {
						post_parent_wrapper.style.border = "2px solid red";
						i = 0;
					}

					var resultBlock = document.getElementById('result');

					if (i == 1) {
						add_houses_button.innerHTML = "отправка..."
						post_parent.checked = false;

						housData.append('post_title',  data["post_title"].value);
						housData.append('post_Sub_title',  data["post_Sub_title"].value);
						housData.append('post_content',  data["post_content"].value);
						housData.append('Square',  data["Square"].value);
						housData.append('Price',  data["Price"].value);
						housData.append('Address',  data["Address"].value);
						housData.append('Living_space',  data["Living_space"].value);
						housData.append('Floor', data["Floor"].value);

						if (data["hous_img"].value !== "") {
							housData.append("hous_img", data["hous_img"].files[0], "img.jpg");
						}
						housData.append('post_parent', post_parent.value);

						jQuery.ajax({
							url:  "wp-admin/admin-ajax.php"+'?action=my_action',   
							type: 'POST',
							processData : false,
							contentType: false,
							data: housData,
							success : function(result) {
								for (let x in data){
									data[x].value = "";
								}
								resultBlock.innerHTML = result;
								add_houses_button.innerHTML = "готово";
							}
						});
					}

					setTimeout (function(){
						document.getElementById('add_houses_button').style.pointerEvents = "";
						document.getElementById('add_houses_button').style.border = "1px solid green";
						add_houses_button.innerHTML = "Отправить";
					}, 5000);
				}

			</script>

		</div>
	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php
get_footer();
