<?php
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$crawl_dow = isset( $_view['options']['crawler-dow'] ) ? $_view['options']['crawler-dow'] : false;
$is_member = empty( $_view['is_member'] ) ? false : true;
$disabled = $is_member ? '' : 'disabled';
$monday = strtotime( 'this Monday' );
?>
<label for="wds-crawler-dow"
       class="sui-label"><?php esc_html_e( 'Day of the week', 'wds' ); ?></label>

<select class="sui-select" <?php echo esc_attr( $disabled ); ?>
        id="wds-crawler-dow"
        data-minimum-results-for-search="-1"
        name="<?php echo esc_attr( $option_name ); ?>[crawler-dow]">

	<?php foreach ( range( 0, 6 ) as $dow ) : ?>
		<option value="<?php echo esc_attr( $dow ); ?>"
			<?php selected( $dow, $crawl_dow ); ?>>
			<?php echo esc_html( date_i18n( 'l', $monday + ( $dow * DAY_IN_SECONDS ) ) ); ?>
		</option>
	<?php endforeach; ?>
</select>
