<?php
/**
 * Invoice email and reminder automation.
 *
 * @package WFD\System
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WFD_INVOICE_REMINDER_EVENT = 'wfd_invoice_reminder_event';

/**
 * Register invoice email settings.
 */
function wfd_register_invoice_email_settings() {
	register_setting( 'wfd_invoice_email', 'wfd_invoice_from_name', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_from_email', array( 'sanitize_callback' => 'sanitize_email' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_reply_to', array( 'sanitize_callback' => 'sanitize_email' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_cc', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_bcc', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_company_logo', array( 'sanitize_callback' => 'esc_url_raw' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_company_name', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_company_address', array( 'sanitize_callback' => 'wp_kses_post' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_company_phone', array( 'sanitize_callback' => 'sanitize_text_field' ) );
	register_setting( 'wfd_invoice_email', 'wfd_default_bank_instructions', array( 'sanitize_callback' => 'wp_kses_post' ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_reminder_days', array( 'sanitize_callback' => 'absint', 'default' => 3 ) );
	register_setting( 'wfd_invoice_email', 'wfd_invoice_token_expiration', array( 'sanitize_callback' => 'absint', 'default' => WEEK_IN_SECONDS ) );
}
add_action( 'admin_init', 'wfd_register_invoice_email_settings' );

/**
 * Add settings submenu under WFD System.
 */
function wfd_invoice_email_settings_menu() {
	add_submenu_page(
	'wfd-system',
	__( 'Invoice Email Settings', 'wfd-system' ),
	__( 'Invoice Emails', 'wfd-system' ),
	'manage_options',
	'wfd-invoice-email',
	'wfd_render_invoice_email_settings_page'
	);
}
add_action( 'admin_menu', 'wfd_invoice_email_settings_menu' );

/**
 * Render the invoice email settings page.
 */
function wfd_render_invoice_email_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
?>
<div class="wrap wfd-invoice-email-settings">
<h1><?php esc_html_e( 'Invoice Email Settings', 'wfd-system' ); ?></h1>
<form action="options.php" method="post">
<?php settings_fields( 'wfd_invoice_email' ); ?>
<table class="form-table" role="presentation">
<tbody>
<tr>
<th scope="row"><label for="wfd_invoice_from_name"><?php esc_html_e( 'From Name', 'wfd-system' ); ?></label></th>
<td><input type="text" class="regular-text" id="wfd_invoice_from_name" name="wfd_invoice_from_name" value="<?php echo esc_attr( get_option( 'wfd_invoice_from_name', 'Waterfilter Direct Accounts' ) ); ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_from_email"><?php esc_html_e( 'From Email', 'wfd-system' ); ?></label></th>
<td><input type="email" class="regular-text" id="wfd_invoice_from_email" name="wfd_invoice_from_email" value="<?php echo esc_attr( get_option( 'wfd_invoice_from_email', get_bloginfo( 'admin_email' ) ) ); ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_reply_to"><?php esc_html_e( 'Reply-To Email', 'wfd-system' ); ?></label></th>
<td><input type="email" class="regular-text" id="wfd_invoice_reply_to" name="wfd_invoice_reply_to" value="<?php echo esc_attr( get_option( 'wfd_invoice_reply_to', get_bloginfo( 'admin_email' ) ) ); ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_cc"><?php esc_html_e( 'CC Recipients', 'wfd-system' ); ?></label></th>
<td><input type="text" class="regular-text" id="wfd_invoice_cc" name="wfd_invoice_cc" value="<?php echo esc_attr( get_option( 'wfd_invoice_cc', '' ) ); ?>" />
<p class="description"><?php esc_html_e( 'Comma separated list of additional recipients to copy on invoice emails.', 'wfd-system' ); ?></p></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_bcc"><?php esc_html_e( 'BCC Recipients', 'wfd-system' ); ?></label></th>
<td><input type="text" class="regular-text" id="wfd_invoice_bcc" name="wfd_invoice_bcc" value="<?php echo esc_attr( get_option( 'wfd_invoice_bcc', '' ) ); ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_company_logo"><?php esc_html_e( 'Company Logo URL', 'wfd-system' ); ?></label></th>
<td><input type="url" class="regular-text" id="wfd_invoice_company_logo" name="wfd_invoice_company_logo" value="<?php echo esc_attr( get_option( 'wfd_invoice_company_logo', '' ) ); ?>" placeholder="https://" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_company_name"><?php esc_html_e( 'Company Name', 'wfd-system' ); ?></label></th>
<td><input type="text" class="regular-text" id="wfd_invoice_company_name" name="wfd_invoice_company_name" value="<?php echo esc_attr( get_option( 'wfd_invoice_company_name', 'Waterfilter Direct' ) ); ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_company_address"><?php esc_html_e( 'Company Address', 'wfd-system' ); ?></label></th>
<td><textarea id="wfd_invoice_company_address" name="wfd_invoice_company_address" rows="4" class="large-text"><?php echo esc_textarea( get_option( 'wfd_invoice_company_address', '' ) ); ?></textarea></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_company_phone"><?php esc_html_e( 'Company Phone', 'wfd-system' ); ?></label></th>
<td><input type="text" class="regular-text" id="wfd_invoice_company_phone" name="wfd_invoice_company_phone" value="<?php echo esc_attr( get_option( 'wfd_invoice_company_phone', '1300 789 132' ) ); ?>" /></td>
</tr>
<tr>
<th scope="row"><label for="wfd_default_bank_instructions"><?php esc_html_e( 'Default Bank Instructions', 'wfd-system' ); ?></label></th>
<td><textarea id="wfd_default_bank_instructions" name="wfd_default_bank_instructions" rows="4" class="large-text"><?php echo esc_textarea( get_option( 'wfd_default_bank_instructions', '' ) ); ?></textarea></td>
</tr>
<tr>
<th scope="row"><label for="wfd_invoice_reminder_days"><?php esc_html_e( 'Reminder Days Before Due', 'wfd-system' ); ?></label></th>
<td><input type="number" min="1" class="small-text" id="wfd_invoice_reminder_days" name="wfd_invoice_reminder_days" value="<?php echo esc_attr( get_option( 'wfd_invoice_reminder_days', 3 ) ); ?>" /></td>
</tr>
</tbody>
</table>
<?php submit_button( __( 'Save Settings', 'wfd-system' ) ); ?>
</form>
</div>
<?php
}

/**
 * Ensure invoice token expiration follows setting.
 */
function wfd_invoice_update_token_expiration_setting( $value ) {
	update_option( 'wfd_invoice_token_expiration', absint( $value ) );
	return absint( $value );
}
add_filter( 'pre_update_option_wfd_invoice_token_expiration', 'wfd_invoice_update_token_expiration_setting' );

/**
 * Hook into status transitions to send invoices and schedule reminders.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function wfd_handle_invoice_status_change( $new_status, $old_status, $post ) {
	if ( 'wfd_invoice' !== $post->post_type ) {
		return;
	}

if ( 'wfd_pending' === $new_status && 'wfd_pending' !== $old_status ) {
	wfd_send_invoice_email( $post->ID, 'invoice_issued' );
	wfd_schedule_invoice_reminder( $post->ID );
}

if ( in_array( $new_status, array( 'wfd_paid', 'wfd_cancelled' ), true ) ) {
	wfd_clear_invoice_reminders( $post->ID );
}

if ( 'wfd_overdue' === $new_status && 'wfd_overdue' !== $old_status ) {
	wfd_send_invoice_email( $post->ID, 'overdue_notice' );
}
}
add_action( 'transition_post_status', 'wfd_handle_invoice_status_change', 10, 3 );

/**
 * Schedule an invoice reminder event.
 *
 * @param int $invoice_id Invoice ID.
 */
function wfd_schedule_invoice_reminder( $invoice_id ) {
	$due_date = wfd_get_invoice_meta( $invoice_id, 'due_date' );

	if ( empty( $due_date ) ) {
		return;
	}

$timestamp         = strtotime( $due_date . ' 09:00:00' );
$days_before_due   = absint( get_option( 'wfd_invoice_reminder_days', 3 ) );
$reminder_time     = $timestamp - ( $days_before_due * DAY_IN_SECONDS );
$reminder_time     = max( $reminder_time, time() + HOUR_IN_SECONDS );
$current_scheduled = wp_next_scheduled( WFD_INVOICE_REMINDER_EVENT, array( $invoice_id ) );

if ( $current_scheduled ) {
	wp_unschedule_event( $current_scheduled, WFD_INVOICE_REMINDER_EVENT, array( $invoice_id ) );
}

wp_schedule_single_event( $reminder_time, WFD_INVOICE_REMINDER_EVENT, array( $invoice_id ) );
}

/**
 * Clear scheduled reminders for invoice.
 *
 * @param int $invoice_id Invoice ID.
 */
function wfd_clear_invoice_reminders( $invoice_id ) {
	$timestamp = wp_next_scheduled( WFD_INVOICE_REMINDER_EVENT, array( $invoice_id ) );

	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, WFD_INVOICE_REMINDER_EVENT, array( $invoice_id ) );
	}
}

