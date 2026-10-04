<?php
/** Enquiry form without a plugin: nonce + honeypot + time-trap + rate limit, sanitised input,
 *  saved as a private "Enquiries" post type (so nothing is lost if mail fails) and emailed to the admin. */
if(!defined('ABSPATH'))exit;

add_action('init',function(){
 register_post_type('sz_enquiry',['label'=>'Enquiries','labels'=>['singular_name'=>'Enquiry'],'public'=>false,'show_ui'=>true,'menu_icon'=>'dashicons-email-alt','supports'=>['title','editor'],
  'capability_type'=>'post','capabilities'=>['create_posts'=>'do_not_allow'],'map_meta_cap'=>true]);
});
add_filter('manage_sz_enquiry_posts_columns',function($c){return['cb'=>$c['cb'],'title'=>'Name / type','sz_email'=>'Email','sz_prod'=>'Product','date'=>'Received'];});
add_action('manage_sz_enquiry_posts_custom_column',function($col,$id){
 if($col==='sz_email'){$e=get_post_meta($id,'email',true);echo '<a href="mailto:'.esc_attr($e).'">'.esc_html($e).'</a>';}
 if($col==='sz_prod')echo esc_html(get_post_meta($id,'product',true)?:'Any');
},10,2);

function sz_types(){return['Retail / shop','Wholesale','Distributor','Caterer / cafe','Something else'];}

/** Render the form. Used in the footer on every page. */
function sz_enquiry_form(){
 $msgs=['ok'=>['Thank you! Your enquiry is in and we will reply soon.','ok'],'err'=>['Please check the highlighted fields and try again.','bad'],'slow'=>['That was a little quick. Please try once more.','bad'],'rate'=>['Too many enquiries from this connection. Please try again later.','bad'],'spam'=>['Sorry, that could not be sent.','bad']];
 $s=isset($_GET['sz'])?sanitize_key($_GET['sz']):'';$pre=is_singular('sz_product')?get_the_title():'';
 ob_start();?>
<form class="eq" method="post" action="<?php echo esc_url(admin_url('admin-post.php'));?>" novalidate>
 <?php if(isset($msgs[$s])):?><p class="eq-note <?php echo esc_attr($msgs[$s][1]);?>" role="status"><?php echo esc_html($msgs[$s][0]);?></p><?php endif;?>
 <input type="hidden" name="action" value="sz_enquiry"><?php wp_nonce_field('sz_enquiry');?><input type="hidden" name="t" value="<?php echo time();?>">
 <p class="hp" aria-hidden="true"><label>Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
 <div class="eq-row"><label>Name<input name="name" required autocomplete="name"></label><label>Email<input type="email" name="email" required autocomplete="email"></label></div>
 <div class="eq-row"><label>Phone (optional)<input type="tel" name="phone" autocomplete="tel"></label><label>Company (optional)<input name="company" autocomplete="organization"></label></div>
 <div class="eq-row"><label>I am a<select name="type"><?php foreach(sz_types() as $t)echo '<option>'.esc_html($t).'</option>';?></select></label>
 <label>Juice<select name="product"><option value="">Any</option><?php foreach(get_posts(['post_type'=>'sz_product','numberposts'=>-1,'orderby'=>'menu_order']) as $p)printf('<option%s>%s</option>',selected($pre,$p->post_title,false),esc_html($p->post_title));?><option>Both</option></select></label></div>
 <label>Message<textarea name="message" rows="4" required></textarea></label>
 <button class="btn" type="submit"><span class="bg"></span><span class="lbl">Send enquiry</span></button>
</form><?php return ob_get_clean();
}

function sz_enquiry_back($status){
 $to=wp_get_referer()?:home_url('/');wp_safe_redirect(add_query_arg('sz',$status,remove_query_arg('sz',$to)).'#contact');exit;
}
function sz_handle_enquiry(){
 if(!isset($_POST['_wpnonce'])||!wp_verify_nonce($_POST['_wpnonce'],'sz_enquiry'))sz_enquiry_back('spam');
 if(!empty($_POST['website']))sz_enquiry_back('ok');                       // honeypot: pretend success to bots
 if(time()-(int)($_POST['t']??0)<3)sz_enquiry_back('slow');                // time-trap: humans take >3s
 $ip='sz_rl_'.md5($_SERVER['REMOTE_ADDR']??'');$n=(int)get_transient($ip);
 if($n>=5)sz_enquiry_back('rate');set_transient($ip,$n+1,HOUR_IN_SECONDS);   // max 5 per hour per IP
 $name=sanitize_text_field(wp_unslash($_POST['name']??''));$email=sanitize_email(wp_unslash($_POST['email']??''));
 $msg=sanitize_textarea_field(wp_unslash($_POST['message']??''));
 if($name===''||!is_email($email)||$msg==='')sz_enquiry_back('err');
 $type=sanitize_text_field(wp_unslash($_POST['type']??''));$type=in_array($type,sz_types(),true)?$type:'Something else';
 $meta=['email'=>$email,'phone'=>sanitize_text_field(wp_unslash($_POST['phone']??'')),'company'=>sanitize_text_field(wp_unslash($_POST['company']??'')),'type'=>$type,'product'=>sanitize_text_field(wp_unslash($_POST['product']??''))];
 $id=wp_insert_post(['post_type'=>'sz_enquiry','post_status'=>'private','post_title'=>$name.' ('.$type.')','post_content'=>$msg,'meta_input'=>$meta]);
 $body="From: $name <$email>\nType: $type\nProduct: ".($meta['product']?:'Any')."\nPhone: {$meta['phone']}\nCompany: {$meta['company']}\n\n$msg\n";
 wp_mail(get_option('admin_email'),'New Shezan enquiry: '.$name,$body,['Reply-To: '.$name.' <'.$email.'>']);
 sz_enquiry_back($id?'ok':'err');
}
add_action('admin_post_nopriv_sz_enquiry','sz_handle_enquiry');
add_action('admin_post_sz_enquiry','sz_handle_enquiry');
