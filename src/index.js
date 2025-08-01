/**
 * Add custom media frame for Gemini Image Generation.
 */

/* global wp */
var frame = wp.media.view.MediaFrame.Select;
var __ = wp.i18n.__;

wp.media.view.MediaFrame.Select = frame.extend({
    initialize: function () {
        frame.prototype.initialize.apply(this, arguments);

        var State = wp.media.controller.State.extend({
            insert: function () {
                this.frame.close();
            },
        });

        this.states.add([
            new State({
                id: 'geminimedia',
                search: false,
                title: __('Gemini Image Generation'),
            }),
        ]);

        this.on(
            'content:render:geminimedia',
            this.renderGeminiMediaContent,
            this
        );
    },
    browseRouter: function (routerView) {
        routerView.set({
            upload: {
                text: wp.media.view.l10n.uploadFilesTitle,
                priority: 20,
            },
            geminimedia: {
                text: __('Gemini Image Generation'),
                priority: 30,
            },
            browse: {
                text: wp.media.view.l10n.mediaLibraryTitle,
                priority: 40,
            },
        });
    },
    renderGeminiMediaContent: function () {
        var GeminiMediaContent = wp.media.View.extend({
            tagName: 'div',
            className: 'geminimedia-content',
            template: wp.template('geminimedia'),
            active: false,
            toolbar: null,
            frame: null,
        });

        var view = new GeminiMediaContent();

        this.content.set(view);
    },
});

jQuery(document).ready(function ($) {
    // Handle the clear button click
    $(document).on('click', '#gemini-clear-button', function () {
        $('#gemini_prompt').val('');
        $('#gemini-preview').empty();
    });

    // Handle the image generation button click
    $(document).on('click', '#gemini-generate-button', function () {
        var $this = $(this);

        var prompt = $('#gemini_prompt').val();
        if (!prompt) {
            alert(__('Please enter a prompt.'));
            return;
        }

        const $preview = $('#gemini-preview');
        $preview.empty();

        $this.prop('disabled', true);
        $this.text(__('Generating...'));

        $.post(
            geminiImgGen.ajaxURL,
            {
                action: 'gemini_img_generate_image',
                prompt: prompt,
                nonce: geminiImgGen.nonce,
            },
            function (response) {
                if (!response.success) {
                    alert(__('Error: ') + response.data.message);
                    return;
                }

                const $infoMessage = $('<span>')
                    .text(__('Click on the image to upload'))
                    .attr('class', 'gemini-info-message');

                const $btn = $('<button>')
                    .attr('id', 'gemini-image-button')
                    .attr('data-title', response.data.title)
                    .attr('data-alt', response.data.alt);

                const $img = $('<img>')
                    .attr('src', response.data.image)
                    .attr('alt', response.data.alt || __('Generated Image'))
                    .attr('class', 'gemini-generated-image');

                $btn.append($img);

                $preview.empty().append($btn);
                $preview.append($infoMessage);
            }
        ).always(function () {
            $this.prop('disabled', false);
            $this.text(__('Generate'));
        });
    });

    // Handle the image button click
    $(document).on('click', '#gemini-image-button', function () {
        const $this = $(this);
        var title = $(this).data('title');
        var alt = $(this).data('alt');
        var imageSrc = $(this).find('img').attr('src');
        var infoMessage = $('.gemini-info-message');

        $this.prop('disabled', true);
        infoMessage.text(__('Inserting image...'));
        infoMessage.addClass('spinner is-active');

        $.post(
            geminiImgGen.ajaxURL,
            {
                action: 'gemini_img_upload_image',
                title: title,
                alt: alt,
                image: imageSrc,
                nonce: geminiImgGen.nonce,
            },
            function (response) {
                if (!response.success) {
                    alert(__('Error: ') + response.data.message);
                    return;
                }

                const $message = $('<div>')
                    .addClass('notice notice-success')
                    .text(
                        __(
                            'Image inserted successfully! Please refresh the page and check the media library.'
                        )
                    );

                $('#gemini-preview').empty().append($message);
            }
        )
            .fail(function () {
                alert(__('Failed to insert image.'));
            })
            .always(function () {
                $this.prop('disabled', false);
                infoMessage.text(__('Click on the image to upload'));
                infoMessage.removeClass('spinner is-active');
            });
    });
});
