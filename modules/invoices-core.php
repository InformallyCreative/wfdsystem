<?php
/**
 * Core invoice functionality.
 *
 * @package WFD\System
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the Invoice custom post type.
 *
 * @return void
 */
function wfd_register_invoice_post_type() {
	$labels = array(
		'name'               => _x( 'Invoices', 'post type general name', 'wfd-system' ),
		'singular_name'      => _x( 'Invoice', 'post type singular name', 'wfd-system' ),
		'menu_name'          => _x( 'Invoices', 'admin menu', 'wfd-system' ),
		'name_admin_bar'     => _x( 'Invoice', 'add new on admin bar', 'wfd-system' ),
		'add_new'            => _x( 'Add New', 'invoice', 'wfd-system' ),
		'add_new_item'       => __( 'Add New Invoice', 'wfd-system' ),
		'new_item'           => __( 'New Invoice', 'wfd-system' ),
		'edit_item'          => __( 'Edit Invoice', 'wfd-system' ),
		'view_item'          => __( 'View Invoice', 'wfd-system' ),
		'all_items'          => __( 'All Invoices', 'wfd-system' ),
		'search_items'       => __( 'Search Invoices', 'wfd-system' ),
		'parent_item_colon'  => __( 'Parent Invoices:', 'wfd-system' ),
		'not_found'          => __( 'No invoices found.', 'wfd-system' ),
		'not_found_in_trash' => __( 'No invoices found in Trash.', 'wfd-system' ),
	);

	$capabilities = array(
		'edit_post'          => 'edit_wfd_invoice',
		'read_post'          => 'read_wfd_invoice',
		'delete_post'        => 'delete_wfd_invoice',
		'edit_posts'         => 'edit_wfd_invoices',
		'edit_others_posts'  => 'edit_others_wfd_invoices',
		'publish_posts'      => 'publish_wfd_invoices',
		'read_private_posts' => 'read_private_wfd_invoices',
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Waterfilter Direct trade invoices.', 'wfd-system' ),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => 'wfd-system',
		'query_var'          => false,
		'rewrite'            => false,
		'capability_type'    => 'wfd_invoice',
		'capabilities'       => $capabilities,
		'map_meta_cap'       => true,
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'author' ),
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-media-spreadsheet',
	);

	register_post_type( 'wfd_invoice', $args );
}
add_action( 'init', 'wfd_register_invoice_post_type' );

/**
 * Register custom invoice statuses.
 *
 * @return void
 */
function wfd_register_invoice_statuses() {
	$statuses = array(
		'wfd_draft'      => __( 'Draft', 'wfd-system' ),
		'wfd_pending'    => __( 'Pending', 'wfd-system' ),
		'wfd_processing' => __( 'Processing', 'wfd-system' ),
		'wfd_paid'       => __( 'Paid', 'wfd-system' ),
		'wfd_cancelled'  => __( 'Cancelled', 'wfd-system' ),
		'wfd_overdue'    => __( 'Overdue', 'wfd-system' ),
	);

	foreach ( $statuses as $status => $label ) {
		register_post_status(
		$status,
		array(
		'label'                     => $label,
		'public'                    => false,
		'show_in_admin_status_list' => true,
		'show_in_admin_all_list'    => true,
		'label_count'               => _n_noop( $label . ' <span class="count">(%s)</span>', $label . ' <span class="count">(%s)</span>', 'wfd-system' ),
		)
		);
	}
}
add_action( 'init', 'wfd_register_invoice_statuses', 11 );

/**
 * Add invoice status labels to the post states list.
 *
 * @param array   $states Current states.
 * @param WP_Post $post   Current post object.
 *
 * @return array
 */
