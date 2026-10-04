<?php
/** SEO module: titles, meta description, Open Graph, JSON-LD schema, breadcrumbs, robots.txt.
 *  Steps aside automatically if Yoast / Rank Math / AIOSEO is active. */
if(!defined('ABSPATH'))exit;
const SZ_HOME_DESC='Shezan mango and apple fruit drinks, bringing real fruit flavour to tables since 1964. Explore the range, ingredients and nutrition, and enquire about wholesale.';
function sz_seo_off(){return defined('WPSEO_VERSION')||defined('RANK_MATH_VERSION')||defined('AIOSEO_VERSION');}

add_filter('pre_get_document_title',function($t){
 if(sz_seo_off())return $t;
 if(is_front_page())return 'Shezan Juices | Mango & Apple Fruit Drinks Since 1964';
 if(is_singular('sz_product')){$d=sz_data(get_queried_object_id());return $d['t'].' '.$d['size'].' Fruit Drink | Shezan';}
 return $t;
},20);

function sz_desc(){
 if(is_front_page())return SZ_HOME_DESC;
 $d='';
 if(is_singular()){$id=get_queried_object_id();$d=get_post_meta($id,'seo_desc',true);
  if(!$d)$d=has_excerpt($id)?get_the_excerpt($id):wp_strip_all_tags(strip_shortcodes(get_post_field('post_content',$id)));}
 return wp_html_excerpt(trim(preg_replace('/\s+/',' ',$d?:SZ_HOME_DESC)),158,'…');
}

/** Breadcrumb trail as [[name,url],...], used for markup AND schema. */
function sz_crumbs(){
 $c=[['Home',home_url('/')]];
 if(is_singular('sz_product')){$c[]=['Juices',home_url('/#products')];$c[]=[get_the_title(),get_permalink()];}
 elseif(is_page()){$c[]=[get_the_title(),get_permalink()];}
 return $c;
}
function sz_breadcrumbs(){
 $c=sz_crumbs();echo '<nav class="crumbs" aria-label="Breadcrumb"><ol>';
 foreach($c as $i=>$x)echo $i<count($c)-1?'<li><a href="'.esc_url($x[1]).'">'.esc_html($x[0]).'</a></li>':'<li aria-current="page">'.esc_html($x[0]).'</li>';
 echo '</ol></nav>';
}

/** FAQ content lives in PHP so the visible FAQ and the FAQPage schema can never drift apart. */
function sz_product_faq($d){
 return array_values(array_filter([
  ['Is '.$d['t'].' halal?','Yes. The pack carries the HALAL mark.'],
  ['What is in '.$d['t'].'?',$d['ing']],
  $d['sto']?['How should I store '.$d['t'].'?',$d['sto'].'.']:null,
  $d['shelf']?['What is the shelf life?','The shelf life is '.$d['shelf'].' when stored as advised.']:null]));
}
function sz_wholesale_faq(){return[
 ['How do I place a wholesale order?','Send the enquiry form on this page. We reply with availability and terms for the juices you choose.'],
 ['Which juices can I stock?','This demo range has Shezan Mango and Shezan Apple in 250 ml packs, with more flavours coming.'],
 ['Do you supply caterers and cafes as well as shops?','Yes. Choose the enquiry type that fits and tell us your usual volumes in the message box.']];}
function sz_faq_schema($faq){return['@type'=>'FAQPage','mainEntity'=>array_map(function($f){return['@type'=>'Question','name'=>$f[0],'acceptedAnswer'=>['@type'=>'Answer','text'=>$f[1]]];},$faq)];}

add_action('wp_head',function(){
 if(sz_seo_off())return;
 $url=is_singular()?get_permalink():home_url('/');$desc=sz_desc();$title=wp_get_document_title();
 $img=is_singular()&&has_post_thumbnail()?get_the_post_thumbnail_url(null,'large'):get_template_directory_uri().'/assets/img/shezan-logo.png';
 echo '<meta name="description" content="'.esc_attr($desc)."\">\n";
 $og=['og:site_name'=>'Shezan Beverages (portfolio demo)','og:type'=>is_singular('sz_product')?'product':'website','og:title'=>$title,'og:description'=>$desc,'og:url'=>$url,'og:image'=>$img,'twitter:card'=>'summary_large_image'];
 foreach($og as $k=>$v)echo '<meta '.(strpos($k,'twitter')===0?'name':'property').'="'.esc_attr($k).'" content="'.esc_attr($v)."\">\n";
 $g=[];$org=['@type'=>'Organization','@id'=>home_url('/#org'),'name'=>'Shezan','url'=>home_url('/'),'logo'=>get_template_directory_uri().'/assets/img/shezan-logo.png','foundingDate'=>'1964'];
 if(is_front_page()){$g[]=$org;$g[]=['@type'=>'WebSite','@id'=>home_url('/#site'),'url'=>home_url('/'),'name'=>'Shezan Beverages','publisher'=>['@id'=>home_url('/#org')]];}
 if(is_singular('sz_product')){$d=sz_data(get_queried_object_id());
  $g[]=['@type'=>'Product','name'=>$d['t'].' '.$d['size'],'description'=>$desc,'image'=>$img,'brand'=>['@type'=>'Brand','name'=>'Shezan'],'url'=>$url];
  $g[]=sz_faq_schema(sz_product_faq($d));}
 if(is_page_template('page-landing-wholesale.php')){$g[]=['@type'=>'WebPage','name'=>$title,'description'=>$desc,'url'=>$url,'publisher'=>['@id'=>home_url('/#org')]];$g[]=sz_faq_schema(sz_wholesale_faq());}
 if(!is_front_page()&&$g){$bc=[];foreach(sz_crumbs() as $i=>$c)$bc[]=['@type'=>'ListItem','position'=>$i+1,'name'=>$c[0],'item'=>$c[1]];$g[]=['@type'=>'BreadcrumbList','itemListElement'=>$bc];}
 if($g)echo '<script type="application/ld+json">'.wp_json_encode(['@context'=>'https://schema.org','@graph'=>$g])."</script>\n";
},5);

add_filter('robots_txt',function($o){return $o."\nSitemap: ".home_url('/wp-sitemap.xml')."\n";},10);
