<?php

namespace App\Traits;

trait ParsableAgent {

    public $parsedAgent;

    private $browserName;

    private $platform;

    private $ub;

    private $version;

    private $pattern;

    final protected function parse(?string $agent) : array {
        $this->parsedAgent = $agent;

        $this->browserName = 'Unknown';
        $this->platform    = 'Unknown';
        $this->version     = '';

        $this->parsePlatform();
        $this->parseBrowser();
        $this->parseVersion();

        return [
            'userAgent' => $this->parsedAgent,
            'name'      => $this->browserName,
            'version'   => $this->version,
            'platform'  => $this->platform,
            'pattern'   => $this->pattern,
        ];
    }

    private function parsePlatform() {
        //First get the platform?
        if(preg_match('/linux/i', $this->parsedAgent)) {
            $this->platform = 'linux';
        }else if(preg_match('/macintosh|mac os x/i', $this->parsedAgent)) {
            $this->platform = 'mac';
        }else if(preg_match('/windows|win32/i', $this->parsedAgent)) {
            $this->platform = 'windows';
        }
    }

    private function parseBrowser() {
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i', $this->parsedAgent) && !preg_match('/Opera/i', $this->parsedAgent)) {
            $this->browserName = 'Internet Explorer';
            $this->ub          = 'MSIE';
        }else if(preg_match('/Firefox/i', $this->parsedAgent)) {
            $this->browserName = 'Mozilla Firefox';
            $this->ub          = 'Firefox';
        }else if(preg_match('/Chrome/i', $this->parsedAgent)) {
            $this->browserName = 'Google Chrome';
            $this->ub          = 'Chrome';
        }else if(preg_match('/Safari/i', $this->parsedAgent)) {
            $this->browserName = 'Apple Safari';
            $this->ub          = 'Safari';
        }else if(preg_match('/Opera/i', $this->parsedAgent)) {
            $this->browserName = 'Opera';
            $this->ub          = 'Opera';
        }else if(preg_match('/Netscape/i', $this->parsedAgent)) {
            $this->browserName = 'Netscape';
            $this->ub          = 'Netscape';
        }
    }

    private function parseVersion() {
        // finally get the correct version number
        $known         = ['Version', $this->ub, 'other'];
        $this->pattern = '#(?<browser>' . implode('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if(!preg_match_all($this->pattern, $this->parsedAgent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if(strripos($this->parsedAgent, 'Version') < strripos($this->parsedAgent, $this->ub)) {
                $this->version = $matches['version'][0];
            }else {
                $this->version = $matches['version'][1];
            }
        }else {
            $this->version = $matches['version'][0];
        }

        // check if we have a number
        if($this->version == NULL || $this->version == '') {
            $this->version = '?';
        }
    }

}
