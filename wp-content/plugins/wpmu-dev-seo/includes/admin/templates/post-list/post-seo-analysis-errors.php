<?php
$errors = empty( $errors ) ? array() : $errors;
$status_class = empty( $status_class ) ? 'wds-status-warning' : $status_class;
$percentage = ! empty( $percentage )
	? intval( $percentage ) . '%'
	: esc_html__( 'Errors:', 'wds' );
?>

<div class="wds-analysis <?php echo esc_attr( $status_class ); ?>" title="<?php esc_attr( $percentage ); ?>">
	<span><?php echo esc_html( $percentage ); ?></span>
</div>
<div class="wds-analysis-details">
	<?php foreach ( $errors as $key => $error ): ?>
		<div class="wds-error <?php echo esc_attr( $key ); ?>">
			<?php echo esc_html( $error ); ?>
		</div>
	<?php endforeach; ?>
</div>
