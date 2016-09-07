<?php

namespace Qcm\Bundle\CoreBundle\Statistics\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Qcm\Component\Statistics\Model\Checker\CheckerValidatorInterface;
use Qcm\Component\Statistics\Model\ScoreInterface;
use Qcm\Component\Statistics\Model\TemplateInterface;
use Qcm\Component\User\Model\UserSessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class QuestionnaireStatistics
 */
abstract class QuestionnaireStatistics
{
    /**
     * @var ScoreInterface $score
     */
    protected $score;

    /**
     * @var ArrayCollection $data
     */
    protected $data;

    /**
     * @var string $answerTemplate
     */
    protected $answerTemplate;

    /**
     * @var CheckerValidatorInterface $checker
     */
    private $checker;

    /**
     * Construct
     *
     * @param ScoreInterface            $score
     * @param string                    $template
     * @param CheckerValidatorInterface $checker
     */
    public function __construct(ScoreInterface $score, $template, CheckerValidatorInterface $checker)
    {
        $this->score = $score;
        $this->answerTemplate = $template;
        $this->checker = $checker;
        $this->data = new ArrayCollection();
    }

    /**
     * Parse questionnaire results
     *
     * @param UserSessionInterface $userSession
     *
     * @return $this
     */
    public function execute(UserSessionInterface $userSession)
    {
        foreach ($userSession->getConfiguration()->getQuestions() as $key => $question) {
            /** @var TemplateInterface $template */
            $template = new $this->answerTemplate;
            $template->setQuestion($question);

            if (is_null($data = $userSession->getConfiguration()->getAnswers()->get($key))) {
                $this->score->addNotValid();
                $this->data->add($template);
                continue;
            }

            $template->setFlag(isset($data['flag']) ? $data['flag'] : false);
            unset($data['flag']);
            $isValid = $this->checker->get($question->getType())->validate($data, $question, $this->score);
            $template->setValid($isValid);

            $this->data->add($template);
        }

        return $this;
    }

    /**
     * Get data results
     *
     * @return ArrayCollection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get specific template by question id
     *
     * @param integer $id
     *
     * @return TemplateInterface
     */
    public function getQuestion($id)
    {
        /** @var TemplateInterface $template */
        foreach ($this->data as $template) {
            if ($template->getQuestion()->getId() == $id) {
                return $template;
            }
        }

        throw new NotFoundHttpException(sprintf('Id question %s not found', $id));
    }

    /**
     * Get score
     *
     * @return array
     */
    public function getScore()
    {
        return $this->score->getScore();
    }
}
