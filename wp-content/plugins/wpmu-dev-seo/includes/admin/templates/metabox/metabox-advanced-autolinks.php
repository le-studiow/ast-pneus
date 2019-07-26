<?php
$autolinks_exclude = empty( $autolinks_exclude ) ? false : $autolinks_exclude;
?>

<div class="sui-box-settings-row">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label" for="wds_autolinks-exclude">
			<?php esc_html_e( 'Automatic Linking', 'wds' ); ?>
		</label>
		<p class="sui-description">
			<?php esc_html_e( 'You can prevent this particular post from being auto-linked', 'wds' ); ?>
		</p>
	</div>
	<div class="sui-box-settings-col-2">
		<?php
		$this->_render( 'toggle-item', array(
			'inverted'   => true,
			'field_name' => 'wds_autolinks-exclude',
			'checked'    => $autolinks_exclude ? 'checked="checked"' : '',
			'item_label' => esc_html__( 'Enable automatic linking for this post', 'wds' ),
		) );
		?>
	</div>
</div>
