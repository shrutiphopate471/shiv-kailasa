<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$old = $flash['old'] ?? [];
require __DIR__ . '/header.php';
?>
<!-- Hero -->
<section class="hero" id="home">
    <!-- Background video -->
<!-- If index.html is in the root and the video is in /videos -->
<video class="hero-video" id="heroVideo" autoplay muted loop playsinline preload="metadata"
       poster="assets/images/IMG_6637.JPG.jpg"
       data-desktop="assets/images/IMG_4588.MP4"
       data-mobile="assets/images/IMG_7096.MP4"></video>

  <div class="wrap">
    <div class="grid">
      <div>
        <div class="eyebrow">Opp. AIIMS &amp; IIM · MIHAN, Nagpur</div>
        <h1><span>The Peak of</span><em>Happy Living</em></h1>
        <p class="sub">Two and three bedroom residences within thirty private acres, where more than a thousand families already call home.</p>
        <div class="hero-prices">
          <div><span>2 BHK</span><b>Starting ₹65L+</b></div>
          <div><span>3 BHK</span><b>Starting ₹75L+</b></div>
        </div>
        <div class="chips">
          <span>2 &amp; 3 BHK</span>
          <span>50+ Amenities</span>
          <span>ISO 9001:2015</span>
          <span>Since 2004</span>
        </div>
      </div>

      <?php if ($flash && ($flash['form'] ?? '') === 'lead' && !empty($flash['success'])): ?>
      <div class="card wizard thankyou">
        <div class="eyebrow mute">Thank you</div>
        <h3><?= !empty($flash['hot']) ? 'We\'ll call you within 24 hours' : 'We\'ll be in touch' ?></h3>
        <p class="note"><?= htmlspecialchars($flash['msg']) ?></p>
        <?php if (!empty($flash['wa'])): ?>
        <div style="margin-top:26px"><a class="btn block" href="<?= htmlspecialchars($flash['wa']) ?>" target="_blank" rel="noopener">Continue on WhatsApp</a></div>
        <?php endif; ?>
        <div class="fine"><a href="index.php#home">Submit another enquiry</a></div>
      </div>
      <?php else: ?>
      <form class="card wizard" method="post" action="process-enquiry.php" id="leadForm" novalidate>
        <div class="eyebrow mute">Private enquiry</div>
        <h3>Find your <em>place</em> at Shiv Kailasa</h3>
        <p class="note">Three quick steps — a senior adviser will call you personally.</p>
        <?php if ($flash && ($flash['form'] ?? '') === 'lead' && empty($flash['success'])): ?>
          <div class="alert error"><?= htmlspecialchars($flash['msg']) ?></div>
        <?php endif; ?>
        <input type="hidden" name="form_type" value="lead">
        <input type="hidden" name="purpose" id="purposeInput" value="<?= htmlspecialchars($old['purpose'] ?? '') ?>">
        <input type="hidden" name="configuration" id="configInput" value="<?= htmlspecialchars($old['configuration'] ?? '') ?>">
        <input type="hidden" name="preferred_location" id="prefLocInput" value="<?= htmlspecialchars($old['preferred_location'] ?? '') ?>">

        <div class="wiz-progress" id="wizProgress">
          <span class="on" data-s="1"><b>1</b><small>Details</small></span>
          <span data-s="2"><b>2</b><small id="stepTwoLabel">Residence</small></span>
          <span data-s="3"><b>3</b><small>Location</small></span>
        </div>

        <!-- Step 1: contact + purpose -->
        <div class="wiz-step on" data-step="1">
          <div class="field">
            <label>Full name</label>
            <input name="name" placeholder="Your full name" value="<?= htmlspecialchars($old['name'] ?? '') ?>">
            <div class="err">Please enter your name.</div>
          </div>
          <div class="field">
            <label>Email</label>
            <input name="email" type="email" placeholder="you@example.com" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
            <div class="err">Enter a valid email address.</div>
          </div>
          <div class="field">
            <label>Mobile</label>
            <div class="phone-wrap"><span class="cc">+91</span><input name="phone" type="text" placeholder="10-digit mobile number"
              inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" autocomplete="tel-national" value="<?= htmlspecialchars($old['phone'] ?? '') ?>"></div>
            <div class="help">Please share a number you can take calls on. We'll call you on this number.</div>
            <div class="err">Enter a valid mobile number.</div>
          </div>
          <div class="field">
            <label>What are you buying for?</label>
           <div id="purposePills">
              <button type="button" class="pill" data-val="Residential">
                  Residential
              </button>

              <button type="button" class="pill" data-val="Investment">
                  Investment
              </button>
          </div>
            <div class="err" id="purposeErr">Please choose one to continue.</div>
          </div>
        </div>

        <!-- Step 2: branch content -->
        <div class="wiz-step" data-step="2">
          <div class="branch" data-branch="Residential">
            <div class="field">
              <label>Configuration</label>
              <div class="pills" id="configPills">
                <button type="button" class="pill" data-val="2 BHK">2 BHK<small>starting ₹65L+</small></button>
                <button type="button" class="pill" data-val="3 BHK">3 BHK<small>starting ₹75L+</small></button>
              </div>
              <div class="err" id="configErr">Please choose a configuration.</div>
            </div>

            <div class="info-card">
              <b>Life at Shiv Kailasa</b>
              <p>A planned home in MIHAN, Nagpur's fastest-growing hub. Close to the airport, IT/SEZ offices and Samruddhi Expressway, with gated security, green open spaces and modern amenities, so you spend less time commuting and more time living.</p>
            </div>

            <div class="tabs">
              <div class="tab-bar">
                <button type="button" class="tab on" data-tab="nearby">📍 Nearby</button>
                <button type="button" class="tab" data-tab="daily">☀️ Daily life</button>
                <button type="button" class="tab" data-tab="amenities">✨ Amenities</button>
              </div>
              <div class="tab-panel on" data-panel="nearby">
                <ul class="nearby-list">
                  <li><span>Nagpur Airport</span><b>~10 min*</b></li>
                  <li><span>MIHAN SEZ / IT parks</span><b>~5 min*</b></li>
                  <li><span>AIIMS Nagpur</span><b>Opposite*</b></li>
                  <li><span>Samruddhi Expressway</span><b>Direct access*</b></li>
                </ul>
                <small class="fineprint">*Indicative, to be confirmed by the developer.</small>
              </div>
              <div class="tab-panel" data-panel="daily">
                <p>Supermarket, bank &amp; ATM, pharmacy, diagnostics and a 24/7 clinic within the gates. A private resident shuttle and direct access to Wardha Road keep everyday errands easy.</p>
              </div>
              <div class="tab-panel" data-panel="amenities">
                <p>Gated security, clubhouse, pools for men/women/children, gym, landscaped greens, children's play area and 50+ amenities woven into the estate.</p>
              </div>
            </div>
          </div>

          <div class="branch" data-branch="Investment">
            <div class="info-card price-card">
              <b>Investment starts at ₹65L</b>
              <p>MIHAN is one of Nagpur's fastest-growing zones — steady demand from SEZ professionals, doctors and faculty keeps rental and resale strong.</p>
            </div>
          </div>
        </div>

        <!-- Step 3: location + submit -->
        <div class="wiz-step" data-step="3">
          <div class="field">
            <label>Preferred location to buy</label>
            <div class="pills" id="locPills">
              <button type="button" class="pill" data-val="MIHAN, Nagpur">MIHAN, Nagpur</button>
              <button type="button" class="pill" data-val="Other area in Nagpur">Other area in Nagpur</button>
              <button type="button" class="pill" data-val="Outside Nagpur">Outside Nagpur</button>
            </div>
            <div class="help">Shiv Kailasa is located in MIHAN, Nagpur only.</div>
            <div class="mihan-note" id="mihanNote">Thanks! This project is in MIHAN, Nagpur only. We'll call you if an opportunity comes up in your preferred area.</div>
            <div class="err" id="locErr">Please choose a preferred location.</div>
          </div>
          <div class="field">
            <label>Your current location</label>
            <input name="current_city" placeholder="City you live in now" value="<?= htmlspecialchars($old['current_city'] ?? '') ?>">
          </div>

          <div class="field">
            <label>Book a site visit <i style="font-style:normal;text-transform:none;letter-spacing:0;opacity:.6">(optional)</i></label>
            <div class="help">Pick a slot and we'll confirm it with you on WhatsApp, along with everything you've shared here.</div>
          </div>
          <div class="row2">
            <div class="field">
              <label>Date</label>
              <input type="date" name="day" min="<?= date('Y-m-d') ?>">
            </div>
            <div class="field">
              <label>Time</label>
              <select name="time">
                <option value="">Any time</option>
                <option>Morning</option>
                <option>Afternoon</option>
                <option>Evening</option>
              </select>
            </div>
          </div>
        </div>

        <div class="wiz-actions">
          <button type="button" class="btn ghost" id="wizBack" style="display:none">Back</button>
          <button type="button" class="btn" id="wizNext">Next</button>
          <button type="submit" class="btn block" id="wizSubmit" style="display:none">Get a call back</button>
        </div>
        <div class="fine">Strictly confidential. Shared only with you.</div>
      </form>
      <?php endif; ?>
    </div>
  

  </div>

  <div class="scroll"></div>
