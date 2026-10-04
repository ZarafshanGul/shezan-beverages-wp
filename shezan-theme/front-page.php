<?php get_header();
$P=sz_products();$B=get_template_directory_uri().'/assets/img/';
/** 3D box faces. Back reuses the front artwork; right reuses the real left-side panel (ingredients). */
function sz_box($p,$B){
 $f=$B.'box/'.$p['tex'].'/';
 $map=['front'=>['front',0],'right'=>['left',90],'back'=>['front',180],'left'=>['left',270],'top'=>['top',0],'bottom'=>['bottom',0]];
 $h='';foreach($map as $face=>$m)$h.='<div class="face f-'.$face.'" data-a="'.$m[1].'" style="background-image:url(\''.esc_url($f.$m[0].'.jpg').'\')"></div>';
 return $h;
}?>
<main id="main">
<div class="wrap" id="home">
<div class="washes"></div><div class="noise"></div>
<section class="hero">
<h1 class="huge titles"><span class="ln"><em>Fruit juice,</em></span><span class="ln"><em>bottled without</em></span><span class="ln alt"><em>fuss</em></span></h1>
<p class="sub">Pure, honest and damn delicious</p>
<div class="slot hero-slot"></div>
<a href="#products" class="scroll" aria-label="Scroll down"><svg viewBox="0 0 40 40"><circle cx="20" cy="20" r="18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 4"/><path d="M20 12v16m-6-6 6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"/></svg></a>
</section>
<section class="show" id="products">
<div class="colL"><?php foreach($P as $i=>$p):?>
<article class="slide<?php echo $i?'':' on';?>" data-c="<?php echo esc_attr($p['c']);?>" data-ink="<?php echo esc_attr($p['ink']);?>" data-c2="<?php echo esc_attr($p['c2']);?>" data-ink2="<?php echo esc_attr($p['ink2']);?>">
<p class="small">Discover our juices</p><h2 class="big"><?php echo esc_html($p['t']);?></h2><p class="norm"><?php echo esc_html($p['size']);?></p>
<dl class="dots"><div><dt>Ingredients</dt><dd><?php echo esc_html($p['ing']);?></dd></div></dl></article><?php endforeach;?></div>
<div class="mid"><div class="frame"><div class="slot frame-slot"></div></div>
<div class="nav"><button class="prev" aria-label="Previous">&larr;</button><span class="count"><b>1</b> / <?php echo count($P);?></span><button class="next" aria-label="Next">&rarr;</button></div></div>
<div class="colR"><?php foreach($P as $i=>$p):?>
<article class="slide<?php echo $i?'':' on';?>">
<p class="small"><?php echo esc_html($p['tag']);?></p><p class="desc"><?php echo esc_html($p['d']);?></p>
<dl class="dots"><div><dt>Storage</dt><dd><?php echo esc_html($p['sto']);?></dd></div><div><dt>Shelf life</dt><dd><?php echo esc_html($p['shelf']);?></dd></div></dl>
<a class="btn sec" href="<?php echo esc_url($p['url']);?>"><span class="bg"></span><span class="lbl">Order now</span></a>
<a class="more" href="<?php echo esc_url($p['link']);?>">Ingredients, nutrition &amp; FAQs</a></article><?php endforeach;?></div>
</section>
<div class="flyer"><div class="bob"><div class="tilt"><?php foreach($P as $i=>$p):?>
<div class="pbox<?php echo $i?'':' on';?>"><div class="box" role="img" aria-label="<?php echo esc_attr($p['t'].' carton, drag to rotate');?>"><?php echo sz_box($p,$B);?></div></div><?php endforeach;?></div></div></div>
</div>
<section id="about" class="about">
<div class="about-bg" aria-hidden="true"><?php foreach($P as $i=>$p):?>
<div class="pane<?php echo $i?'':' on';?>"><img src="<?php echo esc_url($B.'bg-'.$p['tex'].'.webp');?>" alt="" width="1376" height="768" loading="lazy" decoding="async"></div><?php endforeach;?></div>
<div class="about-in"><h2 class="huge rv">Pure. Honest.<br>Damn delicious.</h2><p class="rv">Since 1964, Shezan has been bringing real fruit flavour to tables everywhere. This demo shows two of the range, Mango and Apple, in an interactive WordPress theme.</p></div>
</section>
</main><?php get_footer();