/**
 * Cron callback for invoice reminders.
 *
 * @param int $invoice_id Invoice ID.
 */
function wfd_process_invoice_reminder( $invoice_id ) {
	$status = get_post_status( $invoice_id );

	if ( 'wfd_pending' !== $status && 'wfd_processing' !== $status && 'wfd_overdue' !== $status ) {
		return;
	}

wfd_send_invoice_email( $invoice_id, 'payment_reminder' );
}
add_action( WFD_INVOICE_REMINDER_EVENT, 'wfd_process_invoice_reminder' );

/**
 * Send invoice emails.
 *
 * @param int    $invoice_id Invoice ID.
 * @param string $type       Email type.
 */
function wfd_send_invoice_email( $invoice_id, $type = 'invoice_issued' ) {
	$invoice = get_post( $invoice_id );

	if ( ! $invoice || 'wfd_invoice' !== $invoice->post_type ) {
		return;
	}

$customer_email = wfd_get_invoice_meta( $invoice_id, 'customer_email' );

if ( empty( $customer_email ) || ! is_email( $customer_email ) ) {
	return;
}

$token = wfd_generate_invoice_token( $invoice_id );
$url   = wfd_get_invoice_view_url( $invoice_id, $token );

$context = wfd_prepare_invoice_context( $invoice_id );
$context['secure_url'] = $url;
$context['email_type'] = $type;

$subject = wfd_get_invoice_email_subject( $context );
$body    = wfd_render_template( 'emails/invoice-email.php', $context );

if ( empty( $body ) ) {
	$body = wfd_generate_fallback_invoice_email( $context );
}

$headers   = array( 'Content-Type: text/html; charset=UTF-8' );
$from_name = get_option( 'wfd_invoice_from_name', get_bloginfo( 'name' ) );
$from_email = get_option( 'wfd_invoice_from_email', get_bloginfo( 'admin_email' ) );
$reply_to  = get_option( 'wfd_invoice_reply_to', $from_email );

$headers[] = sprintf( 'From: %s <%s>', $from_name, $from_email );

if ( is_email( $reply_to ) ) {
	$headers[] = sprintf( 'Reply-To: %s <%s>', $from_name, $reply_to );
}

$cc  = array_filter( array_map( 'trim', explode( ',', (string) get_option( 'wfd_invoice_cc', '' ) ) ) );
$bcc = array_filter( array_map( 'trim', explode( ',', (string) get_option( 'wfd_invoice_bcc', '' ) ) ) );

foreach ( $cc as $email ) {
	if ( is_email( $email ) ) {
		$headers[] = 'Cc: ' . sanitize_email( $email );
	}
}

foreach ( $bcc as $email ) {
	if ( is_email( $email ) ) {
		$headers[] = 'Bcc: ' . sanitize_email( $email );
	}
}

wp_mail( $customer_email, $subject, $body, $headers );
}

