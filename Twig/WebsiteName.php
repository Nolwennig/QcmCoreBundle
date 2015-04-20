<?php

namespace Qcm\Bundle\CoreBundle\Twig;

/**
 * Class WebsiteName
 */
class WebsiteName extends \Twig_Extension
{
    protected $websiteName;

    /**
     * @param string $websiteName
     */
    public function __construct($websiteName)
    {
        $this->websiteName = $websiteName;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('website_name', array($this, 'getWebsiteName')),
        );
    }

    /**
     * Get website name
     *
     * @return string
     */
    public function getWebsiteName()
    {
        return $this->websiteName;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'website_name';
    }
}