</section>

<!-- Four quick facts -->
<!-- <section class="quickfacts-section">
  <div class="wrap">
    <div class="quickfacts">
      <div><b>10<i>min</i></b><span>Airport</span></div>
      <div><b>05<i>min</i></b><span>IT Parks</span></div>
      <div><b>Opp.</b><span>AIIMS</span></div>
      <div><b>24/7</b><span>Gated Security</span></div>
    </div>
  </div>
</section> -->

<section class="black-box p-0">
    <div class="container">

        <div class="stats">

            <div>
                <b data-to="30">0</b>
                <span>Private acres</span>
            </div>

            <div>
                <b data-to="13" data-suf="L+">0</b>
                <span>Sq. ft. township</span>
            </div>

            <div>
                <b data-to="2400" data-suf="+">0</b>
                <span>Residences</span>
            </div>

            <div>
                <b data-to="1000" data-suf="+">0</b>
                <span>Families in residence</span>
            </div>

            <div>
                <b data-to="50" data-suf="+">0</b>
                <span>Club amenities</span>
            </div>

        </div>

    </div>
</section>
<!-- About -->
<section class="light about section-lg" id="about"><div class="wrap"><div class="grid">
  <div class="ph reveal-left"><img src="assets/images/IMG_6628.JPG.jpg" alt="The central green" onerror="this.style.display='none'"><span class="tag">The central green</span></div>
  <div class="reveal-right">
    <div class="eyebrow">I &nbsp; The estate</div>
    <h2>A township <em>beyond comparison</em></h2>
    <p class="lead">Wellness, leisure, daily conveniances and round-the-clock care — all within our own gates, so every hour returns to what matters.</p>
    <div class="lines">
      <div><h4>A living community</h4><p>1000+ families already in residence — established, not a promise on paper.</p></div>
      <div><h4>Self-sustained</h4><p>Supermarket, bank &amp; ATM, pharmacy, diagnostics and a 24/7 clinic on the estate.</p></div>
      <div><h4>Considered mobility</h4><p>A private shuttle for residents and direct access to Wardha Road.</p></div>
    </div>
  </div>
