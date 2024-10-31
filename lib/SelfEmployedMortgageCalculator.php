<?php

namespace Conobe;

/**
 * Niche Self Employed Mortgage Calculator Class
 *
 * @author Benjamin Hall <ben@conobe.co.uk>
 */
class SelfEmployedMortgageCalculator
{

    /**
     * Stores the clean display name of the plugin.
     *
     * @param string $plugin_name
     */
    protected $plugin_name = 'Niche Self Employed Mortgage Calculator';

    /**
     * Stores the class name.
     *
     * @var string
     */
    protected $class_name = 'SelfEmployedMortgageCalculator';

    /**
     * The SelfEmployedMortgageCalculator plugin constructor.
     *
     * @param string $path The plugin path.
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Registers the calls that should hook into the init hook.
     *
     * @return void
     */
    public function ignition()
    {
        /**
         * Calls the initial plugin setup.
         */
        add_action(
            'init',
            function () {
                /**
                 * Exposes the 'niche-self-employed-mortgage-calculator' shortcode.
                 */
                add_shortcode('niche-self-employed-mortgage-calculator', array($this, 'shortcode'));
            }
        );

        /**
         * Pins an admin menu.
         */
        add_action(
            'admin_menu',
            function () {
                add_submenu_page(
                    'options-general.php', // where the menu should hook
                    'Niche SE Calculator', // name
                    'Niche SE Calculator', // name
                    'manage_options', // perms
                    'ssmc-options', // the $_GET url slug
                    array($this, 'controlPanel') // the callback
                );
            }
        );

        // add the settings link
        add_filter(
            'plugin_action_links_niche-self-employed-mortgage-calculator/niche-self-employed-mortgage-calculator.php',
            function ($links) {
                // Build and escape the URL.
                $url = esc_url(
                    add_query_arg(
                        'page',
                        'ssmc-options',
                        get_admin_url() . 'options-general.php'
                    )
                );

                // Create the link.
                $settings_link = "<a href='$url'>" . __('Settings') . '</a>';

                // Adds the link to the end of the array.
                array_push(
                    $links,
                    $settings_link
                );

                return $links;
            }
        );

        /**
         * Queue the colour picker styles.
         */
        add_action(
            'admin_enqueue_scripts',
            function () {
                wp_enqueue_style('wp-color-picker');
                wp_enqueue_script('my-script-handle', plugins_url('my-script.js', __FILE__), array('wp-color-picker'), false, true);
            }
        );
    }

    /**
     * Output the control panel used providing management settings
     *
     * @return void
     */
    public function controlPanel()
    {
        $options = [
            'ssmc_colour_3' => 'Text/Border Colour',
            'ssmc_colour_1' => 'Button Colour',
            'ssmc_colour_2' => 'Button Hover Colour',
        ];

        if (isset($_POST['submit']) && check_admin_referer('update_ssmc_settings')) {
            foreach ($options as $key => $name) {
                if (isset($_POST[$key])) {
                    update_option($key, sanitize_title($_POST[$key]));
                }
            }
        }
?>
        <style>
            .input {
                padding: 20px 10px 15px 10px;
                width: 200px;
            }

            td {
                background: #fff;
                border: 1px solid #ccc;
                padding: 20px;
                vertical-align: top;
                text-align: center;
            }
        </style>
<?php

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">' . $this->plugin_name . '</h1>';
        echo '<form id="' . $this->class_name . '-form" method="POST" enctype="multipart/form-data">';
        wp_nonce_field('update_ssmc_settings');

        echo '<table style="margin-top: 20px;">';
        foreach ($options as $key => $name) {
            $value = get_option($key);
            echo '<tr>';
            echo '<td><label for="' . $key . '">' . $name . '</label></td>';
            echo '<td class="input"><input id="' . $key . '" name="' . $key . '" value="' . $value . '" class="color"/></td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '<div style="padding: 20px 0;">';
        echo '<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  />';
        echo '</div>';
        echo '</form>';
        echo '</div>';

        echo '<script>jQuery(document).ready(function($){$(\'.color\').wpColorPicker({palettes:false});});</script>';
    }

    /**
     * Get Placeholder Value
     *
     * @param string $name The name of the placeholder input.
     *
     * @return string
     */
    public function getPlaceholder($name)
    {
        $data = [
            'currency' => '£',
            'sole_trader_profit' => 100000,
            'partnership_profit' => 100000,
            'director_salary' => 8000,
            'director_dividends' => 90000,
            'contractor_daily_rate' => 350,
            'contractor_annual_days_placeholder' => 256,
            'outgoing_personal_loan' => 0,
            'outgoing_car_finance' => 0,
            'outgoing_child_care' => 0,
            'outgoing_school_fees' => 0,
            'outgoing_total_cc_balance' => 0
        ];

        return isset($data[$name]) ? $data[$name] : 'NOT SET';
    }

    /**
     * Returns the shortcode output.
     *
     * @return string $output
     */
    public function shortcode()
    {
        // render
        ob_start();

        // get the calc
        require $this->path . '/template/calculator.php';

        // get the HTML as a var
        $content = ob_get_contents();

        // cleanup
        ob_end_clean();

        // return the string to the content
        return $content;
    }

    /**
     * Print the inline styles for the calculator.
     *
     * @return void
     */
    public function printInlineStyles()
    {
        require $this->path . '/template/style.css';

        $colour_1 = get_option('ssmc_colour_1');
        $colour_2 = get_option('ssmc_colour_2');
        $colour_3 = get_option('ssmc_colour_3');

        if ($colour_1) {
            echo '#ssmc_calculate{background:' . $colour_1 . ';}';
        }
        if ($colour_2) {
            echo '#ssmc_calculate:hover{background:' . $colour_2 . ';}';
        }

        if ($colour_3) {
            echo '#ssmc_employment_status{border: 1px solid ' . $colour_3 . '!important;}';
            echo '.ssmc_title{color:' . $colour_3 . ';}';
            echo '.ssmc_label{color:' . $colour_3 . ';}';
            echo '.ssmc-currency {background:' . $colour_3 . ';border: 1px solid ' . $colour_3 . ';}';
            echo '.ssmc-currency span {background: ' . $colour_3 . '!important;}';
        }
    }

    /**
     * Print the inline scripts for the calculator.
     *
     * @return void
     */
    public function printInlineScript()
    {
        require $this->path . '/template/calculator.js';
    }
}
