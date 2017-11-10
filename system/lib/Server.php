<?php


class Server
{
    private static $_instance;


    public static function instance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public function getIP()
    {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        {
            $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        {
            $ip = getenv("REMOTE_ADDR");
        }
        elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ip = "unknown";
        }
        return $ip;
    }


    public function getOS ()
    {
        $agent = $_SERVER["HTTP_USER_AGENT"];
        $os = false;
        if (preg_match("/win/i", $agent) && strpos($agent, "95"))
        {
            $os = "Windows 95";
        }
        else if (preg_match("/win 9x/i", $agent) && strpos($agent, "4.90"))
        {
            $os = "Windows ME";
        }
        else if (preg_match("/win/i", $agent) && preg_match("/98/", $agent))
        {
            $os = "Windows 98";
        }
        else if (preg_match("/win/i", $agent) && preg_match("/nt 5.1/i", $agent))
        {
            $os = "Windows XP";
        }
        else if (preg_match("/win/i", $agent) && preg_match("/nt 5/i", $agent))
        {
            $os = "Windows 2000";
        }
        else if (preg_match("/win/i", $agent) && preg_match("/nt/i", $agent))
        {
            $os = "Windows NT";
        }
        else if (preg_match("/win/i", $agent) && preg_match("/32/", $agent))
        {
            $os = "Windows 32";
        }
        else if (preg_match("/linux/i", $agent))
        {
            $os = "Linux";
        }
        else if (preg_match("/unix/i", $agent))
        {
            $os = "Unix";
        }
        else if (preg_match("/sun/i", $agent) && preg_match("/os/i", $agent))
        {
            $os = "SunOS";
        }
        else if (preg_match("/ibm/i", $agent) && preg_match("/os/i", $agent))
        {
            $os = "IBM OS/2";
        }
        else if (preg_match("/Mac/i", $agent) && preg_match("/PC/i", $agent))
        {
            $os = "Macintosh";
        }
        else if (preg_match("/PowerPC/i", $agent))
        {
            $os = "PowerPC";
        }
        else if (preg_match("/AIX/i", $agent))
        {
            $os = "AIX";
        }
        else if (preg_match("/HPUX/i", $agent))
        {
            $os = "HPUX";
        }
        else if (preg_match("/NetBSD/i", $agent))
        {
            $os = "NetBSD";
        }
        else if (preg_match("/BSD/i", $agent))
        {
            $os = "BSD";
        }
        else if (preg_match("/OSF1/i", $agent))
        {
            $os = "OSF1";
        }
        else if (preg_match("/IRIX/i", $agent))
        {
            $os = "IRIX";
        }
        else if (preg_match("/FreeBSD/i", $agent))
        {
            $os = "FreeBSD";
        }
        else if (preg_match("/teleport/i", $agent))
        {
            $os = "teleport";
        }
        else if (preg_match("/flashget/i", $agent))
        {
            $os = "flashget";
        }
        else if (preg_match("/webzip/i", $agent))
        {
            $os = "webzip";
        }
        else if (preg_match("/offline/i", $agent))
        {
            $os = "offline";
        }
        else
        {
            $os = "Unknown";
        }
        return $os;
    }


    public function getBrowser()
    {
        $Agent = $_SERVER["HTTP_USER_AGENT"];
        $browser = "";
        $browserver = "";
        $Browsers = array("Lynx", "MOSAIC", "AOL", "Opera", "JAVA", "MacWeb", "WebExplorer", "OmniWeb");
        for ($i = 0; $i <= 7; $i ++)
        {
            if(strpos($Agent, $Browsers[$i]))
            {
                $browser = $Browsers[$i];
            }
        }
        if (preg_match("/Mozilla/", $Agent))
        {

            if (preg_match("/MSIE/", $Agent))
            {
                preg_match("/MSIE (.*);/U",$Agent,$args);
                $browserver = $args[1];
                $browser = "Internet Explorer";
            }
            else if (preg_match("/Opera/", $Agent))
            {
                $temp = explode(")", $Agent);
                $browserver = $temp[1];
                $temp = explode(" ", $browserver);
                $browserver = $temp[2];
                $browser = "Opera";
            }
            else
            {
                $temp = explode("/", $Agent);
                $browserver = $temp[1];
                $temp = explode(" ", $browserver);
                $browserver = $temp[0];
                $browser = "Netscape Navigator";
            }
        }
        if($browser != "")
        {
            $browseinfo = $browser . " " . $browserver;
        }
        else
        {
            $browseinfo = false;
        }
        return $browseinfo;
    }


    public function parseSignature($uname = null)
    {
        $sysmap = array
        (
            "HP-UX" => "hpux",
            "IRIX64" => "irix",
        );
        $cpumap = array
        (
            "i586" => "i386",
            "i686" => "i386",
            "ppc" => "powerpc",
        );
        if ($uname === null)
        {
            $uname = php_uname();
        }
        $parts = split("[[:space:]]+", trim($uname));
        $n = count($parts);

        $release = $machine = $cpu = '';
        $sysname = $parts[0];
        $nodename = $parts[1];
        $cpu = $parts[$n-1];
        $extra = "";
        if ($cpu == "unknown")
        {
            $cpu = $parts[$n-2];
        }

        switch ($sysname)
        {
            case "AIX":
                $release = "{$parts[3]}.{$parts[2]}";
                break;
            case "Windows":
                switch ($parts[1])
                {
                    case "95/98":
                        $release = "9x";
                        break;
                    default:
                        $release = $parts[1];
                        break;
                }
                $cpu = "i386";
                break;
            case "Linux":
                $extra = $this->_detectGlibcVersion();
                // use only the first two digits from the kernel version
                $release = ereg_replace("^([[:digit:]]+\.[[:digit:]]+).*", "\\1", $parts[2]);
                break;
            case "Mac" :
                $sysname = "darwin";
                $nodename = $parts[2];
                $release = $parts[3];
                if ($cpu == "Macintosh")
                {
                    if ($parts[$n - 2] == "Power")
                    {
                        $cpu = "powerpc";
                    }
                }
                break;
            case "Darwin" :
                if ($cpu == "Macintosh")
                {
                    if ($parts[$n - 2] == "Power")
                    {
                        $cpu = "powerpc";
                    }
                }
                $release = ereg_replace("^([[:digit:]]+\.[[:digit:]]+).*", "\\1", $parts[2]);
                break;
            default:
                $release = ereg_replace("-.*", "", $parts[2]);
                break;
        }

        if (isset($sysmap[$sysname]))
        {
            $sysname = $sysmap[$sysname];
        }
        else
        {
            $sysname = strtolower($sysname);
        }
        if (isset($cpumap[$cpu]))
        {
            $cpu = $cpumap[$cpu];
        }
        return array($sysname, $release, $cpu, $extra, $nodename);
    }
}