</div></div></section>


<!-- Residences -->
<section class="light2 section-lg" id="residences">
  <div class="wrap">

    <div class="eyebrow mute" style="color:var(--gold)">
      II &nbsp; The residences
    </div>

    <div class="head" style="color:var(--ink)">
      <h2 style="color:var(--ink)">
        Homes <em>crafted for life</em>
      </h2>
      <p style="color:#6f695d">
        Light-filled plans with generous living rooms, private bedrooms and views across open greens.
      </p>
    </div>

    <div class="res">

      <!-- 2 BHK -->
      <article class="rcard reveal">

        <div class="ph" data-gal="r2">
          <img
            src="assets/images/IMG_6637.JPG.jpg"
            alt="2 BHK Residence"
            onerror="this.style.display='none'"
          >
        </div>

        <div class="rbody">
          <div class="eyebrow">The signature</div>

          <h3>Two Bedroom Residence</h3>

          <p>
            Composed living and dining with two private bedrooms —
            a first home or a considered investment.
          </p>

          <div class="facts">
            <div>
              <small>Carpet area</small>
              <b>1000 sq. ft.</b>
            </div>

            <div>
              <small>Bedrooms</small>
              <b>Two</b>
            </div>

            <div>
              <small>Price</small>
              <b>On request</b>
            </div>
          </div>

          <div class="acts">
            <button type="button" class="btn ghost" data-gal="r2">
  View photos