/**
 * Prepare invoice email subject.
 *
 * @param array $context Email context.
 *
 * @return string
 */
function wfd_get_invoice_email_subject( $context ) {
	$invoice_number = $context['invoice_number'] ?? get_the_title( $context['invoice_id'] );
	$company_name   = get_option( 'wfd_invoice_company_name', 'Waterfilter Direct' );

	switch ( $context['email_type'] ) {
		case 'payment_reminder':
		return sprintf( __( 'Payment Reminder: Invoice %s', 'wfd-system' ), $invoice_number );
		case 'overdue_notice':
		return sprintf( __( 'Overdue Notice: Invoice %s', 'wfd-system' ), $invoice_number );
		default:
		return sprintf( __( '%1$s Invoice %2$s', 'wfd-system' ), $company_name, $invoice_number );
	}
}

/**
 * Generate fallback email body.
 *
 * @param array $context Invoice context.
 *
 * @return string
 */
function wfd_generate_fallback_invoice_email( $context ) {
	$greeting = sprintf( __( 'Hi %s,', 'wfd-system' ), esc_html( $context['customer_name'] ) );
	$intro    = __( 'Your invoice is ready. Please review the details below and complete payment at your earliest convenience.', 'wfd-system' );

	$items_html = '';
	foreach ( $context['line_items'] as $item ) {
		$items_html .= sprintf(
		'<tr><td>%1$s</td><td>%2$s</td><td>%3$s</td><td>%4$s</td></tr>',
		esc_html( $item['description'] ),
		esc_html( $item['quantity'] ),
		esc_html( $item['unit_price_formatted'] ),
		esc_html( $item['total_formatted'] )
		);
	}

return sprintf(
'<p>%1$s</p><p>%2$s</p><table border="0" cellpadding="6" cellspacing="0">%3$s</table><p><strong>%4$s</strong>: %5$s</p><p><a href="%6$s" style="background:#1d5da8;color:#fff;padding:10px 20px;text-decoration:none;display:inline-block;">%7$s</a></p>',
$greeting,
$intro,
$items_html,
esc_html__( 'Amount Due', 'wfd-system' ),
esc_html( $context['total_formatted'] ),
esc_url( $context['secure_url'] ),
esc_html__( 'View Invoice', 'wfd-system' )
);
}

