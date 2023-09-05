<?php get_header(); ?>

<form action="" method="post" class="block-form" enctype="multipart/form-data">
	<h4 class="form-heading"><?php _e("Have an idea? Let’s discuss!","Inmost"); ?></h4>
	<div class="d-flex flex-column form-item">
		<label class="label-form" for="email"><?php _e("Email","Inmost"); ?></label>
		<!--Email-->
		<input class="input-form" type="email" name="email" id="email" pattern="^(0|[1-9][0-9]*)$" placeholder="<?php _e("Email*","Inmost"); ?>" required>
		<p class="message-error"><?php _e("This field is required","Inmost"); ?></p>
	</div>
	<div class="form-item">
		<!--Phone number-->
		<input class="input-form" type="text"  name="number" id="number" placeholder="<?php _e("Phone number","Inmost"); ?>">
	</div>
	<div class="form-item">
		<textarea class="input-form textarea-form" name="message" id="message" rows="4" placeholder="<?php _e("Message*","Inmost"); ?>" required></textarea>
		<p class="message-error"><?php _e("This field is required","Inmost"); ?></p>
	</div>
	<div class="block-add-file form-item">
		<!--Upload files-->
		<input class="input-add-file" type="file" name="file" id="file">
		<label class="label-add-file" for="file"> 
			<svg width="19" height="21" viewBox="0 0 19 21" class="img-add-file">
				<use xlink:href="#add-file"/>
			</svg>
			<span class="name-add-file" id="file-chosen"><?php _e("Add an attachment","Inmost"); ?></span>
		</label>
	</div>
	<div class="block-agree form-item">
		<!--Checkbox agree-->
		<input class="input-agree" type="checkbox" id="agree">
		<label class="label-agree" for="agree"><?php _e(" I agree to receive news and updates. For more info, please, read our","Inmost"); ?>
		<a href="<?php echo get_privacy_policy_url(); ?>" class="link-agree"><?php _e("Privacy policy.","Inmost"); ?></a>
	</label>
	<p class="message-error"><?php _e("This field is required","Inmost"); ?></p>
</div>
<div class="form-item">
	<!--Button send-->
	<button class="btn-send btn-green" id="send_message" data-success="Done!">
		<span class="btn-send__text">
			<?php _e("Send Message","Inmost"); ?>
		</span>
		<img class="btn-send__load" src="<?php echo get_stylesheet_directory_uri(); ?>/img/load.svg" alt="loading">
	</button>
</div>
</form>



<script type="text/javascript">
	
