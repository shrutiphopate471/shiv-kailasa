/* Helpers */
const $=(s,r=document)=>r.querySelector(s),$$=(s,r=document)=>[...r.querySelectorAll(s)];
const WA=window.WA_NUMBER||'';

/* 1. Loader */
addEventListener('load',()=>setTimeout(()=>$('#loader').classList.add('hide'),400));

/* 2. Navbar scroll + 12. Scroll-to-top */
addEventListener('scroll',()=>{$('#hdr').classList.toggle('solid',scrollY>60);$('#top').classList.toggle('show',scrollY>600)});
$('#top').onclick=()=>scrollTo({top:0,behavior:'smooth'});

/* 3. Mobile menu */
const burger=$('#burger'),nav=$('#nav');
burger.onclick=()=>{burger.classList.toggle('open');nav.classList.toggle('open')};
$$('#nav a').forEach(a=>a.onclick=()=>{burger.classList.remove('open');nav.classList.remove('open')});

/* 4. Smooth scrolling with header offset */
$$('a[href^="#"]').forEach(a=>a.addEventListener('click',e=>{
  const id=a.getAttribute('href');if(id.length<2)return;const t=$(id);if(!t)return;
  e.preventDefault();scrollTo({top:t.offsetTop-(id==='#home'?0:50),behavior:'smooth'});}));

/* 5. Scroll reveal + counters */
const io=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('active');io.unobserve(e.target)}}),{threshold:.15});
$$('.reveal,.reveal-left,.reveal-right').forEach(el=>io.observe(el));
const count=el=>{const to=+el.dataset.to,suf=el.dataset.suf||'',t0=performance.now();
  (function f(t){const p=Math.min((t-t0)/1800,1);el.textContent=Math.round(to*(1-Math.pow(1-p,3))).toLocaleString('en-US')+(p<1?'':suf);if(p<1)requestAnimationFrame(f)})(t0)};
const co=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){$$('[data-to]',e.target).forEach(count);co.unobserve(e.target)}}),{threshold:.4});
$$('#stats,.dstats').forEach(el=>co.observe(el));

/* Placeholder images (SVG) so lightbox works before real photos are added */
const ph=(t,c)=>'data:image/svg+xml;utf8,'+encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><rect width="1200" height="800" fill="${c}"/><text x="600" y="410" font-family="Georgia" font-style="italic" font-size="44" text-anchor="middle" fill="#8a8374">${t}</text></svg>`);
const sets={
  r2:['assets/images/residences/2bhk.jpg','Living room','Bedroom','Kitchen','Balcony'].map((s,i)=>i?ph('2 BHK · '+s,'#e4dccb'):s),
  r3:['assets/images/residences/3bhk.jpg','Living room','Master bedroom','Kitchen','Garden view'].map((s,i)=>i?ph('3 BHK · '+s,'#ddd5c0'):s)};
const gdata=[['Exterior','exterior'],['Interiors','interiors'],['Amenities','amenities'],['Landscape','landscape'],['Exterior','exterior'],['Amenities','amenities']];
sets.g=gdata.map((g,i)=>ph(g[0]+' '+(i+1),'#ddd5c0'));
$('#gg').innerHTML=gdata.map((g,i)=>`<div class="gi reveal" data-c="${g[1]}" data-i="${i}"><img src="assets/images/gallery/gallery-${i+1}.jpg" alt="${g[0]}" onerror="this.src='${sets.g[i].replace(/'/g,"%27")}'"><span>${g[0]}</span></div>`).join('');

/* 6/7/8. Modal + lightbox */
let cur=[],idx=0;const lb=$('#lb'),li=$('#li');
const show=()=>{li.src=cur[idx];$('#lc').textContent=(idx+1)+' / '+cur.length};
const open=(set,i=0)=>{cur=set;idx=i;show();lb.classList.add('open');document.body.style.overflow='hidden'};
const close=()=>{lb.classList.remove('open');document.body.style.overflow=''};
const step=d=>{idx=(idx+d+cur.length)%cur.length;show()};
li.onerror=()=>{li.onerror=null;li.src=ph('Add photo','#ddd5c0')};
$$('[data-gal]').forEach(b=>b.addEventListener('click',()=>{li.onerror=()=>{li.onerror=null;li.src=ph('Add photo','#ddd5c0')};open(sets[b.dataset.gal])}));
$$('.gi').forEach(g=>g.onclick=()=>{
  const vis=$$('.gi:not(.hidden)');cur=vis.map(v=>sets.g[v.dataset.i]);
  cur=vis.map(v=>$('img',v).src);open(cur,vis.indexOf(g));});
$('#lx').onclick=close;$('#lp').onclick=()=>step(-1);$('#ln').onclick=()=>step(1);
lb.onclick=e=>{if(e.target===lb)close()};
addEventListener('keydown',e=>{if(!lb.classList.contains('open'))return;if(e.key==='Escape')close();if(e.key==='ArrowLeft')step(-1);if(e.key==='ArrowRight')step(1)});

/* 9. Amenity + gallery filters */
const filter=(bar,items)=>$$('button',$(bar)).forEach(b=>b.onclick=()=>{
  $$('button',$(bar)).forEach(x=>x.classList.remove('on'));b.classList.add('on');
  $$(items).forEach(i=>i.classList.toggle('hidden',b.dataset.f!=='all'&&i.dataset.c!==b.dataset.f))});
filter('#af','.am-item');filter('#gf','.gi');

/* Enquire buttons preselect configuration */
$$('[data-cfg]').forEach(a=>a.addEventListener('click',()=>{$('#cfg2').value=a.dataset.cfg}));

/* 10. Form validation */
const phoneOK=v=>{const d=v.replace(/\D/g,'');return d.length>=10&&d.length<=13};
$$('form').forEach(f=>f.addEventListener('submit',e=>{
  let ok=true;const bad=(n,c)=>{const el=f.elements[n];if(!el)return;const fl=el.closest('.field');fl.classList.toggle('bad',c);if(c)ok=false};
  bad('name',!f.elements.name.value.trim());bad('phone',!phoneOK(f.elements.phone.value));
  if(f.elements.email)bad('email',f.elements.email.value&&!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(f.elements.email.value));
  if(!ok)e.preventDefault();}));

/* 11. WhatsApp */
const wa=()=>{const n=$('#f2 [name=name]').value||'a prospective buyer',c=$('#cfg2').value||'2 / 3 BHK';
  $('#wa').href=`https://wa.me/${WA}?text=`+encodeURIComponent(`Hello, I am ${n}.\n\nI am interested in Shiv Kailasa.\n\nConfiguration: ${c}\n\nI would like to know more details and book a site visit.`)};
wa();$('#wa').addEventListener('click',wa);
