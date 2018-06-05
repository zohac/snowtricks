<?php

namespace tests\AppBundle\Form\registrationTypeTest;

use AppBundle\Entity\User;
use AppBundle\Form\User\RegistrationType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class RegistrationTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $userToTest = new User();

        $formData = [
            'username' => 'zohac',
            'email' => 'zohac@test.fr',
            'plainPassword' => [
                'first' => 'aGreatPassword',
                'second' => 'aGreatPassword',
            ],
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(RegistrationType::class, $userToTest);

        $user = new User();
        $user->setUsername('zohac');
        $user->setEmail('zohac@test.fr');
        $user->setPlainPassword('aGreatPassword');

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($user, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    protected function getExtensions()
    {
        return [new ValidatorExtension(Validation::createValidator())];
    }
}
