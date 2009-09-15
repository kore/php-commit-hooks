<?php

require __DIR__ . '/classes/runner.php';

require __DIR__ . '/classes/reporter.php';
require __DIR__ . '/classes/reporter/cli.php';
require __DIR__ . '/classes/reporter/mail.php';

require __DIR__ . '/classes/check.php';
require __DIR__ . '/classes/check/commit_message.php';
require __DIR__ . '/classes/check/code_sniffer.php';

require __DIR__ . '/classes/issue.php';

require __DIR__ . '/classes/repository.php';
require __DIR__ . '/classes/repository/transaction.php';
require __DIR__ . '/classes/repository/version.php';

?>
