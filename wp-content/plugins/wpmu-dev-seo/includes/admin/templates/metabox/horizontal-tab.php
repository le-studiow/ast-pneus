<?php
$tab_id = empty( $tab_id ) ? '' : $tab_id;
$is_active = empty( $is_active ) ? false : $is_active;
$content_template = empty( $content_template ) ? '' : $content_template;
$content_args = empty( $content_args ) ? array() : $content_args;
?>

<div class="<?php echo esc_attr( $tab_id ); ?> <?php echo $is_active ? 'active' : ''; ?>">
	<?php $this->_render( $content_template, $content_args ); ?>
</div>
