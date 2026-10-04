<?php
/** Security module: practical hardening that needs no plugin. Pair with updates, backups and 2FA. */
if(!defined('ABSPATH'))exit;

if(!defined('DISALLOW_FILE_EDIT'))define('DISALLOW_FILE_EDIT',true);          // no code editing from wp-admin
remove_action('wp_head','wp_generator');add_filter('the_generator','__return_empty_string');   // hide WP version
add_filter('xmlrpc_enabled','__return_false');                                  // XML-RPC is a brute-force favourite
add_filter('login_errors',function(){return 'Login details were not correct.';}); // do not reveal which part was wrong
add_filter('style_loader_src','sz_strip_ver',10);add_filter('script_loader_src','sz_strip_ver',10);
function sz_strip_ver($s){return strpos($s,'ver=')&&strpos($s,get_bloginfo('version'))!==false?remove_query_arg('ver',$s):$s;}

// Security headers on every front-end response.
add_action('send_headers',function(){
 header('X-Content-Type-Options: nosniff');header('X-Frame-Options: SAMEORIGIN');
 header('Referrer-Policy: strict-origin-when-cross-origin');header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
 header_remove('X-Powered-By');
});
// Block user enumeration (?author=1 and /wp-json/wp/v2/users) for visitors.
add_action('template_redirect',function(){if(is_author()||(isset($_GET['author'])&&!is_user_logged_in())){wp_safe_redirect(home_url('/'),301);exit;}});
add_filter('rest_endpoints',function($e){if(!is_user_logged_in()){unset($e['/wp/v2/users'],$e['/wp/v2/users/(?P<id>[\d]+)']);}return $e;});

// Simple login throttle: 5 failed attempts per IP per 15 minutes.
function sz_lk(){return 'sz_lf_'.md5($_SERVER['REMOTE_ADDR']??'');}
add_action('wp_login_failed',function(){set_transient(sz_lk(),(int)get_transient(sz_lk())+1,15*MINUTE_IN_SECONDS);});
add_filter('authenticate',function($u){return (int)get_transient(sz_lk())>=5?new WP_Error('sz_locked','Too many attempts. Please wait 15 minutes.'):$u;},30);
add_action('wp_login',function(){delete_transient(sz_lk());});
