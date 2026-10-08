/* Helpers */
const $ = (s, r = document) => r.querySelector(s), $$ = (s, r = document) => [...r.querySelectorAll(s)];
const WA = window.WA_NUMBER || '';

/* 1. Loader */
addEventListener('load', () => setTimeout(() => $('#loader').classList.add('hide'), 400));

/* 2. Navbar scroll + 12. Scroll-to-top */
const hdr = $('#hdr');
addEventListener('scroll', () => { if (hdr) hdr.classList.toggle('solid', scrollY > 60); $('#top').classList.toggle('show', scrollY > 600) });

/* 3. Mobile menu */
const burger = $('#burger'), nav = $('#nav');
if (burger && nav) {
  burger.onclick = () => { burger.classList.toggle('open'); nav.classList.toggle('open') };
  $$('#nav a').forEach(a => a.onclick = () => { burger.classList.remove('open'); nav.classList.remove('open') });
}
/* 4. Smooth scrolling with header offset */
$$('a[href^="#"]').forEach(a => a.addEventListener('click', e => {
  const id = a.getAttribute('href'); if (id.length < 2) return; const t = $(id); if (!t) return;
  e.preventDefault(); scrollTo({ top: t.offsetTop - (id === '#home' ? 0 : 50), behavior: 'smooth' });
}));


// Hero video: pick mobile or desktop file
const heroVideo = document.getElementById('heroVideo');
if (heroVideo) {
  const mq = window.matchMedia('(max-width: 768px)');
  const loadHeroVideo = () => {
    const src = mq.matches ? heroVideo.dataset.mobile : heroVideo.dataset.desktop;
    if (heroVideo.getAttribute('src') !== src) {
      heroVideo.setAttribute('src', src);
      heroVideo.load();
      heroVideo.play().catch(() => { });
    }
  };
  loadHeroVideo();
  mq.addEventListener('change', loadHeroVideo);
}


/* =========================
   COUNTER ANIMATION
========================= */

// Counter animation for .stats (hero) and .dstats (developer section)
const counterSections = document.querySelectorAll('.stats, .dstats');

const startCounter = (element) => {
  const target = Number(element.dataset.to);
  const suffix = element.dataset.suf || '';
  const duration = 1800;
  let startTime = null;

  const animate = (currentTime) => {
    if (!startTime) startTime = currentTime;

    const progress = Math.min((currentTime - startTime) / duration, 1);
    const easeOut = 1 - Math.pow(1 - progress, 3); // smooth ease-out
    const currentValue = Math.floor(target * easeOut);

    element.textContent = currentValue.toLocaleString('en-US') + suffix;

    if (progress < 1) {
      requestAnimationFrame(animate);
    } else {
      element.textContent = target.toLocaleString('en-US') + suffix; // exact final value
    }
  };

  requestAnimationFrame(animate);
};

if (counterSections.length) {
  const counterObserver = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.querySelectorAll('[data-to]').forEach(startCounter);
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.3 }
  );

  counterSections.forEach(section => counterObserver.observe(section));
}
/* 5. Scroll reveal */

const revealObserver = new IntersectionObserver(
  (entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        observer.unobserve(entry.target);
      }
    });
  },
  {
    threshold: 0.15
  }
);

$$('.reveal, .reveal-left, .reveal-right').forEach(el => {
  revealObserver.observe(el);
});
/* Placeholder images (SVG) so lightbox works before real photos are added */
const ph = (t, c) => 'data:image/svg+xml;utf8,' + encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><rect width="1200" height="800" fill="${c}"/><text x="600" y="410" font-family="Georgia" font-style="italic" font-size="44" text-anchor="middle" fill="#8a8374">${t}</text></svg>`);
const sets = {
  r2: [
    'assets/images/2bhk1.png',
    'assets/images/2bhk2.png',
    'assets/images/2bhk3.png',
    'assets/images/2bhk4.png',
  ],

  r3: [
    'assets/images/3bhk1.png',
    'assets/images/3bhk2.png',
    'assets/images/3bhk3.png',
    'assets/images/3bhk4.png',
    'assets/images/3bhk5.png',
  ]
};
const gdata = [
  ['Exterior', 'exterior', 'assets/images/gallery/gallery-1.jpg'],
  ['Interiors', 'interiors', 'assets/images/gallery/gallery-2.jpg'],
  ['Amenities', 'amenities', 'assets/images/gallery/gallery-3.jpg'],
  ['Landscape', 'landscape', 'assets/images/gallery/gallery-4.jpg'],
  ['Exterior', 'exterior', 'assets/images/gallery/gallery-5.jpg'],
  ['Amenities', 'amenities', 'assets/images/gallery/gallery-6.jpg']
];

sets.g = gdata.map(g => g[2]);

$('#gg').innerHTML = gdata.map((g, i) => `
  <div class="gi reveal" data-c="${g[1]}" data-i="${i}">
    <img 
      src="${g[2]}" 
      alt="${g[0]}"
    >
    <span>${g[0]}</span>
  </div>
