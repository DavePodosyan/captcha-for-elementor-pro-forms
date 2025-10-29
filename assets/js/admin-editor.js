// jQuery(document).ready(function ($) {
//     'use strict';

//     // CAPTCHA field types that should not show required toggle
//     const CAPTCHA_FIELD_TYPES = ['hcaptcha', 'cf_turnstile'];

//     /**
//      * Hide or show required field based on field type
//      */
//     function toggleRequiredField($fieldTypeSelect) {
//         const fieldType = $fieldTypeSelect.val();
//         const $requiredRow = $fieldTypeSelect
//             .closest('.elementor-repeater-fields')
//             .find('[data-setting="required"]')
//             .closest('.elementor-control-required:not(.elementor-hidden-control)');

//         if (CAPTCHA_FIELD_TYPES.includes(fieldType)) {
//             $requiredRow.hide();
//         } else {
//             $requiredRow.show();
//         }
//     }

//     /**
//      * Monitor field type selects
//      */
//     function monitorFieldTypes() {
//         // Handle existing selects
//         $('select[data-setting="field_type"]').each(function () {
//             toggleRequiredField($(this));
//         });

//         // Monitor changes
//         $(document).on('change', 'select[data-setting="field_type"]', function () {
//             toggleRequiredField($(this));
//         });

//         // Monitor for new fields added
//         const observer = new MutationObserver(function (mutations) {
//             mutations.forEach(function (mutation) {
//                 $(mutation.addedNodes)
//                     .find('select[data-setting="field_type"]')
//                     .each(function () {
//                         toggleRequiredField($(this));
//                     });
//             });
//         });

//         observer.observe(document.body, {
//             childList: true,
//             subtree: true,
//         });
//     }

//     // Start monitoring when DOM is ready
//     monitorFieldTypes();
// });
