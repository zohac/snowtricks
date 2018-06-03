<?php

namespace tests\AppBundle\Form\User;

use AppBundle\Entity\User;
use AppBundle\Form\User\ForgotPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;

class ForgotPasswordTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $userToTest = new User();

        $formData = [
            'emailRecovery' => 'zohac@test.fr',
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ForgotPasswordType::class, $userToTest);

        $user = new User();
        $user->setEmailRecovery('zohac@test.fr');

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
}