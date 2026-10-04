<?php
/**
 * Shezan Beverages v2: theme setup, assets, Products CPT, meta box, demo seed.
 * Feature modules live in /inc: seo, forms, performance, security, maintenance.
 */
if(!defined('ABSPATH'))exit;
define('SZ_VER','2.1.2');
foreach(['seo','forms','performance','security','maintenance'] as $m)require_once get_template_directory().'/inc/'.$m.'.php';

/* ---------- Setup ---------- */
add_action('after_setup_theme',function(){
 add_theme_support('title-tag');
 add_theme_support('post-thumbnails');
 add_theme_support('html5',['search-form','comment-form','gallery','caption','style','script']);
 register_nav_menus(['primary'=>'Primary Menu']);
});

/* ---------- Assets (GSAP only where it is needed: the home page) ---------- */
add_action('wp_enqueue_scripts',function(){
 $u=get_template_directory_uri();
 wp_enqueue_style('sz-fonts','https://fonts.googleapis.com/css2?family=Anton&family=Fraunces:wght@600;800&display=swap',[],null);
 wp_enqueue_style('sz-main',$u.'/assets/css/main.css',[],SZ_VER);
 $deps=[];
 if(is_front_page()){
  wp_enqueue_script('gsap','https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js',[],null,['strategy'=>'defer','in_footer'=>true]);
  $deps=['gsap'];
 }
 wp_enqueue_script('sz-main',$u.'/assets/js/main.js',$deps,SZ_VER,['strategy'=>'defer','in_footer'=>true]);
});

/* ---------- Products custom post type ---------- */
add_action('init',function(){
 register_post_type('sz_product',['label'=>'Products','public'=>true,'menu_icon'=>'dashicons-cart','supports'=>['title','editor','thumbnail','excerpt'],'rewrite'=>['slug'=>'products'],'show_in_rest'=>true]);
});

/* Per-product fields: key => [label, textarea?, help] */
function sz_fields(){return[
 'tagline'=>['Tagline',0,''],'size'=>['Pack size',0,''],
 'color'=>['Primary colour (hex)',0,'Page background for this juice'],'color2'=>['Button colour (hex)',0,''],
 'ingredients'=>['Ingredients (printed on the right side of the 3D box)',1,'Copy this from the real pack'],
 'nutrition'=>['Nutrition per 100 ml (left side of the 3D box)',1,'Format: Energy|46 kcal;Fat|0 g;Sugars|10 g . Demo values, replace with the real pack'],
 'storage'=>['Storage advice',0,''],'shelf'=>['Shelf life',0,''],
 'texture'=>['3D box texture (mango or apple)',0,''],'order_url'=>['Order link',0,''],
 'seo_desc'=>['SEO meta description (max ~155 characters)',1,'Shown in Google results']];}

add_action('add_meta_boxes',function(){add_meta_box('sz_meta','Product details',function($p){
 wp_nonce_field('sz_save','sz_nonce');
 foreach(sz_fields() as $k=>$f){
  $v=get_post_meta($p->ID,$k,true);
  echo '<p><label><strong>'.esc_html($f[0]).'</strong><br>';
  if($f[1])printf('<textarea class="widefat" rows="2" name="sz_%s">%s</textarea>',esc_attr($k),esc_textarea($v));
  else printf('<input type="text" class="widefat" name="sz_%s" value="%s">',esc_attr($k),esc_attr($v));
  echo '</label>'.($f[2]?'<span class="description">'.esc_html($f[2]).'</span>':'').'</p>';
 }},'sz_product');});

add_action('save_post_sz_product',function($id){
 if(!isset($_POST['sz_nonce'])||!wp_verify_nonce($_POST['sz_nonce'],'sz_save')||!current_user_can('edit_post',$id))return;
 foreach(sz_fields() as $k=>$f)if(isset($_POST["sz_$k"]))update_post_meta($id,$k,sanitize_textarea_field(wp_unslash($_POST["sz_$k"])));
});

/* ---------- Helpers ---------- */
function sz_lum($hex){ // WCAG relative luminance
 $h=ltrim($hex,'#');if(strlen($h)!=6)return .9;
 $c=array_map(function($x){$x=hexdec($x)/255;return $x<=.03928?$x/12.92:pow(($x+.055)/1.055,2.4);},str_split($h,2));
 return .2126*$c[0]+.7152*$c[1]+.0722*$c[2];
}
function sz_ink($bg){ // pick whichever of dark / cream gives the higher contrast on $bg
 $L=sz_lum($bg);$d=(max($L,sz_lum('#1D1D1D'))+.05)/(min($L,sz_lum('#1D1D1D'))+.05);$c=(max($L,sz_lum('#F8F7E5'))+.05)/(min($L,sz_lum('#F8F7E5'))+.05);
 return $d>=$c?'#1D1D1D':'#F8F7E5';
}
function sz_defaults($tex){
 return $tex==='apple'?[
  'color'=>'#f02f43','color2'=>'#FFC72C',
  'ing'=>'Cane sugar, apple juice, citric acid, CMC (E-466), sodium citrate (E-331), caramel colour (E-150d), artificial flavour, preservative (E-211).',
  'nut'=>'Energy|44 kcal;Fat|0 g;Carbohydrate|10.8 g;Sugars|10.2 g;Protein|0 g;Sodium|9 mg']:[
  'color'=>'#fedc00','color2'=>'#E8452C',
  'ing'=>'Cane sugar, mango pulp, citric acid, CMC (E-466), ascorbic acid, preservative (E-211), artificial flavour, permitted food colours (E-110, E-102).',
  'nut'=>'Energy|46 kcal;Fat|0 g;Carbohydrate|11.2 g;Sugars|10.6 g;Protein|0.1 g;Sodium|8 mg'];
}
function sz_rows($s){$r=[];foreach(explode(';',(string)$s) as $p){$x=array_map('trim',explode('|',$p));if(count($x)==2&&$x[0]!=='')$r[]=$x;}return $r;}

