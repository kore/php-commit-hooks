<?php

require './classes/runner.php';

require './classes/reporter.php';
require './classes/reporter/cli.php';
require './classes/reporter/mail.php';

require './classes/check.php';
require './classes/check/commit_message.php';
require './classes/check/code_sniffer.php';

require './classes/issue.php';

require './classes/repository.php';
require './classes/repository/transaction.php';
require './classes/repository/version.php';

?>