jQuery(document).ready(function($){

    function validateEmail(email) {
        var pattern  = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return pattern .test(email);
    }

    function validateMessage(message) {
        if (message.length === 0) return false;
        return true;
    }

    let inputEmail = $("#email"),
        inputNumber = $("#number"),
        inputMessage = $("#message"),
        inputFile = $("#file"),
        inputCheckRules = $("#agree");

    // current text cosen file
    let fakeInputFile = $("#file-chosen"),
        textFakeInputFile = fakeInputFile.text();

    $('#send_message').on('click', function(e){
        e.preventDefault();

        // reset errors
        let form = $(".block-form");
        let error = false;

       form.find(".form-item .message-error").hide();

        let email = inputEmail.val(),
            number = inputNumber.val(),
            message = inputMessage.val(),
            file = inputFile[0].files,
            agree = inputCheckRules.prop('checked');

    if (!validateEmail(email)) {
        inputEmail.siblings('.message-error').show();
        error = true;
    }

    if (!validateMessage(message)) {
        inputMessage.siblings('.message-error').show();
        error = true;
    }

    if (!agree) {
        inputCheckRules.siblings('.message-error').show();
        error = true;
    }

    if (!error) {
        get_in_touch_form_data = new FormData();

        get_in_touch_form_data.append('email',email);
        get_in_touch_form_data.append('number',number);
        get_in_touch_form_data.append('message',message);
        get_in_touch_form_data.append('file',file[0]);

        // show loading
        let text = $(this).find('.btn-send__text'),
            load = $(this).find('.btn-send__load'),
            textSuccess = $(this).data('success');

        text.hide();
        load.show();


        // send data
        $.ajax({
            url: load_more_param.ajaxurl+'?action=get_in_touch',
            type: 'POST',
            processData : false,
            contentType: false,
            data: get_in_touch_form_data,
            success : function(result) {

                // show done
                load.hide();
                text.text(textSuccess).show();

                // reset form
                form.trigger('reset');
                fakeInputFile.text(textFakeInputFile);
            },
            error: function(result) {
                alert(result);
            }
        });
    }
  });

</script>


<?php
//Форма отправки сообщений с файлом
add_action('wp_ajax_get_in_touch', 'get_in_touch_mail_function');
add_action('wp_ajax_nopriv_get_in_touch', 'get_in_touch_mail_function');

function get_in_touch_mail_function() {

    if (!empty ($_FILES['file']['name']) ) {
 
        $wordpress_upload_dir = wp_upload_dir();
        // $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
        // $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
        $i = 1; // number of tries when the file with the same name is already exists
         
        $attacment_file = $_FILES['file'];
        $new_file_path = $wordpress_upload_dir['path'] . '/' . $attacment_file['name'];
        $new_file_mime = mime_content_type( $attacment_file['tmp_name'] );
         
        if( empty( $attacment_file ) )
            die( 'File is not selected.' );
         
        if( $attacment_file['error'] )
            die( $attacment_file['error'] );
         
        if( $attacment_file['size'] > wp_max_upload_size() )
            die( 'It is too large than expected.' );
         
        if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
            die( 'WordPress doesn\'t allow this type of uploads.' );
         
        while( file_exists( $new_file_path ) ) {
            $i++;
            $new_file_path = $wordpress_upload_dir['path'] . '/' . $i . '_' . $attacment_file['name'];
        }
        
    }
     
    // looks like everything is OK
    if( move_uploaded_file( $attacment_file['tmp_name'], $new_file_path ) ) {
     
        $upload_id = wp_insert_attachment( array(
            'guid'           => $new_file_path, 
            'post_mime_type' => $new_file_mime,
            'post_title'     => preg_replace( '/\.[^.]+$/', '', $attacment_file['name'] ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        ), $new_file_path );
     
        // wp_generate_attachment_metadata() won't work if you do not include this file
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
     
        // Generate and save the attachment metas into the database
        wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
            
    }

    $upload_dir = wp_upload_dir();

    $email = $_POST['email'];
    $number = $_POST['number'];
    $send_message = $_POST['message'];
    
    $mail_to = carbon_get_theme_option('get_in_touch_email'.carbon_lang());

    if($email = trim(htmlspecialchars($_POST['email']))){
    $message .= 
    '
    Email :'. $email;}

    if($number = trim(htmlspecialchars($_POST['number']))){
    $message .= 
    '
    number :'. $number;}

    if($send_message = trim(htmlspecialchars($_POST['message']))){
    $message .= 
    '
    message :'. $send_message;}

    $email_subject = 'Контактная форма';
    
    //$attachments = array(WP_CONTENT_DIR . "/uploads/2021/04/image.jpg");
    $attachments = array(WP_CONTENT_DIR . "/uploads".$upload_dir['subdir']."/".$attacment_file["name"]);

    $headers = array(
        'Reply-To: Person Name <'.$email.'>',
    );

    //$attachments = array(WP_CONTENT_DIR . '/uploads/2021/06/img-sales.png');
    
    if($attacment_file == null){
        if (wp_mail($mail_to, $email_subject, $message)){
            echo json_encode('ok');
        }else{
            echo json_encode('err');
        }
    }
    else{
        if (wp_mail($mail_to, $email_subject, $message, $headers, $attachments)){
            echo json_encode('ok');
        }else{
            echo json_encode('err');
        }
        //Удаляем загруженный файл и после отправки
        if($upload_id){
            wp_delete_attachment($upload_id, true);
        }
    }
    die();
}


 ?>