/** One place that collects everything a template needs about a product. */
function sz_data($id){
 $g=function($k)use($id){return get_post_meta($id,$k,true);};$t=get_the_title($id);
 $tex=$g('texture')?:(stripos($t,'apple')!==false?'apple':'mango');$D=sz_defaults($tex);
 $c=$g('color')?:$D['color'];$c2=$g('color2')?:$D['color2'];
 return['id'=>$id,'t'=>$t,'tex'=>$tex,'c'=>$c,'c2'=>$c2,'ink'=>sz_ink($c),'ink2'=>sz_ink($c2),
  'd'=>wp_strip_all_tags(get_post_field('post_content',$id)),'tag'=>$g('tagline'),'size'=>$g('size')?:'250 ml',
  'ing'=>$g('ingredients')?:$D['ing'],'nut'=>sz_rows($g('nutrition')?:$D['nut']),'sto'=>$g('storage'),'shelf'=>$g('shelf'),
  'url'=>$g('order_url')?:'#contact','img'=>get_the_post_thumbnail_url($id,'large'),'link'=>get_permalink($id)];
}
function sz_products(){
 $q=get_posts(['post_type'=>'sz_product','posts_per_page'=>6,'orderby'=>'menu_order date','order'=>'ASC']);
 return array_map(function($p){return sz_data($p->ID);},$q);
}

/* ---------- Demo seed + upgrade migration (runs once per version) ---------- */
function sz_seed(){
 $items=[
  ['Shezan Mango','Pure mango, pure sunshine','mango','shezan-mango.jpg','Thick, golden and unapologetically sweet. Shezan Mango brings the taste of ripe summer mangoes to every sip.','Shezan Mango fruit drink in a 250 ml pack. Thick, golden and sweet. See ingredients, nutrition and how to order for your shop.'],
  ['Shezan Apple','Crisp apple, every sip','apple','shezan-apple.jpg','Bright, crisp and refreshing. Shezan Apple is the orchard-fresh pick-me-up for any time of day.','Shezan Apple fruit drink in a 250 ml pack. Bright, crisp and refreshing. See ingredients, nutrition and how to order for your shop.'],
 ];
 foreach($items as $i=>$d){
  if(get_posts(['post_type'=>'sz_product','title'=>$d[0],'numberposts'=>1]))continue;
  $D=sz_defaults($d[2]);
  $pid=wp_insert_post(['post_type'=>'sz_product','post_status'=>'publish','post_title'=>$d[0],'post_content'=>$d[4],'menu_order'=>$i]);
  foreach(['tagline'=>$d[1],'size'=>'250 ml','color'=>$D['color'],'color2'=>$D['color2'],'ingredients'=>$D['ing'],'nutrition'=>$D['nut'],'storage'=>'Store in a cool, dry place','shelf'=>'9 months','texture'=>$d[2],'order_url'=>'#contact','seo_desc'=>$d[5]] as $k=>$v)update_post_meta($pid,$k,$v);
  $src=get_template_directory().'/assets/img/'.$d[3];$up=wp_upload_bits($d[3],null,file_get_contents($src));
  if(!$up['error']){require_once ABSPATH.'wp-admin/includes/image.php';
   $a=wp_insert_attachment(['post_mime_type'=>'image/jpeg','post_title'=>$d[0],'post_status'=>'inherit'],$up['file'],$pid);
   wp_update_attachment_metadata($a,wp_generate_attachment_metadata($a,$up['file']));set_post_thumbnail($pid,$a);}
 }
 // SEO landing page (assigned to our landing template)
 if(!get_page_by_path('wholesale')){
  $pg=wp_insert_post(['post_type'=>'page','post_status'=>'publish','post_title'=>'Wholesale & Distribution','post_name'=>'wholesale','post_content'=>'Shezan fruit drinks for shops, caterers and distributors. Tell us what you stock and we will come back with availability and terms.']);
  if($pg)update_post_meta($pg,'_wp_page_template','page-landing-wholesale.php');
 }
}
add_action('after_switch_theme','sz_seed');
add_action('init',function(){ // upgrade path for sites that already ran v1
 if((int)get_option('sz_db_ver')>=2)return;
 sz_seed();
 foreach(get_posts(['post_type'=>'sz_product','numberposts'=>-1]) as $p){
  $d=sz_data($p->ID);
  if(get_post_meta($p->ID,'size',true)==='200 ml')update_post_meta($p->ID,'size','250 ml');
  foreach(['ingredients'=>$d['ing'],'nutrition'=>implode(';',array_map(function($r){return $r[0].'|'.$r[1];},$d['nut']))] as $k=>$v)
   if(strpos((string)get_post_meta($p->ID,$k,true),'(edit me)')!==false||!get_post_meta($p->ID,$k,true))update_post_meta($p->ID,$k,$v);
  if(!get_post_meta($p->ID,'seo_desc',true))update_post_meta($p->ID,'seo_desc',$d['t'].' fruit drink, '.$d['size'].'. See ingredients, nutrition and how to order for your shop.');
 }
 update_option('sz_db_ver',2);
},30);
