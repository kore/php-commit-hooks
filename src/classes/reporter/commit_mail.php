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
 * Mail reporter 
 * 
 * @package php-commit-hooks
 * @version $Revision$
 * @license http://www.opensource.org/licenses/bsd-license.html New BSD license
 */
class pchCommitMailReporter extends pchMailReporter
{
    /**
     * Report occured issues
     *
     * Report occured issues, passed as an array.
     * 
     * @param pchRepository $repository 
     * @param array $issues
     * @return void
     */
    public function report( pchRepository $repository, array $issues ) 
    {
        $hash = md5( microtime() );

        mail(
            $this->replacePlaceholders( $this->receiver, $repository ),
            $this->subject . " r{$repository->version} - " . $this->getBasePath( $repository ),
            "--$hash\n" .
            "Content-Type: text/plain; charset=\"utf-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" .
            $this->getTextMail( $repository ) .
            "\n--$hash\n" .
            "Content-Type: text/html; charset=\"utf-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" .
            $this->getHtmlMail( $repository ) .
            "\n--$hash--\n",
            "From: " . $this->replacePlaceholders( $this->sender, $repository ) . "\r\n" .
            "Content-Type: multipart/alternative; boundary=\"$hash\";"
        );
    }

    /**
     * Get text mail
     *
     * Return contents of a text diff mail
     * 
     * @param pchRepository $repository 
     * @return string
     */
    protected function getTextMail( pchRepository $repository )
    {
        return sprintf(
            "Author:   %s\n" . 
            "Date:     %s\n" . 
            "Revision: %s\n\n" . 
            "Log:\n\n%s\n\n" . 
            "Files changed:\n%s\n\n%s\n",
            $repository->author,
            $repository->date,
            $repository->version,
            $repository->log,
            $repository->changed,
            $repository->diff
        );
    }

    /**
     * Get HTML mail
     *
     * Return contents of a HTML diff mail
     * 
     * @param pchRepository $repository 
     * @return string
     */
    protected function getHtmlMail( pchRepository $repository )
    {
        return sprintf( '<html>
    <head>
        <title>SVN diff mail</title>
        <style type="text/css">
dt {
    width: 20%%;
    font-weight: bold;
    float: left;
    clear: left;
}

dd {
    margin-left: 21%%;
    width: 79%%;
}

pre {
    display: block;
    white-space: pre;

    font-family: monospace;
    color: #000000;
}

ol.diff {
    list-style-type: none;
    padding: 0px;
    margin: 0px;
}

ol.diff li {
    padding: 0px;
    margin: 0px;
    font-family: monospace;
    white-space: pre;
}

li.added {
    background-color: #dff7c6;
    color: #4e9a06;
}

li.removed {
    background-color: #fccbcb;
    color: #A40000;
}

li.comment {
    color: #babdb5;
}
        </style>
    </head>
    <body>
        <dl>
            <dt>Author</dt><dd>%s</dd>
            <dt>Date</dt><dd>%s</dd>
            <dt>Revision</dt><dd>%s</dd>
            <dt>Log</dt><dd><pre>%s</pre></dd>
            <dt>Changed</dt><dd>%s</dd>
        </dl>
        <ol class="diff">%s</pre>
    </body>
</html>',
            $repository->author,
            $repository->date,
            $repository->version,
            $repository->log,
            nl2br( $repository->changed ),
            preg_replace_callback(
                '(^(?P<marker>=|\\+\\+\\+|---|\\+|-|).*$)m',
                function ( $matches )
                {
                    $text = htmlspecialchars( $matches[0], ENT_QUOTES );
                    switch ( $matches['marker'] )
                    {
                        case '=':
                        case '+++':
                        case '---':
                            return '<li class="comment">' . $text . '</li>';

                        case '+':
                            return '<li class="added">' . $text . '</li>';

                        case '-':
                            return '<li class="removed">' . $text . '</li>';

                        default:
                            return '<li>' . $text . '</li>';
                    }
                },
                $repository->diff
            )
        );
    }

    /**
     * Get base path from changes
     * 
     * @param pchRepository $repository 
     * @return void
     */
    protected function getBasePath( pchRepository $repository )
    {
        $changed = $repository->{'dirs-changed'};
        $dirs    = array_map(
            function ( $dir )
            {
                $dir = $dir[0] === '/' ? $dir : '/' . $dir;
                return trim( $dir );
            },
            preg_split( '(\r\n|\r|\n)', $changed )
        );

        $common = array_shift( $dirs );
        foreach ( $dirs as $dir )
        {
            $c = 0;
            $maxLength = min( strlen( $common ), strlen( $dir ) );
            while ( ( $c < $maxLength ) &&
                    ( $common[$c] === $dir[$c] ) )
            {
                ++$c;
            }

            $common = substr( $common, 0, $c );
        }

        return $common;
    }
}

