;(function () {
    // wait for wp.customize to be available
    jQuery(document).ready(function () {
        // wait for customizer to be ready
        wp.customize.bind('ready', function () {
            var customize = this; // set instance

            customize.control('wp_block_ink_enabled', function (control) {
                control.setting.bind(toggleAllFields);
            });

            // tap count field and bind conditional show/hide
            customize.control('wp_block_ink_color_count', function (control) {
                control.setting.bind(togglePressInkFieldsByNumber);
            });

            // loop all 11 colors, activating & deactivating the fields
            // Set val to "12" to enable all color fields, set val to "0" to disable all colors fields
            function togglePressInkFieldsByNumber(val) {
                _.each([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], function (fieldNum) {
                    if (val < fieldNum) {
                        customize.control('wp_block_ink_color_' + fieldNum + '_name').deactivate();
                        customize.control('wp_block_ink_color_' + fieldNum).deactivate();
                    } else {
                        customize.control('wp_block_ink_color_' + fieldNum + '_name').activate();
                        customize.control('wp_block_ink_color_' + fieldNum).activate();
                    }
                });
            }

            function toggleAllFields(enabled) {
                if (enabled) {
                    customize.control('wp_block_ink_color_count').activate();
                    customize.control('wp_block_ink_disable_custom').activate();
                    togglePressInkFieldsByNumber(12);
                } else {
                    customize.control('wp_block_ink_color_count').deactivate();
                    customize.control('wp_block_ink_disable_custom').deactivate();
                    togglePressInkFieldsByNumber(0);
                }
            }

        });
    });
})();