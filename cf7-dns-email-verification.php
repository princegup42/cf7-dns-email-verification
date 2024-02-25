<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://github.com/princegup42
 * @since   1.0.0
 * @package Cf7_Dns_Email_Verification
 *
 * @wordpress-plugin
 * Plugin Name:       CF7 DNS Email Verification
 * Plugin URI:        https://github.com/princegup42
 * Description:       Adds DNS verification for email addresses in Contact Form 7.
 * Version:           1.0.0
 * Author:            Sumit Kumar
 * Author URI:        https://github.com/princegup42/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cf7-dns-email-verification
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined('WPINC') ) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('CF7_DNS_EMAIL_VERIFICATION_VERSION', '1.0.0');

// Hook into Contact Form 7 validation process
add_filter('wpcf7_validate_email*', 'cf7_dns_email_verification', 20, 2);
add_filter('wpcf7_validate_email', 'cf7_dns_email_verification', 20, 2);

function cf7_dns_email_verification( $result, $tag )
{
    $tag_name = $tag['name'];

    // Get the submitted email address from the form
    $submitted_email = isset($_POST[ $tag_name ]) ? sanitize_email($_POST[ $tag_name ]) : '';

    // Check if the email is valid using DNS verification
    if ($submitted_email && ! cf7_dns_verify_email($submitted_email) ) {
        $result->invalidate($tag, 'Email address entered is not valid.');
    }

    return $result;
}

function cf7_dns_verify_email( $email )
{
    // Extract the domain from the email address
    $domain = substr(strrchr($email, '@'), 1);

    // Check DNS for MX records
    if (checkdnsrr($domain, 'MX') ) {
        return true; // Valid email address
    } else {
        return false; // Invalid email address
    }
}
