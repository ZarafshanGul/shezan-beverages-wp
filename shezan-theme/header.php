<!DOCTYPE html><html <?php language_attributes();?>><head><meta charset="<?php bloginfo('charset');?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head();?></head>
<body <?php body_class(is_front_page()?'':'sz-inner');?>><?php wp_body_open();?>
<a class="skip" href="#main">Skip to content</a>
<?php $h=home_url('/');$ws=get_page_by_path('wholesale');?>
<header class="site-header"><a class="logo" href="<?php echo esc_url($h);?>"><img src="<?php echo esc_url(get_template_directory_uri());?>/assets/img/shezan-logo.png" alt="Shezan home" width="110" height="72"></a>
<div class="hdr-right"><a class="btn" href="#contact"><span class="bg"></span><span class="lbl">Order now</span></a>
<button class="burger" aria-label="Menu" aria-expanded="false"><span class="t"><i>Menu</i><i>Close</i></span><span class="bars"><b></b><b></b><b></b></span></button></div></header>
<nav class="overlay" aria-hidden="true" aria-label="Main"><ul>
<li><a href="<?php echo esc_url($h);?>">Home</a></li><li><a href="<?php echo esc_url($h.'#products');?>">Juices</a></li><li><a href="<?php echo esc_url($h.'#about');?>">About</a></li>
<?php if($ws):?><li><a href="<?php echo esc_url(get_permalink($ws));?>">Wholesale</a></li><?php endif;?><li><a href="#contact">Contact</a></li></ul></nav>
