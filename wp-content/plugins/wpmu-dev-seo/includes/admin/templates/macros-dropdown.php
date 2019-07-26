<?php
$macros = Smartcrawl_Onpage_Settings::get_macros();
?>

<select>
	<?php foreach ( $macros as $macro => $label ): ?>

		<option value="<?php echo esc_attr( $macro ); ?>"
		        data-content="<?php echo esc_attr( $macro ); ?>">
			<?php echo esc_html( $label ); ?>
		</option>
	<?php endforeach; ?>
</select>
