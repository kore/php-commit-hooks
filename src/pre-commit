#!/usr/bin/env php
<?php
/**
 * This is an example for a common pre commit configuration. It registers a
 * single check, which validates the commit messages, and echos the result to
 * the CLI, so the report will end in the users SVN client.
 */

require dirname( __FILE__ ) . '/environment.php';

$runner = new pchRunner();

$runner->register( new pchLintCheck() );

$runner->register( new pchSvnKeywordsCheck( array(
    'php' => array(
        'svn:keywords' => array(
            'Revision',
        ),
    ),
) ) );

$runner->register( new pchCommitMessageCheck( array(
    'Refs'        => pchCommitMessageCheck::REQUIRED,
    'Fixed'       => pchCommitMessageCheck::REQUIRED,
    'Closed'      => pchCommitMessageCheck::REQUIRED,
    'Implemented' => pchCommitMessageCheck::OPTIONAL,
    'Documented'  => pchCommitMessageCheck::OPTIONAL,
    'Tested'      => pchCommitMessageCheck::PROHIBITED,
    'Added'       => pchCommitMessageCheck::PROHIBITED,
) ) );

$runner->setReporter( new pchReporterDispatcher( array(
    new pchCliReporter(),
    new pchExitCodeReporter(),
) ) );

$runner->run( new pchRepositoryTransaction( $argv[1], $argv[2] ) );

