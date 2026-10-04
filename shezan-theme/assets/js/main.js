(function(){
 var root=document.documentElement,body=document.body,wrap=document.querySelector('.wrap');
 var burger=document.querySelector('.burger'),ov=document.querySelector('.overlay');
 function menu(o){body.classList.toggle('menu-open',o);burger.setAttribute('aria-expanded',o);ov.setAttribute('aria-hidden',!o);}
 burger.addEventListener('click',function(){menu(!body.classList.contains('menu-open'))});
 ov.addEventListener('click',function(e){if(e.target.tagName==='A')menu(false)});
 document.addEventListener('keydown',function(e){if(e.key==='Escape')menu(false)});
 var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target)}})},{threshold:.2});
 document.querySelectorAll('.rv').forEach(function(el){io.observe(el)});
 var ab=document.querySelector('.about');
 if(ab&&!matchMedia('(prefers-reduced-motion:reduce)').matches){var tk=false;
  var par=function(){tk=false;var r=ab.getBoundingClientRect();if(r.bottom<0||r.top>innerHeight)return;ab.style.setProperty('--py',((r.top+r.height/2-innerHeight/2)*-.12).toFixed(1)+'px')};
  addEventListener('scroll',function(){if(!tk){tk=true;requestAnimationFrame(par)}},{passive:true});par();}
 if(!wrap||!window.gsap)return;
 var $=function(s){return wrap.querySelector(s)},fly=$('.flyer'),tilt=$('.tilt'),hs=$('.hero-slot'),fs=$('.frame-slot'),show=$('.show');
 var Ls=[].slice.call(wrap.querySelectorAll('.colL .slide')),Rs=[].slice.call(wrap.querySelectorAll('.colR .slide')),I=[].slice.call(wrap.querySelectorAll('.pbox'));
 if(!Ls.length)return;
 var cur=0,busy=false,cnt=$('.count b'),F={},cp=0,tp=0,PI=Math.PI,lines=wrap.querySelectorAll('.titles .ln'),sp=[.22,.14,.08];
 var U={ry:0,rx:0},faces=[].slice.call(wrap.querySelectorAll('.f-front,.f-right,.f-back,.f-left')),tops=[].slice.call(wrap.querySelectorAll('.f-top,.f-bottom'));
 function setInk(i){var d=Ls[i].dataset;root.style.setProperty('--ink',d.ink);root.style.setProperty('--c2',d.c2);root.style.setProperty('--ink2',d.ink2);}
 function setBg(i){var d=Ls[i].dataset;root.style.setProperty('--c',d.c);wrap.style.setProperty('--bg',d.c);}
 setInk(0);setBg(0);
 function measure(){var w=wrap.getBoundingClientRect(),a=hs.getBoundingClientRect(),b=fs.getBoundingClientRect(),s=show.getBoundingClientRect(),W=fly.offsetWidth;
  F={ax:a.left+a.width/2-w.left,ay:a.top+a.height/2-w.top,bx:b.left+b.width/2-w.left,by:b.top+b.height/2-w.top,S:Math.max(1,s.top-w.top),s0:a.width/W,s1:b.width/W,W:W,H:fly.offsetHeight};}
 function aim(){tp=Math.min(1,Math.max(0,-wrap.getBoundingClientRect().top/F.S));}
 function L(a,b,t){return a+(b-a)*t}
 function draw(){cp+=(tp-cp)*.12;if(Math.abs(tp-cp)<.0004)cp=tp;
  var e=cp*cp*(3-2*cp),sw=Math.sin(PI*cp),x=L(F.ax,F.bx,cp),y=L(F.ay,F.by,cp),sc=L(F.s0,F.s1,e)*(1+.1*sw);
  var ry=24-384*e+U.ry,rx=8*(1-e)+U.rx;
  fly.style.transform='translate3d('+(x-F.W/2)+'px,'+(y-F.H/2)+'px,0) rotateZ('+(-9*sw)+'deg) scale('+sc+')';
  fly.style.setProperty('--ry',ry+'deg');fly.style.setProperty('--rx',rx+'deg');
  faces.forEach(function(f){var wa=(+f.dataset.a)+ry,b=.5+.5*Math.cos((wa+38)*PI/180);f.style.setProperty('--sh',(Math.max(0,1-b)*.62).toFixed(3));});
  tops.forEach(function(f){f.style.setProperty('--sh',f.classList.contains('f-top')?.0:.55)});
  var sy=scrollY;if(sy<innerHeight)lines.forEach(function(l,i){l.style.transform='translateY('+(-sy*sp[i])+'px)'});
  document.body.classList.toggle('dark-h',wrap.getBoundingClientRect().bottom<70);
  requestAnimationFrame(draw);}
 function init(){measure();aim();cp=tp;}
 init();addEventListener('resize',function(){measure();aim()});addEventListener('load',init);if(document.fonts&&document.fonts.ready)document.fonts.ready.then(function(){measure();aim()});if(window.ResizeObserver)new ResizeObserver(function(){measure();aim()}).observe(wrap);addEventListener('scroll',aim,{passive:true});draw();
 gsap.from('.titles em',{yPercent:110,duration:1.1,ease:'power4.out',stagger:.12,delay:.2});
 gsap.from('.sub',{opacity:0,y:20,duration:.8,delay:.9});
 gsap.from('.site-header',{y:-40,opacity:0,duration:.8,delay:.4});
 gsap.from('.pbox.on',{rotateY:-200,scale:.5,opacity:0,duration:1.6,ease:'power3.out',delay:.4});
 gsap.to('.bob',{y:-10,duration:2.6,yoyo:true,repeat:-1,ease:'sine.inOut'});
 function go(n){
  n=(n+Ls.length)%Ls.length;if(busy||n===cur||Ls.length<2)return;busy=true;
  var dir=n>cur?1:-1,r=fly.getBoundingClientRect(),w=wrap.getBoundingClientRect(),
   at=(r.left+r.width/2-w.left)+'px '+(r.top+r.height/2-w.top)+'px',R=Math.hypot(w.width,w.height);
  var d=document.createElement('div');d.className='wash';d.style.background=Ls[n].dataset.c;$('.washes').appendChild(d);
  gsap.fromTo(d,{clipPath:'circle(0px at '+at+')'},{clipPath:'circle('+R+'px at '+at+')',duration:1,ease:'power3.inOut',onComplete:function(){setBg(n);d.remove();}});
  gsap.delayedCall(.4,function(){setInk(n)});
  gsap.to(I[cur],{rotateY:-120*dir,scale:.7,opacity:0,duration:.6,ease:'power2.in'});
  gsap.fromTo(I[n],{rotateY:120*dir,scale:.7,opacity:0},{rotateY:0,scale:1,opacity:1,duration:1,delay:.45,ease:'back.out(1.3)'});
  [Ls,Rs].forEach(function(A){var o=A[cur],nw=A[n];
   gsap.to(o.children,{y:-30*dir,opacity:0,duration:.35,stagger:.03,onComplete:function(){o.classList.remove('on');gsap.set(o.children,{clearProps:'all'});}});
   nw.classList.add('on');gsap.set(nw,{opacity:1});
   gsap.fromTo(nw.children,{y:40*dir,opacity:0},{y:0,opacity:1,duration:.7,delay:.55,stagger:.08,ease:'power3.out',onComplete:function(){busy=false;}});});
  var pn=document.querySelectorAll('.about .pane');gsap.delayedCall(.35,function(){[].forEach.call(pn,function(el,k){el.classList.toggle('on',k===n)})});
  cur=n;cnt.textContent=n+1;
 }
 $('.next').addEventListener('click',function(){go(cur+1)});$('.prev').addEventListener('click',function(){go(cur-1)});
 document.addEventListener('keydown',function(e){if(e.key==='ArrowRight')go(cur+1);if(e.key==='ArrowLeft')go(cur-1)});
 var tx=0;wrap.addEventListener('touchstart',function(e){tx=e.touches[0].clientX},{passive:true});
 wrap.addEventListener('touchend',function(e){var d=e.changedTouches[0].clientX-tx;if(Math.abs(d)>60)go(cur+(d<0?1:-1))});
 var dr=false,lx=0,ly=0;
 tilt.addEventListener('pointerdown',function(e){dr=true;lx=e.clientX;ly=e.clientY;gsap.killTweensOf(U);tilt.setPointerCapture(e.pointerId)});
 tilt.addEventListener('pointermove',function(e){if(dr){U.ry+=(e.clientX-lx)*.7;U.rx=Math.max(-40,Math.min(40,U.rx-(e.clientY-ly)*.3));lx=e.clientX;ly=e.clientY;}else if(e.pointerType==='mouse'){var b=tilt.getBoundingClientRect();gsap.to(U,{rx:-((e.clientY-b.top)/b.height-.5)*16,duration:.6,overwrite:'auto'});}});
 function rel(){dr=false;gsap.to(U,{ry:0,rx:0,duration:1.4,ease:'elastic.out(1,.55)'})}
 tilt.addEventListener('pointerup',rel);tilt.addEventListener('pointerleave',function(){if(!dr)gsap.to(U,{rx:0,duration:.8})});tilt.addEventListener('pointercancel',rel);
})();
