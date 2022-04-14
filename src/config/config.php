<?php

/*
 * The configuration for laravel-tickets
 */
return [
    /*
     * Should file upload be enabled?
     */
    'files' => true,
    'file' => [
        /*
         * Where should the files be saved?
         */
        'driver' => 'local',
        /*
         * Path for files
         */
        'path' => 'tickets/',
        /*
         * File size limit
         * The size is in kilobytes, so 5120 = 5 megabytes
         */
        'size-limit' => 5120,
        /*
         * Allowed file types
         * Full extension list: https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
         */
        'mimetype' => 'pdf,png,jpg,txt',
        /*
         * Maximal file uploads for message
         */
        'max-files' => 5
    ],

    /*
     * The user model
     */
    'user' => App\Models\User::class,

    /*
     * The admin model
     */
    'admin' => App\Models\User::class,

    /*
     * Database tables name
     */
    'admins-table' => 'users',
    'users-table' => 'users',
    'tickets-table' => 'tickets',
    'ticket-messages-table' => 'ticket_messages',
    'ticket-comments-table' => 'ticket_comments',
    'ticket-uploads-table' => 'ticket_uploads',
    'ticket-categories-table' => 'ticket_categories',
    'ticket-references-table' => 'ticket_references',
    'ticket-activities-table' => 'ticket_activities',
    'ticket-labels-table' => 'ticket_labels',

    /*
     * How many tickets the user can have open
     */
    'maximal-open-tickets' => 3,

    /**
     * Max request per minute to add tickets
     */
    'requests-throttle' => 'throttle:10,120',
    /*
     * How many days after last message sent, the ticket gets as closed declared
     * Use 0 for disabling this feature
     */
    'autoclose-days' => 7,

    /*
     * User can reopen a ticket with a answer
     */
    'open-ticket-with-answer' => false,

    /*
     * Date format
     */
    'datetime-format' => 'H:i d.m.Y',

    /*
     * The priorities
     */
    'priorities' => ['LOW', 'MID', 'HIGH'],
    /*
     * Layout view
     */
    'layouts' => 'laravel-tickets::layouts.main',

    /*
     * Enable categories for tickets
     */
    'category' => true
];
