<?php

namespace tests\AppBundle\Form\User;

use AppBundle\Entity\User;
use AppBundle\Form\User\ResetPasswordType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

class ResetPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $userToTest = new User();

        $formData = [
            'plainPassword' => [
                'first' => 'aGreatPassword',
                'second' => 'aGreatPassword',
            ],
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ResetPasswordType::class, $userToTest);

        $user = new User();
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