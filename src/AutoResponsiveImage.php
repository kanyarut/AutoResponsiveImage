<?php 
namespace AutoResponsiveImage;

class AutoResponsiveImage
{
    public static $config = array(
        "quality" => 100,
        "sizes" => array(100, 60, 30),
        "target_folder" => "auto-resizes",
    );
    // Return array of images with sizes 
    public static function resize( $image, $maxwidth )
    {
        $result = array();
        $public_root = $_SERVER['DOCUMENT_ROOT'];
        $target_folder = \AutoResponsiveImage\AutoResponsiveImage::$config['target_folder'];
        $sizes = \AutoResponsiveImage\AutoResponsiveImage::$config['sizes'];
        $img_path = $public_root.'/'.$image;
        $image_name = basename($image);
        $new_folder_path = '/'.$target_folder.'/'.md5($image);
        if (!file_exists($img_path)) {
            return null;
        }

        $size_array = array();
        // create size for 2x image
        // var_dump($sizes);die();

        $size_array[] = ($maxwidth*2);
        foreach( $sizes as $size ) 
        {
            $size_array[] = ($maxwidth*$size)/100;
        }
        
        // resized files already exist
        if( is_dir($public_root.$new_folder_path) && (filemtime($img_path) <  filemtime($public_root.$new_folder_path) ) ) 
        {
            foreach( $size_array as $size)
            {
                $new_folder = $new_folder_path.'/'.$size;
                $result[] = array('size' => $size, 'file' => $new_folder.'/'.$image_name);
            }
        } else {
            $imagick = new \Imagick(realpath($img_path));
            $img_width = $imagick->getImageWidth();
            foreach( $size_array as $size)
            {
                if( is_dir($public_root.$new_folder_path))touch($public_root.$new_folder_path);
                $new_folder = $new_folder_path.'/'.$size;
                $abs_file = $public_root.$new_folder.'/'.$image_name;
                //var_dump($public_root.$new_folder);die();
                if( !is_dir($public_root.$new_folder) ) {
                    //var_dump($public_root.$new_folder);die();
                    mkdir($public_root.$new_folder,0755,true);
                } 

                $imagick->resizeImage($size, 0,\Imagick::FILTER_LANCZOS, 1);
                $imagick->writeImage( $abs_file );
                $result[] = array('size' => $size, 'file' => $new_folder.'/'.$image_name);
            }  
        }
        return $result;
    }

    // Return HTML code to display responsive image 
    public static function render( $image, $maxwidth, $alt = null )
    {
        $images = AutoResponsiveImage::resize($image, $maxwidth);
        //var_dump($images);die();
        $result = "<img ";
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
        return $result;
    }
}
