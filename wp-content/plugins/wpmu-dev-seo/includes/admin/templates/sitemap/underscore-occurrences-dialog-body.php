<div data-issue-id="{{- issue_id }}">
	<p>
		<?php
		printf(
			esc_html__( 'We found links to %s in these locations, you might want to remove these links or direct them somewhere else.', 'wds' ),
			'<strong>{{- issue_path }}</strong>'
		);
		?>
	</p>
	<ul>
		<?php if ( ! empty( $issue['origin'] ) ) : ?>
			<?php foreach ( $issue['origin'] as $origin ) : ?>
				<li>
					<?php $origin = is_array( $origin ) && ! empty( $origin[0] ) ? $origin[0] : $origin; ?>

					<a href="<?php echo is_string( $origin ) ? esc_url( $origin ) : esc_url( $origin[0] ); ?>">
						<?php echo is_string( $origin ) ? esc_html( $origin ) : esc_html( $origin[0] ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>
	</ul>
	<div class="wds-issue-occurrences">
		{{ if(issue_occurrences){ }}
			{{- issue_occurrences }}
		{{ } else { }}
			<span class="wds-issue-occurrences-loading"></span>
		{{ } }}
	</div>

	<button data-a11y-dialog-hide=""
	        type="button"
	        aria-label="<?php esc_attr_e( 'Close this dialog window', 'wds' ); ?>" class="sui-button sui-button-ghost">
		<?php esc_html_e( 'Close', 'wds' ); ?>
	</button>
</div>
