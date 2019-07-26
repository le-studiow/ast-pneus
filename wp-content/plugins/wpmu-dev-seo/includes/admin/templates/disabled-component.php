<?php
$content = empty( $content ) ? '' : $content;
$image = empty( $image ) ? '' : $image;
$component = empty( $component ) ? '' : $component;
$button_text = empty( $button_text ) ? '' : $button_text;
?>
<form method='post'>
	<div class="sui-box">
		<div class="sui-box-header">
			<h2 class="sui-box-title"><?php esc_html_e( 'Get Started', 'wds' ); ?></h2>
		</div>
		<div class="sui-box-body">
			<?php
			$this->_render( 'disabled-component-inner', array(
				'content'     => $content,
				'image'       => $image,
				'component'   => $component,
				'button_text' => $button_text,
			) );
			?>
		</div>
	</div>
</form>
