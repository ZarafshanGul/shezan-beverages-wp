<?php /** SEO landing page for one juice: keyword H1, breadcrumbs, schema (Product + FAQ + Breadcrumb), internal links, CTA. */
get_header();while(have_posts()){the_post();$d=sz_data(get_the_ID());$faq=sz_product_faq($d);
$ws=get_page_by_path('wholesale');$wl=$ws?get_permalink($ws):home_url('/');
$others=array_filter(sz_products(),function($x)use($d){return $x['id']!==$d['id'];});?>
<main id="main" class="pg">
<section class="pg-hero" style="--hbg:<?php echo esc_attr($d['c']);?>;--hink:<?php echo esc_attr($d['ink']);?>;--hbtn:<?php echo esc_attr($d['c2']);?>;--hbtnink:<?php echo esc_attr($d['ink2']);?>">
<div class="pg-hero-in">
<div class="pg-copy"><?php sz_breadcrumbs();?>
<h1 class="pg-h1"><?php echo esc_html($d['t']);?><span><?php echo esc_html($d['size']);?> fruit drink</span></h1>
<p class="pg-lead"><?php echo esc_html($d['tag']);?>. <?php echo esc_html($d['d']);?></p>
<a class="btn hb" href="#contact"><span class="bg"></span><span class="lbl">Enquire to stock it</span></a></div>
<div class="pg-pic"><?php if(has_post_thumbnail())the_post_thumbnail('large',['alt'=>$d['t'].' '.$d['size'].' carton with fresh fruit','loading'=>'eager','fetchpriority'=>'high']);?></div>
</div></section>
<section class="pg-sec"><div class="pg-cols">
<div><h2>Ingredients</h2><p class="pg-txt"><?php echo esc_html($d['ing']);?></p><h2 class="gap">Storage</h2><dl class="dots"><div><dt>Store</dt><dd><?php echo esc_html($d['sto']);?></dd></div><div><dt>Shelf life</dt><dd><?php echo esc_html($d['shelf']);?></dd></div></dl></div>
<div><h2>Nutrition <small>per 100 ml</small></h2><dl class="dots"><?php foreach($d['nut'] as $r)echo '<div><dt>'.esc_html($r[0]).'</dt><dd>'.esc_html($r[1]).'</dd></div>';?></dl></div>
</div></section>
<section class="pg-sec alt"><h2>Questions about <?php echo esc_html($d['t']);?></h2><div class="faq"><?php foreach($faq as $f)echo '<details><summary>'.esc_html($f[0]).'</summary><p>'.esc_html($f[1]).'</p></details>';?></div></section>
<section class="pg-sec"><h2>Also in the range</h2><div class="cards"><?php foreach($others as $o)printf('<a class="card has-img" href="%s" style="--cimg:url(%s)"><b>%s</b><span>%s &middot; see ingredients and nutrition</span></a>',esc_url($o['link']),esc_url(get_template_directory_uri().'/assets/img/bg-'.$o['tex'].'.webp'),esc_html($o['t']),esc_html($o['size']));?>
<a class="card" href="<?php echo esc_url($wl);?>" style="--cbg:#F8F7E5;--cink:#1D1D1D"><b>Stock Shezan</b><span>Wholesale and distribution</span></a></div></section>
</main><?php }get_footer();
