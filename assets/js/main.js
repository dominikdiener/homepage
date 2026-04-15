// ===== SCROLL REVEAL =====
const reveals = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), i * 80);
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.08 });
reveals.forEach(el => revealObserver.observe(el));

// ===== NAV SCROLL EFFECT =====
const nav = document.querySelector('nav');
window.addEventListener('scroll', () => {
  if (nav) {
    nav.style.background = window.scrollY > 40
      ? 'rgba(15,26,28,0.98)'
      : 'rgba(15,26,28,0.88)';
  }
});

// ===== MOBILE NAV =====
function closeNav() {
  document.querySelector('.nav-hamburger')?.classList.remove('active');
  document.querySelector('.nav-links')?.classList.remove('open');
  document.body.classList.remove('nav-open');
}

function toggleNav(btn) {
  btn.classList.toggle('active');
  document.querySelector('.nav-links').classList.toggle('open');
  document.body.classList.toggle('nav-open');
}

// ===== ACTIVE NAV LINK =====
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.nav-links a').forEach(link => {
  if (link.getAttribute('href') === currentPage) {
    link.classList.add('active');
  }
});

// ===== SCROLL-BASED SECTION ACTIVE =====
(function () {
  const sectionIds = ['how', 'value', 'technik'];
  const sections = sectionIds.map(id => document.getElementById(id)).filter(Boolean);
  if (!sections.length) return;

  function updateActiveNav() {
    const scrollY = window.scrollY + 120;
    let activeId = '';
    sections.forEach(s => { if (s.offsetTop <= scrollY) activeId = s.id; });
    document.querySelectorAll('.nav-links a[href^="#"]').forEach(a => {
      a.classList.toggle('active', a.getAttribute('href') === '#' + activeId);
    });
  }

  window.addEventListener('scroll', updateActiveNav, { passive: true });
  updateActiveNav();
})();

// ===== HOW-STEP ACCORDION =====
function toggleStep(el) {
  const detail = el.querySelector('.how-step-detail');
  if (el.classList.contains('active')) {
    detail.style.maxHeight = null;
    el.classList.remove('active');
  } else {
    // close other open steps
    el.parentElement.querySelectorAll('.how-step.active').forEach(s => {
      s.querySelector('.how-step-detail').style.maxHeight = null;
      s.classList.remove('active');
    });
    el.classList.add('active');
    detail.style.maxHeight = detail.scrollHeight + 'px';
  }
}

// ===== CONTACT FORM =====
const form = document.getElementById('contact-form');
if (form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const msg = document.getElementById('success-msg');
    const errMsg = document.getElementById('error-msg');
    btn.textContent = 'Wird gesendet…';
    btn.disabled = true;
    if (msg) msg.style.display = 'none';
    if (errMsg) errMsg.style.display = 'none';

    fetch('/api/contact.php', {
      method: 'POST',
      body: new FormData(form)
    })
    .then(r => r.json().then(data => ({ ok: r.ok, data })))
    .then(({ ok, data }) => {
      btn.textContent = 'Anfrage senden';
      btn.disabled = false;
      if (ok && data.success) {
        form.reset();
        if (msg) msg.style.display = 'block';
      } else {
        if (errMsg) errMsg.style.display = 'block';
      }
    })
    .catch(() => {
      btn.textContent = 'Anfrage senden';
      btn.disabled = false;
      if (errMsg) errMsg.style.display = 'block';
    });
  });
}