</button>

            <a
              href="#contact"
              class="btn"
              data-cfg="Two Bedroom Residence"
            >
              Enquire now
            </a>
          </div>
        </div>

      </article>


      <!-- 3 BHK -->
      <article class="rcard reveal">

        <div class="ph" data-gal="r3">

          <img
            src="assets/images/IMG_6638.JPGnew6.jpg"
            alt="3 BHK Residence"
            onerror="this.style.display='none'"
          >

          <span
            class="tag"
            style="background:#2a2620"
          >
            Most sought after
          </span>

        </div>

        <div class="rbody">

          <div class="eyebrow">The grand</div>

          <h3>Three Bedroom Residence</h3>

          <p>
            Grand living spaces and three bedrooms, with garden-facing
            homes for those who want the view.
          </p>

          <div class="facts">

            <div>
              <small>Carpet area</small>
              <b>1200 sq. ft.</b>
            </div>

            <div>
              <small>Bedrooms</small>
              <b>Three</b>
            </div>

            <div>
              <small>Price</small>
              <b>On request</b>
            </div>

          </div>

          <div class="acts">

            <button type="button" class="btn ghost" data-gal="r3">
  View photos
</button>

            <a
              href="#contact"
              class="btn"
              data-cfg="Three Bedroom Residence"
            >
              Enquire now
            </a>

          </div>

        </div>

      </article>

    </div>


    <!-- Commercial -->
    <div class="invest">

      <div>

        <div class="eyebrow">
          For investors · Kailasa commercial complex
        </div>

        <div class="serif">
          Retail and commercial addresses with a built-in catchment
          of 2,400+ homes.
        </div>

      </div>

      <a href="#contact" class="btn">
        Request pricing
      </a>

    </div>

  </div>
</section>



<!-- Location -->
<section class="loc section-lg" id="location"><div class="wrap">
  <div class="eyebrow mute">III &nbsp; The address</div>
  <div class="head"><h2>The heart of <em>MIHAN</em></h2><p>Neighbours to AIIMS and IIM — minutes from the MIHAN SEZ — an address of standing, and of enduring demand.</p></div>
  <div class="dist">
    <div><b>Opp.</b><span>AIIMS &amp; IIM</span></div><div><b>05<i>min</i></b><span>MIHAN SEZ</span></div>
    <div><b>10<i>min</i></b><span>Airport</span></div><div><b>01<i>km</i></b><span>Metro</span></div><div><b>Direct</b><span>Wardha Road</span></div>
  </div>
  <div class="pers">
    <div><h4>For the professional</h4><p>Walk to AIIMS and IIM; the MIHAN SEZ is five minutes, the airport is ten.</p></div>
    <div><h4>For the family</h4><p>Healthcare, daily needs and open greens within the gates; the metro a kilometre away.</p></div>
    <div><h4>For the investor</h4><p>Doctors, faculty and SEZ professionals bring steady demand to live and to lease.</p></div>
  </div>
  <div class="map"><iframe loading="lazy" title="Map" src="https://www.google.com/maps?q=AIIMS+Nagpur+MIHAN&output=embed"></iframe></div>
  <a class="btn ghost mapbtn" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=Shiv+Kailasa+MIHAN+Nagpur">Open in Google Maps</a>
</div></section>

<!-- Amenities -->
<section class="am section-lg" id="amenities"><div class="wrap">
  <div class="eyebrow mute">IV &nbsp; The club</div>
  <div class="head"><h2>50+ amenities, <em>and more</em></h2><p>Quietly woven into the estate — for wellness, for leisure and for everyday ease.</p></div>
  <div class="feat">
  <div>
    <img src="assets/images/pool.png" alt="The Pools">
    <div class="feat-text">
      <h3>The Pools</h3>
      <small>For men, women &amp; children</small>
    </div>
  </div>
  <div>
    <img src="assets/images/gym.jpg" alt="The Gym">
    <div class="feat-text">
      <h3>The Gym</h3>
      <small>Modern fitness center &amp; workout facilities</small>
    </div>
  </div>
