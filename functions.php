<?php

// подключаем скрипты 
add_action( 'wp_enqueue_scripts', 'my_theme_load_resources', 100 );
function my_theme_load_resources() {
	wp_enqueue_style('defolrt', get_stylesheet_uri());
}

add_action( 'init', 'register_post_types' );

function register_post_types(){

	register_post_type( 'houses', [
		'label'  => null,
		'labels' => [
			'name'               => 'Недвижимость',
			'singular_name'      => 'Недвижимость',
			'add_new'            => 'Добавить Недвижимость',
			'add_new_item'       => 'Добавление Недвижимость',
			'edit_item'          => 'Редактирование Недвижимость',
			'new_item'           => 'Новая Недвижимость',
			'view_item'          => 'Смотреть Недвижимость',
			'search_items'       => 'Искать Недвижимость',
			'not_found'          => 'Не найдено',
			'not_found_in_trash' => 'Не найдено в корзине',
			'parent_item_colon'  => '',
			'menu_name'          => 'Недвижимость',
		],
		'description'            => '',
		'public'                 => true,
		'show_in_menu'           => null,
		'show_in_rest'        => null,
		'rest_base'           => null,
		'menu_position'       => null,
		'menu_icon'           => null,
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor' ],
		'taxonomies'          => [],
		'has_archive'         => false,
		'rewrite'             => true,
		'query_var'           => true,
		'supports' => array('title', 'editor', 'thumbnail'),
	] )	;

	register_taxonomy( 'taxonomy', [ 'houses' ], [
		'label'                 => '',
		'labels'                => [
			'name'              => 'Тип недвижимости',
			'singular_name'     => 'Тип недвижимости',
			'search_items'      => 'Search Genres',
			'all_items'         => 'All Genres',
			'view_item '        => 'View Genre',
			'parent_item'       => 'Parent Genre',
			'parent_item_colon' => 'Parent Genre:',
			'edit_item'         => 'Edit Genre',
			'update_item'       => 'Update Genre',
			'add_new_item'      => 'Add New Genre',
			'new_item_name'     => 'New Genre Name',
			'menu_name'         => 'Тип недвижимости',
			'back_to_items'     => '← Back to Genre',
		],
		'description'           => '',
		'public'                => true,
		'hierarchical'          => false,
		'rewrite'               => true,
		'capabilities'          => array(),
		'meta_box_cb'           => null,
		'show_admin_column'     => false,
		'show_in_rest'          => null,
		'rest_base'             => null,
	] );

	register_post_type( 'cities', [
		'label'  => null,
		'labels' => [
			'name'               => 'города',
			'singular_name'      => 'города',
			'add_new'            => 'Добавить город',
			'add_new_item'       => 'Добавление города',
			'edit_item'          => 'Редактирование города',
			'new_item'           => 'Новые города',
			'view_item'          => 'Смотреть города',
			'search_items'       => 'Искать город',
			'not_found'          => 'Не найдено',
			'not_found_in_trash' => 'Не найдено в корзине',
			'parent_item_colon'  => '',
			'menu_name'          => 'города',
		],
		'description'            => '',
		'public'                 => true,
		'show_in_menu'           => null,
		'show_in_rest'        => null,
		'rest_base'           => null,
		'menu_position'       => null,
		'menu_icon'           => null,
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor' ],
		'taxonomies'          => [],
		'has_archive'         => false,
		'rewrite'             => true,
		'query_var'           => true,
		'supports' => array('title', 'editor', 'thumbnail'),
	] )	;
}

// подключаем функцию активации мета блока (my_extra_fields)
add_action('add_meta_boxes', 'my_extra_fields', 1);

function my_extra_fields() {
	add_meta_box( 'extra_fields', 'Дополнительные поля', 'extra_fields_box_func', 'Houses', 'normal', 'high'  );
}

// код блока
function extra_fields_box_func( $post ){
	?>
	<p><label><input type="text" name="extra[Square]" value="<?php echo get_post_meta($post->ID, 'Square', 1); ?>" style="width:50%" /> Площадь </label></p>

	<p><label><input type="text" name="extra[Price]" value="<?php echo get_post_meta($post->ID, 'Price', 1); ?>" style="width:50%" /> Стоимость</label></p>

	<p><label><input type="text" name="extra[Address]" value="<?php echo get_post_meta($post->ID, 'Address', 1); ?>" style="width:50%" /> Адрес </label></p>

	<p><label><input type="text" name="extra[Living_space]" value="<?php echo get_post_meta($post->ID, 'Living_space', 1); ?>" style="width:50%" /> Жилая площадь </label></p>

	<p><label><input type="text" name="extra[Floor]" value="<?php echo get_post_meta($post->ID, 'Floor', 1); ?>" style="width:50%" /> Этаж </label></p>

	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
	<?php

	$cities = get_posts(array( 'post_type'=>'cities', 'posts_per_page'=>999, 'orderby'=>'post_title', 'order'=>'ASC' ));

	foreach( $cities as $city ){
		echo '
		<label>
		<input type="radio" name="post_parent" value="'. $city->ID .'" '. checked($city->ID, $post->post_parent, 0) .'> '. esc_html($city->post_title) .'
		</label>
		';
	}
}

