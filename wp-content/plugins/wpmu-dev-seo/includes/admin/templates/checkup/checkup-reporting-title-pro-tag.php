<?php
$is_member = empty( $_view['is_member'] ) ? false : true;
if ( $is_member ) {
	return;
}
$upgrade_url = 'https://premium.wpmudev.org/project/smartcrawl-wordpress-seo/?utm_source=smartcrawl&utm_medium=plugin&utm_campaign=smartcrawl_seocheckup_reporting_pro_tag';
?>
<a target="_blank" href="<?php echo esc_attr( $upgrade_url ); ?>">
    <span class="sui-tag sui-tag-pro sui-tooltip"
          data-tooltip="<?php esc_attr_e( 'Upgrade to Pro to schedule automated checkups and email reports', 'wds' ); ?>">
        <?php esc_html_e( 'Pro', 'wds' ); ?>
    </span>
</a>
