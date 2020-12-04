<?php

namespace App\Library\Factory\Mail;

abstract class AbstractMail
{
    /** @var string */
    protected $to;

    /** @var string */
    protected $subject;

    /** @var string */
    protected $template;

    /** @var array */
    protected $parameters;

    /**
     * @param string $to
     * @param array $parameters
     */
    public function prepare(string $to, array $parameters)
    {
        $this->to = $to;
        $this->parameters = $parameters;
        $this->subject = $this->getMailSubject();
        $this->template = $this->getMailTemplate();
    }

    /**
     * @return string
     */
    abstract protected function getMailTemplate(): string;

    /**
     * @return string
     */
    abstract protected function getMailSubject(): string;

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to): void
    {
        $this->to = $to;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
