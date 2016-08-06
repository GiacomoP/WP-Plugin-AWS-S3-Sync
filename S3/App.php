<?php

namespace S3;

/**
 * The general App.
 */
class App
{
    /**
     * @var Cron
     */
    private static $cron;

    /**
     * @var Syncer
     */
    private static $syncer;

    /**
     * @var Settings
     */
    private static $settings;

    public static function run()
    {
        self::$cron = new Cron();
        self::$syncer = new Syncer();
        self::$settings = new Settings();
    }

    /**
     * Methods to run on plugin activation.
     */
    public static function runActivation()
    {
        self::$settings->doDefaults();
        self::$cron->register();
    }

    /**
     * Methods to run on plugin deactivation.
     */
    public static function runDeactivation()
    {
        self::$settings->purge();
        self::$cron->deregister();
    }

    /**
     * @return Syncer
     */
    public static function getSyncer()
    {
        return self::$syncer;
    }

    /**
     * @return Cron
     */
    public static function getCron()
    {
        return self::$cron;
    }
}