/**
 * Gather invoice data for templates.
 *
 * @param int $invoice_id Invoice ID.
 *
 * @return array
 */
function wfd_prepare_invoice_context( $invoice_id ) {
	$company_logo     = get_option( 'wfd_invoice_company_logo', '' );
	$company_name     = get_option( 'wfd_invoice_company_name', 'Waterfilter Direct' );
	$company_address  = get_option( 'wfd_invoice_company_address', '' );
	$company_phone    = get_option( 'wfd_invoice_company_phone', '' );
	$bank_instructions = wfd_get_invoice_meta( $invoice_id, 'bank_instructions', get_option( 'wfd_default_bank_instructions', '' ) );

	$line_items = array();

	foreach ( wfd_get_invoice_line_items( $invoice_id ) as $item ) {
		$line_items[] = array(
		'description'          => $item['description'],
		'quantity'             => $item['quantity'],
		'unit_price'           => $item['unit_price'],
		'unit_price_formatted' => wfd_format_currency( $item['unit_price'] ),
		'total'                => $item['total'],
		'total_formatted'      => wfd_format_currency( $item['total'] ),
		);
	}

$total = (float) wfd_get_invoice_meta( $invoice_id, 'total', 0 );
$tax   = (float) wfd_get_invoice_meta( $invoice_id, 'tax_amount', 0 );
$subtotal = (float) wfd_get_invoice_meta( $invoice_id, 'subtotal', $total - $tax );

return array(
'invoice_id'         => $invoice_id,
'invoice_number'     => wfd_get_invoice_meta( $invoice_id, 'invoice_number', get_the_title( $invoice_id ) ),
'po_number'          => wfd_get_invoice_meta( $invoice_id, 'po_number', '' ),
'due_date'           => wfd_get_invoice_meta( $invoice_id, 'due_date', '' ),
'due_date_formatted' => wfd_get_invoice_meta( $invoice_id, 'due_date', '' ) ? wp_date( get_option( 'date_format' ), strtotime( wfd_get_invoice_meta( $invoice_id, 'due_date' ) ) ) : '',
'customer_name'      => wfd_get_invoice_meta( $invoice_id, 'customer_name', '' ),
'customer_company'   => wfd_get_invoice_meta( $invoice_id, 'customer_company', '' ),
'customer_email'     => wfd_get_invoice_meta( $invoice_id, 'customer_email', '' ),
'payment_link'       => wfd_get_invoice_meta( $invoice_id, 'payment_link', '' ),
'bank_instructions'  => $bank_instructions,
'notes'              => wfd_get_invoice_meta( $invoice_id, 'invoice_notes', '' ),
'subtotal'           => $subtotal,
'subtotal_formatted' => wfd_format_currency( $subtotal ),
'tax_amount'         => $tax,
'tax_formatted'      => wfd_format_currency( $tax ),
'total'              => $total,
'total_formatted'    => wfd_format_currency( $total ),
'company_logo'       => $company_logo,
'company_name'       => $company_name,
'company_address'    => $company_address,
'company_phone'      => $company_phone,
'line_items'         => $line_items,
'invoice_content'    => apply_filters( 'the_content', get_post_field( 'post_content', $invoice_id ) ),
);
}

