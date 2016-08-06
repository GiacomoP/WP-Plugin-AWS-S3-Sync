<?php

namespace S3;

use Aws\Credentials\Credentials;
use Aws\S3\S3Client;

/**
 * Handles the sync with AWS S3.
 */
class Syncer
{
    /**
     * Listens to the cron-job-fired action.
     */
    public function __construct()
    {
        add_action(App::getCron()->getName(), [$this, 'doSync']);
    }

    /**
     * Executes the synchronization.
     */
    public function doSync()
    {
        $credentials = new Credentials(
                get_option('aws_credentials')['key'],
                get_option('aws_credentials')['secret']
        );
        $bucket = get_option('aws_s3_bucket_name');
        $folder = wp_upload_dir()['basedir'] . get_option('aws_sync_folder');

        try {
            $client = new S3Client([
                'version'     => '2006-03-01',
                'region'      => 'eu-west-1',
                'credentials' => $credentials
            ]);
            $client->uploadDirectory($folder, $bucket, null, ['concurrency' => 20]);
            update_option('aws_last_sync', time());
        } catch (\Exception $e) {
            //
        }
    }
}