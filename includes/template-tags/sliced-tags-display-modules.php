<?php
// Exit if accessed directly
if ( ! defined('ABSPATH') ) { exit; }

if ( ! function_exists( 'sliced_display_business' ) ) :

	function sliced_display_business() { ?>

			<a target="_blank" href="<?php echo esc_url( sliced_get_business_website() ); ?>">
				<?php echo sliced_get_business_logo() ? '<img class="logo" src="' . esc_url( sliced_get_business_logo() ) . '">' : '<h1>' . esc_html( sliced_get_business_name() ) . '</h1>' ?>
			</a>

		<?php
	}

endif;


if ( ! function_exists( 'sliced_display_from_address' ) ) :

	function sliced_display_from_address() { ?>

			<div class="from"><strong><?php _e( 'From:', 'sliced-invoices' ) ?></strong></div>
			<div class="wrapper">
			<div class="name"><a target="_blank" href="<?php echo esc_url( sliced_get_business_website() ); ?>"><?php echo esc_html( sliced_get_business_name() ); ?></a></div>

			<?php echo sliced_get_business_address() ? '<div class="address">' . wpautop( wp_kses_post( sliced_get_business_address() ) ) . '</div>' : ''; ?>
			<?php echo sliced_get_business_extra_info() ? '<div class="extra_info">' . wpautop( wp_kses_post( sliced_get_business_extra_info() ) ) . '</div>' : ''; ?>
			</div>

		<?php
	}

endif;


if ( ! function_exists( 'sliced_display_to_address' ) ) :

	function sliced_display_to_address() {

		$output = '<div class="to"><strong>' . __( 'To:', 'sliced-invoices' ) . '</strong></div>';
		$output .= '<div class="wrapper">';
		$output .= '<div class="name">' . esc_html( sliced_get_client_business() ) . '</div>';
		$output .= sliced_get_client_address() ? '<div class="address">' . wpautop( wp_kses_post( sliced_get_client_address() ) ) . '</div>' : '';
		$output .= sliced_get_client_extra_info() ? '<div class="extra_info">' . wpautop( wp_kses_post( sliced_get_client_extra_info() ) ) . '</div>' : '';
		$output .= sliced_get_client_website() ? '<div class="website">' . esc_html( sliced_get_client_website() ) . '</div>' : '';
		$output .= sliced_get_client_email() ? '<div class="email">' . esc_html( sliced_get_client_email() ) . '</div>' : '';
		$output .= '</div>';
		$output = apply_filters( 'sliced_to_address_output', $output );

		echo $output;
	}

endif;


if ( ! function_exists( 'sliced_display_invoice_details' ) ) :

	function sliced_display_invoice_details() { ?>

			<table class="table table-bordered table-sm">

				<?php if( sliced_get_invoice_number() ) : ?>
					<tr>
						<td><?php printf( esc_html_x( '%s Number', 'invoice number', 'sliced-invoices' ), sliced_get_invoice_label() ); ?></td>
						<td><?php echo esc_html( sliced_get_invoice_prefix() ); ?><?php echo esc_html( sliced_get_invoice_number() ); ?></td>
					</tr>
				<?php endif; ?>

				<?php if( sliced_get_invoice_order_number() ) : ?>
					<tr>
						<td><?php _e( 'Order Number', 'sliced-invoices' ) ?></td>
						<td><?php echo esc_html( sliced_get_invoice_order_number() ); ?></td>
					</tr>
				<?php endif; ?>

				<?php if( sliced_get_invoice_created() ) : ?>
					<tr>
						<td><?php printf( esc_html_x( '%s Date', 'invoice date', 'sliced-invoices' ), sliced_get_invoice_label() ); ?></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), (int) sliced_get_invoice_created() ) ?></td>
					</tr>
				<?php endif; ?>

				<?php if( sliced_get_invoice_due() ) : ?>
					<tr>
						<td><?php _e( 'Due Date', 'sliced-invoices' ) ?></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), (int) sliced_get_invoice_due() ) ?></td>
					</tr>
				<?php endif; ?>

					<tr class="table-active">
						<td><strong><?php _e( 'Total Due', 'sliced-invoices' ) ?></strong></td>
						<td><strong><?php echo sliced_get_invoice_total(); ?></strong></td>
					</tr>

			</table>

		<?php
	}

endif;

if ( ! function_exists( 'sliced_display_quote_details' ) ) :

	function sliced_display_quote_details() { ?>

			<table class="table table-bordered table-sm">

				<?php if( sliced_get_quote_number() ) : ?>
					<tr>
						<td><?php printf( esc_html_x( '%s Number', 'quote number', 'sliced-invoices' ), sliced_get_quote_label() ); ?></td>
						<td><?php esc_html_e( sliced_get_quote_prefix() ); ?><?php esc_html_e( sliced_get_quote_number() ); ?></td>
					</tr>
				<?php endif; ?>

				<?php do_action( 'sliced_after_quote_number' ); ?>

				<?php if( sliced_get_quote_created() ) : ?>
					<tr>
						<td><?php printf( esc_html_x( '%s Date', 'quote date', 'sliced-invoices' ), sliced_get_quote_label() ); ?></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), (int) sliced_get_quote_created() ) ?></td>
					</tr>
				<?php endif; ?>

				<?php if( sliced_get_quote_valid() ) : ?>
					<tr>
						<td><?php _e( 'Valid Until', 'sliced-invoices' ) ?></td>
						<td><?php echo date_i18n( get_option( 'date_format' ), (int) sliced_get_quote_valid() ) ?></td>
					</tr>
				<?php endif; ?>

					<tr class="table-active">
						<td><strong><?php _e( 'Total', 'sliced-invoices' ) ?></strong></td>
						<td><strong><?php echo sliced_get_quote_total(); ?></strong></td>
					</tr>

			</table>

		<?php
	}

