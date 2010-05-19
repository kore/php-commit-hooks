#!/usr/bin/env php
<?php
/**
 * This is an example for a common post commit configuration. It registers a
 * single check, the code sniffer check, and sends the report to the user, who
 * committed.
 */

require dirname( __FILE__ ) . '/environment.php';

$runner = new pchRunner();
$runner->register( new pchCodeSnifferCheck( 'Arbit' ) );

$runner->setReporter( new pchReporterDispatcher( array(
    new pchMailReporter(
        'postcommit@example.org', // Sender
        '{user}@example.org', // Receiver
        'Coding style violations' // Mail topic
    ),
//    new pchKbotReporter( 'http://kbot.k023.de/commit.php', 'secret', 'My Project', '#project' ),
) ) );

$runner->run( new pchRepositoryVersion( $argv[1], $argv[2] ) );

