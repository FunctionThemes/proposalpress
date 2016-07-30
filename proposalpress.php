<?php

/**
 * @link              https://www.proposalpress.com
 * @since             1.0.0
 * @package           proposalpress
 *
 * @wordpress-plugin
 * Plugin Name:       Proposal Press
 * Plugin URI:        https://www.proposalpress.com
 * Description:       Create professional Quotes & Proposals that clients can't resist.
 * Version:           1.0.0
 * Author:            Function Themes
 * Author URI:        https://www.functionthemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       proposalpress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PROPOSALPRESS_PATH', plugin_dir_path( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_proposalpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proposalpress-activator.php';
	ProposalPress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_proposalpress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proposalpress-deactivator.php';
	ProposalPress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_proposalpress' );
register_deactivation_hook( __FILE__, 'deactivate_proposalpress' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-proposalpress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_proposalpress() {

	$plugin = new ProposalPress();
	$plugin->run();

}
run_proposalpress();
