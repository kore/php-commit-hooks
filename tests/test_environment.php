<?php

/**
 * Set timezone to get clearly defined dates
 */
date_default_timezone_set( 'UTC' );

require __DIR__ . '/../src/environment.php';

// Require special test classes
require __DIR__ . '/mocks.php';

/**
 * Fix error reporting settings for test runs
 */
error_reporting( E_ALL | E_STRICT | E_DEPRECATED );
ini_set( 'display_errors', true );
if ( function_exists( 'xdebug_enable' ) )
{
    xdebug_enable( true );
}

// Flag that the test suite has alreday been initialized
define( 'PHC_STARTED', true );