</div>
  
  <div class="amgrid" id="ag">
    <div class="am-item" data-c="wellness"><h4>Wellness</h4><p>Gymnasium · Yoga &amp; meditation · Jogging &amp; cycling track · Senior lounge</p></div>
    <div class="am-item" data-c="sports"><h4>Leisure</h4><p>Clubhouse · Cricket pitch · Table tennis · Billiards · Chess &amp; carrom</p></div>
    <div class="am-item" data-c="nature"><h4>Landscape</h4><p>Central garden · Water bodies &amp; fountains · Event lawns · Children's park</p></div>
    <div class="am-item" data-c="daily"><h4>Conveniences</h4><p>Supermarket · Pharmacy · Salon · Bank &amp; ATM · Lal Pathulia · Library</p></div>
    <div class="am-item" data-c="security"><h4>Care &amp; security</h4><p>24/7 CCTV · 24/7 clinic &amp; ambulance · Power backup · High-speed lifts</p></div>
    <div class="am-item" data-c="nature"><h4>Sustainability</h4><p>Solar utilities · Rainwater harvesting · Sewage treatment · Resident shuttle</p></div>
    <div class="am-item" data-c="sports"><h4>Swimming pool</h4><p>Separate pools for men, women and children</p></div>
    <div class="am-item" data-c="security"><h4>Parking</h4><p>Dedicated resident and visitor parking</p></div>
  </div>
</div></section>

<section class="gallery-section" id="gallery">
  <div class="container">

    <div class="section-heading">
      <span class="subtitle">Explore Our Spaces</span>
      <h2>Gallery</h2>
      <p>
        Take a glimpse into the thoughtfully designed spaces,
        beautiful landscapes and lifestyle amenities.
      </p>
    </div>

    <!-- Gallery Grid -->
    <div class="gallery-grid" id="galleryGrid">

      <div class="gallery-item gallery-large" data-src="assets/images/IMG_4786.PNG" data-sub-html="<h4>Beautiful Architecture</h4><p>Exterior</p>">
        <img src="assets/images/IMG_4786.PNG" alt="Exterior">
        <div class="gallery-overlay">
          <span>Exterior</span>
          <h3>Beautiful Architecture</h3>
          <button type="button" class="view-btn">↗</button>
        </div>
      </div>

      <div class="gallery-item" data-src="assets/images/IMG_4790.PNG" data-sub-html="<h4>Elegant Living</h4><p>Interior</p>">
        <img src="assets/images/IMG_4790.PNG" alt="Interior">
        <div class="gallery-overlay">
          <span>Interior</span>
          <h3>Elegant Living</h3>
          <button type="button" class="view-btn">↗</button>
        </div>
      </div>

      <div class="gallery-item" data-src="assets/images/IMG_4749-MP4-10-08-2026_10_30_AM.png" data-sub-html="<h4>Green Spaces</h4><p>Landscape</p>">
        <img src="assets/images/IMG_4749-MP4-10-08-2026_10_30_AM.png" alt="Landscape">
        <div class="gallery-overlay">
          <span>Landscape</span>
          <h3>Green Spaces</h3>
          <button type="button" class="view-btn">↗</button>
        </div>
      </div>

      <div class="gallery-item" data-src="assets/images/IMG_4797.PNG" data-sub-html="<h4>The Clubhouse</h4><p>Amenities</p>">
        <img src="assets/images/IMG_4797.PNG" alt="Clubhouse">
        <div class="gallery-overlay">
          <span>Amenities</span>
          <h3>The Clubhouse</h3>
          <button type="button" class="view-btn">↗</button>
        </div>
      </div>

      <div class="gallery-item gallery-tall" data-src="assets/images/IMG_4785.PNG" data-sub-html="<h4>Central Green</h4><p>Landscape</p>">
        <img src="assets/images/IMG_4785.PNG" alt="Garden">
        <div class="gallery-overlay">
          <span>Landscape</span>
          <h3>Central Green</h3>
          <button type="button" class="view-btn">↗</button>
        </div>
      </div>

    </div>

  </div>
</section>


<!-- Gallery
<section class="gal section-lg" id="gallery"><div class="wrap">
  <div class="eyebrow">V &nbsp; The gallery</div>
  <div class="head" style="color:var(--ink)"><h2 style="color:var(--ink)">Life at <em>Shiv Kailasa</em></h2><p style="color:#6f695d">A look across the estate — add your licensed photographs in the images folder.</p></div>
  
  <div class="ggrid" id="gg"></div>
</div></section> -->

