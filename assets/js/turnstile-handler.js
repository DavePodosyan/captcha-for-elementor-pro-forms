if (typeof window.ElementorTurnstileHandler === 'undefined') {
    class TurnstileHandler extends elementorModules.frontend.handlers.Base {
        getDefaultSettings() {
            return {
                selectors: {
                    turnstile: '.elementor-cf-turnstile:last',
                    submit: 'button[type="submit"]'
                }
            };
        }

        getDefaultElements() {
            const selectors = this.getDefaultSettings().selectors;
            const $turnstile = this.$element.find(selectors.turnstile);
            const $form = $turnstile.parents('form');
            const $submit = $form.find(selectors.submit);

            return { $turnstile, $form, $submit };
        }

        bindEvents() {
            this.waitForTurnstile();
        }

        waitForTurnstile() {
            if (window.turnstile && typeof window.turnstile.render === 'function') {
                this.renderTurnstile();
            } else {
                setTimeout(() => this.waitForTurnstile(), 350);
            }
        }

        renderTurnstile() {
            const el = this.elements.$turnstile[0];

            if (!el || el.dataset.turnstileRendered === 'true') return;

            if (!jQuery(el).is(':visible')) {
                setTimeout(() => this.renderTurnstile(), 200);
                return;
            }

            el.dataset.turnstileRendered = 'true';

            const sitekey = this.elements.$turnstile.data('sitekey');

            const widgetId = window.turnstile.render(el, {
                sitekey: sitekey,
                callback: (token) => {
                    let $input = this.elements.$form.find('[name="cf-turnstile-response"]');
                    if (!$input.length) {
                        $input = jQuery('<input>', {
                            type: 'hidden',
                            name: 'cf-turnstile-response'
                        }).appendTo(this.elements.$form);
                    }
                    $input.val(token);
                }
            });

            this.elements.$form.on('reset error', () => {
                if (window.turnstile && typeof window.turnstile.reset === 'function') {
                    window.turnstile.reset(widgetId);
                }
            });
        }
    }

    window.ElementorTurnstileHandler = TurnstileHandler;
}

jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('form', window.ElementorTurnstileHandler);
});