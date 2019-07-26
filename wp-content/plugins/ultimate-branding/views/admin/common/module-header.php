<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-tab="<?php echo esc_attr( sanitize_title( $slug ) ); ?>"<?php echo $current === $slug ? '':' style="display: none;"'; ?>>
    <div class="sui-box-header">
        <h2 class="sui-box-title"><?php echo esc_html( $box_title ); ?></h2>
<?php if ( $is_active && 'show' === $status_indicator ) { ?>
        <div class="sui-box-status">
            <div class="sui-status">
                <div class="sui-status-changes sui-hidden branda-status-changes-unsaved">
                    <i class="sui-icon-update" aria-hidden="true"></i>
                    <?php esc_html_e( 'Unsaved changes', 'ub' ); ?>
                </div>
                <div class="sui-status-changes branda-status-changes-saved">
                    <i class="sui-icon-check-tick" aria-hidden="true"></i>
                    <?php esc_html_e( 'Saved', 'ub' ); ?>
                </div>
            </div>
        </div>
<?php } ?>
<?php echo apply_filters( 'branda_settings_after_box_title', '', $module ); ?>
<?php if ( $is_active ) { ?>
<?php echo $copy_button; ?>
        <div class="sui-actions-right"><?php echo $buttons; ?></div>
<?php } ?>
<?php echo apply_filters( 'branda_settings_after_box_title_after_actions', '', $module ); ?>
    </div>
<?php if ( ! $is_active ) { ?>
    <div class="sui-box-body">
        <p><?php echo $module['description']; ?></p>
        <?php echo $buttons ?>
    </div>
<?php } ?>
</div>
