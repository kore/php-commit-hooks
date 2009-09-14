<?php
/**
 * Copyright (c) <2009>, all contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, 
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, 
 *   this list of conditions and the following disclaimer in the documentation 
 *   and/or other materials provided with the distribution.
 * - Neither the name of the project nor the names of its contributors may 
 *   be used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 *
 * This software is provided by the copyright holders and contributors "as is" 
 * and any express or implied warranties, including, but not limited to, the 
 * implied warranties of merchantability and fitness for a particular purpose 
 * are disclaimed. in no event shall the copyright owner or contributors be 
 * liable for any direct, indirect, incidental, special, exemplary, or 
 * consequential damages (including, but not limited to, procurement of 
 * substitute goods or services; loss of use, data, or profits; or business 
 * interruption) however caused and on any theory of liability, whether in 
 * contract, strict liability, or tort (including negligence or otherwise) 
 * arising in any way out of the use of this software, even if advised of the 
 * possibility of such damage.
 *
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */

/**
 * Runner 
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchRunner {
    /**
     * List of checks registered in the runner and executed by the runner.
     * 
     * @var array
     */
    protected $checks;

    /**
     * Reporter used to report issues found by the checks.
     * 
     * @var pchReported
     */
    protected $reporter;

    /**
     * Constructor for pchRunner
     *
     * Initilizes instance properties.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->reporter = new pchCliReporter();
        $this->checks   = array();
    }

    /**
     * Register a check
     *
     * Register a check, which will be executed by the runner. Each check will 
     * be called in the order they are registerd.
     * 
     * @param pchCheck $check 
     * @return void
     */
    public function register( pchCheck $check )
    {
        $this->checks[] = $check;
    }

    /**
     * Set reporter
     *
     * Set the reporter used to report the issues found by the registered 
     * checks.
     * 
     * @param pchReporter $reporter 
     * @return void
     */
    public function setReporter( pchReporter $reporter )
    {
        $this->reporter = $reporter;
    }

    /**
     * Run all checks and report them
     *
     * Runs all registered checks, aggregates their found issues and passes 
     * them to the reporter, so the user will be notified in the configured 
     * way.
     * 
     * @param string $repository 
     * @param string $transaction 
     * @return void
     */
    public function run( $repository, $transaction )
    {
        $issues = array();
        foreach ( $checks as $check )
        {
            $issues = array_merge(
                $issues,
                $check->validate( $repository, $transaction )
            );
        }

        $this->reporter->report( $issues );
    }
}

