<?php
/**
 * Invoice email HTML template.
 *
 * @var array $context
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

$context = isset( $context ) ? $context : array();
$company_name = $context['company_name'] ?? get_option( 'wfd_invoice_company_name', get_bloginfo( 'name' ) );
$cta_label    = __( 'View Invoice', 'wfd-system' );

switch ( $context['email_type'] ?? 'invoice_issued' ) {
case 'payment_reminder':
$intro = __( 'Friendly reminder that the invoice below is coming due soon. Please review the balance and arrange payment.', 'wfd-system' );
break;
case 'overdue_notice':
$intro = __( 'Our records indicate this invoice is now overdue. We would appreciate payment at your earliest convenience.', 'wfd-system' );
$cta_label = __( 'Pay Now', 'wfd-system' );
break;
default:
$intro = __( 'Thanks for partnering with Waterfilter Direct. Your invoice is ready and can be reviewed below.', 'wfd-system' );
break;
}

?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?php echo esc_html( sprintf( __( 'Invoice %s', 'wfd-system' ), $context['invoice_number'] ?? '' ) ); ?></title>
</head>
<body style="margin:0;padding:0;background:#f5f7fb;">
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f5f7fb;padding:32px 0;">
<tr>
<td align="center">
<table role="presentation" cellpadding="0" cellspacing="0" width="640" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 20px 45px rgba(15, 63, 118, 0.18);">
<tr>
<td style="background:linear-gradient(135deg,#1d5da8 0%,#0f3f76 100%);padding:40px 48px;color:#ffffff;">
<?php if ( ! empty( $context['company_logo'] ) ) : ?>
<img src="<?php echo esc_url( $context['company_logo'] ); ?>" alt="<?php echo esc_attr( $company_name ); ?>" style="max-height:60px;width:auto;margin-bottom:20px;" />
<?php endif; ?>
<h1 style="margin:0;font-size:28px;font-weight:700;letter-spacing:.02em;"><?php echo esc_html( $company_name ); ?></h1>
<p style="margin:12px 0 0;font-size:16px;opacity:0.9;"><?php echo esc_html( sprintf( __( 'Invoice %s', 'wfd-system' ), $context['invoice_number'] ?? '' ) ); ?></p>
</td>
</tr>
<tr>
<td style="padding:40px 48px;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:#1f2937;font-size:15px;line-height:1.6;">
<p style="margin-top:0;"><?php echo esc_html( sprintf( __( 'Hi %s,', 'wfd-system' ), $context['customer_name'] ?? __( 'there', 'wfd-system' ) ) ); ?></p>
<p><?php echo esc_html( $intro ); ?></p>
<table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="margin:32px 0;border-collapse:collapse;">
<tr>
<td style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
<tr>
<td style="width:50%;vertical-align:top;padding:0 12px 24px 0;">
<strong><?php esc_html_e( 'Billed To', 'wfd-system' ); ?></strong><br />
<?php echo esc_html( $context['customer_name'] ?? '' ); ?><br />
<?php if ( ! empty( $context['customer_company'] ) ) : ?>
<span><?php echo esc_html( $context['customer_company'] ); ?></span><br />
<?php endif; ?>
<?php echo esc_html( $context['customer_email'] ?? '' ); ?>
</td>
<td style="width:50%;vertical-align:top;padding:0 0 24px 12px;text-align:right;">
<strong><?php esc_html_e( 'Invoice Date', 'wfd-system' ); ?></strong><br />
<?php echo esc_html( wp_date( get_option( 'date_format' ), get_post_timestamp( $context['invoice_id'] ) ) ); ?><br />
<strong><?php esc_html_e( 'Due Date', 'wfd-system' ); ?></strong><br />
<?php echo esc_html( $context['due_date_formatted'] ?? __( 'Not set', 'wfd-system' ) ); ?><br />
<strong><?php esc_html_e( 'Amount Due', 'wfd-system' ); ?></strong><br />
<span style="font-size:20px;font-weight:700;color:#1d5da8;"><?php echo esc_html( $context['total_formatted'] ?? '' ); ?></span>
</td>
</tr>
<tr>
<td colspan="2" style="padding:0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:12px;border-collapse:collapse;">
<thead>
<tr style="background:#eef3fb;text-align:left;">
<th style="padding:12px 16px;font-size:13px;color:#475569;">&nbsp;<?php esc_html_e( 'Description', 'wfd-system' ); ?></th>
<th style="padding:12px 16px;font-size:13px;color:#475569;text-align:center;"><?php esc_html_e( 'Qty', 'wfd-system' ); ?></th>
<th style="padding:12px 16px;font-size:13px;color:#475569;text-align:right;"><?php esc_html_e( 'Unit Price', 'wfd-system' ); ?></th>
<th style="padding:12px 16px;font-size:13px;color:#475569;text-align:right;"><?php esc_html_e( 'Total', 'wfd-system' ); ?></th>
</tr>
</thead>
<tbody>
<?php foreach ( $context['line_items'] as $item ) : ?>
<tr>
<td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;"><?php echo esc_html( $item['description'] ); ?></td>
<td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;text-align:center;"><?php echo esc_html( $item['quantity'] ); ?></td>
<td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;text-align:right;"><?php echo esc_html( $item['unit_price_formatted'] ); ?></td>
<td style="padding:12px 16px;border-bottom:1px solid #e2e8f0;text-align:right;"><?php echo esc_html( $item['total_formatted'] ); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr>
<td colspan="3" style="padding:12px 16px;text-align:right;font-weight:600;"><?php esc_html_e( 'Subtotal', 'wfd-system' ); ?></td>
<td style="padding:12px 16px;text-align:right;font-weight:600;"><?php echo esc_html( $context['subtotal_formatted'] ?? '' ); ?></td>
</tr>
<tr>
<td colspan="3" style="padding:12px 16px;text-align:right;font-weight:600;"><?php esc_html_e( 'Tax', 'wfd-system' ); ?></td>
<td style="padding:12px 16px;text-align:right;font-weight:600;"><?php echo esc_html( $context['tax_formatted'] ?? '' ); ?></td>
</tr>
<tr>
<td colspan="3" style="padding:12px 16px;text-align:right;font-size:16px;font-weight:700;"><?php esc_html_e( 'Total Due', 'wfd-system' ); ?></td>
<td style="padding:12px 16px;text-align:right;font-size:16px;font-weight:700;"><?php echo esc_html( $context['total_formatted'] ?? '' ); ?></td>
</tr>
</tfoot>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:32px;">
<tr>
<td style="text-align:center;">
<a href="<?php echo esc_url( $context['secure_url'] ?? '' ); ?>" style="display:inline-block;background:#1d5da8;color:#ffffff;padding:14px 36px;border-radius:999px;font-weight:600;text-decoration:none;box-shadow:0 12px 24px rgba(29,93,168,.25);">
<?php echo esc_html( $cta_label ); ?>
</a>
</td>
</tr>
</table>
<?php if ( ! empty( $context['payment_link'] ) ) : ?>
<p style="margin-top:24px;text-align:center;font-size:13px;color:#475569;">
<?php esc_html_e( 'Prefer to pay instantly? Use the button above to complete payment online.', 'wfd-system' ); ?>
</p>
<?php endif; ?>
<?php if ( ! empty( $context['bank_instructions'] ) ) : ?>
<div style="margin-top:24px;padding:20px;border:1px dashed #cbd5f5;border-radius:12px;background:#f8fbff;color:#334155;">
<strong><?php esc_html_e( 'Bank Transfer Instructions', 'wfd-system' ); ?></strong>
<?php echo wp_kses_post( wpautop( $context['bank_instructions'] ) ); ?>
</div>
<?php endif; ?>
<?php if ( ! empty( $context['notes'] ) ) : ?>
<div style="margin-top:24px;padding:20px;border-radius:12px;background:#f8fafc;color:#334155;">
<strong><?php esc_html_e( 'Additional Notes', 'wfd-system' ); ?></strong>
<?php echo wp_kses_post( wpautop( $context['notes'] ) ); ?>
</div>
<?php endif; ?>
<p style="margin-top:32px;font-size:13px;color:#64748b;text-align:center;">
<?php esc_html_e( 'Thank you for choosing Waterfilter Direct. We appreciate your business!', 'wfd-system' ); ?>
</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
