<?php
echo $this->enqueue( 'jquery/jquery.js', false, true );
echo $this->enqueue( 'vendor/jquery-final-countdown/js/kinetic.js', '5.1.0' );
echo $this->enqueue( 'vendor/jquery-final-countdown/js/jquery.final-countdown.min.js' );
?>
<script type="text/javascript" id="<?php echo esc_attr( $id ); ?>">
jQuery( document ).ready( function( $ ) {
    $( '.countdown' ).final_countdown( {
        'end': <?php echo $distance_raw; ?>,
        'now': <?php echo time(); ?>
    }, function() {
        window.location.reload();
    });
});
</script>