// связь недвижимости с городом
add_action('add_meta_boxes', 'my_extra_fields1', 1);

function my_extra_fields1() {
	add_meta_box( 'my_extra_fields1', 'связаная недвижимость', 'associated_cities_metabox', 'cities', 'normal', 'high'  );
}

function associated_cities_metabox( $post ){
	$houses = get_posts(array( 'post_type'=>'houses', 'post_parent'=>$post->ID, 'posts_per_page'=>-1, 'orderby'=>'post_title', 'order'=>'ASC' ));

	if( $houses ){
		foreach( $houses as $hous ){
			echo $hous->post_title .'<br>';
		}
	}
}

// включаем обновление полей при сохранении
add_action( 'save_post', 'my_extra_fields_update', 0 );

## Сохраняем данные, при сохранении поста
function my_extra_fields_update( $post_id ){
	// базовая проверка
	if (
		empty( $_POST['extra'] )
		|| ! wp_verify_nonce( $_POST['extra_fields_nonce'], __FILE__ )
		|| wp_is_post_autosave( $post_id )
		|| wp_is_post_revision( $post_id )
	)
		return false;

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$_POST['extra'] = array_map( 'sanitize_text_field', $_POST['extra'] ); // чистим все данные от пробелов по краям
	foreach( $_POST['extra'] as $key => $value ){
		if( empty($value) ){
			delete_post_meta( $post_id, $key ); // удаляем поле если значение пустое
			continue;
		}

		update_post_meta( $post_id, $key, $value ); // add_post_meta() работает автоматически
	}

	return $post_id;
}

add_action( 'wp_ajax_my_action', 'my_action_callback' );
add_action( 'wp_ajax_nopriv_my_action', 'my_action_callback' );
function my_action_callback() {


	foreach($_POST as $key=>$value)	{
		$_POST[$key]=strip_tags(htmlspecialchars(stripslashes($value)));
	}

	foreach($_GET as $key=>$value)	{
		$_GET[$key]=strip_tags(htmlspecialchars(stripslashes($value)));
	}

	if ( $_POST['post_title'] == "" ){
		// echo "пустой post_title" . "\n";
		$my_eror = 2;
	}

	if ( $_POST['post_Sub_title'] !== "" ){
		// проверка на заполненость спрятаного поля, своего рода антиспам
		// echo "не пустой post_Sub_title" . "\n";
		$my_eror = 3;
	}

	if ( $_POST['post_content'] == "" ){
		// echo "пустой post_content" . "\n";
		$my_eror = 4;
	}

	if ( $_POST['post_parent'] == "" ){
		// echo "пустой post_parent" . "\n";
		$my_eror = 5;
	}	

	if ( $_POST['Square'] == "" ){
		// echo "пустой Square" . "\n";
		$my_eror = 6;
	}

	if ( $_POST['Square'] == "" ){
		// echo "пустой Square" . "\n";
		$my_eror = 7;
	}

	if ( $_POST['Price'] == "" ){
		// echo "пустой Price" . "\n";
		$my_eror = 8;
	}

	if ( $_POST['Address'] == "" ){
		// echo "пустой Address" . "\n";
		$my_eror = 9;
	}

	if ( $_POST['Address'] == "" ){
		// echo "пустой Address" . "\n";
		$my_eror = 10;
	}

	if ( $_POST['Living_space'] == "" ){
		// echo "пустой Living_space" . "\n";
		$my_eror = 11;
	}

	if ( $_POST['Floor'] == "" ){
		// echo "пустой Floor" . "\n";
		$my_eror = 12;
	}

	if ( !isset($_FILES['hous_img']) ){
		// echo "пустой hous_img" . "\n";
		$my_eror = 13;
	}

	if ($my_eror) {
		echo "Ошибка:" . $my_eror;
		wp_die();
	}

	$post_data = array(
		'post_title'    => $_POST["post_title"],
		'post_content'  => $_POST["post_content"],
		'post_parent'  => $_POST["post_parent"],
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type'     => 'houses',
	);

	$post_id = wp_insert_post( $post_data );

	update_post_meta( $post_id, "Square", $_POST["Square"] );
	update_post_meta( $post_id, "Price", $_POST["Price"] );
	update_post_meta( $post_id, "Address", $_POST["Address"] );
	update_post_meta( $post_id, "Living_space", $_POST["Living_space"] );
	update_post_meta( $post_id, "Floor", $_POST["Floor"] );

	foreach ( $_FILES as $file => $array ) {
		$img_id = $newupload = media_handle_upload( $file, 0);
	}

	set_post_thumbnail( $post_id, $img_id );

	if ( is_wp_error( $img_id ) ) {
		wp_delete_post($post_id);

		echo $img_id->get_error_message();
		$my_eror = 14;
		echo "Ошибка:" . $my_eror;
	} else{
		echo '<a href="'.get_permalink( $post_id).'">Просмотр</a>';
	}

	wp_die();
}