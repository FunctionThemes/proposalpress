<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) { exit; }

if ( ! function_exists( 'proposalpress_get_section_label' ) ) :

	function proposalpress_get_section_label() {
		$translate = get_option( 'proposalpress_translate' );
		$label = isset( $translate['section-label'] ) ? $translate['section-label'] : __( 'Quote', 'proposalpress-invoices');
		return apply_filters( 'proposalpress_get_section_label', $label );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_label_plural' ) ) :

	function proposalpress_get_section_label_plural() {
		$translate = get_option( 'proposalpress_translate' );
		$label = isset( $translate['section-label-plural'] ) ? $translate['section-label-plural'] : __( 'Quotes', 'proposalpress-invoices');
		return apply_filters( 'proposalpress_get_section_label_plural', $label );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_id' ) ) :

	function proposalpress_get_section_id() {
		$id = ProposalPress_Shared::get_item_id();
		return apply_filters( 'proposalpress_get_section_id', $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_section_number' ) ) :

	function proposalpress_get_section_number( $id = 0 ) {
		$output = ProposalPress_Quote::get_number( $id );
		return apply_filters( 'proposalpress_get_section_number', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_section_prefix' ) ) :

	function proposalpress_get_section_prefix( $id = 0 ) {
		$output = ProposalPress_Quote::get_prefix( $id );
		return apply_filters( 'proposalpress_get_section_prefix', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_next_section_number' ) ) :

	function proposalpress_get_next_section_number() {
		$output = ProposalPress_Quote::get_next_section_number();
		return apply_filters( 'proposalpress_get_next_section_number', $output );
	}

endif;


if ( ! function_exists( 'proposalpress_get_section_created' ) ) :

	function proposalpress_get_section_created( $id = 0 ) {
		$output = ProposalPress_Quote::get_created_date( $id );
		return apply_filters( 'proposalpress_get_section_created', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_valid' ) ) :

	function proposalpress_get_section_valid( $id = 0 ) {
		$output = ProposalPress_Quote::get_valid_date( $id );
		return apply_filters( 'proposalpress_get_section_valid', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_get_section_status' ) ) :

	function proposalpress_get_section_status( $id = 0, $type = 'section' ) {
		$output = ProposalPress_Shared::get_status( $id, $type );
		return apply_filters( 'proposalpress_get_section_status', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_line_items' ) ) :

	function proposalpress_get_section_line_items( $id = 0 ) {
		$output = ProposalPress_Shared::get_line_items( $id );
		return apply_filters( 'proposalpress_get_section_line_items', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_sub_total' ) ) :

	function proposalpress_get_section_sub_total( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$sub_total = ProposalPress_Shared::get_formatted_currency( $output['sub_total'] );
		return apply_filters( 'proposalpress_get_section_sub_total', $sub_total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_total' ) ) :

	function proposalpress_get_section_total( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$total = ProposalPress_Shared::get_formatted_currency( $output['total'] );
		return apply_filters( 'proposalpress_get_section_total', $total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_tax' ) ) :

	function proposalpress_get_section_tax( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$tax = ProposalPress_Shared::get_formatted_currency( $output['tax'] );
		return apply_filters( 'proposalpress_get_section_tax', $tax, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_sub_total_raw' ) ) :

	function proposalpress_get_section_sub_total_raw( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$sub_total = $output['sub_total'];
		return apply_filters( 'proposalpress_get_section_sub_total_raw', $sub_total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_total_raw' ) ) :

	function proposalpress_get_section_total_raw( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$total = $output['total'];
		return apply_filters( 'proposalpress_get_section_total_raw', $total, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_tax_raw' ) ) :

	function proposalpress_get_section_tax_raw( $id = 0 ) {
		$output = ProposalPress_Shared::get_totals( $id );
		$tax = $output['tax'];
		return apply_filters( 'proposalpress_get_section_tax_raw', $tax, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_description' ) ) :

	function proposalpress_get_section_description( $id = 0 ) {
		$output = ProposalPress_Quote::get_description( $id );
		return apply_filters( 'proposalpress_get_section_description', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_terms' ) ) :

	function proposalpress_get_section_terms( $id = 0 ) {
		$output = ProposalPress_Quote::get_terms( $id );
		return apply_filters( 'proposalpress_get_section_terms', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_template' ) ) :

	function proposalpress_get_section_template( $id = 0 ) {
		$output = ProposalPress_Quote::get_template( $id );
		return apply_filters( 'proposalpress_get_section_template', $output, $id );
	}

endif;

if ( ! function_exists( 'proposalpress_get_section_css' ) ) :

	function proposalpress_get_section_css( $id = 0 ) {
		$output = ProposalPress_Quote::get_css( $id );
		return apply_filters( 'proposalpress_get_section_css', $output, $id );
	}

endif;


if ( ! function_exists( 'proposalpress_section_hide_adjust_field' ) ) :

	function proposalpress_section_hide_adjust_field() {
		$output = ProposalPress_Quote::hide_adjustment_field();
		return apply_filters( 'proposalpress_section_hide_adjust_field', $output );
	}

endif;
