/*!
 * Contronic theme — vanilla JS, no dependencies.
 * Navigation · header state · hero slider · reveal on scroll · lightbox · inquiry form.
 */
(function () {
	'use strict';

	var doc = document;
	var root = doc.documentElement;
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	root.classList.add('js');

	function $(sel, ctx) { return (ctx || doc).querySelector(sel); }
	function $$(sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); }

	/* ---------------------------------------------------------------
	 * Header: scrolled state, hide on scroll down, progress bar.
	 * ------------------------------------------------------------- */
	var header = $('[data-header]');
	var lastY = window.scrollY;
	var ticking = false;
	function onScroll() {
		var y = window.scrollY;
		if (header) {
			header.classList.toggle('is-scrolled', y > 40);
			var navOpen = doc.body.classList.contains('nav-open');
			header.classList.toggle('is-hidden', !navOpen && y > 400 && y > lastY + 4 && !header.contains(doc.activeElement));
			if (y < lastY - 4) { header.classList.remove('is-hidden'); }
		}
		var max = root.scrollHeight - window.innerHeight;
		root.style.setProperty('--progress', max > 0 ? (y / max).toFixed(4) : 0);
		lastY = y;
		ticking = false;
	}
	window.addEventListener('scroll', function () {
		if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; }
	}, { passive: true });
	onScroll();

	/* ---------------------------------------------------------------
	 * Mobile navigation drawer (focus-trapped, Esc to close).
	 * ------------------------------------------------------------- */
	var nav = $('[data-nav]');
	var toggle = $('[data-nav-toggle]');
	var scrim = $('.nav-scrim');
	var mq = window.matchMedia('(max-width: 1080px)');

	function setNav(open) {
		if (!nav || !toggle) { return; }
		nav.classList.toggle('is-open', open);
		doc.body.classList.toggle('nav-open', open);
		toggle.setAttribute('aria-expanded', String(open));
		if (scrim) { scrim.hidden = !open; }
		if (open) {
			var first = $('a, button', nav);
			if (first) { first.focus(); }
		} else {
			toggle.focus({ preventScroll: true });
		}
	}
	if (toggle) {
		toggle.addEventListener('click', function () { setNav(!nav.classList.contains('is-open')); });
	}
	$$('[data-nav-close]').forEach(function (el) { el.addEventListener('click', function () { setNav(false); }); });
	if (nav) {
		nav.addEventListener('click', function (e) {
			var a = e.target.closest('a');
			if (a && mq.matches) { setNav(false); }
		});
		nav.addEventListener('keydown', function (e) {
			if (!nav.classList.contains('is-open')) { return; }
			if (e.key === 'Escape') { setNav(false); }
			if (e.key === 'Tab') {
				var items = $$('a[href], button:not([disabled])', nav).filter(function (el) { return el.offsetParent !== null; });
				var first = items[0];
				var last = items[items.length - 1];
				if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
				else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
			}
		});
	}
	mq.addEventListener('change', function (e) { if (!e.matches) { setNav(false); } });

	/* ---------------------------------------------------------------
	 * Hero slider: fade, autoplay, pause on hover/focus/hidden tab,
	 * keyboard + swipe, respects reduced motion.
	 * ------------------------------------------------------------- */
	$$('[data-slider]').forEach(function (slider) {
		var slides = $$('[data-slide]', slider);
		if (slides.length < 2) { return; }
		var dots = $$('[data-slider-dot]', slider);
		var pauseBtn = $('[data-slider-pause]', slider);
		var interval = 7000;
		var index = 0;
		var timer = null;
		var userPaused = reduceMotion;
		slider.style.setProperty('--slide-ms', interval + 'ms');

		function go(i) {
			index = (i + slides.length) % slides.length;
			slides.forEach(function (s, n) {
				var active = n === index;
				s.classList.toggle('is-active', active);
				if (active) { s.removeAttribute('aria-hidden'); } else { s.setAttribute('aria-hidden', 'true'); }
				$$('a, button', s).forEach(function (el) { el.tabIndex = active ? 0 : -1; });
			});
			dots.forEach(function (d, n) {
				if (n === index) { d.setAttribute('aria-current', 'true'); } else { d.removeAttribute('aria-current'); }
				// Restart progress animation.
				var bar = d.querySelector('i');
				if (bar) { bar.style.animation = 'none'; void bar.offsetWidth; bar.style.animation = ''; }
			});
			schedule();
		}
		function schedule() {
			window.clearTimeout(timer);
			if (!userPaused && !slider.classList.contains('is-hover')) {
				timer = window.setTimeout(function () { go(index + 1); }, interval);
			}
		}
		function setPaused(p) {
			userPaused = p;
			slider.classList.toggle('is-paused', p);
			if (pauseBtn) { pauseBtn.setAttribute('aria-label', p ? 'Play slideshow' : 'Pause slideshow'); }
			schedule();
		}

		var prev = $('[data-slider-prev]', slider);
		var next = $('[data-slider-next]', slider);
		if (prev) { prev.addEventListener('click', function () { go(index - 1); }); }
		if (next) { next.addEventListener('click', function () { go(index + 1); }); }
		dots.forEach(function (d) { d.addEventListener('click', function () { go(parseInt(d.getAttribute('data-slider-dot'), 10)); }); });
		if (pauseBtn) { pauseBtn.addEventListener('click', function () { setPaused(!userPaused); }); }

		slider.addEventListener('mouseenter', function () { slider.classList.add('is-hover', 'is-paused'); window.clearTimeout(timer); });
		slider.addEventListener('mouseleave', function () { slider.classList.remove('is-hover'); slider.classList.toggle('is-paused', userPaused); schedule(); });
		slider.addEventListener('focusin', function () { window.clearTimeout(timer); });
		slider.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { go(index - 1); }
			if (e.key === 'ArrowRight') { go(index + 1); }
		});
		doc.addEventListener('visibilitychange', function () { if (doc.hidden) { window.clearTimeout(timer); } else { schedule(); } });

		var startX = null;
		slider.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
		slider.addEventListener('touchend', function (e) {
			if (startX === null) { return; }
			var dx = e.changedTouches[0].clientX - startX;
			if (Math.abs(dx) > 50) { go(index + (dx < 0 ? 1 : -1)); }
			startX = null;
		});

		if (reduceMotion) { slider.classList.add('is-paused'); }
		go(0);
	});

	// Scroll cue: go to the next section after the hero.
	$$('.hero__scroll').forEach(function (cue) {
		cue.addEventListener('click', function (e) {
			var hero = cue.closest('.hero');
			var target = hero && hero.nextElementSibling;
			if (target) {
				e.preventDefault();
				target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth' });
			}
		});
	});

	/* ---------------------------------------------------------------
	 * Reveal on scroll.
	 * ------------------------------------------------------------- */
	var reveals = $$('.reveal');
	if ('IntersectionObserver' in window && !reduceMotion) {
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('is-visible');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.08, rootMargin: '0px 0px -6% 0px' });
		reveals.forEach(function (el) { io.observe(el); });
	} else {
		reveals.forEach(function (el) { el.classList.add('is-visible'); });
	}

	/* ---------------------------------------------------------------
	 * Lightbox for [data-lightbox] galleries (native <dialog>).
	 * ------------------------------------------------------------- */
	var dialog = $('[data-lightbox-dialog]');
	if (dialog && typeof dialog.showModal === 'function') {
		var img = $('img', dialog);
		var cap = $('figcaption', dialog);
		var group = [];
		var current = 0;
		var opener = null;

		function show(i) {
			current = (i + group.length) % group.length;
			var a = group[current];
			var thumb = a.querySelector('img');
			img.src = a.getAttribute('href');
			img.alt = thumb ? thumb.alt : '';
			cap.textContent = a.getAttribute('data-caption') || '';
			$('[data-lightbox-prev]', dialog).hidden = group.length < 2;
			$('[data-lightbox-next]', dialog).hidden = group.length < 2;
		}
		doc.addEventListener('click', function (e) {
			var a = e.target.closest('[data-lightbox] a');
			if (!a || e.metaKey || e.ctrlKey) { return; }
			e.preventDefault();
			opener = a;
			group = $$('a', a.closest('[data-lightbox]'));
			show(group.indexOf(a));
			dialog.showModal();
		});
		$('[data-lightbox-close]', dialog).addEventListener('click', function () { dialog.close(); });
		$('[data-lightbox-prev]', dialog).addEventListener('click', function () { show(current - 1); });
		$('[data-lightbox-next]', dialog).addEventListener('click', function () { show(current + 1); });
		dialog.addEventListener('click', function (e) { if (e.target === dialog) { dialog.close(); } });
		dialog.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') { show(current - 1); }
			if (e.key === 'ArrowRight') { show(current + 1); }
		});
		dialog.addEventListener('close', function () { img.src = 'data:image/gif;base64,R0lGODlhAQABAAAAACw='; if (opener) { opener.focus(); } });
	}

	/* ---------------------------------------------------------------
	 * Inquiry forms: client-side validation + AJAX submit.
	 * Falls back to a normal POST without JS.
	 * ------------------------------------------------------------- */
	var i18n = window.ctrTheme || {};

	function fieldError(form, name, message) {
		var input = form.querySelector('[name="' + name + '"]');
		var holder = form.querySelector('[data-error-for="' + name + '"]');
		if (holder) { holder.textContent = message || ''; }
		if (input) {
			var wrap = input.closest('.field');
			if (wrap) { wrap.classList.toggle('has-error', !!message); }
			if (message) {
				input.setAttribute('aria-invalid', 'true');
				if (holder) {
					holder.id = holder.id || (input.id + '-error');
					input.setAttribute('aria-describedby', holder.id);
				}
			} else {
				input.removeAttribute('aria-invalid');
			}
		}
	}

	function validate(form) {
		var ok = true;
		var first = null;
		var rules = {
			ctr_name: function (v) { return v.trim().length >= 2 ? '' : 'Please enter your name.'; },
			ctr_email: function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()) ? '' : 'Please enter a valid email address.'; },
			ctr_phone: function (v) { return v === '' || /^[0-9+\-\s().]{7,20}$/.test(v.trim()) ? '' : 'Please enter a valid phone number.'; },
			ctr_message: function (v) { return v.trim().length >= 10 ? '' : 'Please tell us a little more (at least 10 characters).'; }
		};
		Object.keys(rules).forEach(function (name) {
			var input = form.querySelector('[name="' + name + '"]');
			if (!input) { return; }
			var msg = rules[name](input.value);
			fieldError(form, name, msg);
			if (msg) { ok = false; first = first || input; }
		});
		var consent = form.querySelector('[name="ctr_consent"]');
		if (consent) {
			var cmsg = consent.checked ? '' : 'Please confirm you agree to be contacted.';
			fieldError(form, 'ctr_consent', cmsg);
			if (cmsg) { ok = false; first = first || consent; }
		}
		if (first) { first.focus(); }
		return ok;
	}

	function setStatus(form, type, message) {
		var box = form.querySelector('[data-inquiry-status]');
		if (!box) { return; }
		box.hidden = !message;
		box.innerHTML = '';
		if (message) {
			var p = doc.createElement('p');
			p.className = type === 'success' ? 'is-success' : 'is-error';
			p.textContent = message;
			box.appendChild(p);
		}
	}

	$$('[data-inquiry]').forEach(function (form) {
		form.addEventListener('input', function (e) {
			if (e.target.name && e.target.closest('.has-error')) { fieldError(form, e.target.name, ''); }
		});
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			if (!validate(form)) { return; }
			if (!window.fetch || !window.FormData) { form.submit(); return; }

			var btn = form.querySelector('[data-inquiry-submit]');
			var label = btn ? btn.querySelector('span') : null;
			var original = label ? label.textContent : '';
			form.classList.add('is-sending');
			if (label) { label.textContent = i18n.sending || 'Sending…'; }
			setStatus(form, '', '');

			fetch(form.action, {
				method: 'POST',
				body: new FormData(form),
				headers: { Accept: 'application/json' },
				credentials: 'same-origin'
			})
				.then(function (res) { return res.json().catch(function () { return { ok: false }; }); })
				.then(function (data) {
					if (data && data.ok) {
						form.reset();
						setStatus(form, 'success', data.message);
						form.dispatchEvent(new CustomEvent('ctr:inquiry-sent', { bubbles: true }));
					} else {
						if (data && data.errors) {
							Object.keys(data.errors).forEach(function (name) { fieldError(form, name, data.errors[name]); });
						}
						setStatus(form, 'error', (data && data.message) || i18n.error);
					}
				})
				.catch(function () { setStatus(form, 'error', i18n.error); })
				.then(function () {
					form.classList.remove('is-sending');
					if (label) { label.textContent = original; }
					var box = form.querySelector('[data-inquiry-status]');
					if (box && !box.hidden) { box.scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' }); }
				});
		});
	});
})();
