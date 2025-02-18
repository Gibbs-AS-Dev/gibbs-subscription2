<?php
/* Copy of Listeo Core Widget Class */
class Custom_Core_Widget extends WP_Widget {

	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;

	public function __construct() {
		$this->register();
	}

	public function register() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);
		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );
		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	public function get_cached_widget( $args ) {
		return false;
		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();
		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}
		return false;
	}

	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;
		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
		}
		$this->flush_widget_cache();
		return $instance;
	}

	function form( $instance ) {
		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {
			$value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];
			switch ( $setting['type'] ) {
				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'checkbox' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="checkbox" <?php checked( esc_attr( $value ), 'on' ); ?> />
					</p>
					<?php
				break;
				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case 'dropdown' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
						<?php foreach ($setting['options'] as $key => $option_value) { ?>
							<option <?php selected($value,$key); ?> value="<?php echo esc_attr($key); ?>"><?php echo esc_attr($option_value); ?></option>
						<?php } ?></select>
					</p>
					<?php
				break;
			}
		}
	}

	public function widget( $args, $instance ) {}
}

/* Custom Owner Widget */
class custom_Listeo_Core_Owner_Widget extends Custom_Core_Widget  {

    public function __construct() {
        $this->widget_cssclass    = 'listeo_core widget_listing_owner boxed-widget margin-bottom-35';
        $this->widget_description = __( 'Shows Listing Owner box.', 'listeo_core' );
        $this->widget_id          = 'widget_listing_owner';
        $this->widget_name        =  __( 'Child Listeo Owner Widget', 'listeo_core' );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => __( 'Hosted By', 'listeo_core' ),
                'label' => __( 'Title', 'listeo_core' )
            ),
            'phone' => array(
                'type'  => 'checkbox',
                'std'   => 'on',
                'label' => __( 'Phone number', 'listeo_core' )
            ),
            'email' => array(
                'type'  => 'checkbox',
                'std'   => 'on',
                'label' => __( 'Email', 'listeo_core' )
            ),
            'bio' => array(
                'type'  => 'checkbox',
                'std'   => 'on',
                'label' => __( 'Biographical info', 'listeo_core' )
            ),
            'social' => array(
                'type'  => 'checkbox',
                'std'   => 'on',
                'label' => __( 'Social Sites profiles', 'listeo_core' )
            ),
            'contact' => array(
                'type'  => 'checkbox',
                'std'   => 'on',
                'label' => __( 'Show Send message button', 'listeo_core' )
            ),
        );
        $this->register();
    }

    public function widget( $args, $instance ) {

        ob_start();
        extract( $args );
        $title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
        $queried_object = get_queried_object();
        if(!$queried_object){
            return;
        }
        $owner_id = $queried_object->post_author;
        if(!$owner_id) {
            return;
        }
        $owner_data = get_userdata( $owner_id );
        if ( $queried_object ) {
            $post_id = $queried_object->ID;
            $listing_type = get_post_meta( $post_id, '_listing_type', true );
        }
        echo $before_widget;
        if ( $title ) {	?>
            <div class="hosted-by-title">
                <h4><span><?php echo $title; ?></span> <a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>">
                    <?php echo listeo_get_users_name($owner_id); ?></a></h4>
                <a href="<?php echo esc_url(get_author_posts_url( $owner_id )); ?>" class="hosted-by-avatar"><?php echo get_avatar( $owner_id, 56 );  ?></a>
            </div>
        <?php }
        $show_bio = (isset($instance['bio']) && !empty($instance['bio']) && !empty($owner_data->user_description)) ? true : false ;
        if($show_bio){ ?>
            <div class="hosted-by-bio">
                <?php echo wpautop($owner_data->user_description); ?>
            </div>
        <?php }
        $show_email = (isset($instance['email']) && !empty($instance['email'])) ? true : false ;
        $show_phone = (isset($instance['phone']) && !empty($instance['phone'])) ? true : false ;
        $show_social = (isset($instance['social']) && !empty($instance['social'])) ? true : false ;
        $visibility_setting = get_option('listeo_user_contact_details_visibility'); // hide_all, show_all, show_logged, show_booked,
        if($visibility_setting == 'hide_all') {
            $show_details = false;
        } elseif ($visibility_setting == 'show_all') {
            $show_details = true;
        } else {
            if(is_user_logged_in() ){
                if($visibility_setting == 'show_logged'){
                    $show_details = true;
                } else {
                    $show_details = false;
                }
            } else {
                $show_details = false;
            }
        }
        if($show_details){
            if(  $show_email || $show_phone ) {  ?>
                <ul class="listing-details-sidebar">
                    <?php if($show_phone) {  ?>
                        <?php if(isset($owner_data->phone) && !empty($owner_data->phone)): ?>
                            <li><i class="sl sl-icon-phone"></i> <?php echo esc_html($owner_data->phone); ?></li>
                        <?php endif;
                    }
                    if($show_email) {
                        if(isset($owner_data->user_email)): $email = $owner_data->user_email; ?>
                            <li><i class="fa fa-envelope"></i><a href="mailto:<?php echo esc_attr($email);?>"><?php echo esc_html($email);?></a></li>
                        <?php endif; ?>
                    <?php } ?>
                </ul>
            <?php }
        } else {
            if($visibility_setting != 'hide_all') { ?>
            <p id="owner-widget-not-logged-in"><?php printf( esc_html__( 'Please %s sign %s in to see contact details.', 'listeo_core' ), '<a href="#sign-in-dialog" class="sign-in popup-with-zoom-anim">', '</a>' ) ?></p>
        <?php }
        }?>
        <?php if($show_details && $show_social){ ?>
            <ul class="listing-details-sidebar social-profiles">
                <?php if(isset($owner_data->twitter) && !empty($owner_data->twitter)) : ?><li><a href="<?php echo esc_url($owner_data->twitter) ?>" class="twitter-profile"><i class="fa fa-twitter"></i> Twitter</a></li><?php endif; ?>
                <?php if(isset($owner_data->facebook) && !empty($owner_data->facebook)) : ?><li><a href="<?php echo esc_url($owner_data->facebook) ?>" class="facebook-profile"><i class="fa fa-facebook-square"></i> Facebook</a></li><?php endif; ?>
                <?php if(isset($owner_data->instagram) && !empty($owner_data->instagram)) : ?><li><a href="<?php echo esc_url($owner_data->instagram) ?>" class="instagram-profile"><i class="fa fa-instagram"></i> Instagram</a></li><?php endif; ?>
                <?php if(isset($owner_data->linkedin) && !empty($owner_data->linkedin)) : ?><li><a href="<?php echo esc_url($owner_data->linkedin) ?>" class="linkedin-profile"><i class="fa fa-linkedin"></i> LinkedIN</a></li><?php endif; ?>
                <?php if(isset($owner_data->youtube) && !empty($owner_data->youtube)) : ?><li><a href="<?php echo esc_url($owner_data->youtube) ?>" class="youtube-profile"><i class="fa fa-youtube"></i> YouTube</a></li><?php endif; ?>
                <?php if(isset($owner_data->whatsapp) && !empty($owner_data->whatsapp)) : ?><li><a href="<?php if(strpos($owner_data->whatsapp, 'http') === 0) { echo esc_url($owner_data->whatsapp); } else { echo "https://wa.me/".$owner_data->whatsapp; } ?>" class="whatsapp-profile"><i class="fa fa-whatsapp"></i> WhatsApp</a></li><?php endif; ?>
                <?php if(isset($owner_data->skype) && !empty($owner_data->skype)) : ?><li>
                    <a href="<?php if(strpos($owner_data->skype, 'http') === 0) { echo esc_url($owner_data->skype); } else { echo "skype:+".$owner_data->skype."?call"; } ?>" class="skype-profile"><i class="fa fa-skype"></i> Skype</a></li><?php endif; ?>
                <!-- <li><a href="#" class="gplus-profile"><i class="fa fa-google-plus"></i> Google Plus</a></li> -->
            </ul>
        <?php } ?>
            <?php
            if(is_user_logged_in()):
                if((isset($instance['contact']) && !empty($instance['contact']))) : ?>
                <!-- Reply to review popup -->
                <div id="small-dialog" class="zoom-anim-dialog mfp-hide">
                    <div class="small-dialog-header">
                        <h3><?php esc_html_e('Send Message', 'listeo_core'); ?></h3>
                    </div>
                    <div class="message-reply margin-top-0">
                        <form action="" id="send-message-from-widget" data-listingid="<?php echo esc_attr($post_id); ?>">
                            <textarea required data-recipient="<?php echo esc_attr($owner_id); ?>" data-referral="listing_<?php echo esc_attr($post_id); ?>" cols="40" id="contact-message" name="message" rows="3" placeholder="<?php esc_attr_e('Your message to ','listeo_core'); echo $owner_data->first_name; ?>"></textarea>
                            <button class="button">
                            <i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i><?php esc_html_e('Send Message', 'listeo_core'); ?></button>
                            <div class="notification closeable success margin-top-20"></div>
                        </form>
                    </div>
                </div>
                <a href="#small-dialog" class="send-message-to-owner button popup-with-zoom-anim"><i class="sl sl-icon-envelope-open"></i> <?php esc_html_e('Send Message', 'listeo_core'); ?></a>
                <?php endif; ?>
            <?php endif; ?>
    <?php
        echo $after_widget;
        $content = ob_get_clean();
        echo $content;
        $this->cache_widget( $args, $content );
    }
}
