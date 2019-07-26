<?php
$label = empty( $label ) ? '' : $label;
$description = empty( $description ) ? '' : $description;
$items = empty( $items ) ? array() : $items;
$view_option_name = empty( $_view['option_name'] ) ? '' : $_view['option_name'];
?>

<div class="sui-box-settings-row <?php echo isset( $separator ) && $separator ? '' : 'wds-no-separator'; ?>">
	<div class="sui-box-settings-col-1">
		<label class="sui-settings-label"><?php echo esc_html( $label ); ?></label>
		<p class="sui-description">
			<?php echo esc_html( $description ); ?>
		</p>
	</div>

	<div class="sui-box-settings-col-2">
		<?php foreach ( $items as $item => $details ) : ?>
			<?php
			$option_name = $view_option_name;
			$field_id = $option_name . '-' . $item;
			$field_name = $option_name . "[$item]";
			$checked = ( ! empty( $_view['options'][ $item ] ) ) ? "checked='checked' " : '';

			if ( is_array( $details ) ) {
				$details = wp_parse_args(
					$details,
					array(
						'value'            => '',
						'label'            => '',
						'description'      => '',
						'html_description' => '',
						'inverted'         => false,
					)
				);

				$item_label = $details['label'];
				$item_description = $details['description'];
				$item_value = $details['value'] ? $details['value'] : $item;
				$inverted = $details['inverted'];
				$html_description = $details['html_description'];
			} else {
				$item_label = $details;
				$item_description = '';
				$item_value = $item;
				$inverted = false;
				$html_description = '';
			}
			?>

			<?php
			$this->_render( 'toggle-item', array(
				'inverted'         => $inverted,
				'item_value'       => $item_value,
				'field_name'       => $field_name,
				'field_id'         => $field_id,
				'checked'          => $checked,
				'item_label'       => $item_label,
				'item_description' => $item_description,
				'html_description' => $html_description,
			) );
			?>

		<?php endforeach; ?>
	</div>
</div>