<!-- Developer + FAQ -->
<section class="light dev section-lg"><div class="wrap"><div class="grid">
  <div class="reveal">
    <div class="eyebrow">V &nbsp; The developer</div>
    <h2>Two decades of <em>building trust</em></h2>
    <p class="p">Since 2004, GSBPL Group has built on integrity, innovation and trust. Shiv Kailasa is that conviction at the scale of a township.</p>
    <div class="dstats"><div><b data-to="20" data-suf="+">0</b><small>Years of delivery</small></div><div><b>ISO</b><small>9001:2015 certified</small></div><div><b data-to="1000" data-suf="+">0</b><small>Families settled</small></div><div><b>MahaRERA</b><small>Registration no. [—]</small></div></div>
  </div>
  <div class="reveal">
    <div class="eyebrow">VI &nbsp; Good to know</div>
    <div class="faq">
      <details name="faq" open><summary>Where exactly is Shiv Kailasa?</summary><p>Opposite AIIMS and IIM in MIHAN, Nagpur 441108, with direct access to Wardha Road.</p></details>
      <details name="faq"><summary>Which residences are available?</summary><p>Two and three bedroom residences. Availability is shared with the private portfolio.</p></details>
      <details name="faq"><summary>Are families already living here?</summary><p>Yes — more than a thousand families are already in residence.</p></details>
      <details name="faq"><summary>Why is pricing shared privately?</summary><p>Pricing varies by floor and view, so an adviser shares current availability with you directly.</p></details>
      <details name="faq"><summary>When can I visit?</summary><p>Viewings are by appointment, every day, at a time of your choosing.</p></details>
    </div>
  </div>
</div></div></section>
<!-- Appointment -->
<section class="appt section-lg" id="contact"><div class="wrap"><div class="grid">
  <div>
    <div class="eyebrow mute">VII &nbsp; Appointment</div>
    <h2>Come home to <em>Shiv Kailasa</em></h2>
    <p class="sub">Four quiet steps, each guided personally by a senior adviser.</p>
    <div class="steps">
      <div><small>i.</small><h4>Private enquiry</h4><p>Your adviser reaches out personally.</p></div>
      <div><small>ii.</small><h4>The portfolio</h4><p>Plans, availability and pricing.</p></div>
      <div><small>iii.</small><h4>Private viewing</h4><p>At a time of your choosing.</p></div>
      <div><small>iv.</small><h4>Your residence</h4><p>Select and reserve your home.</p></div>
    </div>
  </div>
  <form class="card" method="post" action="process-enquiry.php" id="f2" novalidate>
    <div class="eyebrow mute">Reserve a viewing</div>
    <h3>At your <em style="color:#e9e2d3">convenience</em></h3>
    <?php if ($flash && $flash['form'] === 'viewing'): ?><div class="alert error"><?= htmlspecialchars($flash['msg']) ?></div><?php endif; ?>
    <input type="hidden" name="form_type" value="viewing">
    <div class="field"><label>Name</label><input name="name" placeholder="Your full name"><div class="err">Please enter your name.</div></div>
    <div class="field"><label>Mobile</label><input name="phone" type="tel" placeholder="10-digit mobile number"
           inputmode="numeric" maxlength="10" pattern="[6-9][0-9]{9}" placeholder="+91"><div class="err">Enter a valid mobile number.</div></div>
    <div class="field"><label>Email (optional)</label><input name="email" type="email" placeholder="you@example.com"><div class="err">Enter a valid email address.</div></div>
    <div class="field"><label>Preferred configuration</label><select name="configuration" id="cfg2"><option value="">No preference</option><option>Two Bedroom Residence</option><option>Three Bedroom Residence</option></select></div>
    <div class="row2"><div class="field"><label>Day</label><select name="day"><option>Saturday</option><option>Sunday</option><option>Weekday</option></select></div>
    <div class="field"><label>Time</label><select name="time"><option>Morning</option><option>Afternoon</option><option>Evening</option></select></div></div>
    <div class="field"><label>Message (optional)</label><textarea name="message" rows="2"></textarea></div>
    <div style="margin-top:26px"><button class="btn block">Reserve my viewing</button></div>
    <div class="fine">Daily 9 AM – 7 PM · Strictly confidential</div>
  </form>
</div></div></section>

<?php require __DIR__ . '/footer.php'; ?>
