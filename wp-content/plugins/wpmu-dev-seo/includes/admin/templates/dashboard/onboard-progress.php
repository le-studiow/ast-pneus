<p><?php esc_html_e( 'Please wait a few moments while we activate those services', 'wds' ); ?></p>
<?php
$this->_render( 'progress-bar', array(
	'progress' => 0,
) );
?>