/**
 * Generate secure invoice view URL.
 *
 * @param int    $invoice_id Invoice ID.
 * @param string $token      Access token.
 *
 * @return string
 */
function wfd_get_invoice_view_url( $invoice_id, $token ) {
	return add_query_arg(
	array(
	'wfd_invoice' => $invoice_id,
	'token'       => $token,
	),
	home_url( '/' )
	);
}

/**
 * Add query vars for secure invoice viewing.
 *
 * @param array $vars Query vars.
 *
 * @return array
 */
function wfd_invoice_query_vars( $vars ) {
	$vars[] = 'wfd_invoice';
	$vars[] = 'token';

	return $vars;
}
add_filter( 'query_vars', 'wfd_invoice_query_vars' );

/**
 * Render public invoice view when token is provided.
 */
function wfd_maybe_render_invoice_view() {
	$invoice_id = absint( get_query_var( 'wfd_invoice' ) );
	$token      = sanitize_text_field( get_query_var( 'token' ) );

	if ( ! $invoice_id || empty( $token ) ) {
		return;
	}

nocache_headers();

if ( ! wfd_validate_invoice_token( $invoice_id, $token ) ) {
	wp_die( esc_html__( 'Invoice link has expired or is invalid.', 'wfd-system' ), esc_html__( 'Invoice Unavailable', 'wfd-system' ), array( 'response' => 403 ) );
}

$context = wfd_prepare_invoice_context( $invoice_id );
$context['secure_url'] = wfd_get_invoice_view_url( $invoice_id, $token );

echo wfd_render_template( 'invoice-view.php', $context );
exit;
}
add_action( 'template_redirect', 'wfd_maybe_render_invoice_view' );

/**
 * Output admin notices when invoice lacks customer email.
 */
function wfd_invoice_email_admin_notices() {
	global $pagenow;

	if ( 'post.php' !== $pagenow ) {
		return;
	}

$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if ( ! $post_id ) {
	return;
}

$post = get_post( $post_id );

if ( ! $post || 'wfd_invoice' !== $post->post_type ) {
	return;
}

$email = wfd_get_invoice_meta( $post_id, 'customer_email' );

if ( empty( $email ) ) {
	printf( '<div class="notice notice-warning"><p>%s</p></div>', esc_html__( 'Customer email address is required to send invoice notifications.', 'wfd-system' ) );
}
}
add_action( 'admin_notices', 'wfd_invoice_email_admin_notices' );
