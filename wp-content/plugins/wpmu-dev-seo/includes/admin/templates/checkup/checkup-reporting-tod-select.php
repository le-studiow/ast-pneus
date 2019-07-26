<?php
$is_member = empty( $_view['is_member'] ) ? false : true;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$disabled = $is_member ? '' : 'disabled';

$midnight = strtotime( 'today' );
$checkup_tod = isset( $_view['options']['checkup-tod'] ) ? $_view['options']['checkup-tod'] : false;
?>

<label for="wds-checkup-tod"
       class="sui-label"><?php esc_html_e( 'Time of day', 'wds' ); ?></label>

<select <?php echo esc_attr( $disabled ); ?>
		class="sui-select"
		id="wds-checkup-tod"
		data-minimum-results-for-search="-1"
		name="<?php echo esc_attr( $option_name ); ?>[checkup-tod]">

	<?php foreach ( range( 0, 23 ) as $tod ) : ?>
		<option value="<?php echo esc_attr( $tod ); ?>" <?php selected( $tod, $checkup_tod ); ?>>
			<?php echo esc_html( date_i18n( get_option( 'time_format' ), $midnight + ( $tod * HOUR_IN_SECONDS ) ) ); ?>
		</option>
	<?php endforeach; ?>
</select>
