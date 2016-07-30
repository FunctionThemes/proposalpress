<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) { exit; }

if ( ! function_exists( 'proposalpress_get_proposal_label' ) ) :

	function proposalpress_get_proposal_label() {
		$translate = get_option( 'proposalpress_translate' );
		$label = isset( $translate['proposal-label'] ) ? $translate['proposal-label'] : __( 'Invoice', 'proposalpress-proposals');
		return apply_filters( 'proposalpress_get_proposal_label', $label );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_label_plural' ) ) :

	function proposalpress_get_proposal_label_plural() {
		$translate = get_option( 'proposalpress_translate' );
		$label = isset( $translate['proposal-label-plural'] ) ? $translate['proposal-label-plural'] : __( 'Invoices', 'proposalpress-proposals');
		return apply_filters( 'proposalpress_get_proposal_label_plural', $label );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_id' ) ) :

	function proposalpress_get_proposal_id() {
		$id = ProposalPress_Shared::get_item_id();
		return apply_filters( 'proposalpress_get_proposal_id', $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_number' ) ) :

	function proposalpress_get_proposal_number( $id = 0 ) {
		$output = ProposalPress_Invoice::get_number( $id );
		return apply_filters( 'proposalpress_get_proposal_number', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_proposal_prefix' ) ) :

	function proposalpress_get_proposal_prefix( $id = 0 ) {
		$output = ProposalPress_Invoice::get_prefix( $id );
		return apply_filters( 'proposalpress_get_proposal_prefix', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_next_proposal_number' ) ) :

	function proposalpress_get_next_proposal_number() {
		$output = ProposalPress_Invoice::get_next_proposal_number();
		return apply_filters( 'proposalpress_get_next_proposal_number', $output);
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_order_number' ) ) :

	function proposalpress_get_proposal_order_number( $id = 0 ) {
		$output = ProposalPress_Invoice::get_order_number( $id );
		return apply_filters( 'proposalpress_get_proposal_order_number', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_created' ) ) :

	function proposalpress_get_proposal_created( $id = 0 ) {
		$output = ProposalPress_Invoice::get_created_date( $id );
		return apply_filters( 'proposalpress_get_proposal_created', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_due' ) ) :

	function proposalpress_get_proposal_due( $id = 0 ) {
		$output = ProposalPress_Invoice::get_due_date( $id );
		return apply_filters( 'proposalpress_get_proposal_due', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_status' ) ) :

	function proposalpress_get_proposal_status( $id = 0, $type = 'proposal' ) {
		$output = ProposalPress_Shared::get_status( $id, $type );
		return apply_filters( 'proposalpress_get_proposal_status', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_line_items' ) ) :

	function proposalpress_get_proposal_line_items( $id = 0 ) {
		$output = ProposalPress_Shared::get_line_items( $id );
		return apply_filters( 'proposalpress_get_proposal_line_items', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_sub_total' ) ) :

	function proposalpress_get_proposal_sub_total( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$sub_total = ProposalPress_Shared::get_formatted_currency( $output['sub_total'] );
		return apply_filters( 'proposalpress_get_proposal_sub_total', $sub_total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_total' ) ) :

	function proposalpress_get_proposal_total( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$total = ProposalPress_Shared::get_formatted_currency( $output['total'] );
		return apply_filters( 'proposalpress_get_proposal_total', $total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_tax' ) ) :

	function proposalpress_get_proposal_tax( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$tax = ProposalPress_Shared::get_formatted_currency( $output['tax'] );
		return apply_filters( 'proposalpress_get_proposal_tax', $tax, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_sub_total_raw' ) ) :

	function proposalpress_get_proposal_sub_total_raw( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$sub_total = round( $output['sub_total'], proposalpress_get_decimals() );
		return apply_filters( 'proposalpress_get_proposal_sub_total_raw', $sub_total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_total_raw' ) ) :

	function proposalpress_get_proposal_total_raw( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$total = round( $output['total'], proposalpress_get_decimals());
		return apply_filters( 'proposalpress_get_proposal_total_raw', $total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_tax_raw' ) ) :

	function proposalpress_get_proposal_tax_raw( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$tax = round( $output['tax'], proposalpress_get_decimals());
		return apply_filters( 'proposalpress_get_proposal_tax_raw', $tax, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_description' ) ) :

	function proposalpress_get_proposal_description( $id = 0 ) {
		$output = ProposalPress_Invoice::get_description( $id );
		return apply_filters( 'proposalpress_get_proposal_description', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_terms' ) ) :

	function proposalpress_get_proposal_terms( $id = 0 ) {
		$output = ProposalPress_Invoice::get_terms( $id );
		return apply_filters( 'proposalpress_get_proposal_terms', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_proposal_payment_methods' ) ) :

	function proposalpress_get_proposal_payment_methods( $id = 0 ) {
		$output = ProposalPress_Invoice::get_payment_methods( $id );
		return apply_filters( 'proposalpress_get_proposal_payment_methods', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_proposal_deposit' ) ) :

	function proposalpress_get_proposal_deposit( $id = 0 ) {
		$output = ProposalPress_Invoice::get_deposit( $id );
		return apply_filters( 'proposalpress_get_proposal_deposit', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_proposal_template' ) ) :

	function proposalpress_get_proposal_template( $id = 0 ) {
		$output = ProposalPress_Invoice::get_template( $id );
		return apply_filters( 'proposalpress_get_proposal_template', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_css' ) ) :

	function proposalpress_get_proposal_css( $id = 0 ) {
		$output = ProposalPress_Invoice::get_css( $id );
		return apply_filters( 'proposalpress_get_proposal_css', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_proposal_watermark' ) ) :

	function proposalpress_get_proposal_watermark( $id = 0 ) {
		$output = ProposalPress_Invoice::get_proposal_watermark( $id );
		return apply_filters( 'proposalpress_get_proposal_watermark', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_proposal_hide_adjust_field' ) ) :

	function proposalpress_proposal_hide_adjust_field() {
		$output = ProposalPress_Invoice::hide_adjustment_field();
		return apply_filters( 'proposalpress_proposal_hide_adjust_field', $output );
	}

endif;