function wfd_invoice_status_labels( $states, $post ) {
	if ( 'wfd_invoice' !== $post->post_type ) {
		return $states;
	}

	$labels = array(
		'wfd_draft'      => __( 'Draft', 'wfd-system' ),
		'wfd_pending'    => __( 'Pending', 'wfd-system' ),
		'wfd_processing' => __( 'Processing', 'wfd-system' ),
		'wfd_paid'       => __( 'Paid', 'wfd-system' ),
		'wfd_cancelled'  => __( 'Cancelled', 'wfd-system' ),
		'wfd_overdue'    => __( 'Overdue', 'wfd-system' ),
	);

	$status = get_post_status( $post );

	if ( isset( $labels[ $status ] ) ) {
		$states[ $status ] = $labels[ $status ];
	}

	return $states;
}
add_filter( 'display_post_states', 'wfd_invoice_status_labels', 10, 2 );

/**
 * Register invoice meta boxes.
 *
 * @return void
 */
function wfd_register_invoice_meta_boxes() {
	add_meta_box(
		'wfd_invoice_details',
		__( 'Invoice Details', 'wfd-system' ),
		'wfd_render_invoice_details_meta_box',
		'wfd_invoice',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_wfd_invoice', 'wfd_register_invoice_meta_boxes' );

/**
 * Render the invoice details meta box.
 *
 * @param WP_Post $post Post object.
 *
 * @return void
 */
function wfd_render_invoice_details_meta_box( $post ) {
	wp_nonce_field( 'wfd_save_invoice_details', 'wfd_invoice_nonce' );

	$invoice_number    = wfd_get_invoice_meta( $post->ID, 'invoice_number' );
	$customer_name     = wfd_get_invoice_meta( $post->ID, 'customer_name' );
	$customer_email    = wfd_get_invoice_meta( $post->ID, 'customer_email' );
	$due_date          = wfd_get_invoice_meta( $post->ID, 'due_date' );
	$subtotal          = wfd_get_invoice_meta( $post->ID, 'subtotal' );
	$tax_amount        = wfd_get_invoice_meta( $post->ID, 'tax_amount' );
	$total             = wfd_get_invoice_meta( $post->ID, 'total' );
	$payment_link      = wfd_get_invoice_meta( $post->ID, 'payment_link' );
	$bank_instructions = wfd_get_invoice_meta( $post->ID, 'bank_instructions', wfd_get_setting( 'default_bank_instructions', '' ) );
	$line_items        = wfd_get_invoice_meta( $post->ID, 'line_items_raw' );
	$notes             = wfd_get_invoice_meta( $post->ID, 'invoice_notes' );
	$po_number         = wfd_get_invoice_meta( $post->ID, 'po_number' );
	$customer_company  = wfd_get_invoice_meta( $post->ID, 'customer_company' );
	?>
	<p class="description">
	<?php esc_html_e( 'Provide the customer and invoice details that will appear on generated emails and invoice pages.', 'wfd-system' ); ?>
	</p>
	<table class="form-table wfd-invoice-table">
	<tbody>
	<tr>
	<th scope="row"><label for="wfd_invoice_number"><?php esc_html_e( 'Invoice Number', 'wfd-system' ); ?></label></th>
	<td><input type="text" class="regular-text" name="wfd_invoice_number" id="wfd_invoice_number" value="<?php echo esc_attr( $invoice_number ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_po_number"><?php esc_html_e( 'PO Number', 'wfd-system' ); ?></label></th>
	<td><input type="text" class="regular-text" name="wfd_po_number" id="wfd_po_number" value="<?php echo esc_attr( $po_number ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_customer_company"><?php esc_html_e( 'Customer Company', 'wfd-system' ); ?></label></th>
	<td><input type="text" class="regular-text" name="wfd_customer_company" id="wfd_customer_company" value="<?php echo esc_attr( $customer_company ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_customer_name"><?php esc_html_e( 'Customer Name', 'wfd-system' ); ?></label></th>
	<td><input type="text" class="regular-text" name="wfd_customer_name" id="wfd_customer_name" value="<?php echo esc_attr( $customer_name ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_customer_email"><?php esc_html_e( 'Customer Email', 'wfd-system' ); ?></label></th>
	<td><input type="email" class="regular-text" name="wfd_customer_email" id="wfd_customer_email" value="<?php echo esc_attr( $customer_email ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_due_date"><?php esc_html_e( 'Due Date', 'wfd-system' ); ?></label></th>
	<td><input type="date" name="wfd_due_date" id="wfd_due_date" value="<?php echo esc_attr( $due_date ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_subtotal"><?php esc_html_e( 'Subtotal', 'wfd-system' ); ?></label></th>
	<td><input type="number" step="0.01" class="small-text" name="wfd_subtotal" id="wfd_subtotal" value="<?php echo esc_attr( $subtotal ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_tax_amount"><?php esc_html_e( 'Tax Amount', 'wfd-system' ); ?></label></th>
	<td><input type="number" step="0.01" class="small-text" name="wfd_tax_amount" id="wfd_tax_amount" value="<?php echo esc_attr( $tax_amount ); ?>" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_total"><?php esc_html_e( 'Total', 'wfd-system' ); ?></label></th>
	<td><input type="number" step="0.01" class="small-text" name="wfd_total" id="wfd_total" value="<?php echo esc_attr( $total ); ?>" required /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_payment_link"><?php esc_html_e( 'Payment Link', 'wfd-system' ); ?></label></th>
	<td><input type="url" class="regular-text" name="wfd_payment_link" id="wfd_payment_link" value="<?php echo esc_url( $payment_link ); ?>" placeholder="https://" /></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_bank_instructions"><?php esc_html_e( 'Bank Transfer Instructions', 'wfd-system' ); ?></label></th>
	<td><textarea name="wfd_bank_instructions" id="wfd_bank_instructions" rows="4" class="large-text"><?php echo esc_textarea( $bank_instructions ); ?></textarea></td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_line_items_raw"><?php esc_html_e( 'Line Items', 'wfd-system' ); ?></label></th>
	<td>
	<textarea name="wfd_line_items_raw" id="wfd_line_items_raw" rows="6" class="large-text"><?php echo esc_textarea( $line_items ); ?></textarea>
	<p class="description"><?php esc_html_e( 'Enter one line per item in the format: Description | Qty | Unit Price | Total.', 'wfd-system' ); ?></p>
	</td>
	</tr>
	<tr>
	<th scope="row"><label for="wfd_invoice_notes"><?php esc_html_e( 'Internal Notes', 'wfd-system' ); ?></label></th>
	<td><textarea name="wfd_invoice_notes" id="wfd_invoice_notes" rows="4" class="large-text"><?php echo esc_textarea( $notes ); ?></textarea></td>
	</tr>
	</tbody>
	</table>
<?php
}

/**
 * Persist invoice meta box data.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function wfd_save_invoice_details( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! isset( $_POST['wfd_invoice_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['wfd_invoice_nonce'] ), 'wfd_save_invoice_details' ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_wfd_invoice', $post_id ) ) {
		return;
	}

	$fields = array(
		'invoice_number'    => 'sanitize_text_field',
		'po_number'         => 'sanitize_text_field',
		'customer_company'  => 'sanitize_text_field',
		'customer_name'     => 'sanitize_text_field',
		'customer_email'    => 'sanitize_email',
		'due_date'          => 'sanitize_text_field',
		'subtotal'          => 'floatval',
		'tax_amount'        => 'floatval',
		'total'             => 'floatval',
		'payment_link'      => 'esc_url_raw',
		'bank_instructions' => 'wp_kses_post',
		'line_items_raw'    => 'wfd_sanitize_invoice_lines',
		'invoice_notes'     => 'wp_kses_post',
	);

	foreach ( $fields as $field => $callback ) {
		$key = 'wfd_' . $field;

		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$value = $_POST[ $key ]; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( is_callable( $callback ) ) {
			$value = call_user_func( $callback, wp_unslash( $value ) );
		}

		wfd_update_invoice_meta( $post_id, $field, $value );
	}
}
add_action( 'save_post_wfd_invoice', 'wfd_save_invoice_details' );

/**
 * Sanitize the invoice line items field.
 *
 * @param string $value Raw line items input.
 *
 * @return string
 */
function wfd_sanitize_invoice_lines( $value ) {
	$lines = array_filter( array_map( 'trim', explode( "
", (string) $value ) ) );

	return implode( "
", $lines );
}

/**
 * Retrieve line items as structured arrays.
 *
 * @param int $invoice_id Invoice ID.
 *
 * @return array
 */
function wfd_get_invoice_line_items( $invoice_id ) {
	$raw = wfd_get_invoice_meta( $invoice_id, 'line_items_raw', '' );

	if ( empty( $raw ) ) {
		return array();
	}

	$lines = array_map( 'trim', explode( "
", (string) $raw ) );
	$items = array();

	foreach ( $lines as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );

		$items[] = array(
			'description' => $parts[0] ?? '',
			'quantity'    => isset( $parts[1] ) ? (float) $parts[1] : 1,
			'unit_price'  => isset( $parts[2] ) ? (float) $parts[2] : 0,
			'total'       => isset( $parts[3] ) ? (float) $parts[3] : 0,
		);
	}

	return $items;
}

/**
 * Add custom columns to the invoice admin list.
 *
 * @param array $columns Registered columns.
 *
 * @return array
 */
function wfd_invoice_admin_columns( $columns ) {
	$columns['wfd_invoice_number'] = __( 'Invoice #', 'wfd-system' );
	$columns['wfd_customer']       = __( 'Customer', 'wfd-system' );
	$columns['wfd_total']          = __( 'Total', 'wfd-system' );
	$columns['wfd_due']            = __( 'Due Date', 'wfd-system' );

	return $columns;
}
add_filter( 'manage_wfd_invoice_posts_columns', 'wfd_invoice_admin_columns' );

/**
 * Render custom column content.
 *
 * @param string $column  Column name.
 * @param int    $post_id Invoice ID.
 *
 * @return void
 */
function wfd_invoice_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'wfd_invoice_number':
		echo esc_html( wfd_get_invoice_meta( $post_id, 'invoice_number' ) );
		break;
		case 'wfd_customer':
		$customer = wfd_get_invoice_meta( $post_id, 'customer_name' );

		if ( empty( $customer ) ) {
		$customer = wfd_get_invoice_meta( $post_id, 'customer_company' );
		}

		echo esc_html( $customer );
		break;
		case 'wfd_total':
		$total = wfd_get_invoice_meta( $post_id, 'total', 0 );
		echo esc_html( wfd_format_currency( $total ) );
		break;
		case 'wfd_due':
		$due = wfd_get_invoice_meta( $post_id, 'due_date' );

		if ( $due ) {
		echo esc_html( wp_date( get_option( 'date_format' ), strtotime( $due ) ) );
		}
		break;
	}
}
add_action( 'manage_wfd_invoice_posts_custom_column', 'wfd_invoice_admin_column_content', 10, 2 );

/**
 * Make custom columns sortable.
 *
 * @param array $columns Sortable columns.
 *
 * @return array
 */
function wfd_invoice_sortable_columns( $columns ) {
	$columns['wfd_invoice_number'] = 'wfd_invoice_number';
	$columns['wfd_due']            = 'wfd_due';

	return $columns;
}
add_filter( 'manage_edit-wfd_invoice_sortable_columns', 'wfd_invoice_sortable_columns' );

/**
 * Adjust the admin query for sortable columns.
 *
 * @param WP_Query $query Query instance.
 *
 * @return void
 */
function wfd_invoice_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );

	if ( 'wfd_invoice_number' === $orderby ) {
		$query->set( 'meta_key', '_wfd_invoice_number' );
		$query->set( 'orderby', 'meta_value' );
	}

	if ( 'wfd_due' === $orderby ) {
		$query->set( 'meta_key', '_wfd_due_date' );
		$query->set( 'orderby', 'meta_value' );
	}
}
add_action( 'pre_get_posts', 'wfd_invoice_orderby' );
