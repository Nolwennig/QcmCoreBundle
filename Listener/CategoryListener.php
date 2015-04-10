<?php

namespace Qcm\Bundle\CoreBundle\Listener;

use Qcm\Component\Category\Model\CategoryInterface;
use Sylius\Component\Resource\Event\ResourceEvent;

/**
 * Class CategoryListener
 */
class CategoryListener
{
    /**
     * Pre delete
     *
     * @param ResourceEvent $event
     */
    public function preDelete(ResourceEvent $event)
    {
        /** @var CategoryInterface $resource */
        $resource = $event->getSubject();

        if ($resource->getQuestions()->count() > 0) {
            $event->stop('question_exist', 'danger');
        }
    }
}
