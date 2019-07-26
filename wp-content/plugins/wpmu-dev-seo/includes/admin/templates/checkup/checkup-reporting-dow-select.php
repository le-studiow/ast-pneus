<?php
$is_member = empty( $_view['is_member'] ) ? false : true;
$option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
$disabled = $is_member ? '' : 'disabled';

$monday = strtotime( 'this Monday' );
$checkup_dow = isset( $_view['options']['checkup-dow'] ) ? $_view['options']['checkup-dow'] : false;
?>

<label for="wds-checkup-dow"
       class="sui-label"><?php esc_html_e( 'Day of the week', 'wds' ); ?></label>

<select <?php echo esc_attr( $disabled ); ?>
		class="sui-select"
		id="wds-checkup-dow"
		data-minimum-results-for-search="-1"
		name="<?php echo esc_attr( $option_name ); ?>[checkup-dow]">

	<?php foreach ( range( 0, 6 ) as $dow ) : ?>
		<option value="<?php echo esc_attr( $dow ); ?>" <?php selected( $dow, $checkup_dow ); ?>>
			<?php echo esc_html( date_i18n( 'l', $monday + ( $dow * DAY_IN_SECONDS ) ) ); ?>
		</option>
	<?php endforeach; ?>
</select>
