<?php
$tabs = empty( $tabs ) || ! is_array( $tabs ) ? array() : $tabs;
$active_tab = empty( $active_tab ) ? '' : $active_tab;
?>
<div class="wds-vertical-tabs sui-sidenav">
	<ul class="sui-vertical-tabs">
		<?php foreach ( $tabs as $tab ): ?>
			<?php
			$tab_id = smartcrawl_get_array_value( $tab, 'id' );
			$tab_name = smartcrawl_get_array_value( $tab, 'name' );
			$spinner = smartcrawl_get_array_value( $tab, 'spinner' );
			$tag_value = smartcrawl_get_array_value( $tab, 'tag_value' );
			$tag_class = smartcrawl_get_array_value( $tab, 'tag_class' );
			$tick = smartcrawl_get_array_value( $tab, 'tick' );
			$tick_class = smartcrawl_get_array_value( $tab, 'tick_class' );
			$tick_class = empty( $tick_class ) ? 'sui-info' : $tick_class;
			?>
			<li class="sui-vertical-tab <?php echo esc_attr( $tab_id ); ?> <?php echo $active_tab === $tab_id ? esc_attr( 'current' ) : ''; ?>">

				<a role="button" data-target="<?php echo esc_attr( $tab_id ); ?>" href="#">
					<?php echo esc_html( $tab_name ); ?>
				</a>

				<i class="sui-icon-loader sui-loading"
				   aria-hidden="true"
				   style="<?php echo $spinner ? '' : 'display:none;'; ?>"></i>

				<span class="sui-tag <?php echo esc_attr( $tag_class ); ?>"
				      style="<?php echo $tag_value ? '' : 'display:none;'; ?>">
					<?php echo esc_html( $tag_value ); ?>
				</span>

				<i class="sui-icon-check-tick <?php echo esc_attr( $tick_class ); ?>"
				   aria-hidden="true"
				   style="<?php echo $tick ? '' : 'display:none;'; ?>"></i>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
