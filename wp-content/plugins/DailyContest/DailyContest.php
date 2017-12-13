<?php defined('ABSPATH') or exit;

/**
 * Plugin Name: Daily Contest
 * Plugin URI: https://stevie.io
 * Description: Allows you to run a daily contest.
 * Version: 1.0.0
 * Author: Stevie McComb
 * Author URI: https://stevie.io
 * Text Domain: daily-contest
 */

class DailyContest
{
    private $entries_table;

    private $tokens_table;

    public function __construct()
    {
        global $wpdb;

        $this->entries_table = $wpdb->prefix . 'daily_contest_entries';
        $this->tokens_table = $wpdb->prefix . 'daily_contest_tokens';

        register_activation_hook(__FILE__, array($this, 'migrate'));

        add_shortcode('daily-contest', array($this, 'shortcode_callback'));

        $this->init_api();
    }

    public function migrate()
    {
        ob_start();

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $entries_sql = "
            CREATE TABLE IF NOT EXISTS `{$this->entries_table}` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `verified` TINYINT(1) DEFAULT 0 NOT NULL,
                `entered_at` date,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `{$this->entries_table}_email_entered_at_unique` UNIQUE (`email`, `entered_at`)
            ) $charset_collate;
        ";

        $tokens_sql = "
            CREATE TABLE IF NOT EXISTS `{$this->tokens_table}` (
                `id` int(10) NOT NULL AUTO_INCREMENT,
                `entry_id` int(10) UNSIGNED NOT NULL,
                `token` varchar(16) NOT NULL,
                `email` varchar(255) NOT NULL,
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                `expires_at` datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (`id`),
                CONSTRAINT `{$this->tokens_table}_token_unique` UNIQUE (`token`)
            ) $charset_collate;
        ";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta($entries_sql);
        dbDelta($tokens_sql);

        $output = ob_get_contents();

        ob_end_clean();

        if (!empty($output)) file_put_contents(plugin_dir_path(__FILE__) . '/activation-errors.txt', $output);
    }

    private function init_api()
    {
        if (empty($_REQUEST['context']) || $_REQUEST['context'] !== 'daily-contest' || empty($_REQUEST['action'])) return false;

        switch ($_REQUEST['action']) {
            case 'submit-form':
                $this->submit_form();
                break;

            case 'verify-email':
                $this->verify_email();
                break;

            default:
                break;
        }

        exit;
    }

    public function shortcode_callback()
    {
        ob_start();

        require_once plugin_dir_path(__FILE__) . '/form.php';

        $html = ob_get_contents();

        ob_end_clean();

        return $html;
    }

    private function submit_form()
    {
        global $wpdb;

        require_once ABSPATH . 'wp-includes/pluggable.php';

        $errors = array();
        $data = filter_var_array($_POST, FILTER_SANITIZE_STRING);

        if (empty($data['name'])) $errors['name'] = 'The name field is required.';
        if (empty($data['email'])) $errors['email'] = 'The email field is required.';

        $data['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);

        if (empty($data['email']) && !isset($errors['email'])) $errors['email'] = 'The provided email address is invalid.';

        if (!empty($errors)) {
            echo json_encode(array(
                'status' => 422,
                'errors' => $errors,
            ));
            exit;
        }

        $entry_data = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'entered_at' => date('Y-m-d', strtotime('now')),
        );

        $wpdb->insert($this->entries_table, $entry_data);

        $entry_id = $wpdb->get_var("
            SELECT
                `id`
            FROM
                `{$this->entries_table}`
            WHERE
                `email` = '{$data['email']}'
            ORDER BY
                `created_at` DESC
            LIMIT 1
        ");

        $token = $this->generate_token();
        $token_data = array(
            'entry_id' => $entry_id,
            'email' => $data['email'],
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', strtotime('now + 30 minutes')),
        );

        $wpdb->insert($this->tokens_table, $token_data);

        $to = $data['email'];
        $subject = 'Please verify your email address.';
        $message = 'To verify your email address, please visit the following URL: <a href="' . get_site_url() . '">' . get_site_url() . '?context=daily-contest&action=verify-email&email=' . $data['email'] . '&token=' . $token . '</a>';

        if (wp_mail($to, $subject, $message)) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'Verification email successfully sent. Please check your email to verify your address.',
            ));
        } else {
            echo json_encode(array(
                'status' => 500,
                'errors' => array(
                    'mailer' => 'The verification email could not be sent. Please contact the website administrator to resolve the issue.',
                ),
            ));
        }
    }

    private function verify_email()
    {
        global $wpdb;

        $email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);
        $token = filter_var($_GET['token'], FILTER_SANITIZE_STRING);

        if (empty($email)) die('The provided email is invalid.');

        $token_record = $wpdb->get_row("SELECT * FROM `{$this->tokens_table}` WHERE `email` = '$email' ORDER BY `created_at` DESC LIMIT 1;");

        if ($token_record->token !== $token) die('The provided token is invalid.');
        if (date('Y-m-d H:i:s', strtotime($token_record->expires_at)) <= date('Y-m-d H:i:s', strtotime('now'))) die('The provided token has expired.');

        $entry_verified = $wpdb->update($this->entries_table, array('verified' => 1), array('id' => $token_record->entry_id));

        $entry_verified ? die('Your entry has been verified. You have now officially been entered into the contest!') : die('There was a problem verifying your entry, please contact the site\'s administrator to resolve the issue.');
    }

    private function generate_token()
    {
        $token = '';
        $chars = str_split('abcdefghijklmnopqrstuvwxyz');

        while (strlen($token) < 16) {
            $token .= $chars[rand(0, count($chars) - 1)];
        }

        return $token;
    }
}

new DailyContest();
