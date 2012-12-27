<?php
/*
Plugin Name: Lean Media 
Plugin URI: http://codemaster.fi/wordpress/plugins/lean-media/
Description: Delete large image files
Version: 3.5
Author: S H Mohanjith (Code Master Oy)
Author URI: http://codemaster.fi/
*/

add_filter('wp_generate_attachment_metadata', 'delete_fullsize_image');

function delete_fullsize_image($metadata) {
    
    $upload_dir = wp_upload_dir();
    $full_image_path = trailingslashit( $upload_dir['basedir'] ) . $metadata['file'];
    
    $width = 0;
    $height = 0;
    
    foreach ($metadata['sizes'] as $thumbnail) {
        if ($thumbnail['width'] > $width) {
            $largest_thumbnail = $thumbnail['file'];
            $width = $thumbnail['width'];
            $height = $thumbnail['height'];
        } else if ($thumbnail['height'] > $height) {
            $largest_thumbnail = $thumbnail['file'];
            $width = $thumbnail['width'];
            $height = $thumbnail['height'];
        }
    }
    
    $full_large_thumb_path = trailingslashit( $upload_dir['path'] ) . $largest_thumbnail;
    
    if ($metadata['width'] > $width || $metadata['height'] > $height) {
        $deleted = @unlink( $full_image_path );
        $copied = @copy($full_large_thumb_path, $full_image_path);
    }
    
    return $metadata;
}
