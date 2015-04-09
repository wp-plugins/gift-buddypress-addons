<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Gift_Buddypress
 *
 * @wordpress-plugin
 * Plugin Name:       Gift Buddypress Addons
 * Plugin URI:        http://wordpress.org
 * Description:       Gift Buddypress Addons provide gift management functionality with buddypress plugin. 
 * Version:           1.0.0
 * Author:            Aiyaz
 * Author URI:        http://ayaz.co.nf
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gift-buddypress
 * Domain Path:       /languages
 */


if(!class_exists('Gift_Buddypress_Template'))
{
	class Gift_Buddypress_Template
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			global $bp;
			add_action('wp_enqueue_scripts', array( $this, 'add_my_stylesheet'));
			
			// Initialize Settings
			//require_once(sprintf("%s/settings.php", dirname(__FILE__)));
			//$Gift_Buddypress_Template_Settings = new Gift_Buddypress_Template_Settings();

			
			$plugin = plugin_basename(__FILE__);
			//add_filter("plugin_action_links_$plugin", array( $this, 'plugin_settings_link' ));
			
			// Register custom post types
			require_once(sprintf("%s/includes/gift_post_template.php", dirname(__FILE__)));
			$Gift_Post_Type_Template = new Gift_Post_Type_Template();
			
			require_once(sprintf("%s/includes/gift_post_texonomy.php", dirname(__FILE__)));
			$Gift_Post_Texonomy_Template = new Gift_Post_Texonomy_Template();
			
			add_action( 'init',array( $this,'check_bp_loaded'));
			
			
			add_action( 'bp_setup_nav',array( $this, 'profile_tab_gifts') );
		
		} 

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			/* global $wpdb;
			$table_name = $wpdb->prefix . 'gifts';
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
				title varchar(50) NOT NULL,
				description longtext NOT NULL,
				author mediumint(9) NOT NULL,
				type varchar(50) NOT NULL,
				PRIMARY KEY  (id)
				);";
		 
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			*/
		} 

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} 

		
		public function add_my_stylesheet() 
		{
			
			wp_enqueue_style( 'myCSS', plugins_url( '/css/gift-buddypress.css', __FILE__ ) , '', '', 'screen' );
			wp_enqueue_script(
				'jquery-validate',
				plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js',
				array('jquery'),
				'1.10.0',
				true
			);
			
			wp_enqueue_script( 'main-js', plugin_dir_url( __FILE__ ) . '/js/main.js', array(), '1.0.0', true );
			
			wp_enqueue_style(
				'jquery-validate',
				plugin_dir_url( __FILE__ ) . 'css/style.css',
				array(),
				'1.0'
			);

   
		}

		
		// Add the settings link to the plugins page
		public function plugin_settings_link($links)
		{
			$settings_link = '<a href="options-general.php?page=gift_buddypress_template">Settings</a>';
			array_unshift($links, $settings_link);
			return $links;
		}
		
		public function check_bp_loaded()
		{
			//$integrate_into_buddypress = function_exists( 'buddypress' ) && buddypress() && ! buddypress()->maintenance_mode && bp_is_active( 'xprofile' );
			if (function_exists( 'buddypress' )  && buddypress())
				return;
			else
				add_action('admin_notices', array($this, 'wpse8170_admin_notice') );
				return;
			do_action('check_bp_loaded_or_not');
			
		}
		
		public function wpse8170_admin_notice(){
			echo '<div class="error"><p>BuddyPress Required.</p></div>';
		}

		
		//Buddypress profile navigation menu
		public function profile_tab_gifts() {
			global $bp;
		 
			  bp_core_new_nav_item( array( 
					'parent_url'      => bp_loggedin_user_domain() . '/gifts/',
					'parent_slug'     => $bp->profile->slug,
					'default_subnav_slug' => 'send_gift',
					'show_for_displayed_user' => false, 
					'name' => 'Gifts', 
					'slug' => 'gifts', 
					'screen_function' => array( $this,'send_gift_posts'), 
					'position' => 40,
			  ) );
			  
			   bp_core_new_subnav_item( array( 
					'name' => 'Send Gift',
					'slug' => 'send_gift', 
					'show_for_displayed_user' => false, 
					'parent_url' => bp_loggedin_user_domain() . '/gifts/' ,
					'parent_slug' => $bp->bp_nav['gifts']['slug'],
					'position' => 10,
					'screen_function' => array( $this,'send_gift_posts'),
					) 
				); 
				
				 bp_core_new_subnav_item( array( 
					'name' => 'Received Gift',
					'slug' => 'received_gift', 
					'parent_url' => bp_loggedin_user_domain() . '/gifts/' ,
					'parent_slug' => $bp->bp_nav['gifts']['slug'],
					'position' => 10,
					'screen_function' => array( $this,'received_gifts_posts'),
					) 
				); 
		}
		
		public function send_gift_posts(){	
	
			add_action( 'bp_template_content',array( $this,'send_gifts_content') );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}
		
		public function received_gifts_posts(){	
	
			add_action( 'bp_template_content',array( $this,'received_gifts_content') );
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		}
		
		
		public function send_gifts_content() { 
			global $bp;
			
			if( isset( $_POST["submitgift"])&& !empty($_POST['post_id']) )
			{
				//print_r($_POST); die;

				$post_id = $_POST['post_id'];
				$sender = $_POST['sender'];
				$reciever = $_POST['reciever'];
				
				update_post_meta( $post_id, 'sender_id', $sender );
				update_post_meta( $post_id, 'reciever_id', $reciever );
				//$userid = get_current_user_id();
				//bp_core_add_notification('100', (int)$bp->loggedin_user->id, 'activity', 'activity_viewed');
				//bp_core_add_notification( '100', 1, 'logbooks', 'new_dive' );
				
			} ?>

			
			<form name="SendGiftForm" id="SendGiftForm" method="POST" action="">
			
			<?php
			// show all gifts
			$all_gifts = $this->show_all_gifts();
			echo $all_gifts;
			
			//Show all users
			$this->show_all_users_list();

			?>
			<input type='hidden' name='sender' value='<?php echo $bp->loggedin_user->id; ?>'>
			<input type='submit' name='submitgift' value='Buy Gift' class='form_submit' > 
			</form>
	<?php
		}
		
		public function received_gifts_content() {
	
			$args1 = array (
				'post_type'              => 'gift-post',
				'post_status'            => 'publish',
				'order'                  => 'DESC',
				'orderby'                => 'date',
				'meta_query'             => array(
					array(
						'key'       => 'reciever_id',
						'value'     => get_current_user_id(),
						'compare'   => '=',
					),
				),
			);
			// The Query
			// The Query
			$the_query = new WP_Query( $args1 );
			$html_content = '<div class="gift-div"> <h2>Your Received Gift</h2>';
	
			// The Loop
			if ( $the_query->have_posts() ) {
				$html_content .= '<ul>';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					
					$html_content .= '<li><div class="top_0"><div class="top_1"><span class="img_1"><em></em>';
					$html_content .= get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
					$html_content .= '</span>'; 
					$html_content .= '<h3>'.get_the_title().'</h3>';
					$html_content .= '<span class="hover"><samp></samp><aside><p>';
					$html_content .= get_the_excerpt();
					$html_content .= '</p>';
					$html_content .= '</aside></span></div>';
					//$html_content .= '<b>Price : $'.$meta[0].' </b>';
					//$html_content .= '<input type="radio"  id="post_id" name="post_id" value="'.get_the_ID().'" required /><label> BUY</label></div></li>';
					
				}
				$html_content .= '</ul></div>';
			} else {
				$html_content .= 'No Gift Available';
			}
			echo $html_content;
			/* Restore original Post Data */
			wp_reset_postdata();
		}
		
		
		public function show_all_gifts() {

			$args = array (
				'post_type'              => 'gift-post',
				'post_status'            => 'publish',
				'order'                  => 'DESC',
			);

			// The Query
			$the_query = new WP_Query( $args );
			$html_content = '<div class="gift-div"> <h2>Send Gift</h2>';
			$html_content .= '<p><em>You can only send one gift to a specific friend.</em> </p>';
			// The Loop
			if ( $the_query->have_posts() ) {
				$html_content .= '<ul>';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$meta = get_post_meta( get_the_ID(), 'price' );
					$html_content .= '<li><div class="top_0"><div class="top_1"><span class="img_1"><em></em>';
					$html_content .= get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
					$html_content .= '</span>'; 
					$html_content .= '<h3>'.get_the_title().'</h3>';
					$html_content .= '<span class="hover"><samp></samp><aside><p>';
					$html_content .= get_the_excerpt();
					$html_content .= '</p>';
					$html_content .= '</aside></span></div>';
					$html_content .= '<b>Price : $'.$meta[0].' </b>';
					$html_content .= '<input type="radio"  id="post_id" name="post_id" value="'.get_the_ID().'" required /><label> BUY</label></div></li>';
					
				}
				$html_content .= '</ul></div>';
			} else {
				$html_content .= 'No Gift Available';
			}
		
			/* Restore original Post Data */
			wp_reset_postdata();
			return 	$html_content;
		} // End Show All gifts
		
		
		public function show_all_users_list() {
			//Start BP User List
			global $bp;
			echo '<div class="user-list">';
			if ( bp_has_members() ) : 
				echo '<select name="reciever" required>';
				while ( bp_members() ) : bp_the_member(); 
				echo $bp->bp_loggedin_user_id();
				if(bp_member_user_id() !=  bp_loggedin_user_id() )
				{
				?>
					<option value="<?php echo bp_member_user_id() ?> "> 
					<?php
				
					echo bp_member_name();
					echo '</option>'; 
				}
				endwhile; 
				echo '<select>';
			endif;
			echo '</div>';
			// End BP User List
		}
		
		//Validation form 

			/**
			 * Initiate the script.
			 * Calls the validation options on the comment form.
			 */
		

		// End validation
		
		
		
		
	} // END class
} // END if

