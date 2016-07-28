<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) { exit; }

class ProposalPress_Admin {

	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		if( proposalpress_get_the_type() ) {
			remove_action( 'media_buttons_context', 'add_slgf_custom_button' );
			remove_action( 'admin_footer', 'add_slgf_inline_popup_content' );
		}

	}

	public function enqueue_styles() {

		global $pagenow;

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin.css', array(), $this->version, 'all' );

		if ( ( $pagenow == 'post.php' || $pagenow == 'edit.php' ) && proposalpress_get_the_type() ) {
			wp_enqueue_style( 'thickbox' );
		}

	}

	public function enqueue_scripts() {

		global $pagenow;

		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'page' )
			return;

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name , 'proposalpress_payments', apply_filters( 'proposalpress_payments_localized_script', array(
			'tax' => proposalpress_get_tax_amount(),
			'currency_symbol' => proposalpress_get_currency_symbol(),
			'currency_pos' => proposalpress_get_currency_position(),
			'thousand_sep' => proposalpress_get_thousand_seperator(),
			'decimal_sep' => proposalpress_get_decimal_seperator(),
			'decimals' => proposalpress_get_decimals(),
			)
		)
		);
		wp_localize_script( $this->plugin_name, 'proposalpress_confirm', array(
			'convert_quote' => sprintf( __( 'Are you sure you want to convert from %1s to %2s', 'proposalpress' ), proposalpress_get_quote_label(), proposalpress_get_invoice_label() ),
			)
		);

		/*
		 * Conditionally enqueue the new client script
		 */
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && ( proposalpress_get_the_type() ) ) {
			wp_enqueue_script( $this->plugin_name . '-new-client', plugin_dir_url( __FILE__ ) . 'js/new-client.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( $this->plugin_name . '-new-client' , 'proposalpress_new_client', array( 'proposalpress_ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			wp_enqueue_script( 'password-strength-meter' );
			wp_enqueue_script( 'user-profile' );
		}

		/*
		 * Conditionally enqueue thickbox
		 */
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'edit.php' ) && ( proposalpress_get_the_type() ) ) {
			wp_enqueue_script( 'thickbox' );
		}

		/*
		 * Conditionally enqueue the quick edit js
		 */
		if ( ( $pagenow == 'edit.php' ) && ( proposalpress_get_the_type() ) ) {
			//wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ), '', false );
			wp_enqueue_script( $this->plugin_name . 'quick-edit', plugin_dir_url( __FILE__ ) . 'js/quick-edit.js', array( 'jquery' ), $this->version, false );
		}

		/*
		 * Conditionally enqueue the charts script
		 */
		if ( ( $pagenow == 'admin.php' ) && ( $_GET['page'] == 'proposalpress_reports' ) ) {
			wp_enqueue_script( $this->plugin_name . '-chart', plugin_dir_url( __FILE__ ) . 'js/Chart.min.js', array( 'jquery' ), $this->version, false );
		}

	}

	public function add_admin_body_class( $classes ) {

		global $pagenow;
		$add_class = false;
		if( $pagenow == 'admin.php' && isset( $_GET['page'] ) ) {
			$add_class = strpos( $_GET['page'], 'proposalpress_' );
		}

		if ( proposalpress_get_the_type() || $add_class !== false ) {
			$classes .= ' proposalpress ';
		}

		return $classes;
	}

	public function new_cpt_quote() {

		$translate = get_option( 'proposalpress_translate' );

		$cap_type 	= 'post';
		$plural 	= proposalpress_get_quote_label_plural();
		$single 	= proposalpress_get_quote_label();
		$cpt_name 	= 'proposalpress_quote';

		$opts['can_export']								= TRUE;
		$opts['capability_type']						= $cap_type;
		$opts['description']							= '';
		$opts['exclude_from_search']					= TRUE;
		$opts['has_archive']							= FALSE;
		$opts['hierarchical']							= TRUE;
		$opts['map_meta_cap']							= TRUE;
		$opts['menu_icon']								= 'dashicons-proposalpress';
		// $opts['menu_position']							= 99.3;
		$opts['public']									= TRUE;
		$opts['publicly_querable']						= TRUE;
		$opts['query_var']								= TRUE;
		$opts['register_meta_box_cb']					= '';
		$opts['rewrite']								= FALSE;
		$opts['show_in_admin_bar']						= TRUE;
		$opts['show_in_menu']							= TRUE;
		$opts['show_in_nav_menu']						= TRUE;
		$opts['show_ui']								= TRUE;
		$opts['supports']								= array( 'title', 'comments' );
		$opts['taxonomies']								= array( 'quote_category' );

		$opts['capabilities']['delete_others_posts']	= "delete_others_{$cap_type}s";
		$opts['capabilities']['delete_post']			= "delete_{$cap_type}";
		$opts['capabilities']['delete_posts']			= "delete_{$cap_type}s";
		$opts['capabilities']['delete_private_posts']	= "delete_private_{$cap_type}s";
		$opts['capabilities']['delete_published_posts']	= "delete_published_{$cap_type}s";
		$opts['capabilities']['edit_others_posts']		= "edit_others_{$cap_type}s";
		$opts['capabilities']['edit_post']				= "edit_{$cap_type}";
		$opts['capabilities']['edit_posts']				= "edit_{$cap_type}s";
		$opts['capabilities']['edit_private_posts']		= "edit_private_{$cap_type}s";
		$opts['capabilities']['edit_published_posts']	= "edit_published_{$cap_type}s";
		$opts['capabilities']['publish_posts']			= "publish_{$cap_type}s";
		$opts['capabilities']['read_post']				= "read_{$cap_type}";
		$opts['capabilities']['read_private_posts']		= "read_private_{$cap_type}s";

		$opts['labels']['add_new']						= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['add_new_item']					= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['all_items']					= __( $plural, 'proposalpress' );
		$opts['labels']['edit_item']					= __( "Edit {$single}" , 'proposalpress' );
		$opts['labels']['menu_name']					= __( $plural, 'proposalpress' );
		$opts['labels']['name']							= __( $plural, 'proposalpress' );
		$opts['labels']['name_admin_bar']				= __( $single, 'proposalpress' );
		$opts['labels']['new_item']						= __( "New {$single}", 'proposalpress' );
		$opts['labels']['not_found']					= __( "No {$plural} Found", 'proposalpress' );
		$opts['labels']['not_found_in_trash']			= __( "No {$plural} Found in Trash", 'proposalpress' );
		$opts['labels']['parent_item_colon']			= __( "Parent {$plural} :", 'proposalpress' );
		$opts['labels']['search_items']					= __( "Search {$plural}", 'proposalpress' );
		$opts['labels']['singular_name']				= __( $single, 'proposalpress' );
		$opts['labels']['view_item']					= __( "View {$single}", 'proposalpress' );

		$opts['rewrite']['slug']						= FALSE;
		$opts['rewrite']['with_front']					= FALSE;
		$opts['rewrite']['feeds']						= FALSE;
		$opts['rewrite']['pages']						= FALSE;

		$opts = apply_filters( 'proposalpress_quote_params', $opts );

		register_post_type( 'proposalpress_quote', $opts );

	}
	
	public function new_cpt_section() {

		$translate = get_option( 'proposalpress_translate' );

		$cap_type 	= 'post';
		$plural 	= proposalpress_get_section_label_plural();
		$single 	= proposalpress_get_section_label();
		$cpt_name 	= 'proposalpress_section';

		$opts['can_export']								= TRUE;
		$opts['capability_type']						= $cap_type;
		$opts['description']							= '';
		$opts['exclude_from_search']					= TRUE;
		$opts['has_archive']							= FALSE;
		$opts['hierarchical']							= TRUE;
		$opts['map_meta_cap']							= TRUE;
		$opts['menu_icon']								= 'dashicons-proposalpress';
		// $opts['menu_position']							= 99.3;
		$opts['public']									= TRUE;
		$opts['publicly_querable']						= TRUE;
		$opts['query_var']								= TRUE;
		$opts['register_meta_box_cb']					= '';
		$opts['rewrite']								= FALSE;
		$opts['show_in_admin_bar']						= TRUE;
		$opts['show_in_menu']							= TRUE;
		$opts['show_in_nav_menu']						= TRUE;
		$opts['show_ui']								= TRUE;
		$opts['supports']								= array( 'title', 'comments' );
		$opts['taxonomies']								= array( 'section_category' );

		$opts['capabilities']['delete_others_posts']	= "delete_others_{$cap_type}s";
		$opts['capabilities']['delete_post']			= "delete_{$cap_type}";
		$opts['capabilities']['delete_posts']			= "delete_{$cap_type}s";
		$opts['capabilities']['delete_private_posts']	= "delete_private_{$cap_type}s";
		$opts['capabilities']['delete_published_posts']	= "delete_published_{$cap_type}s";
		$opts['capabilities']['edit_others_posts']		= "edit_others_{$cap_type}s";
		$opts['capabilities']['edit_post']				= "edit_{$cap_type}";
		$opts['capabilities']['edit_posts']				= "edit_{$cap_type}s";
		$opts['capabilities']['edit_private_posts']		= "edit_private_{$cap_type}s";
		$opts['capabilities']['edit_published_posts']	= "edit_published_{$cap_type}s";
		$opts['capabilities']['publish_posts']			= "publish_{$cap_type}s";
		$opts['capabilities']['read_post']				= "read_{$cap_type}";
		$opts['capabilities']['read_private_posts']		= "read_private_{$cap_type}s";

		$opts['labels']['add_new']						= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['add_new_item']					= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['all_items']					= __( $plural, 'proposalpress' );
		$opts['labels']['edit_item']					= __( "Edit {$single}" , 'proposalpress' );
		$opts['labels']['menu_name']					= __( $plural, 'proposalpress' );
		$opts['labels']['name']							= __( $plural, 'proposalpress' );
		$opts['labels']['name_admin_bar']				= __( $single, 'proposalpress' );
		$opts['labels']['new_item']						= __( "New {$single}", 'proposalpress' );
		$opts['labels']['not_found']					= __( "No {$plural} Found", 'proposalpress' );
		$opts['labels']['not_found_in_trash']			= __( "No {$plural} Found in Trash", 'proposalpress' );
		$opts['labels']['parent_item_colon']			= __( "Parent {$plural} :", 'proposalpress' );
		$opts['labels']['search_items']					= __( "Search {$plural}", 'proposalpress' );
		$opts['labels']['singular_name']				= __( $single, 'proposalpress' );
		$opts['labels']['view_item']					= __( "View {$single}", 'proposalpress' );

		$opts['rewrite']['slug']						= FALSE;
		$opts['rewrite']['with_front']					= FALSE;
		$opts['rewrite']['feeds']						= FALSE;
		$opts['rewrite']['pages']						= FALSE;

		$opts = apply_filters( 'proposalpress_section_params', $opts );

		register_post_type( 'proposalpress_section', $opts );

	}

	public function new_cpt_proposal() {

		$translate = get_option( 'proposalpress_translate' );

		$cap_type 	= 'post';
		$plural 	= proposalpress_get_proposal_label_plural();
		$single 	= proposalpress_get_proposal_label();
		$cpt_name 	= 'proposalpress_proposal';

		$opts['can_export']								= TRUE;
		$opts['capability_type']						= $cap_type;
		$opts['description']							= '';
		$opts['exclude_from_search']					= TRUE;
		$opts['has_archive']							= FALSE;
		$opts['hierarchical']							= TRUE;
		$opts['map_meta_cap']							= TRUE;
		$opts['menu_icon']								= 'dashicons-proposalpress';
		// $opts['menu_position']							= 99.4;
		$opts['public']									= TRUE;
		$opts['publicly_querable']						= TRUE;
		$opts['query_var']								= TRUE;
		$opts['register_meta_box_cb']					= '';
		$opts['rewrite']								= FALSE;
		$opts['show_in_admin_bar']						= TRUE;
		$opts['show_in_menu']							= TRUE;
		$opts['show_in_nav_menu']						= TRUE;
		$opts['show_ui']								= TRUE;
		$opts['supports']								= array( 'title' );
		$opts['taxonomies']								= array( 'proposal_status' );

		$opts['capabilities']['delete_others_posts']	= "delete_others_{$cap_type}s";
		$opts['capabilities']['delete_post']			= "delete_{$cap_type}";
		$opts['capabilities']['delete_posts']			= "delete_{$cap_type}s";
		$opts['capabilities']['delete_private_posts']	= "delete_private_{$cap_type}s";
		$opts['capabilities']['delete_published_posts']	= "delete_published_{$cap_type}s";
		$opts['capabilities']['edit_others_posts']		= "edit_others_{$cap_type}s";
		$opts['capabilities']['edit_post']				= "edit_{$cap_type}";
		$opts['capabilities']['edit_posts']				= "edit_{$cap_type}s";
		$opts['capabilities']['edit_private_posts']		= "edit_private_{$cap_type}s";
		$opts['capabilities']['edit_published_posts']	= "edit_published_{$cap_type}s";
		$opts['capabilities']['publish_posts']			= "publish_{$cap_type}s";
		$opts['capabilities']['read_post']				= "read_{$cap_type}";
		$opts['capabilities']['read_private_posts']		= "read_private_{$cap_type}s";

		$opts['labels']['add_new']						= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['add_new_item']					= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['all_items']					= __( $plural, 'proposalpress' );
		$opts['labels']['edit_item']					= __( "Edit {$single}" , 'proposalpress' );
		$opts['labels']['menu_name']					= __( $plural, 'proposalpress' );
		$opts['labels']['name']							= __( $plural, 'proposalpress' );
		$opts['labels']['name_admin_bar']				= __( $single, 'proposalpress' );
		$opts['labels']['new_item']						= __( "New {$single}", 'proposalpress' );
		$opts['labels']['not_found']					= __( "No {$plural} Found", 'proposalpress' );
		$opts['labels']['not_found_in_trash']			= __( "No {$plural} Found in Trash", 'proposalpress' );
		$opts['labels']['parent_item_colon']			= __( "Parent {$plural} :", 'proposalpress' );
		$opts['labels']['search_items']					= __( "Search {$plural}", 'proposalpress' );
		$opts['labels']['singular_name']				= __( $single, 'proposalpress' );
		$opts['labels']['view_item']					= __( "View {$single}", 'proposalpress' );

		$opts['rewrite']['slug']						= FALSE;
		$opts['rewrite']['with_front']					= FALSE;
		$opts['rewrite']['feeds']						= FALSE;
		$opts['rewrite']['pages']						= FALSE;

		$opts = apply_filters( 'proposalpress_proposal_params', $opts );

		register_post_type( 'proposalpress_proposal', $opts );

	}

	public function new_taxonomy_quote_category() {

		$plural 	= 'Categories';
		$single 	= 'Category';
		$tax_name 	= 'quote_category';

		$opts['hierarchical']							= TRUE;
		$opts['public']									= TRUE;
		$opts['query_var']								= $tax_name;
		$opts['show_admin_column'] 						= TRUE;
		$opts['show_in_nav_menus']						= FALSE;
		$opts['show_tag_cloud'] 						= FALSE;
		$opts['show_ui']								= FALSE;
		$opts['sort'] 									= '';

		$opts['capabilities']['assign_terms'] 			= 'edit_posts';
		$opts['capabilities']['delete_terms'] 			= 'manage_categories';
		$opts['capabilities']['edit_terms'] 			= 'manage_categories';
		$opts['capabilities']['manage_terms'] 			= 'manage_categories';

		$opts['labels']['add_new_item'] 				= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['add_or_remove_items'] 			= __( "Add or remove {$plural}", 'proposalpress' );
		$opts['labels']['all_items'] 					= __( $plural, 'proposalpress' );
		$opts['labels']['choose_from_most_used'] 		= __( "Choose from most used {$plural}", 'proposalpress' );
		$opts['labels']['edit_item'] 					= __( "Edit {$single}" , 'proposalpress');
		$opts['labels']['menu_name'] 					= __( $plural, 'proposalpress' );
		$opts['labels']['name'] 						= __( $plural, 'proposalpress' );
		$opts['labels']['new_item_name'] 				= __( "New {$single} Name", 'proposalpress' );
		$opts['labels']['not_found'] 					= __( "No {$plural} Found", 'proposalpress' );
		$opts['labels']['parent_item'] 					= __( "Parent {$single}", 'proposalpress' );
		$opts['labels']['parent_item_colon'] 			= __( "Parent {$single}:", 'proposalpress' );
		$opts['labels']['popular_items'] 				= __( "Popular {$plural}", 'proposalpress' );
		$opts['labels']['search_items'] 				= __( "Search {$plural}", 'proposalpress' );
		$opts['labels']['separate_items_with_commas'] 	= __( "Separate {$plural} with commas", 'proposalpress' );
		$opts['labels']['singular_name'] 				= __( $single, 'proposalpress' );
		$opts['labels']['update_item'] 					= __( "Update {$single}", 'proposalpress' );
		$opts['labels']['view_item'] 					= __( "View {$single}", 'proposalpress' );

		$opts['rewrite']['slug']						= __( strtolower( $tax_name ), 'proposalpress' );

		$opts = apply_filters( 'proposalpress_quote_category_params', $opts );

		register_taxonomy( $tax_name, 'proposalpress_quote', $opts );

	}

	public function new_taxonomy_proposal_status() {

	    $plural 	= 'Statuses';
		$single 	= 'Status';
		$tax_name 	= 'proposal_status';

		$opts['hierarchical']							= TRUE;
		$opts['public']									= TRUE;
		$opts['query_var']								= $tax_name;
		$opts['show_admin_column'] 						= TRUE;
		$opts['show_in_nav_menus']						= FALSE;
		$opts['show_tag_cloud'] 						= FALSE;
		$opts['show_ui']								= FALSE;
		$opts['sort'] 									= '';

		$opts['capabilities']['assign_terms'] 			= 'edit_posts';
		$opts['capabilities']['delete_terms'] 			= 'manage_categories';
		$opts['capabilities']['edit_terms'] 			= 'manage_categories';
		$opts['capabilities']['manage_terms'] 			= 'manage_categories';

		$opts['labels']['add_new_item'] 				= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['add_or_remove_items'] 			= __( "Add or remove {$plural}", 'proposalpress' );
		$opts['labels']['all_items'] 					= __( $plural, 'proposalpress' );
		$opts['labels']['choose_from_most_used'] 		= __( "Choose from most used {$plural}", 'proposalpress' );
		$opts['labels']['edit_item'] 					= __( "Edit {$single}" , 'proposalpress');
		$opts['labels']['menu_name'] 					= __( $plural, 'proposalpress' );
		$opts['labels']['name'] 						= __( $plural, 'proposalpress' );
		$opts['labels']['new_item_name'] 				= __( "New {$single} Name", 'proposalpress' );
		$opts['labels']['not_found'] 					= __( "No {$plural} Found", 'proposalpress' );
		$opts['labels']['parent_item'] 					= __( "Parent {$single}", 'proposalpress' );
		$opts['labels']['parent_item_colon'] 			= __( "Parent {$single}:", 'proposalpress' );
		$opts['labels']['popular_items'] 				= __( "Popular {$plural}", 'proposalpress' );
		$opts['labels']['search_items'] 				= __( "Search {$plural}", 'proposalpress' );
		$opts['labels']['separate_items_with_commas'] 	= __( "Separate {$plural} with commas", 'proposalpress' );
		$opts['labels']['singular_name'] 				= __( $single, 'proposalpress' );
		$opts['labels']['update_item'] 					= __( "Update {$single}", 'proposalpress' );
		$opts['labels']['view_item'] 					= __( "View {$single}", 'proposalpress' );

		$opts['rewrite']['slug']						= __( strtolower( $tax_name ), 'proposalpress' );

		$opts = apply_filters( 'proposalpress_proposal_status_params', $opts );

		register_taxonomy( $tax_name, 'proposalpress_proposal', $opts );

	}
	
	public function new_taxonomy_section_category() {

		$plural 	= 'Categories';
		$single 	= 'Category';
		$tax_name 	= 'section_category';

		$opts['hierarchical']							= TRUE;
		$opts['public']									= TRUE;
		$opts['query_var']								= $tax_name;
		$opts['show_admin_column'] 						= TRUE;
		$opts['show_in_nav_menus']						= FALSE;
		$opts['show_tag_cloud'] 						= FALSE;
		$opts['show_ui']								= FALSE;
		$opts['sort'] 									= '';

		$opts['capabilities']['assign_terms'] 			= 'edit_posts';
		$opts['capabilities']['delete_terms'] 			= 'manage_categories';
		$opts['capabilities']['edit_terms'] 			= 'manage_categories';
		$opts['capabilities']['manage_terms'] 			= 'manage_categories';

		$opts['labels']['add_new_item'] 				= __( "Add New {$single}", 'proposalpress' );
		$opts['labels']['add_or_remove_items'] 			= __( "Add or remove {$plural}", 'proposalpress' );
		$opts['labels']['all_items'] 					= __( $plural, 'proposalpress' );
		$opts['labels']['choose_from_most_used'] 		= __( "Choose from most used {$plural}", 'proposalpress' );
		$opts['labels']['edit_item'] 					= __( "Edit {$single}" , 'proposalpress');
		$opts['labels']['menu_name'] 					= __( $plural, 'proposalpress' );
		$opts['labels']['name'] 						= __( $plural, 'proposalpress' );
		$opts['labels']['new_item_name'] 				= __( "New {$single} Name", 'proposalpress' );
		$opts['labels']['not_found'] 					= __( "No {$plural} Found", 'proposalpress' );
		$opts['labels']['parent_item'] 					= __( "Parent {$single}", 'proposalpress' );
		$opts['labels']['parent_item_colon'] 			= __( "Parent {$single}:", 'proposalpress' );
		$opts['labels']['popular_items'] 				= __( "Popular {$plural}", 'proposalpress' );
		$opts['labels']['search_items'] 				= __( "Search {$plural}", 'proposalpress' );
		$opts['labels']['separate_items_with_commas'] 	= __( "Separate {$plural} with commas", 'proposalpress' );
		$opts['labels']['singular_name'] 				= __( $single, 'proposalpress' );
		$opts['labels']['update_item'] 					= __( "Update {$single}", 'proposalpress' );
		$opts['labels']['view_item'] 					= __( "View {$single}", 'proposalpress' );

		$opts['rewrite']['slug']						= __( strtolower( $tax_name ), 'proposalpress' );

		$opts = apply_filters( 'proposalpress_section_category_params', $opts );

		register_taxonomy( $tax_name, 'proposalpress_section', $opts );

	}

	public function custom_admin_notices( $post_states ) {

	    global $pagenow;

		/*
		 * Options updated notice
		 */
		if ( $pagenow == 'admin.php' && ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'proposalpress_' ) !== false ) && isset( $_POST['submit-cmb'] ) ) {
			echo '<div class="updated">
				<p>' . __( 'Settings saved successfully.', 'proposalpress' ) . '</p>
			</div>';
		}

		/*
		 * Email sent notice
		 */
		if ( $pagenow == 'edit.php' && isset($_GET['email']) && $_GET['email'] == 'sent' ) {
			echo '<div class="updated">
				<p>' . __( 'Email was sent successfully.', 'proposalpress' ) . '</p>
			</div>';
		}
		/*
		 * Converted quote to invoice notice
		 */
		if ( $pagenow == 'post.php' && isset($_GET['converted']) && $_GET['converted'] == 'invoice' ) {
			echo '<div class="updated">
				<p>' . sprintf( __( 'Successfully converted %1s to %2s', 'proposalpress' ), proposalpress_get_quote_label(), proposalpress_get_invoice_label() ) . '</p>
			</div>';
		}
		/*
		 * Possible not compatible notices
		 */
		$errors = get_transient( 'proposalpress_activation_warning' );
	    if ( $errors ) {
	    
		    if ( $pagenow == 'plugins.php' && isset($errors['wp_error'] ) ) {
		         echo '<div class="error">
		             <p>' . __( 'Your WordPress version may not be compatible with the Proposal Press plugin. If you are having issues with the plugin, we recommend making a backup of your site and upgrading to the latest version of WordPress.', 'proposalpress' ) . '</p>
		         </div>';
		    }
		    if ( $pagenow == 'plugins.php' && isset($errors['php_error'] ) ) {
		         echo '<div class="error">
		             <p>' . __( 'Your PHP version may not be compatible with the Proposal Press plugin. We recommend contacting your server administrator and getting them to upgrade to a newer version of PHP.', 'proposalpress' ) . '</p>
		         </div>';
		    }
		    if ( $pagenow == 'plugins.php' && isset($errors['curl_error'] ) ) {
		         echo '<div class="error">
		             <p>' . __( 'You do not have the cURL extension installed on your server. This extension is required for some tasks including PayPal payments. Please contact your server administrator to have them install this on your server.', 'proposalpress' ) . '</p>
		         </div>';
		    }

		}

	}

	public function proposal_quote_updated_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		if ( $post_type == 'proposalpress_quote' || $post_type == 'proposalpress_proposal' ) {

			$label = proposalpress_get_label();

			$messages[$post_type] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => sprintf( __( '%s updated.', 'proposalpress' ), $label ),
				2  => '',
				3  => '',
				4  => sprintf( __( '%s updated.', 'proposalpress' ), $label ),
				/* translators: %s: date and time of the revision */
				5  => isset( $_GET['revision'] ) ? sprintf( __( '%1s restored to revision from %2s', 'proposalpress' ), $label, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => sprintf( __( '%s published.', 'proposalpress' ), $label ),
				7  => sprintf( __( '%s saved.', 'proposalpress' ), $label ),
				8  => sprintf( __( '%s submitted.', 'proposalpress' ), $label ),
				9  => '',
				10 => sprintf( __( '%s draft updated.', 'proposalpress' ), $label )
			);

			if ( $post_type_object->publicly_queryable ) {

				$permalink = get_permalink( $post->ID );

				$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View ' . $label, 'proposalpress' ) );
				$messages[ $post_type ][1] .= $view_link;
				$messages[ $post_type ][6] .= $view_link;
				$messages[ $post_type ][9] .= $view_link;

				$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
				$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview ' . $label, 'proposalpress' ) );
				$messages[ $post_type ][8]  .= $preview_link;
				$messages[ $post_type ][10] .= $preview_link;
			}

		}

		return $messages;

	}

	public function custom_enter_title( $input ) {

		global $post_type;

		if ( is_admin() ) {
			if ( 'proposalpress_quote' == $post_type )
				return sprintf( __( 'Enter %s title', 'proposalpress' ), proposalpress_get_quote_label() );

			if ( 'proposalpress_proposal' == $post_type )
				return sprintf( __( 'Enter %s title', 'proposalpress' ), proposalpress_get_invoice_label() );
		}

		return $input;
	}

	public function plugin_action_links( $links ) {

		$links[] = '<a href="'. esc_url( get_admin_url( null, 'admin.php?page=proposalpress_general' ) ) .'">' . __( 'Settings', 'proposalpress' ) . '</a>';
		$links[] = '<a href="https://proposalpress.com/extensions/?utm_source=Plugin&utm_medium=Plugins-Page&utm_content=Extensions&utm_campaign=Free" target="_blank">' . __( 'Extensions', 'proposalpress' ) . '</a>';
		return $links;

	}

	public function admin_footer_text( $footer_text ) {

		if ( ! current_user_can( 'manage_options' ) )
			return $footer_text;

		if ( proposalpress_get_the_type() || ( isset( $_GET['page'] ) && strpos( $_GET['page'], 'proposalpress') ) ) {

			$footer_text = sprintf( __( 'If you like <strong>Proposal Press</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you in advance!', 'proposalpress' ), '<a href="https://wordpress.org/support/view/plugin-reviews/proposalpress?filter=5#postform" target="_blank" class="">', '</a>' );

		}

		return $footer_text;
	}

	public function remove_some_junk() {

		$type = proposalpress_get_the_type();

		if ( $type ) {

			remove_meta_box('pageparentdiv', 'proposalpress_' . $type, 'side');
			remove_meta_box( $type . '_statusdiv', 'proposalpress_' . $type, 'side' );

		}

	}

	private function work_out_date_format( $date ) {

		$format = get_option( 'date_format' );

		if (strpos( $format, 'd/m') !== false) {
			$date = str_replace("/", ".", $date);
		}

		$date = date("Y-m-d H:i:s", strtotime( $date ) );

		// final check if we get a weird data
		if( $date == '1970-01-01 00:00:00' ) {
			$date = current_time( 'mysql' ); 
		}

		return $date;

	}

	public function set_published_date_as_created( $post_id ) {
		// If this is a revision, get real post ID
		if ( $parent_id = wp_is_post_revision( $post_id ) )
			$post_id = $parent_id;

		if ( ! $_POST )
			return;

		// Check if this post is in default category
		if ( proposalpress_get_the_type( $post_id ) ) {

			// unhook this function so it doesn't loop infinitely
			remove_action( 'save_post', array( $this, 'set_published_date_as_created' ) );

			// update the post, which calls save_post again
			$type = proposalpress_get_the_type($post_id);

			if( isset( $_POST['proposalpress_created'] ) )	{
				$created = $_POST['proposalpress_created'];
			} elseif ( isset( $_POST['_proposalpress_' . $type . '_created'] ) ) {
				$created = $_POST['_proposalpress_' . $type . '_created'];
			} else {
				$created = current_time( 'mysql' ); 
			}
			// change the format if we have slashes
			$created = $this->work_out_date_format( $created );
			
			wp_update_post( array( 'ID' => $post_id, 'post_date' => $created ) );

			// re-hook this function
			add_action( 'save_post', array( $this, 'set_published_date_as_created' ) );
		}
	}

	public function mark_proposal_expired() {

		$taxonomy = 'proposal_status';
		$args = array(
			'post_type'     =>  'proposalpress_proposal',
			'status'     	=>  'publish',
			'meta_query'    =>  array(
				array(
					'key' 		=>  '_proposalpress_proposal_due',
					'value' 	=>  current_time( 'timestamp' ),
					'compare' 	=>  '<',
				),
			),
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field'    => 'slug',
					'terms'    => 'unpaid',
				),
			),
		);
		$overdue = get_posts( apply_filters( 'proposalpress_mark_expired_query', $args ) );

		/*
		 * If a post exists, mark it as overdue.
		 */
		foreach ( $overdue as $post ) {
			Sliced_Invoice::set_as_overdue( $post->ID );
		}

	}

	public static function get_statuses() {
		$type = proposalpress_get_the_type();
		$terms = get_terms( $type . '_status', array( 'hide_empty' => 0 ) );
		return $terms;
	}

	public static function get_clients() {

		global $current_user;

		if( ! function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php");
		}

		$current_user = wp_get_current_user();

		$args = array(
			'orderby'   => 'meta_value',
			'order'     => 'ASC',
			'exclude'   => $current_user->ID,
			'meta_key'  => '_proposalpress_client_business',
			'compare'   => 'EXISTS',
		);

		$user_query = new WP_User_Query( apply_filters( 'proposalpress_client_query', $args ) );

		$user_options = array( '' => __( 'Choose client', 'proposalpress' ) );

		if ( ! empty( $user_query->results ) ) {
			foreach ( $user_query->results as $user ) {
				$user_options[$user->ID] = get_user_meta( $user->ID, '_proposalpress_client_business', true );
			}
		}

		return $user_options;

	}

	public function client_registration_form() {

	    global $pagenow;

		/*
		 * Only load on the post edit or post new screens.
		 */
		if ( ( $pagenow == 'post.php' || $pagenow == 'post-new.php' ) && ( proposalpress_get_the_type() ) ) {

			/*
			 * Load up the passed data, else set to a default.
			 */
			$creating = isset( $_POST['createuser'] );

			$new_user_login = $creating && isset( $_POST['user_login'] ) ? wp_unslash( $_POST['user_login'] ) : '';
			$new_user_firstname = $creating && isset( $_POST['first_name'] ) ? wp_unslash( $_POST['first_name'] ) : '';
			$new_user_lastname = $creating && isset( $_POST['last_name'] ) ? wp_unslash( $_POST['last_name'] ) : '';
			$new_user_email = $creating && isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
			$new_user_uri = $creating && isset( $_POST['url'] ) ? wp_unslash( $_POST['url'] ) : '';
			$new_user_role = $creating && isset( $_POST['role'] ) ? wp_unslash( $_POST['role'] ) : '';
			$new_user_send_password = $creating && isset( $_POST['send_password'] ) ? wp_unslash( $_POST['send_password'] ) : true;
			$new_user_ignore_pass = $creating && isset( $_POST['noconfirmation'] ) ? wp_unslash( $_POST['noconfirmation'] ) : '';

			/*
			 * The form is basically copied from the core new user page.
			 */
			?>
				<div id="add-ajax-user" style="display:none">

					<div class="alert result-message">&nbsp;</div>

					<p>Add a new client here. This will create a new WordPress User in the database.<br>
					<span class="description">NOTE: To show an existing user in the Client dropdown, simply edit that user and fill in the Business/Client Name field.</span></p>

					<form action="" method="post" name="create-user" id="create-user" class="validate proposalpress-new-client" novalidate="novalidate"<?php do_action( 'user_new_form_tag' );?>>

					<input name="action" type="hidden" value="create-user" />
					<?php wp_nonce_field( 'create-user', '_wpnonce_create-user' ); ?>

					<table class="form-table popup-form">

					<tbody>
						<tr class="form-field form-required">
							<th scope="row"><label for="user_login"><?php _e('Username'); ?>*</label></th>
							<td><input name="user_login" type="text" id="user_login" value="<?php echo esc_attr( $new_user_login ); ?>" aria-required="true" autocapitalize="none" autocorrect="off" /></td>
						</tr>
						<tr class="form-field form-required">
							<th scope="row"><label for="email"><?php _e('E-mail'); ?>*</label></th>
							<td><input name="email" type="email" id="email" value="<?php echo esc_attr( $new_user_email ); ?>" /></td>
						</tr>

						<tr class="form-field form-required">
							<th scope="row">
								<label for="_proposalpress_client_business"><?php _e( 'Business/Client Name', 'proposalpress' ); ?>*</label>
							</th>
							<td><input name="_proposalpress_client_business" id="_proposalpress_client_business" value="" type="text" /></td>
						</tr>

						<tr class="form-field">
							<th scope="row">
								<label for="_proposalpress_client_address"><?php _e( 'Address', 'proposalpress' ); ?></label>
							</th><td>
								<textarea class="regular-text" name="_proposalpress_client_address" id="_proposalpress_client_address"></textarea></td>
						</tr>

						<tr class="form-field">
							<th scope="row">
								<label for="_proposalpress_client_extra_info"><?php _e( 'Extra Info', 'proposalpress' ); ?></label>
							</th><td>
								<textarea class="regular-text" name="_proposalpress_client_extra_info" id="_proposalpress_client_extra_info"></textarea></td>
						</tr>

						<tr class="form-field">
							<th scope="row"><label for="first_name"><?php _e('First Name') ?> </label></th>
							<td><input name="first_name" type="text" id="first_name" value="<?php echo esc_attr( $new_user_firstname ); ?>" /></td>
						</tr>
						<tr class="form-field">
							<th scope="row"><label for="last_name"><?php _e('Last Name') ?> </label></th>
							<td><input name="last_name" type="text" id="last_name" value="<?php echo esc_attr( $new_user_lastname ); ?>" /></td>
						</tr>
						<tr class="form-field">
							<th scope="row"><label for="url"><?php _e('Website') ?></label></th>
							<td><input name="url" type="url" id="url" class="code" value="<?php echo esc_attr( $new_user_uri ); ?>" /></td>
						</tr>

						<tr class="form-field form-required user-pass1-wrap">
							<th scope="row">
								<label for="pass1">
									<?php _e( 'Password' ); ?>
									<span class="description hide-if-js"><?php _e( '(required)' ); ?></span>
								</label>
							</th>
							<td>
								<input class="hidden" value=" " /><!-- #24364 workaround -->
								<button type="button" class="button button-secondary wp-generate-pw hide-if-no-js"><?php _e( 'Show password' ); ?></button>
								<div class="wp-pwd hide-if-js">
									<?php $initial_password = wp_generate_password( 24 ); ?>
									<span class="password-input-wrapper">
										<input type="password" name="pass1" id="pass1" class="regular-text" autocomplete="off" data-reveal="1" data-pw="<?php echo esc_attr( $initial_password ); ?>" aria-describedby="pass-strength-result" />
									</span>
									<button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
										<span class="dashicons dashicons-hidden"></span>
										<span class="text"><?php _e( 'Hide' ); ?></span>
									</button>
									<button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
										<span class="text"><?php _e( 'Cancel' ); ?></span>
									</button>
									<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
								</div>

							</td>
						</tr>
						<tr class="form-field form-required user-pass2-wrap hide-if-js">
							<th scope="row"><label for="pass2"><?php _e( 'Repeat Password' ); ?> <span class="description"><?php _e( '(required)' ); ?></span></label></th>
							<td>
							<input name="pass2" type="password" id="pass2" autocomplete="off" />
							</td>
						</tr>
						<tr class="pw-weak">
							<th><?php _e( 'Confirm Password' ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="pw_weak" class="pw-checkbox" />
									<?php _e( 'Confirm use of weak password' ); ?>
								</label>
							</td>
						</tr>

					</tbody>
					</table>

					<?php submit_button( __( 'Add New User '), 'primary', 'create-user', true, array( 'id' => 'submit', 'class' => 'submit button button-primary button-large' ) ); ?>

					<div class="indicator" style="display:none"><?php _e( 'Please wait...', 'proposalpress' ); ?></div>

				</form>
			</div>
		<?php
		}
	}

	public function register_client() {

		/*
		 * Verify the nonce
		 */
		if ( ! current_user_can('create_users') )
			wp_die( __( 'Cheatin&#8217; uh?' ), 403 );

		if( !isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'create-user' ) )
			wp_die( 'Ooops, something went wrong, please try again later.' );

		if( empty( $_POST['business'] ) ) {
			die( 'Error adding the new user.' );
		}

		/*
		 * Put the POSTED user data into array
		 */
		$userdata = array(
			'user_login' 	=> sanitize_text_field( $_POST['user_login'] ),
			'user_pass'  	=> sanitize_text_field( $_POST['password'] ),
			'user_email' 	=> sanitize_text_field( $_POST['email'] ),
			'first_name' 	=> sanitize_text_field( $_POST['first_name'] ),
			'last_name' 	=> sanitize_text_field( $_POST['last_name'] ),
			'user_url'   	=> sanitize_text_field( $_POST['website'] ),
		);

		/*
		 * Inserts the user into the database
		 */
		$user_id = wp_insert_user( apply_filters( 'proposalpress_register_client_data', $userdata ) );

		/*
		 * Add the custom user meta
		 */
		update_user_meta( $user_id, '_proposalpress_client_business', sanitize_text_field( $_POST['business'] ) );
		update_user_meta( $user_id, '_proposalpress_client_address', wp_kses_post( $_POST['address'] ) );
		update_user_meta( $user_id, '_proposalpress_client_extra_info', wp_kses_post( $_POST['extra_info'] ) );

		/*
		 * Returns the updated client select input
		 */
		if( ! is_wp_error( $user_id ) ) {

			$clients = $this->get_clients();

			$option = '';

			foreach ($clients as $id => $business_name) {
				$option .= '<option value="' . esc_attr( $id ) . '">';
				$option .= esc_html( $business_name );
				$option .= '</option>';
			}

			echo $option;

		} else {


			die( 'Error adding the new user.' );

		}

		die();

	}
	
	public static function get_pre_defined_items() {

		/*
		 * fetch pre-defined items
		 */
		$general     = get_option( 'proposalpress_general' );
		$pre_defined = isset( $general['pre_defined'] ) ? $general['pre_defined'] : '';

		/*
		 * Explode each line into an array
		 */
		$items = explode("\n", $pre_defined);
		$items = array_filter( $items ); // remove any empty items
		$price_array[] = "<option value='' data-qty='' data-price='' data-title='' data-desc=''>" . __( 'Add a pre-defined line item', 'proposalpress' ) . "</option>";

		/*
		 * Check that we have items
		 */
		if( $items ) :

			$index = 0;
			foreach ( $items as $item ) {

				list( $qty, $title, $price, $desc ) = explode( '|', $item );
				$qty   = trim( $qty );
				$title = trim( $title );
				$price = trim( $price );
				$desc  = trim( $desc );

				$price_array[] = "<option value='" . esc_html( $title ) . "' data-qty='" . esc_html( $qty ) . "' data-price='" . esc_html( $price ) . "' data-title='" . esc_html( $title ) . "' data-desc='" . wp_kses_post( $desc ) . "'>" . esc_html( $title ) . "</option>";

				$index++;
			}

		endif;

		$set_items = "<select class='pre_defined_products' id='pre_defined_select'>" . implode( "", $price_array ) . "</select>";

		return $set_items;

	}

	public function set_csv_headers( $filename ) {

		/*
		 * Disables caching
		 */
		$now = date("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		/*
		 * Forces the download
		 */
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		/*
		 * disposition / encoding on response body
		 */
		header("Content-Disposition: attachment;filename={$filename}");
		header("Content-Transfer-Encoding: binary");
	}

	public function export_csv() {

		/*
		 * Do the checks
		 */
		if ( ! isset( $_GET['export'] ) )
			return;

		if ( $_GET['export'] != 'csv' )
			return;

		/*
		 * Work out the post type
		 */
		$post_type = esc_html( $_GET['post_type'] );
		$type = proposalpress_get_the_type();

		/*
		 * Create the header rows for the CSV
		 */
		$header_row = array(
			0 => __( 'Number', 'proposalpress' ),
			1 => __( 'Title', 'proposalpress' ),
			2 => __( 'Client', 'proposalpress' ),
			3 => __( 'Status', 'proposalpress' ),
			4 => __( 'Created', 'proposalpress' ),
			5 => __( 'Sub Total', 'proposalpress' ),
			6 => __( 'Tax', 'proposalpress' ),
			7 => __( 'Total', 'proposalpress' ),
		);

		$data_rows = array();

		/*
		 * Query the posts
		 */
		$args 	= array (
			'post_type'     => $post_type,
			'posts_per_page'=> -1,
			'post_status'   => 'p`ublish',
			);
		$the_query = new WP_Query( apply_filters( 'proposalpress_export_csv_query', $args ) );

		/*
		 * Filter the query if they are active
		 */
		if ( isset( $_GET['proposalpress_client'] ) && $_GET['proposalpress_client'] ) {
			$the_query->query_vars['meta_query'] = array(
				array(
					'key'      => '_proposalpress_client',
					'value'    => (int)$_GET['proposalpress_client']
				)
			);
		}

		if ( isset( $_GET['m'] ) && $_GET['m'] ) {
			$date  = isset( $_GET['m'] ) ? $_GET['m'] : null;
			$year  = $date ? substr($date, 0, 4) : null;
			$month = $date ? substr($date, -2) : null;
			$the_query->query_vars['date_query'] = array(
				array(
					'year'  => $year,
					'month' => $month,
				),
			);
		}

		if ( $the_query->have_posts() ) :
			while ( $the_query->have_posts() ) : $the_query->the_post();

			/*
			 * Get statuses and create a comma separated list if more than one status exists
			 */
			$status_array = array();
			$statuses     = get_the_terms( Sliced_Shared::get_item_id(), $type . '_status' );
			if ( ! empty( $statuses ) && ! is_wp_error( $statuses ) ) {
				foreach ( $statuses as $status ) {
					$status_array[] = $status->name;
				}
			}

			if ( isset( $_GET[$type . '_status'] ) && $_GET[$type . '_status'] && ! in_array( ucfirst($_GET[$type . '_status'] ), $status_array) )
				continue;

			/*
			 * Put each posts data into the appropriate cell
			 */
			$row = array();
			$row[0] = proposalpress_get_prefix() . proposalpress_get_number();
			$row[1] = wp_kses_decode_entities( get_the_title() );
			$row[2] = proposalpress_get_client_business();
			$row[3] = rtrim( implode( ',', $status_array ), ',' );
			$row[4] = date_i18n( get_option( 'date_format' ), (int) proposalpress_get_created() );
			$row[5] = proposalpress_get_sub_total();
			$row[6] = proposalpress_get_tax_total();
			$row[7] = proposalpress_get_total();

			$data_rows[] = $row;

			endwhile;
		endif;

		/*
		 * Create the filename
		 */
		$filename = sanitize_file_name( $type . '-export-' . date( 'Y-m-d' ) . '.csv' );

		$this->set_csv_headers( $filename );

		$fh = @fopen( 'php://output', 'w' );
		fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) );
		fputcsv( $fh, $header_row );

		foreach ( $data_rows as $data_row ) {
			fputcsv( $fh, $data_row );
		}

		fclose( $fh );
		die();

	}

} // end class
