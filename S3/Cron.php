<?php

namespace S3;

/**
 * Handles the Wordpress Cron Job.
 */
class Cron
{
    /**
     * @var string
     */
    private $name;

    /**
     * Initializes the object, checking if current jobs need an update.
     */
    public function __construct()
    {
        $this->name = 'aws_s3_sync';

        // Check if plugin settings were updated
        $schedule = wp_get_schedule($this->name);
        $syncEvery = get_option('aws_sync_every');
        if ($schedule !== false
            && $syncEvery !== false
            && $schedule !== $syncEvery
        ) {
            $this->register(true);
            /*add_action('admin_init', function() {
                App::getSyncer()->doSync();
            });*/
        }
    }

    /**
     * Registers the cron job.
     * @param boolean $forceUpdate [optional]
     */
    public function register($forceUpdate = false)
    {
        $syncEvery = get_option('aws_sync_every');
        if (!wp_next_scheduled($this->name) || $forceUpdate) {
            wp_clear_scheduled_hook($this->name);
            wp_schedule_event(time(), $syncEvery, $this->name);
        }
    }

    /**
     * Deregisters the cron job.
     */
    public function deregister()
    {
        wp_clear_scheduled_hook($this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}