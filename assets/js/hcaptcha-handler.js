if (typeof window.ElementorHcaptchaHandler === 'undefined') {
    class HcaptchaHandler extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    hcaptcha: '.elementor-hcaptcha:last',
                    submit: 'button[type="submit"]'
                }
            };
        }

        getDefaultElements() {
            const selectors = this.getDefaultSettings().selectors;
            const $hcaptcha = this.$element.find(selectors.hcaptcha);
            const $form = $hcaptcha.parents('form');
            const $submit = $form.find(selectors.submit);

            return { $hcaptcha, $form, $submit };
        }

        bindEvents() {
            this.waitForHcaptcha();
        }

        waitForHcaptcha() {
            if (window.hcaptcha && typeof window.hcaptcha.render === 'function') {
                this.renderHcaptcha();
            } else {
                setTimeout(() => this.waitForHcaptcha(), 350);
            }
        }

        renderHcaptcha() {
            const el = this.elements.$hcaptcha[0];

            if (!el || el.dataset.hcaptchaRendered === 'true') {
                return;
            }

            if (!jQuery(el).is(':visible')) {
                setTimeout(() => this.renderHcaptcha(), 200);
                return;
            }

            el.dataset.hcaptchaRendered = 'true';

            const sitekey = this.elements.$hcaptcha.data('sitekey');

            const widgetId = window.hcaptcha.render(el, {
                sitekey: sitekey,
                callback: (token) => {
                    let $input = this.elements.$form.find('[name="hcaptcha-response"]');
                    if (!$input.length) {
                        $input = jQuery('<input>', {
                            type: 'hidden',
                            name: 'hcaptcha-response'
                        }).appendTo(this.elements.$form);
                    }
                    $input.val(token);
                }
            });

            this.elements.$form.on('reset error', () => {
                if (window.hcaptcha && typeof window.hcaptcha.reset === 'function') {
                    window.hcaptcha.reset(widgetId);
                }
            });
        }
    }

    window.ElementorHcaptchaHandler = HcaptchaHandler;
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('form', window.ElementorHcaptchaHandler);
});