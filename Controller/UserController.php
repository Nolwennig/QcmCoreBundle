<?php

namespace Qcm\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 */
class UserController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request)
    {
        $resource = $this->findOr404($request);

        if ($this->config->isApiRequest()) {
            $form = $this->container->get('form.factory')->createNamed('', $this->config->getFormType(), $resource);
        } else {
            $form = $this->createForm('qcm_core_user_profile', $resource);
        }

        if ($resource ==  $this->container->get('security.context')->getToken()->getUser()) {
            $form->remove('enabled');
        }

        $method = $request->getMethod();

        if (in_array($method, array('POST', 'PUT', 'PATCH')) &&
            $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->domainManager->update($resource);

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($resource));
            }

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView()
            ));

        return $this->handleView($view);
    }
}
