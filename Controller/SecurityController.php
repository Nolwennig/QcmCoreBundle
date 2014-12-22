<?php

namespace Qcm\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class SecurityController
 */
class SecurityController extends ResourceController
{
    /**
     * Login action
     *
     * @return Response
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('login.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error
            ));

        return $this->handleView($view);
    }
}
