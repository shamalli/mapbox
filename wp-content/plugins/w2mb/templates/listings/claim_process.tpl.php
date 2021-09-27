<?php w2mb_renderTemplate('admin_header.tpl.php'); ?>

<h2>
	<?php printf(esc_html__('Approve or decline claim of listing "%s"', 'W2MB'), $listing->title()); ?>
</h2>

<?php if ($action == 'show'): ?>
<p><?php printf(esc_html__('User "%s" had claimed this listing.', 'W2MB'), $listing->claim->claimer->display_name); ?></p>
<?php if ($listing->claim->claimer_message): ?>
<p><?php esc_html_e('Message from claimer:', 'W2MB'); ?><br /><i><?php echo $listing->claim->claimer_message; ?></i></p>
<?php endif; ?>
<p><?php esc_html_e('Claimer will receive email notification.', 'W2MB'); ?></p>

<a href="<?php echo admin_url('options.php?page=w2mb_process_claim&listing_id=' . $listing->post->ID . '&claim_action=approve&referer=' . urlencode($referer)); ?>" class="button button-primary"><?php esc_html_e('Approve', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo admin_url('options.php?page=w2mb_process_claim&listing_id=' . $listing->post->ID . '&claim_action=decline&referer=' . urlencode($referer)); ?>" class="button button-primary"><?php esc_html_e('Decline', 'W2MB'); ?></a>
&nbsp;&nbsp;&nbsp;
<a href="<?php echo $referer; ?>" class="button button-primary"><?php esc_html_e('Cancel', 'W2MB'); ?></a>
<?php elseif ($action == 'processed'): ?>
<a href="<?php echo $referer; ?>" class="button button-primary"><?php esc_html_e('Go back ', 'W2MB'); ?></a>
<?php endif; ?>

<?php w2mb_renderTemplate('admin_footer.tpl.php'); ?>