<style type="text/css" id="<?php echo esc_attr( $id ); ?>">
.cb-slideshow,
.cb-slideshow li {
    margin: 0;
    padding: 0;
    line-height: 0;
}
.cb-slideshow,
.cb-slideshow:after {
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: -1;
}
.cb-slideshow:after {
    content: '';
}
.cb-slideshow li span {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    color: transparent;
    background-size: <?php
	if ( is_array( $background_size ) ) {
		echo $background_size[0];
		echo ' ';
		echo $background_size[1];
	} else {
		echo $background_size;
	}
?>;
    background-position: <?php echo $background_position_x; ?> <?php echo $background_position_y; ?>;
    background-repeat: no-repeat;
    opacity: 0;
    z-index: -1;
    -webkit-backface-visibility: hidden;
    -webkit-animation: imageAnimation <?php echo $duration * 2; ?>s linear infinite 0s;
    -moz-animation: imageAnimation <?php echo $duration * 2; ?>s linear infinite 0s;
    -o-animation: imageAnimation <?php echo $duration * 2; ?>s linear infinite 0s;
    -ms-animation: imageAnimation <?php echo $duration * 2; ?>s linear infinite 0s;
    animation: imageAnimation <?php echo $duration * 2; ?>s linear infinite 0s;
}
<?php
$i = 0;
foreach ( $images as $image ) {
?>
.cb-slideshow li:nth-child(<?php echo $i + 1; ?>) span {
    background-image: url(<?php echo $image; ?>);
<?php if ( 0 < $i ) { ?>
    animation-delay: <?php echo $duration * $i; ?>s;
<?php } ?>
}
<?php
	$i++;
}
?>
@keyframes imageAnimation {
    0% { opacity: 0; animation-timing-function: ease-in; }
    100% { opacity: 1; animation-timing-function: ease-out; }
    20% { opacity: 1 }
    80% { opacity: 0 }
    100% { opacity: 0 }
}
</style>
