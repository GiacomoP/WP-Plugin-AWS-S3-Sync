<?php

namespace S3;

/**
 * Registers the needed custom options for the plugin.
 */
class Settings
{
    /**
     * @var array
     */
    private $settings = [
        'aws_s3_api' => [
            'aws_credentials' => [
                'key' => 'YOUR_KEY',
                'secret' => 'YOUR_SECRET_KEY'
            ],
            'aws_s3_bucket_name' => 'YOUR_BUCKET_NAME',
            'aws_sync_folder' => '/agreements',
            'aws_sync_every' => 'hourly',
            'aws_last_sync' => 'never'
        ]
    ];

    /**
     * Adds the admin menu, inserts the settings and their options page.
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('admin_init', [$this, 'initSettings']);
    }

    /**
     * Inserts the default values in the plugin options.
     */
    public function doDefaults()
    {
        foreach ($this->settings as $group => $settings) {
            foreach ($settings as $setting => $default) {
                update_option($setting, $default);
            }
        }
    }

    /**
     * Removes all registered custom options.
     */
    public function purge()
    {
        foreach ($this->settings as $group => $settings) {
            foreach ($settings as $setting => $default) {
                delete_option($setting);
            }
        }
    }

    /**
     * Adds a new admin menu item.
     */
    public function addAdminMenu()
    {
        add_menu_page(
            'Amazon S3 Sync',
            'Amazon S3 Sync',
            'administrator',
            'aws_s3_sync',
            [$this, 'makeOptionsPage']
        );
    }

    /**
     * Renders the template for the options page.
     */
    public function makeOptionsPage() {
        ?>
        <h2>Amazon S3 Settings</h2>
        <form action='options.php' method='post'>
        <?php
            settings_fields('aws_s3_api');
            do_settings_sections('aws_s3_api');
            submit_button();
        ?>
        </form>
        <?php
    }

    /**
     * Creates the options page and registers the settings.
     */
    public function initSettings()
    {
        foreach ($this->settings as $group => $settings) {
            foreach ($settings as $setting => $default) {
                register_setting($group, $setting);
            }
        }

        // Amazon S3 Credentials API
        add_settings_section(
            'aws_s3_api_section',
            'Amazon S3 API',
            [$this, 'awsSectionCallback'],
            'aws_s3_api'
        );
        add_settings_field(
            'aws_s3_api_key',
            'Access Key ID',
            [$this, 'awsApiKeyRender'],
            'aws_s3_api',
            'aws_s3_api_section',
            [
                'label_for' => 'aws_s3_api_key',
                'option' => 'key'
            ]
        );
        add_settings_field(
            'aws_s3_secret_key',
            'Secret Access Key',
            [$this, 'awsApiKeyRender'],
            'aws_s3_api',
            'aws_s3_api_section',
            [
                'label_for' => 'aws_s3_secret_key',
                'option' => 'secret'
            ]
        );
        // Preferences
        add_settings_field(
            'aws_s3_bucket_name',
            'Bucket Name',
            [$this, 'awsBucketNameRender'],
            'aws_s3_api',
            'aws_s3_api_section',
            ['label_for' => 'aws_s3_bucket_name']
        );
        add_settings_field(
            'aws_sync_folder',
            'Folder to sync',
            [$this, 'awsFolderToSyncRender'],
            'aws_s3_api',
            'aws_s3_api_section',
            ['label_for' => 'aws_sync_folder']
        );
        add_settings_field(
            'aws_s3_sync_every',
            'Sync files',
            [$this, 'awsSyncEveryRender'],
            'aws_s3_api',
            'aws_s3_api_section'
        );
        add_settings_field(
            'aws_s3_last_sync',
            'Last successful sync',
            [$this, 'awsLastSyncRender'],
            'aws_s3_api',
            'aws_s3_api_section'
        );
    }

    public function awsSectionCallback()
    {
        echo 'Please enter your credentials for Amazon S3 API.';
    }

    public function awsApiKeyRender($args)
    {
        $credentials = get_option('aws_credentials');
        echo <<<EOD
        <input type="text" name="aws_credentials[{$args['option']}]" value="{$credentials[$args['option']]}" id="{$args['label_for']}">
EOD;
    }

    public function awsBucketNameRender($args)
    {
        $bucket = get_option('aws_s3_bucket_name');
        echo <<<EOD
        <input type="text" name="aws_s3_bucket_name" value="{$bucket}" id="{$args['label_for']}">
EOD;
    }

    public function awsFolderToSyncRender($args)
    {
        $folder = get_option('aws_sync_folder');
        echo <<<EOD
        <input type="text" name="aws_s3_sync_folder" value="{$folder}" id="{$args['label_for']}">
EOD;
    }

    public function awsSyncEveryRender()
    {
        $syncEvery = get_option('aws_sync_every');
        ?>
        <p>
            <label>
                <input type="radio" name="aws_sync_every" value="hourly" <?php checked($syncEvery, 'hourly'); ?>>
                Hourly
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="aws_sync_every" value="twicedaily" <?php checked($syncEvery, 'twicedaily'); ?>>
                Twice a day
            </label>
        </p>
        <p>
            <label>
                <input type="radio" name="aws_sync_every" value="daily" <?php checked($syncEvery, 'daily'); ?>>
                Daily
            </label>
        </p>
        <?php
    }

    public function awsLastSyncRender()
    {
        $lastSync = get_option('aws_last_sync');
        if ($lastSync !== 'never') {
            $lastSync = date('l, F j, Y @ g:i:s A', $lastSync);
        }
        echo "<p>{$lastSync}</p>";
        echo <<<EOD
        <input type="hidden" name="aws_last_sync" value="{$lastSync}">
EOD;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->settings;
    }
}