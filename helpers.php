<?php
function responsive_image( $image, $maxwidth, $alt = null ){
    return \AutoResponsiveImage\AutoResponsiveImage::render($image, $maxwidth, $alt);
}