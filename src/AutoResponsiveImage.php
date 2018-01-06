<?php 
namespace AutoResponsiveImage;

class AutoResponsiveImage
{
    private $config = array(
        "quality": 100,
        "sizes": array(100, 60, 30),
        "public_root": "",
        "target_folder": "auto-resizes",
    );
    // Return array of images with sizes 
    public static function resize( $image )
    {
        return __FILE__;
        /*
        $source = imagecreatefromjpeg($image);
        list($width, $height) = getimagesize($filename);
        */
    }

    // Return HTML code to display responsive image 
    public static function render( $image, $alt = null )
    {
        $result = "<img ";

        $images = AutoResponsiveImage::resize( $image );
        if( is_array($images) && count($images)>0 )
        {
            $result_images = array();
            foreach( $images as $img )
            {
                $result_images[] = $img['file']." ".$img['size']."w";
            }
            
            $result .= "srcset=\"".implode(",",$result_images)."\" ";
            $result .= "src=\"".$images[0]['file']."\" ";
        }
        if( $alt )
        {
            $result .= "alt=\"".$alt."\" ";
        }
        $result .= "/>";
    }
}