function custom_excerpt_length( $length )
{
	return 10;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );
		
function custom_excerpt_more($more) {
    global $post;
    return '<a href="'. get_permalink($post->ID) . '">Read More</a>';
}
add_filter('excerpt_more', 'custom_excerpt_more');

if(class_exists('Gift_Buddypress_Template'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Gift_Buddypress_Template', 'activate'));
	register_deactivation_hook(__FILE__, array('Gift_Buddypress_Template', 'deactivate'));

	// instantiate the plugin class
	$gift_buddypress_template = new Gift_Buddypress_Template();
	//$gift_buddypress_template->check_bp_loaded();
}

		//Notification
		 function log_setup_globals()
			{
				global $bp;
				// some stuff here
				$bp->logbooks->format_notification_function = 'log_format_notifications';
				// some more stuff here
			}
			add_action( 'bp_setup_globals', 'log_setup_globals' );


		function log_format_notifications( $action, $item_id, $secondary_item_id, $total_items )
			{
				global $bp;
				if( 'new_dive' == $action )
				{
					//if ( (int)$total_items > 1 )
					//return apply_filters( 'log_multiple_verifications_notification', 'loggedin_user->domain . logbook/verifications/', title= ' . __( 'Verifications', 'logs' ) . '">' . sprintf( __('You have %d new logbook verifications', 'logs' ), (int)$total_items ) . '', $total_items );
					//else
					//return apply_filters( 'log_single_verification_notification', 'loggedin_user->domain . 'logbook/verifications/” title=”' . __( 'Verifications', 'logs' ) . '”>' . sprintf( __('You have %d new logbook verification', 'logs' ), (int)$total_items ) . '', $total_items );
				}
				do_action( 'log_format_notifications', $action, $item_id, $secondary_item_id, $total_items );
				return false;
			}

		//End Notification
		
