<?php
// Required
$tab_id = empty( $tab_id ) ? '' : $tab_id;
$tab_name = empty( $tab_name ) ? '' : $tab_name;
$is_active = empty( $is_active ) ? false : $is_active;
$tab_sections = ! empty( $tab_sections ) && is_array( $tab_sections ) ? $tab_sections : array();
$title_actions_left = empty( $title_actions_left ) ? false : $title_actions_left;
$is_member = empty( $_view['is_member'] ) ? false : true;

// Optional
$button_text = isset( $button_text ) ? $button_text : esc_html__( 'Save Settings', 'wds' );

?>
<div class="wds-vertical-tab-section sui-box <?php echo esc_attr( $tab_id ); ?> <?php echo $is_active ? '' : 'hidden'; ?>"
     id="<?php echo esc_attr( $tab_id ); ?>">

	<div class="sui-box-header">
		<h2 class="sui-box-title">
			<?php echo esc_html( $tab_name ); ?>
		</h2>

		<?php if ( $title_actions_left ): ?>
			<div class="sui-actions-left">
				<?php $this->_render( $title_actions_left ); ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="sui-box-body wds-upsell-items">
		<?php foreach ( $tab_sections as $section ) : ?>
			<?php $this->_render( 'vertical-tab-section', $section ); ?>
		<?php endforeach; ?>
	</div>

	<?php if ( $button_text ): ?>
		<div class="sui-box-footer">
			<button name="submit"
			        type="submit"
			        class="sui-button sui-button-blue">
				<i class="sui-icon-save" aria-hidden="true"></i>

				<?php echo esc_html( $button_text ); ?>
			</button>
		</div>
	<?php endif; ?>
</div>
