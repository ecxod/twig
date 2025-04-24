<?php

declare(strict_types=1);

namespace Ecxod\Twig;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class T
{
    public             $loader;
    public string      $namespace;
    public Environment $twig;
    public string      $templatePath;

    public function __construct(string $namespace = 'header', string $templatePath = "templates")
    {
        $this->namespace    = $namespace;
        $this->templatePath = $templatePath;
    }

    public function twig(): Environment
    {
        $this->loader = new FilesystemLoader(paths: $this->getTwigTemplatespath());
        // Namespacing : wir erstellen ein twig namespace 'index'
        $this->loader->addPath(path: $this->getTwigTemplatespath(), namespace: $this->namespace);
        // twig Objekt erstellen
        return new Environment(loader: $this->loader);
    }



    /** Gets the path of the inner Library Twigg folder
     *  - it uses getVendorpath() only if no vendorpath was injected in getTwigpath()
     * TODO: sollte irgendwann in ein ecxod/twigg umziehen 
     * @param bool|string $vendorPath
     * @return bool|string
     */
    public function getTwigpath(bool|string $vendorPath = null): bool|string
    {
        $vendorPath ??= $this->getVendorpath();
        $namespace  = \str_replace(search: '\\', replace: DIRECTORY_SEPARATOR, subject: \strtolower(string: __NAMESPACE__));
        $twigPath   = \realpath($vendorPath . DIRECTORY_SEPARATOR . $namespace);
        if(empty($twigPath))
        {
            die("The Library " . __NAMESPACE__ . " is missing, damaged or in a wrong version! ERR[H00600]");
        }
        else
            return $twigPath;
    }

    /** Gets the path of the inner Library templates folder
     *  - it uses getVendorpath() only if no vendorpath was injected in getTwigpath()
     * TODO: sollte irgendwann in ein ecxod/twigg umziehen 
     * @param bool|string $vendorPath
     * @return bool|string
     */
    public function getTwigTemplatespath(bool|string $vendorPath = null): bool|string
    {
        $vendorPath ??= $this->getVendorpath();
        $twigTemplatePath = \realpath($this->getTwigpath($vendorPath) . DIRECTORY_SEPARATOR . $this->templatePath);
        if(empty($twigTemplatePath))
        {
            die("The Library " . __NAMESPACE__ . " is missing, damaged or in a wrong version! ERR[H00700]");
        }
        else
            return $twigTemplatePath;
    }

    public function getStaticFolderPath()
    {
        $publicFolderPath = $this->getPublicFolderPath();
        if(empty($_ENV["STATIC"]))
            die("Please set \$_ENV['STATIC'] to the foldername of the static folder inside the public folder ( example: \$_ENV['STATIC']='static'). ERR[H00300]");
        $staticFolderPath = \realpath($publicFolderPath . DIRECTORY_SEPARATOR . $_ENV["STATIC"]);
        if(empty($staticFolderPath))
        {
            die("Please set \$_ENV['STATIC'] to the foldername of the static folder inside the public folder ( example: \$_ENV['STATIC']='static'). ERR[H00400]");
        }
        else
            return $staticFolderPath;
    }
    public function getPublicFolderPath()
    {
        if(empty($_SERVER["DOCUMENT_ROOT"]))
            die("Please set DOCUMENT_ROOT.");

        $rootFolderPath = \realpath(path: $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . '..');
        if(empty($_ENV["ROOT"]) or strval($_ENV["ROOT"]) !== $rootFolderPath)
            die("Please set \$_ENV['DOCROOT'] to '{$rootFolderPath}'. - ERR[0001]");

        if(empty($_ENV["DOCROOT"]) or strval($_ENV["DOCROOT"]) !== strval($_SERVER["DOCUMENT_ROOT"]))
            die("Please set \$_ENV['DOCROOT'] to '{$_SERVER["DOCUMENT_ROOT"]}'. - ERR[0001]");

        if(empty($_ENV["PUBLIC"]))
            die("Please set \$_ENV['PUBLIC'] to something like \$_ENV['PUBLIC']='public' inside your Document Root.");

        $publicFolderPath = \realpath(path: $_ENV["ROOT"] . DIRECTORY_SEPARATOR . $_ENV['PUBLIC']);
        if(empty($publicFolderPath))
            die("Unable to find Public Folder Path! - ERR[H00100]");
        else
            return $publicFolderPath;
    }
    /** Get the Composer Vendor Path
     * @return string
     * TODO: sollte irgendwann in ein ecxod/composer umziehen 
     */
    public function getVendorpath(): string
    {
        $vendorpath = "";
        if(empty($_SERVER["DOCUMENT_ROOT"]))
            die("Please set DOCUMENT_ROOT.");

        if(empty($_ENV["VENDOR"]))
            die("Please set \$_ENV['VENDOR'] to something like '" . $_SERVER["DOCUMENT_ROOT"] . "/../vendor/" . "' inside your Document Root.");

        $envVendorPath = \strval(\realpath($_ENV['VENDOR']));

        if(empty($envVendorPath))
        {
            die("Unable to find Composer Vendor Path! - ERR[H00500]");
        }
        else
        {
            $vendorpath = $envVendorPath;
            return $vendorpath;
        }
    }
}
