<?php

namespace Qcm\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class QuestionController
 */
class QuestionController extends Controller
{
    public function indexAction()
    {
        return $this->render('QcmCoreBundle:Question:index.html.twig');
    }
}