`).join('');

/* 6/7/8. Modal + lightbox */

/* =========================
   RESIDENCE LIGHTBOX
========================= */

let cur = [];
let idx = 0;

const lb = $('#lb');
const li = $('#li');
const lc = $('#lc');

const show = () => {
  if (!cur.length) return;

  li.src = cur[idx];

  if (lc) {
    lc.textContent = `${idx + 1} / ${cur.length}`;
  }
};

const open = (set, i = 0) => {
  if (!set || !set.length) return;

  cur = set;
  idx = i;

  show();

  lb.classList.add('open');
  document.body.style.overflow = 'hidden';
};

const close = () => {
  lb.classList.remove('open');
  document.body.style.overflow = '';
};

const step = (direction) => {
  if (!cur.length) return;

  idx = (idx + direction + cur.length) % cur.length;
  show();
};


/* Broken image fallback */
li.onerror = () => {
  li.onerror = null;
  li.src = ph('Add photo', '#ddd5c0');
};


/* Residence buttons */
$$('[data-gal]').forEach(button => {

  button.addEventListener('click', () => {

    const galleryName = button.dataset.gal;
    const galleryImages = sets[galleryName];

    if (!galleryImages || !galleryImages.length) {
      console.warn(`Gallery "${galleryName}" not found.`);
      return;
    }

    open(galleryImages, 0);
  });

});


/* Main gallery images */
$$('.gi').forEach(item => {

  item.addEventListener('click', () => {

    const visibleItems = $$('.gi:not(.hidden)');

    const images = visibleItems.map(item => {
      return $('img', item).src;
    });

    const currentIndex = visibleItems.indexOf(item);

    open(images, currentIndex);
  });

});


/* Lightbox buttons */
$('#lx').addEventListener('click', close);

$('#lp').addEventListener('click', () => {
  step(-1);
});

$('#ln').addEventListener('click', () => {
  step(1);
});


/* Close when clicking outside image */
lb.addEventListener('click', e => {

  if (e.target === lb) {
    close();
  }

});


/* Keyboard controls */
addEventListener('keydown', e => {

  if (!lb.classList.contains('open')) return;

  if (e.key === 'Escape') {
    close();
  }

  if (e.key === 'ArrowLeft') {
    step(-1);
  }

  if (e.key === 'ArrowRight') {
    step(1);
  }

});
// let cur = [], idx = 0; const lb = $('#lb'), li = $('#li');
// const show = () => { li.src = cur[idx]; $('#lc').textContent = (idx + 1) + ' / ' + cur.length };
// const open = (set, i = 0) => { cur = set; idx = i; show(); lb.classList.add('open'); document.body.style.overflow = 'hidden' };
// const close = () => { lb.classList.remove('open'); document.body.style.overflow = '' };
// const step = d => { idx = (idx + d + cur.length) % cur.length; show() };
// li.onerror = () => { li.onerror = null; li.src = ph('Add photo', '#ddd5c0') };
// $$('[data-gal]').forEach(b => b.addEventListener('click', () => { li.onerror = () => { li.onerror = null; li.src = ph('Add photo', '#ddd5c0') }; open(sets[b.dataset.gal]) }));
// $$('.gi').forEach(g => g.onclick = () => {
//   const vis = $$('.gi:not(.hidden)'); cur = vis.map(v => sets.g[v.dataset.i]);
//   cur = vis.map(v => $('img', v).src); open(cur, vis.indexOf(g));
// });
// $('#lx').onclick = close; $('#lp').onclick = () => step(-1); $('#ln').onclick = () => step(1);
// lb.onclick = e => { if (e.target === lb) close() };
// addEventListener('keydown', e => { if (!lb.classList.contains('open')) return; if (e.key === 'Escape') close(); if (e.key === 'ArrowLeft') step(-1); if (e.key === 'ArrowRight') step(1) });

/* 9. Amenity + gallery filters */
const filter = (bar, items) => $$('button', $(bar)).forEach(b => b.onclick = () => {
  $$('button', $(bar)).forEach(x => x.classList.remove('on')); b.classList.add('on');
  $$(items).forEach(i => i.classList.toggle('hidden', b.dataset.f !== 'all' && i.dataset.c !== b.dataset.f))
});
filter('#af', '.am-item'); filter('#gf', '.gi');

/* Enquire buttons preselect configuration */
$$('[data-cfg]').forEach(a => a.addEventListener('click', () => { $('#cfg2').value = a.dataset.cfg }));

/* 10. Form validation */
const phoneOK = v => { const d = v.replace(/\D/g, ''); return d.length >= 10 && d.length <= 13 };
$$('form').forEach(f => f.addEventListener('submit', e => {
  let ok = true; const bad = (n, c) => { const el = f.elements[n]; if (!el) return; const fl = el.closest('.field'); fl.classList.toggle('bad', c); if (c) ok = false };
  bad('name', !f.elements.name.value.trim()); bad('phone', !phoneOK(f.elements.phone.value));
  if (f.elements.email) bad('email', f.elements.email.value && !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(f.elements.email.value));
  if (!ok) e.preventDefault();
}));

/* 11. WhatsApp */
const wa = () => {
  const n = $('#f2 [name=name]').value || 'a prospective buyer', c = $('#cfg2').value || '2 / 3 BHK';
  $('#wa').href = `https://wa.me/${WA}?text=` + encodeURIComponent(`Hello, I am ${n}.\n\nI am interested in Shiv Kailasa.\n\nConfiguration: ${c}\n\nI would like to know more details and book a site visit.`)
};
wa(); $('#wa').addEventListener('click', wa);


// FAQ accordion: opening one closes the others
const faqItems = document.querySelectorAll('.faq details');
faqItems.forEach(item => {
  item.addEventListener('toggle', () => {
    if (item.open) {
      faqItems.forEach(other => {
        if (other !== item) other.removeAttribute('open');
      });
    }
  });
});



// Mobile fields: digits only, max 10 (works for every phone field, even ones added later)
document.addEventListener('keydown', function (e) {
  if (!e.target.matches('input[name="phone"]')) return;
  var allowed = ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];
  if (allowed.indexOf(e.key) > -1 || e.ctrlKey || e.metaKey) return;
  if (!/^\d$/.test(e.key)) e.preventDefault();
});
document.addEventListener('input', function (e) {
  if (!e.target.matches('input[name="phone"]')) return;
  e.target.value = e.target.value.replace(/\D/g, '').slice(0, 10);
});


