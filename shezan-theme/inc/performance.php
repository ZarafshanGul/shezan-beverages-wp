<?php
/** Speed module: remove what visitors never need, load the rest smartly. */
if(!defined('ABSPATH'))exit;

// 1. Stop shipping emoji + oEmbed scripts and head clutter.
add_action('init',function(){
 remove_action('wp_head','print_emoji_detection_script',7);remove_action('wp_print_styles','print_emoji_styles');
 remove_action('admin_print_scripts','print_emoji_detection_script');remove_action('admin_print_styles','print_emoji_styles');
 remove_action('wp_head','wp_oembed_add_discovery_links');remove_action('wp_head','wp_oembed_add_host_js');
 remove_action('wp_head','rsd_link');remove_action('wp_head','wlwmanifest_link');remove_action('wp_head','wp_shortlink_wp_head');
 remove_action('wp_head','feed_links_extra',3);
});
// 2. Drop block-editor CSS: this theme does not use it on the front end (saves a render-blocking request).
add_action('wp_enqueue_scripts',function(){
 foreach(['wp-block-library','wp-block-library-theme','classic-theme-styles','global-styles'] as $h)wp_dequeue_style($h);
 if(!is_user_logged_in())wp_deregister_style('dashicons');
},100);
// 3. Faster font connection (preconnect) + fewer DB rows (limit post revisions).
add_filter('wp_resource_hints',function($u,$t){
 if($t==='preconnect'){$u[]='https://fonts.gstatic.com';$u[]=['href'=>'https://cdnjs.cloudflare.com','crossorigin'=>'anonymous'];}return $u;},10,2);
add_filter('wp_revisions_to_keep',function(){return 5;});
// 4. No Heartbeat polling for visitors.
add_action('init',function(){if(!is_admin())wp_deregister_script('heartbeat');});
// 5. Hint the browser: first product image is the likely largest paint, give it priority.
add_filter('wp_get_attachment_image_attributes',function($a){if(is_front_page())$a['decoding']='async';return $a;});
