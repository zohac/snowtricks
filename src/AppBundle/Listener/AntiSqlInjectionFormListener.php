<?php

namespace AppBundle\Listener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AntiSqlInjectionFormListener implements EventSubscriberInterface
{
    /**
     * An array of sql command.
     *
     * @var array
     */
    private $sqlCommand = [
        'SELECT',
        'UPDATE',
        'DELETE FROM',
        'ALTER TABLE',
        'CREATE TABLE',
        'INSERT INTO',
        'DROP',
    ];

    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SUBMIT => 'onPreSubmit'];
    }

    /**
     * on preSubmit form, we remove sql command.
     *
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();

        // do webservice validation here and
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->exploreArray($value);
            }
            if (is_string($value)) {
                $data[$key] = trim(str_ireplace($this->sqlCommand, '', $value));
            }
        }
        // set new data
        $event->setData($data);
    }

    /**
     * remove sql command in an array.
     *
     * @param array $data
     */
    public function exploreArray(array $data)
    {
        foreach ($data as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $data[$key] = trim(str_ireplace($this->sqlCommand, '', $value));
            }
            if (is_array($value)) {
                $this->exploreArray($value);
            }
        }

        return $data;
    }
}
