<?php

/**
 * This snippet injects anchor tags to UABB Advanced Accordion buttons,
 * allowing the buttons to be linked to directly from other pages.
 *
 * The margin-top property adjusts how far above a given button the anchor
 * is placed.
 */
function uabb_advanced_accordion_anchors() {
    ?>
    <style>
        a.anchor {
            position: absolute;
            margin-top: -200px;
        }
    </style>

    <script>
        jQuery(document).ready(function ($) {

            // Reference the accordion buttons.
            var el = $('.uabb-adv-accordion-button');

            // Loop through each button.
            el.each(function () {

                // Get the button text, then make the anchor name
                // value, lower case with spaces replaced by hyphens.
                var key = $(this).text().trim().toLowerCase().replace(' ', '-');

                // Inject an anchor tag immediately before the button.
                $(this).before('<a class="anchor" name="' + key + '"></a>');
            });

            // If a hash value is present in the URL,
            // simulate a click on its corresponding button.
            setTimeout(function () {
                if (window.location.hash) {
                    var hash = window.location.hash.replace('#', '');
                    $('a[name="' + hash + '"]').next().trigger('click');
                }
            }, 125);
        });
    </script>
    <?php
}

add_action( 'wp_head', 'uabb_advanced_accordion_anchors' );
