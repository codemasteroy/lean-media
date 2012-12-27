<?php
/*
Plugin Name: Lean Media 
Plugin URI: http://codemaster.fi/wordpress/plugins/lean-media/
Description: Delete large image files
Version: 3.5
Author: S H Mohanjith (Code Master Oy)
Author URI: http://codemaster.fi/
*/

add_action('init', 'lean_media_init');
add_action('admin_init', 'lean_media_admin_init');

function lean_media_init() {
    load_plugin_textdomain('lean-media', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    
    if (get_option('lean_media_settings-delete_large_files', 1) == 1) {
        add_filter('wp_generate_attachment_metadata', 'lean_media_delete_fullsize_image');
    }
}

function lean_media_admin_init() {
    add_settings_field('lean_media_settings-delete_large_files', __('Delete Large Images', 'lean-media'), 'lean_media_setting_delete_large_files_callback', 'media');
    register_setting('media', 'lean_media_settings-delete_large_files', 'intval');
}

function lean_media_delete_fullsize_image($metadata) {
    
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
    
    if ($metadata['width'] > get_option('large_size_w') || $metadata['height'] > get_option('large_size_h')) {
        $deleted = @unlink( $full_image_path );
        $copied = @copy($full_large_thumb_path, $full_image_path);
        
        $metadata['width'] = $width;
        $metadata['height'] = $height;
    }
    
    return $metadata;
}

function lean_media_setting_delete_large_files_callback() {
    ?>
    <input id="lean_media_settings-delete_large_files" name="lean_media_settings-delete_large_files" type="checkbox" value="1" <?php echo (get_option('lean_media_settings-delete_large_files', 1) == 1)?'checked="checked"':''; ?> />
    <?php
}
