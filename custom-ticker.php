<?php
/*
Plugin Name: Custom Ticker
Plugin URI:  https://www.fiverr.com/asfiqurrahman
Description: A simple custom ticker that displays a scrolling message.
Version:     1.0
Author:      Asfiqur Rahman Omey
Author URI:  https://www.linkedin.com/in/asfiqurrahmanomey/
License:     01785948127
*/

// Register the settings page
function custom_ticker_add_admin_menu() {
    add_menu_page(
        'Custom Ticker Settings', // Page title
        'Custom Ticker', // Menu title
        'manage_options', // Capability
        'custom-ticker', // Menu slug
        'custom_ticker_settings_page' // Callback function
    );
}
add_action('admin_menu', 'custom_ticker_add_admin_menu');

// Register settings
function custom_ticker_settings_init() {
    register_setting('customTickerGroup', 'custom_ticker_texts');
    register_setting('customTickerGroup', 'custom_ticker_icons');

    add_settings_section(
        'custom_ticker_section',
        __('Configure your ticker', 'custom-ticker'),
        null,
        'custom-ticker'
    );

    add_settings_field(
        'custom_ticker_texts_field',
        __('Ticker Texts', 'custom-ticker'),
        'custom_ticker_texts_render',
        'custom-ticker',
        'custom_ticker_section'
    );

    add_settings_field(
        'custom_ticker_icons_field',
        __('Ticker Icons (URLs)', 'custom-ticker'),
        'custom_ticker_icons_render',
        'custom-ticker',
        'custom_ticker_section'
    );
}
add_action('admin_init', 'custom_ticker_settings_init');

// Render text input fields
function custom_ticker_texts_render() {
    $texts = get_option('custom_ticker_texts', []);
    echo '<div id="custom-ticker-texts">';
    if (!empty($texts)) {
        foreach ($texts as $index => $text) {
            echo '<input type="text" name="custom_ticker_texts[]" value="' . esc_attr($text) . '" placeholder="Enter ticker text"><br>';
        }
    }
    echo '</div>';
    echo '<button type="button" onclick="addTickerTextField()">Add Text Field</button>';
}

// Render icon input fields
function custom_ticker_icons_render() {
    $icons = get_option('custom_ticker_icons', []);
    echo '<div id="custom-ticker-icons">';
    if (!empty($icons)) {
        foreach ($icons as $index => $icon) {
            echo '<input type="text" name="custom_ticker_icons[]" value="' . esc_attr($icon) . '" placeholder="Enter icon URL"><br>';
        }
    }
    echo '</div>';
    echo '<button type="button" onclick="addTickerIconField()">Add Icon Field</button>';
}

// Output the settings page HTML
function custom_ticker_settings_page() {
    ?>
    <form action="options.php" method="post">
        <?php
        settings_fields('customTickerGroup');
        do_settings_sections('custom-ticker');
        submit_button();
        ?>
    </form>
    <script>
        function addTickerTextField() {
            const container = document.getElementById('custom-ticker-texts');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'custom_ticker_texts[]';
            input.placeholder = 'Enter ticker text';
            container.appendChild(input);
            container.appendChild(document.createElement('br'));
        }

        function addTickerIconField() {
            const container = document.getElementById('custom-ticker-icons');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'custom_ticker_icons[]';
            input.placeholder = 'Enter icon URL';
            container.appendChild(input);
            container.appendChild(document.createElement('br'));
        }
    </script>
    <?php
}

// Shortcode to display the ticker
function custom_ticker_shortcode() {
    $texts = get_option('custom_ticker_texts', []);
    $icons = get_option('custom_ticker_icons', []);
    if (empty($texts)) return '';

    $output = '<div class="ticker-wrap"><div class="ticker"><span class="item-collection">';
    foreach ($texts as $index => $text) {
        $icon = isset($icons[$index]) ? esc_url($icons[$index]) : '';
        $output .= '<span class="item">';
        if ($icon) {
            $output .= '<img src="' . $icon . '" class="icon" alt="icon">';
        }
        $output .= esc_html($text) . '</span>';
    }
    $output .= '</span></div></div>';
    $output .= '
    <style>
        body { margin: 0; }
        .ticker-wrap {
            width: 100%;
            height: 64px;
            margin: 0 auto;
            overflow: hidden;
            white-space: nowrap;
            position: fixed;
            bottom: 0;
            background-color: #1E1E1E;
        }
        .ticker {
            display: inline-block;
            margin-top: 16px;
            animation: marquee 20s linear infinite;
        }
        .item {
            display: inline-block;
            padding: 0 1rem;
            font-size: 24px;
            color: #C6C6C6;
            font-weight: 800;
            font-family: sans-serif;
        }
        .icon {
            width: 32px;
            height: 32px;
            vertical-align: middle;
            margin-right: 10px;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-100%); }
        }
    </style>';

    return $output;
}
add_shortcode('custom_ticker', 'custom_ticker_shortcode');
