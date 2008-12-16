<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * An interface for TinyURL
 *
 * PHP version 5.1.0+
 *
 * Copyright (c) 2007, The PEAR Group
 * 
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *  - Neither the name of the The PEAR Group nor the names of its contributors 
 *    may be used to endorse or promote products derived from this software 
 *    without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" 
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE 
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR 
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF 
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS 
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN 
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) 
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE 
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category    Services
 * @package     Services_TinyURL
 * @author      Joe Stump <joe@joestump.net> 
 * @copyright   1997-2007 The PHP Group
 * @license     http://www.opensource.org/licenses/bsd-license.php 
 * @version     CVS: $Id:$
 * @link        http://pear.php.net/package/Services_TinyURL
 * @link        http://tinyurl.com
 */

require_once 'Services/TinyURL/Exception.php';

/**
 * Services_TinyURL
 *
 * <code>
 * <?php
 * require_once 'Services/TinyURL.php';
 * try {
 *     $tiny = new Services_TinyURL();
 *     $url = $tiny->create('http://www.joestump.net');
 *     echo $url;
 * } catch (Services_TinyURL_Exception $e) {
 *     echo $e->getMessage(); 
 * }
 * ?>
 * </code>
 *
 * @category    Services
 * @package     Services_TinyURL
 * @author      Joe Stump <joe@joestump.net> 
 * @link        http://tinyurl.com
 */
class Services_TinyURL
{
    /**
     * Location of TinyURL API
     *
     * @var         string      $api            URL of TinyURL API
     * @static
     */
    protected $api = 'http://tinyurl.com/api-create.php';

    /**
     * Create a TinyURL
     *
     * @access      public
     * @param       string      $destination    The URL to make tiny
     * @return      string
     * @static 
     */
    public function create($destination)
    {
        $ch = curl_init();

        $uri = $this->api . '?url=' . $destination;
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Services_TinyURL @version@');
        $result = curl_exec($ch);
        if ($result === false) {
            throw new Services_TinyURL_Exception(curl_error($ch), curl_errno($ch));
        }

        curl_close($ch);

        if (!preg_match('/^https?:\/\//i', $result)) {
            throw new Services_TinyURL_Exception('Unexpected response from the API');
        }

        return $result;
    }

    /**
     * Do a reverse lookup of a TinyURL
     *
     * @access      public
     * @param       string      $url        TinyURL to look up
     * @return      string      The destination URL of the TinyURL
     * @static
     */
    public function lookup($url) 
    {
        if (!preg_match('/^http:\/\/tinyurl.com\/[a-z0-9]+/i', $url)) {
            throw new Services_TinyURL_Exception('Invalid TinyURL ' . $url);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);        
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Services_TinyURL @version@');
        $result = curl_exec($ch);
        curl_close($ch);

        $m = array();
        if (preg_match("/Location: (.*)\n/", $result, $m)) {
            if (isset($m[1]) && preg_match('/^https?:\/\//i', $m[1])) {
                return trim($m[1]);
            }
        }

        throw new Services_TinyURL_Exception('No redirection found');
    }
}

?>
