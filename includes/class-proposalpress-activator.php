<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.proposalpress.com
 * @since      1.0.0
 *
 * @package    proposalpress
 * @subpackage proposalpress/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    proposalpress
 * @subpackage proposalpress/includes
 * @author     FunctionThemes <support@functionthemes.com>
 */
class ProposalPress_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
global $wp_version;

		$plugin_name = 'proposalpress';
		$require = array(
			'wordpress' => '4.0',
			'php' => '5.3',
			'curl' => true,
		);

		$wp_version  = $wp_version;
		$php_version = phpversion();
        $extensions     = get_loaded_extensions();
        $curl           = in_array('curl', $extensions);

        $error = array();
        if($wp_version < $require['wordpress']) {
            $error['wp_error'] = 'yes';
        }
        if($php_version < $require['php']) {
            $error['php_error'] = 'yes';
        }
        if($curl != true) {
            $error['curl_error'] = 'yes';
        }

        set_transient( 'proposalpress_activation_warning', $error, 5 );

		// add default options
		$business_exists = get_option('proposalpress_business');
		$general_exists  = get_option('proposalpress_general');
		$payment_exists  = get_option('proposalpress_payments');
		$proposals_exists = get_option('proposalpress_proposals');
		$quotes_exists   = get_option('proposalpress_quotes');
		$email           = get_option('proposalpress_emails');


		if( ! $business_exists) {

			$business_array = array(
				'name'      => get_bloginfo('name'),
				'address'   => 'Suite 5A-1204
123 Somewhere Street
Your City AZ 12345',
				'extra_info'   => get_bloginfo('admin_email'),
				'website'   => get_bloginfo('url'),
				);

			update_option('proposalpress_business', $business_array);

		}

		if( ! $general_exists) {

			$general_array = array(
				'year_start'    => '07',
				'year_end'      => '06',
				'pre_defined'   => '
1 | Web Design | 85 | Design work on the website
1 | Web Development | 95 | Back end development of website',
				'footer'        => 'Thanks for choosing <a href="' . get_bloginfo('url') . '">' . get_bloginfo('site_name') . '</a> | <a href="mailto:' . get_bloginfo('admin_email') . '">' . get_bloginfo('admin_email') . '</a>'
				);

			update_option('proposalpress_general', $general_array);

		}

		if( ! $payment_exists) {

			// Create post object
			$payment_page = array(
				'post_title'      => 'Payment',
				'post_content'    => '',
				'post_status'     => 'publish',
				'post_type'       => 'page',
			);

			// Insert the post into the database
			$payment_id = wp_insert_post( $payment_page );

			$payment_array = array(
				'currency_symbol'   => '$',
				'currency_pos'      => 'left',
				'thousand_sep'      => ',',
				'decimal_sep'       => '.',
				'decimals'          => '2',
				'tax'               => '10',
				'tax_name'          => 'Tax',
				'payment_page'      => $payment_id,
			);

			update_option('proposalpress_payments', $payment_array);

		}

		if( ! $proposals_exists) {

			$proposal_array = array(
				'terms'         => 'Payment is due within 30 days from date of proposal. Late payment is subject to fees of 5% per month.',
				'css'           => 'body {}',
				'number'        => '0001',
				'prefix'        => 'INV-',
				'increment'     => 'on',
				'template'      => 'template1',
			);

			update_option('proposalpress_proposals', $proposal_array);

		}

		if( ! $quotes_exists) {

			$quote_array = array(
				'terms'         => 'This is a fixed price quote. If accepted, we require a 25% deposit upfront before work commences.',
				'css'               => 'body {}',
				'number'            => '0001',
				'prefix'            => 'QUO-',
				'increment'         => 'on',
				'template'          => 'template1',
				'accept_quote'      => 'on',
				'accept_quote_text' => sprintf( __( '**Please Note: After accepting this %1s an %2s will be automatically generated. This will then become a legally binding contract.', 'proposalpress' ), proposalpress_get_quote_label(), proposalpress_get_proposal_label() ),
			);

			update_option('proposalpress', $quote_array);

		}

		// if a new install
		if( ! $email ) {

			$email['from'] = get_option( 'admin_email' );
			$email['name'] = get_option( 'blogname' );
			$email['bcc'] = 'on';
			$email['footer'] = sprintf( 'Copyright %1s. %2s', date('Y'), proposalpress_get_business_name() );

			$email['quote_available_subject'] = 'New quote %number% available';
			$email['proposal_available_subject'] = 'New proposal %number% available';
			$email['payment_received_client_subject'] = 'Thanks for your payment!';
			$email['payment_reminder_subject'] = 'A friendly reminder';

			$email['quote_available_content'] = 'Hi %client_first_name%,

							You have a new quote available ( %number% ) which can be viewed at %link%.<br>';
			$email['proposal_available_content'] = 'Hi %client_first_name%,

							You have a new proposal available ( %number% ) which can be viewed at %link%.<br>';
			$email['payment_received_client_content'] = 'Thanks for your payment, %client_first_name%.

Your recent payment for %total% on proposal %number% has been successful.<br>';
			$email['payment_reminder_content'] = 'Hi %client_first_name%,

Just a friendly reminder that your proposal %number% for %total% %is_was% due on %due_date%.';

			update_option('proposalpress_emails', $email);

		}

		// call the custom posts and taxonomnies
		$admin = new ProposalPress_Admin( $plugin_name, PROPOSALPRESS_VERSION );
		$admin->new_cpt_quote();
		$admin->new_cpt_section();
		$admin->new_cpt_proposal();
		
		$admin->new_taxonomy_quote_category();
		$admin->new_taxonomy_proposal_status();
		$admin->new_taxonomy_section_category();

		$quote_status = array(
			'proposal_status' => array(
				'Draft',
				'Sent',
				'Declined',
				'Cancelled',
			)
		);

		foreach ($quote_status as $taxonomy => $terms) {
			foreach ($terms as $term) {
				if (! get_term_by('slug', sanitize_title($term), $taxonomy)) {
					$result = wp_insert_term($term, $taxonomy);
				}
			}
		}

		flush_rewrite_rules();
	}

}
