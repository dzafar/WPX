<?php
/**
 * The template for displaying all single posts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>


<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

	<div class="wrapper" id="single-wrapper">
		<h2>шаблон для недвижимости</h2>
		<div class="row">

			<?php
			// Do the left sidebar check and open div#primary.
			get_template_part( 'global-templates/left-sidebar-check' );
			?>

			<main class="site-main" id="main">

				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'loop-templates/content', 'single' );
					get_the_post_thumbnail();

					$Square =  get_post_meta($post->ID, 'Square', 1); 
					$Price =  get_post_meta($post->ID, 'Price', 1); 
					$Address =  get_post_meta($post->ID, 'Address', 1); 
					$Living_space = get_post_meta($post->ID, 'Living_space', 1); 
					$Floor = get_post_meta($post->ID, 'Floor', 1); 

					if ($Square) {
						echo "<p>плошадь ".$Square."</p>";
					}
					if ($Price) {
						echo "<p>цена ".$Price."</p>";
					}
					if ($Address) {
						echo "<p>адрес ".$Address."</p>";
					}
					if ($Living_space) {
						echo "<p>жилая плошадь ".$Living_space."</p>";
					}

					$city_id = $post->post_parent;
					$city = get_post( $city_id );
					if ($post->post_parent) {

						echo "город " . '<a  href='.$city->guid.'>'.$city->post_title.'</a><hr>';
					}

					understrap_post_nav();

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				}
				?>
			</main>
			<?php
			// Do the right sidebar check and close div#primary.
			get_template_part( 'global-templates/right-sidebar-check' );
			?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #single-wrapper -->

<?php
get_footer();
