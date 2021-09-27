<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php echo apply_filters('w2mb_renew_option', esc_html__('Renew listing', 'W2MB'), $listing); ?>
</h2>

<p><?php esc_html_e('Listing will be renewed and raised up to the top of all lists, those ordered by date.', 'W2MB'); ?></p>

<?php do_action('w2mb_renew_html', $listing); ?>

<?php if ($action == 'show'): ?>
<a href="<?php echo admin_url('options.php?page=w2mb_renew&listing_id=' . $listing->post->ID . '&renew_action=renew&referer=' . urlencode($referer)); ?>" class="button button-primary"><?php esc_html_e('Renew listing', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $referer; ?>" class="button button-primary"><?php esc_html_e('Cancel', 'W2MB'); ?></a>
<?php elseif ($action == 'renew'): ?>
<a href="<?php echo $referer; ?>" class="button button-primary"><?php esc_html_e('Go back ', 'W2MB'); ?></a>
<?php endif; ?>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>