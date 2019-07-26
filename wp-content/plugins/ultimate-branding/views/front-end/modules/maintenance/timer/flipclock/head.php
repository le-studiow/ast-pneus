<?php
echo $this->enqueue( 'jquery/jquery.js', false, true );
echo $this->enqueue( 'vendor/flipclock/flipclock.min.js', '2018-04-12' );
echo $this->enqueue( 'vendor/flipclock/flipclock.css', '2018-04-12' );
?>
<script type="text/javascript">
var clock;
jQuery(document).ready(function($) {
    // Instantiate a coutdown FlipClock
    clock = $('.clock').FlipClock( <?php echo $distance; ?>, {
    clockFace: 'DailyCounter',
        countdown: true,
        language: '<?php echo $language_code; ?>',
        callbacks: {
        stop: function() {
            window.location.reload();
            }
        }
    });
});
</script>
