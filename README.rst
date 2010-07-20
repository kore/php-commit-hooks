================
PHP Commit Hooks
================

*PHP Commit Hooks* implement an extensible framework for commit hooks, mainly
for PHP software, written in PHP. There are example configurations for commit
hooks available in the ``src/examples`` directory.

The design of PCH is quite simple and is structured into two main entities:

- Checks

  Implements checks, for example verifying the commit message, or if the
  committed files are valid.

- Reporters

  Report the results of the checks to different channels, like commit mails to
  a mailing list, violations to the committer or leaving a message on the SVN
  shell.

Configuration
=============

Each check can be registered for pre- and post-commit hooks. Also all reporters
may be registered for both, but most reporters only make sense in either pre-
or post-commit hooks. An example for a pre-commit hooks looks like::

    require dirname( __FILE__ ) . '/../environment.php';

    $runner = new pchRunner();

    $runner->register( new pchLintCheck() );
    $runner->register( new pchSvnKeywordsCheck( array(
        'php' => array(
            'svn:keywords' => array(
                'Revision',
            ),
        ),
    ) ) );

    $runner->setReporter( new pchReporterDispatcher( array(
        new pchCliReporter(),
        new pchExitCodeReporter(),
    ) ) );

    $runner->run( new pchRepositoryTransaction( $argv[1], $argv[2] ) );

The ``environment.php`` loads all required classes. The ``$runner`` object is
the main class managing the checks and reporters. So the checks are registered
with the runner.

In this example two checks are registered, the link check, which currently only
lints PHP files (checks them for syntax errors) and the svn keyword check,
which ensures that specific svn keywords are set for the given file types.

This is the default reporter configuration for SVN pre-commit hooks. If the
pre-commit hook exits with a non-zero exit code SVN rejects the commit and
echos the message it recieved to the user. The ``pchCliReporter`` returns this
message to SVN and ``pchExitCodeReporter`` returns a non-zero exit code in case
of a violation.

Post-commit hook
----------------

The reporters do not work this way for post-commit hooks. Post-commit hooks are
called asynchronously and therefore are better for executing long-running tasks
and report the results per mail afterwards. An example of such a post-commit
hooks could look like::

    require dirname( __FILE__ ) . '/../environment.php';

    $runner = new pchRunner();
    $runner->register( new pchCodeSnifferCheck( 'Arbit' ) );

    $runner->setReporter( new pchReporterDispatcher( array(
        new pchMailReporter(
            'postcommit@example.org', // Sender
            '{user}@example.org',     // Receiver
            'Coding style violations' // Mail topic
        ),
        new pchCommitMailReporter(
            '{user}@example.org',     // Sender
            'list@example.org',       // Reciever
            '[SVN]'                   // Subject prefix
        ),
    ) ) );

    $runner->run( new pchRepositoryVersion( $argv[1], $argv[2] ) );

It only registers one single check, which calls *PHP_CodeSniffer* to check the
committed files (only the added and changed files).

Then two reporters are registered, the first standard mail reporter reports the
results of the CodeSniffer check to the comitter (notice the receiver mail
address). The second send a commit mail to the mailing list, ignoring any
errors which might have been reported by the registered checks.

The ``pchCommitMailReporter`` sends the commit mail as HTML and text, so that
it looks nice in every client.

Checks
======

There are more checks available then the ones listed above. Here is a list of
the currently implemented checks:

- *pchCodeSnifferCheck*

  Checks the changed files against a specified coding standard using
  PHP_CoodeSniffer.

- *pchCommitMessageCheck*

  Parses and validates the commit message. Only accepts a specific markup of
  commit messages, for common parsable commit messages, which for example
  allows automatic changelog generation and leads to easier readable commit
  messages.

  Read the class documentation for details.

- *pchLintCheck*

  Implements linting (syntax checking) for files. Currently only the syntax of
  PHP files is checked, but it allows to register additional linters for other
  file types, too.

- *pchSvnKeywordsCheck*

  Checks that svn keywords are set for certain file types. If you are using
  something like ``@version $Revision$`` in your PHPDoc comments, for example,
  this is useful to ensure the revision keyword is actually set for those
  files.

Installation
============

To install the commit hooks you basically have to check out the repository,
call ``make init`` to fetch the dependencies and link the examples to
``/path/to/$REPO/hooks/{pre,post}-commit``.

You can also write more complex generic pre- and post-commit hooks, which, for
example, register checks and reporters based on the name of the repository
(``basename( $argv[1] )``). This should be fairly trivial and is left as an
exercise to the user.


..
   Local Variables:
   mode: rst
   fill-column: 79
   End: 
   vim: et syn=rst tw=79
