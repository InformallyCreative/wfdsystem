<?php
/**
 * Secure invoice view template.
 *
 * @var array $context
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

$context = isset( $context ) ? $context : array();

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo esc_html( sprintf( __( 'Invoice %s', 'wfd-system' ), $context['invoice_number'] ?? '' ) ); ?></title>
<?php wp_head(); ?>
<style>
body {
font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
background: #f5f7fb;
margin: 0;
padding: 2rem;
}
.wfd-invoice-wrapper {
max-width: 900px;
margin: 0 auto;
background: #ffffff;
border-radius: 12px;
box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
overflow: hidden;
}
.wfd-invoice-header {
display: flex;
align-items: center;
justify-content: space-between;
padding: 2.5rem 3rem;
background: linear-gradient(135deg, #1d5da8 0%, #0f3f76 100%);
color: #ffffff;
}
.wfd-invoice-header img {
height: 70px;
max-width: 200px;
object-fit: contain;
}
.wfd-invoice-header h1 {
margin: 0;
font-size: 2.25rem;
letter-spacing: 0.02em;
}
.wfd-invoice-body {
padding: 3rem;
}
.wfd-invoice-meta {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
gap: 1.5rem;
margin-bottom: 3rem;
}
.wfd-card {
background: #f8fafc;
border-radius: 10px;
padding: 1.75rem;
border: 1px solid rgba(29, 93, 168, 0.1);
}
.wfd-card h3 {
margin-top: 0;
margin-bottom: 1rem;
font-size: 1.1rem;
color: #1d5da8;
}
.wfd-line-items table {
width: 100%;
border-collapse: collapse;
}
.wfd-line-items thead tr {
background: #eef3fb;
}
.wfd-line-items th,
.wfd-line-items td {
padding: 1rem;
border-bottom: 1px solid #e6ecf5;
text-align: left;
font-size: 0.95rem;
}
.wfd-line-items tfoot td {
font-weight: 600;
border-bottom: none;
}
.wfd-amount-due {
text-align: right;
margin-top: 2rem;
}
.wfd-amount-due h2 {
font-size: 2rem;
margin: 0;
color: #1d5da8;
}
.wfd-actions {
margin-top: 2.5rem;
}
.wfd-actions .wfd-button {
display: inline-block;
background: #1d5da8;
color: #ffffff;
padding: 0.95rem 2.75rem;
border-radius: 999px;
text-decoration: none;
font-weight: 600;
box-shadow: 0 10px 20px rgba(29, 93, 168, 0.25);
transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.wfd-actions .wfd-button:hover {
transform: translateY(-2px);
box-shadow: 0 16px 24px rgba(29, 93, 168, 0.3);
}
.wfd-footer {
margin-top: 3rem;
padding-top: 2rem;
border-top: 1px solid #e2e8f0;
color: #64748b;
font-size: 0.9rem;
}
@media (max-width: 600px) {
body {
padding: 1rem;
}
.wfd-invoice-body {
padding: 2rem 1.5rem;
}
.wfd-invoice-header {
flex-direction: column;
text-align: center;
gap: 1rem;
}
}
</style>
</head>
<body>
<div class="wfd-invoice-wrapper">
<header class="wfd-invoice-header">
<div class="wfd-brand">
<?php if ( ! empty( $context['company_logo'] ) ) : ?>
<img src="<?php echo esc_url( $context['company_logo'] ); ?>" alt="<?php echo esc_attr( $context['company_name'] ); ?>" />
<?php endif; ?>
<h1><?php echo esc_html( $context['company_name'] ?? '' ); ?></h1>
</div>
<div class="wfd-invoice-meta-number">
<strong><?php esc_html_e( 'Invoice', 'wfd-system' ); ?>:</strong>
<div><?php echo esc_html( $context['invoice_number'] ?? '' ); ?></div>
</div>
</header>
<section class="wfd-invoice-body">
<div class="wfd-invoice-meta">
<div class="wfd-card">
<h3><?php esc_html_e( 'Billed To', 'wfd-system' ); ?></h3>
<p>
<?php echo esc_html( $context['customer_name'] ?? '' ); ?><br />
<?php if ( ! empty( $context['customer_company'] ) ) : ?>
<span><?php echo esc_html( $context['customer_company'] ); ?></span>
<?php endif; ?><br />
<?php echo esc_html( $context['customer_email'] ?? '' ); ?>
</p>
</div>
<div class="wfd-card">
<h3><?php esc_html_e( 'Invoice Details', 'wfd-system' ); ?></h3>
<p>
<strong><?php esc_html_e( 'PO Number:', 'wfd-system' ); ?></strong> <?php echo esc_html( $context['po_number'] ?? __( 'Not provided', 'wfd-system' ) ); ?><br />
<strong><?php esc_html_e( 'Due Date:', 'wfd-system' ); ?></strong> <?php echo esc_html( $context['due_date_formatted'] ?? __( 'Not set', 'wfd-system' ) ); ?><br />
<strong><?php esc_html_e( 'Status:', 'wfd-system' ); ?></strong> <?php echo esc_html( get_post_status_object( get_post_status( $context['invoice_id'] ) )->label ?? '' ); ?>
</p>
</div>
<div class="wfd-card">
<h3><?php esc_html_e( 'Our Details', 'wfd-system' ); ?></h3>
<p>
<?php echo wp_kses_post( wpautop( $context['company_address'] ?? '' ) ); ?>
<?php if ( ! empty( $context['company_phone'] ) ) : ?>
<span style="display:block;margin-top:0.5rem;"><?php echo esc_html( $context['company_phone'] ); ?></span>
<?php endif; ?>
</p>
</div>
</div>
<div class="wfd-line-items">
<table>
<thead>
<tr>
<th><?php esc_html_e( 'Description', 'wfd-system' ); ?></th>
<th><?php esc_html_e( 'Quantity', 'wfd-system' ); ?></th>
<th><?php esc_html_e( 'Unit Price', 'wfd-system' ); ?></th>
<th><?php esc_html_e( 'Total', 'wfd-system' ); ?></th>
</tr>
</thead>
<tbody>
<?php foreach ( $context['line_items'] as $item ) : ?>
<tr>
<td><?php echo esc_html( $item['description'] ); ?></td>
<td><?php echo esc_html( $item['quantity'] ); ?></td>
<td><?php echo esc_html( $item['unit_price_formatted'] ); ?></td>
<td><?php echo esc_html( $item['total_formatted'] ); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr>
<td colspan="3" style="text-align:right;"><?php esc_html_e( 'Subtotal', 'wfd-system' ); ?></td>
<td><?php echo esc_html( $context['subtotal_formatted'] ?? '' ); ?></td>
</tr>
<tr>
<td colspan="3" style="text-align:right;"><?php esc_html_e( 'Tax', 'wfd-system' ); ?></td>
<td><?php echo esc_html( $context['tax_formatted'] ?? '' ); ?></td>
</tr>
<tr>
<td colspan="3" style="text-align:right;font-size:1.1rem;"><?php esc_html_e( 'Total Due', 'wfd-system' ); ?></td>
<td style="font-size:1.1rem;"><?php echo esc_html( $context['total_formatted'] ?? '' ); ?></td>
</tr>
</tfoot>
</table>
</div>
<div class="wfd-amount-due">
<h2><?php echo esc_html( $context['total_formatted'] ?? '' ); ?></h2>
<p><?php esc_html_e( 'Thank you for your prompt payment.', 'wfd-system' ); ?></p>
</div>
<?php if ( ! empty( $context['payment_link'] ) ) : ?>
<div class="wfd-actions">
<a class="wfd-button" href="<?php echo esc_url( $context['payment_link'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Pay Invoice Online', 'wfd-system' ); ?></a>
</div>
<?php endif; ?>
<?php if ( ! empty( $context['bank_instructions'] ) ) : ?>
<div class="wfd-card" style="margin-top:2.5rem;">
<h3><?php esc_html_e( 'Bank Transfer Instructions', 'wfd-system' ); ?></h3>
<?php echo wp_kses_post( wpautop( $context['bank_instructions'] ) ); ?>
</div>
<?php endif; ?>
<?php if ( ! empty( $context['notes'] ) ) : ?>
<div class="wfd-card" style="margin-top:2.5rem;">
<h3><?php esc_html_e( 'Notes', 'wfd-system' ); ?></h3>
<?php echo wp_kses_post( wpautop( $context['notes'] ) ); ?>
</div>
<?php endif; ?>
<?php if ( ! empty( $context['invoice_content'] ) ) : ?>
<div class="wfd-card" style="margin-top:2.5rem;">
<?php echo wp_kses_post( $context['invoice_content'] ); ?>
</div>
<?php endif; ?>
<footer class="wfd-footer">
<?php esc_html_e( 'If you have any questions about this invoice, reply to this email or contact our accounts team.', 'wfd-system' ); ?>
</footer>
</section>
</div>
<?php wp_footer(); ?>
</body>
</html>
