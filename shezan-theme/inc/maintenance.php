<?php
/** Client-site maintenance module: health dashboard, maintenance mode, backup reminder. */
if(!defined('ABSPATH'))exit;

/** Each check: [label, passed?, what to do]. */
function sz_checks(){
 $u=wp_get_update_data()['counts'];$days=get_option('sz_last_backup')?floor((time()-(int)get_option('sz_last_backup'))/DAY_IN_SECONDS):null;
 return[
  ['WordPress core is up to date',empty($u['wordpress']),'Update core after a backup.'],
  ['Plugins up to date ('.(int)$u['plugins'].' pending)',!$u['plugins'],'Update plugins, then test forms and the home page.'],
  ['Themes up to date ('.(int)$u['themes'].' pending)',!$u['themes'],'Update, or delete unused themes.'],
  ['PHP 8.1 or newer ('.PHP_VERSION.')',version_compare(PHP_VERSION,'8.1','>='),'Ask the host to raise the PHP version.'],
  ['HTTPS active',is_ssl(),'Install an SSL certificate and force HTTPS.'],
  ['Debug mode is off',!(defined('WP_DEBUG')&&WP_DEBUG),'Turn WP_DEBUG off on live sites.'],
  ['Admin file editor disabled',defined('DISALLOW_FILE_EDIT')&&DISALLOW_FILE_EDIT,'Set DISALLOW_FILE_EDIT in wp-config.php.'],
  ['Search engines allowed',(bool)get_option('blog_public'),'Settings > Reading > untick "Discourage search engines" before launch.'],
  ['Pretty permalinks on',(bool)get_option('permalink_structure'),'Settings > Permalinks > Post name.'],
  ['Backup in the last 7 days'.($days===null?' (never recorded)':" ($days days ago)"),$days!==null&&$days<=7,'Take a full backup, then press the button below.'],
 ];
}
add_action('admin_menu',function(){add_management_page('Shezan Care','Shezan Care','manage_options','sz-care','sz_care_page');});

function sz_care_page(){
 if(!current_user_can('manage_options'))return;$on=get_option('sz_maint');$ch=sz_checks();$ok=count(array_filter($ch,function($c){return $c[1];}));?>
<div class="wrap"><h1>Shezan Care: site health &amp; maintenance</h1>
 <p><strong><?php echo $ok;?> of <?php echo count($ch);?> checks passing.</strong></p>
 <table class="widefat striped" style="max-width:820px"><tbody><?php foreach($ch as $c)printf('<tr><td style="width:28px">%s</td><td>%s</td><td>%s</td></tr>',$c[1]?'<span style="color:#1a7f37">&#10003;</span>':'<span style="color:#b32d2e">&#10007;</span>',esc_html($c[0]),$c[1]?'':esc_html($c[2]));?></tbody></table>
 <h2>Actions</h2>
 <form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>" style="display:inline-block;margin-right:12px"><input type="hidden" name="action" value="sz_backup_done"><?php wp_nonce_field('sz_care');?><?php submit_button('I took a backup today','secondary','',false);?></form>
 <form method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>" style="display:inline-block"><input type="hidden" name="action" value="sz_toggle_maint"><?php wp_nonce_field('sz_care');?><?php submit_button($on?'Turn maintenance mode OFF':'Turn maintenance mode ON','primary','',false);?></form>
 <p class="description">Maintenance mode shows visitors a branded 503 "back soon" page (search engines keep your rankings). Logged-in admins still see the real site.</p>
 <h2>Monthly routine</h2><ol><li>Full backup (files + database), stored off the server.</li><li>Update core, themes, plugins; check the home page, product pages and the enquiry form.</li><li>Delete unused plugins/themes and spam enquiries.</li><li>Run PageSpeed Insights on home + one product page.</li><li>Check Search Console for errors and uptime alerts.</li></ol></div><?php
}
function sz_care_guard(){if(!current_user_can('manage_options')||!isset($_POST['_wpnonce'])||!wp_verify_nonce($_POST['_wpnonce'],'sz_care'))wp_die('Not allowed',403);}
add_action('admin_post_sz_backup_done',function(){sz_care_guard();update_option('sz_last_backup',time());wp_safe_redirect(admin_url('tools.php?page=sz-care'));exit;});
add_action('admin_post_sz_toggle_maint',function(){sz_care_guard();update_option('sz_maint',get_option('sz_maint')?0:1);wp_safe_redirect(admin_url('tools.php?page=sz-care'));exit;});

// Dashboard widget so the client sees status on login.
add_action('wp_dashboard_setup',function(){if(!current_user_can('manage_options'))return;
 wp_add_dashboard_widget('sz_care','Shezan Care',function(){$ch=sz_checks();$bad=array_filter($ch,function($c){return !$c[1];});
  echo '<p><strong>'.(count($ch)-count($bad)).' / '.count($ch).'</strong> health checks passing.</p>';
  foreach(array_slice($bad,0,3) as $c)echo '<p style="color:#b32d2e">&#10007; '.esc_html($c[0]).'</p>';
  echo '<p><a href="'.esc_url(admin_url('tools.php?page=sz-care')).'">Open Shezan Care</a></p>';});});

// Maintenance mode: 503 + Retry-After keeps SEO safe. Admins bypass.
add_action('template_redirect',function(){
 if(!get_option('sz_maint')||current_user_can('manage_options'))return;
 status_header(503);header('Retry-After: 3600');nocache_headers();
 wp_die('<h1 style="font-family:Georgia,serif">Shezan is getting a little fresher.</h1><p>We are doing some quick maintenance and will be back shortly.</p>','Back soon',['response'=>503]);
});
