<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) { exit; }

if ( ! function_exists( 'proposalpress_get_business_logo' ) ) :

	function proposalpress_get_business_logo() {
		$business = ProposalPress_Shared::get_business_details();
		return apply_filters( 'proposalpress_get_business_logo', $business['logo'], $business );
	}

endif;

if ( ! function_exists( 'proposalpress_get_business_name' ) ) :

	function proposalpress_get_business_name() {
		$business = ProposalPress_Shared::get_business_details();
		return apply_filters( 'proposalpress_get_business_name', $business['name'], $business );
	}

endif;

if ( ! function_exists( 'proposalpress_get_business_address' ) ) :

	function proposalpress_get_business_address() {
		$business = ProposalPress_Shared::get_business_details();
		return apply_filters( 'proposalpress_get_business_address', $business['address'], $business );
	}

endif;

if ( ! function_exists( 'proposalpress_get_business_extra_info' ) ) :

	function proposalpress_get_business_extra_info() {
		$business = ProposalPress_Shared::get_business_details();
		return apply_filters( 'proposalpress_get_business_extra_info', $business['extra_info'], $business );
	}

endif;


if ( ! function_exists( 'proposalpress_get_business_website' ) ) :

	function proposalpress_get_business_website() {
		$business = ProposalPress_Shared::get_business_details();
		return apply_filters( 'proposalpress_get_business_website', $business['website'], $business );
	}

endif;


if ( ! function_exists( 'proposalpress_get_business_footer' ) ) :

	function proposalpress_get_business_footer() {
		$business = ProposalPress_Shared::get_business_details();
		return apply_filters( 'proposalpress_get_business_footer', $business['footer'], $business );
	}

endif;
