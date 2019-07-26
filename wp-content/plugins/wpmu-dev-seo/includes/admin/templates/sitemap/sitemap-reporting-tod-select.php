<?php
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$crawl_tod = isset( $_view['options']['crawler-tod'] ) ? $_view['options']['crawler-tod'] : false;
$is_member = empty( $_view['is_member'] ) ? false : true;
$disabled = $is_member ? '' : 'disabled';
$midnight = strtotime( 'today' );
?>

<label for="wds-crawler-tod" class="sui-label">
	<?php esc_html_e( 'Time of day', 'wds' ); ?>
</label>

<select class="sui-select" <?php echo esc_attr( $disabled ); ?>
        id="wds-crawler-tod"
        data-minimum-results-for-search="-1"
        name="<?php echo esc_attr( $option_name ); ?>[crawler-tod]">

	<?php foreach ( range( 0, 23 ) as $tod ) : ?>
		<option value="<?php echo esc_attr( $tod ); ?>"
			<?php selected( $tod, $crawl_tod ); ?>>
			<?php echo esc_html( date_i18n( get_option( 'time_format' ), $midnight + ( $tod * HOUR_IN_SECONDS ) ) ); ?>
		</option>
	<?php endforeach; ?>
</select>
