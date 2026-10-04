<?php /** Template Name: Landing: Wholesale & Distribution */
get_header();$P=sz_products();?>
<main id="main" class="pg">
<section class="pg-hero" style="--hbg:#FFC72C;--hink:#1D1D1D;--hbtn:#1D1D1D;--hbtnink:#F8F7E5"><div class="pg-hero-in one">
<div class="pg-copy"><?php sz_breadcrumbs();?>
<h1 class="pg-h1">Stock Shezan juices<span>for shops, caterers and distributors</span></h1>
<div class="pg-lead"><?php while(have_posts()){the_post();the_content();}?></div>
<a class="btn hb" href="#contact"><span class="bg"></span><span class="lbl">Send a wholesale enquiry</span></a></div></div></section>
<section class="pg-sec"><h2>The range</h2><div class="cards"><?php foreach($P as $p)printf('<a class="card has-img" href="%s" style="--cimg:url(%s)"><b>%s</b><span>%s &middot; ingredients, nutrition and FAQs</span></a>',esc_url($p['link']),esc_url(get_template_directory_uri().'/assets/img/bg-'.$p['tex'].'.webp'),esc_html($p['t']),esc_html($p['size']));?></div></section>
<section class="pg-sec alt"><h2>How it works</h2><ol class="steps"><li><b>Tell us what you need</b><span>Send the form below with your shop type and the juices you want.</span></li><li><b>We reply with availability</b><span>You get terms and stock details for the juices you picked.</span></li><li><b>Confirm and stock up</b><span>Agree the order and we arrange delivery.</span></li></ol></section>
<section class="pg-sec"><h2>Wholesale questions</h2><div class="faq"><?php foreach(sz_wholesale_faq() as $f)echo '<details><summary>'.esc_html($f[0]).'</summary><p>'.esc_html($f[1]).'</p></details>';?></div></section>
</main><?php get_footer();
