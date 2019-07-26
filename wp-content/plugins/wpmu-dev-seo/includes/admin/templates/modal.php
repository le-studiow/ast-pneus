<?php
$id = empty( $id ) ? '' : $id;
$title = empty( $title ) ? '' : $title;
$description = empty( $description ) ? '' : $description;
$header_actions_template = empty( $header_actions_template ) ? '' : $header_actions_template;
$body_template = empty( $body_template ) ? '' : $body_template;
$body_template_args = empty( $body_template_args ) ? array() : $body_template_args;
$footer_template = empty( $footer_template ) ? '' : $footer_template;
$footer_template_args = empty( $footer_template_args ) ? array() : $footer_template_args;
$small = empty( $small ) ? false : $small;
$is_member = empty( $_view['is_member'] ) ? false : true;
?>

<div class="sui-dialog <?php echo esc_attr( $id ); ?>-dialog <?php echo $small ? 'sui-dialog-sm' : ''; ?> <?php echo $is_member ? 'is-member' : ''; ?>"
     aria-hidden="true" tabindex="-1"
     id="<?php echo esc_attr( $id ); ?>">

	<div class="sui-dialog-overlay" data-a11y-dialog-hide></div>

	<div class="sui-dialog-content" aria-labelledby="<?php echo esc_attr( $id ); ?>-dialog-title"
	     aria-describedby="<?php echo esc_attr( $id ); ?>-dialog-description" role="dialog">

		<div class="sui-box" role="document">

			<div class="sui-box-header">
				<h3 class="sui-box-title"
				    id="<?php echo esc_attr( $id ); ?>-dialog-title"><?php echo esc_html( $title ); ?></h3>
				<div class="sui-actions-right">
					<?php if ( $header_actions_template ): ?>
						<?php $this->_render( $header_actions_template ); ?>
					<?php else: ?>
						<button data-a11y-dialog-hide class="sui-dialog-close"
						        type="button"
						        aria-label="<?php esc_html_e( 'Close this dialog window', 'wds' ); ?>"></button>
					<?php endif; ?>
				</div>
			</div>

			<div class="sui-box-body">
				<?php if ( $description ): ?>
					<p id="<?php echo esc_attr( $id ); ?>-dialog-description"><?php echo wp_kses_post( $description ); ?></p>
				<?php endif; ?>
				<?php if ( $body_template ): ?>
					<?php $this->_render(
						$body_template,
						array_merge(
							array( 'id' => $id ),
							$body_template_args
						)
					); ?>
				<?php endif; ?>
			</div>

			<?php if ( $footer_template ): ?>
				<div class="sui-box-footer">
					<?php $this->_render(
						$footer_template,
						array_merge(
							array( 'id' => $id ),
							$footer_template_args
						)
					); ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

</div>