endif;



if ( ! function_exists( 'sliced_display_line_items' ) ) :

	function sliced_display_line_items() {

		$shared = new Sliced_Shared;

			$output = '<table class="table table-sm table-bordered table-striped">
				<thead>
					<tr>
						<th class="qty"><strong>' . __( "Hrs/Qty", "sliced-invoices" ) . '</strong></th>
						<th class="service"><strong>' . __( "Service", "sliced-invoices" ) . '</strong></th>
						<th class="rate"><strong>' . __( "Rate/Price", "sliced-invoices" ) . '</strong></th>';
						if ( sliced_hide_adjust_field() === false ) {
							$output .= '<th class="adjust"><strong>' . __( "Adjust", "sliced-invoices" ) . '</strong></th>';
						}
						$output .= '<th class="total"><strong>' . __( "Sub Total", "sliced-invoices" ) . '</strong></th>
					</tr>
				</thead>
				<tbody>';

				$count = 0;
				$items = sliced_get_invoice_line_items(); // gets quote and invoice
				if( !empty( $items ) && !empty( $items[0] ) ) :

					foreach ( $items[0] as $item ) {

						$class = ($count % 2 == 0) ? "even" : "odd";

						$qty = isset( $item["qty"] ) ? $shared->get_raw_number( $item["qty"] ) : 0;
						$amt = isset( $item["amount"] ) ? $shared->get_raw_number( $item["amount"] ) : 0;
						$tax = isset( $item["tax"] ) ? $shared->get_raw_number( $item["tax"] ) : "0.00";
						$line_total = $shared->get_line_item_sub_total( $qty, $amt, $tax );

							$output .= '<tr class="row_' . $class . ' sliced-item">

								<td class="qty">' . esc_html__( $shared->get_formatted_number( $qty ) ) . '</td>
								<td class="service">' . esc_html__( isset( $item["title"] ) ? $item["title"] : "" );
									if ( isset( $item["description"] ) ) :
										$output .= '<br/><span class="description">' . wpautop( wp_kses_post( $item["description"] ) ) . '</span>';
									endif;
								$output .= '</td>
								<td class="rate">' . esc_html__( $shared->get_formatted_currency( $amt ) ) . '</td>';
								if ( sliced_hide_adjust_field() === false) {
									$output .= '<td class="adjust">' . esc_html__( $tax . "%" ) . '</td>';
								}
								$output .= '<td class="total">' . esc_html__( $shared->get_formatted_currency( $line_total ) ) . '</td>

							</tr>';

					$count++;
					}
				endif;

				$output .= '</tbody></table>';

				$output = apply_filters( 'sliced_invoice_line_items_output', $output );

			echo $output;

	}

endif;



if ( ! function_exists( 'sliced_display_invoice_totals' ) ) :

	function sliced_display_invoice_totals() {

		do_action( 'sliced_invoice_before_totals_table' ); 
		
		// need to fix this up
		if( function_exists('sliced_woocommerce_get_order_id') ) {
			$order_id = sliced_woocommerce_get_order_id( get_the_ID() );
			if ( $order_id )
				return;
		}
		?>

		<table class="table table-sm table-bordered">
			<tbody>
				<?php do_action( 'sliced_invoice_before_totals' ); ?>
				<tr class="row-sub-total">
					<td class="rate"><?php _e( 'Sub Total', 'sliced-invoices' ) ?></td>
					<td class="total"><?php _e( sliced_get_invoice_sub_total() ) ?></td>
				</tr>
				<?php do_action( 'sliced_invoice_after_sub_total' ); ?>
				<tr class="row-tax">
					<td class="rate"><?php _e( sliced_get_tax_name() ) ?></td>
					<td class="total"><?php _e( sliced_get_invoice_tax() ) ?></td>
				</tr>
				<?php do_action( 'sliced_invoice_after_tax' ); ?>
				<tr class="table-active row-total">
					<td class="rate"><strong><?php _e( 'Total', 'sliced-invoices' ) ?></strong></td>
					<td class="total"><strong><?php _e( sliced_get_invoice_total() ) ?></strong></td>
				</tr>
				<?php do_action( 'sliced_invoice_after_totals' ); ?>
			</tbody>

		</table>

		<?php do_action( 'sliced_invoice_after_totals_table' );

	}

endif;

if ( ! function_exists( 'sliced_display_quote_totals' ) ) :

	function sliced_display_quote_totals() { ?>

			<table class="table table-sm table-bordered">

				<tbody>
					<?php do_action( 'sliced_quote_before_totals' ); ?>
					<tr class="row-sub-total">
						<td class="rate"><?php echo _e( 'Sub Total', 'sliced-invoices' ); ?></td>
						<td class="total"><?php echo esc_html( sliced_get_quote_sub_total() ); ?></td>
					</tr>
					<tr class="row-tax">
						<td class="rate"><?php echo esc_html( sliced_get_tax_name() ); ?></td>
						<td class="total"><?php echo esc_html( sliced_get_quote_tax() ); ?></td>
					</tr>
					<tr class="table-active row-total">
						<td class="rate"><strong><?php echo _e( 'Total', 'sliced-invoices' ); ?></strong></td>
						<td class="total"><strong><?php echo esc_html( sliced_get_quote_total() ); ?></strong></td>
					</tr>
					<?php do_action( 'sliced_quote_after_totals' ); ?>
				</tbody>

			</table>

		<?php
	}

endif;
