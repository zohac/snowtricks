<?php

namespace AppBundle\Listener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AntiSqlInjectionFormListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SUBMIT => 'onPreSubmit'];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $sqlCommand = [
            'SELECT',
            'UPDATE',
            'DELETE FROM',
            'ALTER TABLE',
            'CREATE TABLE',
            'INSERT INTO',
        ];

        $data = $event->getData();

        // do webservice validation here and
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    $data[$key][$subKey] = trim(str_ireplace($sqlCommand, '', $subValue));
                }
            }
            if (is_string($value)) {
                $data[$key] = trim(str_ireplace($sqlCommand, '', $value));
            }
        }
        var_dump($data);
        // set new data
        $event->setData($data);
    }
}
