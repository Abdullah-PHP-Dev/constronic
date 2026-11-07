/* Contronic Core — media pickers and SEO preview for meta boxes. */
(function ($) {
	'use strict';

	// Single image / file picker.
	$(document).on('click', '.ctr-media__select', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.ctr-media');
		var isImage = $wrap.data('type') === 'image';
		var frame = wp.media({
			title: isImage ? 'Select image' : 'Select file',
			button: { text: 'Use this' },
			library: isImage ? { type: 'image' } : {},
			multiple: false
		});
		frame.on('select', function () {
			var att = frame.state().get('selection').first().toJSON();
			$wrap.find('.ctr-media__input').val(att.id).trigger('change');
			var html = isImage
				? '<img src="' + ((att.sizes && att.sizes.thumbnail) ? att.sizes.thumbnail.url : att.url) + '" alt="">'
				: '<span>' + $('<div>').text(att.filename).html() + '</span>';
			$wrap.find('.ctr-media__preview').html(html);
			$wrap.find('.ctr-media__remove').prop('hidden', false);
		});
		frame.open();
	});

	$(document).on('click', '.ctr-media__remove', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.ctr-media');
		$wrap.find('.ctr-media__input').val('').trigger('change');
		$wrap.find('.ctr-media__preview').empty();
		$(this).prop('hidden', true);
	});

	// Gallery picker (uses the native WP gallery editor, keeps order).
	$(document).on('click', '.ctr-gallery__edit', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.ctr-gallery');
		var $input = $wrap.find('.ctr-gallery__input');
		var ids = $input.val() ? $input.val().split(',').map(Number) : [];
		var frame;
		if (ids.length) {
			var shortcode = '[gallery ids="' + ids.join(',') + '"]';
			frame = wp.media.gallery.edit(shortcode);
		} else {
			frame = wp.media({
				frame: 'post',
				state: 'gallery-library',
				library: { type: 'image' },
				multiple: true
			});
			frame.setState('gallery-library');
		}
		frame.on('update', function (selection) {
			var models = selection.models || selection;
			var newIds = [];
			var html = '';
			models.forEach(function (m) {
				var a = m.toJSON();
				newIds.push(a.id);
				var src = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url;
				html += '<li data-id="' + a.id + '"><img src="' + src + '" alt=""></li>';
			});
			$input.val(newIds.join(','));
			$wrap.find('.ctr-gallery__list').html(html);
			$wrap.find('.ctr-gallery__clear').prop('hidden', !newIds.length);
		});
		if (!ids.length) {
			frame.open();
		}
	});

	$(document).on('click', '.ctr-gallery__clear', function (e) {
		e.preventDefault();
		var $wrap = $(this).closest('.ctr-gallery');
		$wrap.find('.ctr-gallery__input').val('');
		$wrap.find('.ctr-gallery__list').empty();
		$(this).prop('hidden', true);
	});

	// Drag to reorder gallery thumbnails.
	$(function () {
		$('.ctr-gallery__list').sortable({
			update: function () {
				var $list = $(this);
				var ids = $list.children().map(function () { return $(this).data('id'); }).get();
				$list.closest('.ctr-gallery').find('.ctr-gallery__input').val(ids.join(','));
			}
		});
	});

	// Live SEO snippet preview with character counts.
	function seoPreview() {
		var $box = $('#ctr_seo');
		if (!$box.length) { return; }
		var $t = $('#ctr-field-seo_title');
		var $d = $('#ctr-field-seo_description');
		function docTitle() {
			var t = $('.editor-post-title__input, #title').first();
			return t.is('input') ? t.val() : t.text();
		}
		function update() {
			var title = $t.val() || docTitle() || '';
			var desc = $d.val() || '';
			$box.find('.ctr-seo-preview__title').text(title || 'Page title');
			$box.find('.ctr-seo-preview__desc').text(desc || 'The excerpt or opening text will be used as the description.');
			$t.next('.ctr-count').remove();
			$d.next('.ctr-count').remove();
			$t.after('<span class="ctr-count' + ($t.val().length > 60 ? ' is-over' : '') + '">' + $t.val().length + ' / 60</span>');
			$d.after('<span class="ctr-count' + ($d.val().length > 160 ? ' is-over' : '') + '">' + $d.val().length + ' / 160</span>');
		}
		$t.add($d).on('input', update);
		update();
	}
	$(window).on('load', seoPreview);
})(jQuery);
