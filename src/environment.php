<?php

// Core classes
require __DIR__ . '/classes/runner.php';

require __DIR__ . '/classes/string_stream.php';

require __DIR__ . '/classes/reporter.php';
require __DIR__ . '/classes/reporter/exit_code.php';
require __DIR__ . '/classes/reporter/dispatcher.php';
require __DIR__ . '/classes/reporter/text.php';
require __DIR__ . '/classes/reporter/cli.php';
require __DIR__ . '/classes/reporter/mail.php';
require __DIR__ . '/classes/reporter/kbot.php';
require __DIR__ . '/classes/reporter/commit_mail.php';

require __DIR__ . '/classes/check.php';
require __DIR__ . '/classes/check/code_sniffer.php';
require __DIR__ . '/classes/check/commit_message.php';
require __DIR__ . '/classes/check/lint.php';
require __DIR__ . '/classes/check/lint/base.php';
require __DIR__ . '/classes/check/lint/php.php';
require __DIR__ . '/classes/check/lint/sieve.php';
require __DIR__ . '/classes/check/svn_keywords.php';

require __DIR__ . '/classes/issue.php';

require __DIR__ . '/classes/repository.php';
require __DIR__ . '/classes/repository/transaction.php';
require __DIR__ . '/classes/repository/version.php';

// Externals: System Process
require __DIR__ . '/external/system_process/systemProcess.php';
require __DIR__ . '/external/system_process/argument.php';
require __DIR__ . '/external/system_process/argument/escaped.php';
require __DIR__ . '/external/system_process/argument/path.php';
require __DIR__ . '/external/system_process/argument/unescaped.php';
require __DIR__ . '/external/exceptions/system_process/nonZeroExitCode.php';
require __DIR__ . '/external/exceptions/system_process/invalidCustomFileDescriptor.php';
require __DIR__ . '/external/exceptions/system_process/notRunning.php';
require __DIR__ . '/external/exceptions/system_process/recursivePipe.php';

