<?php

namespace tests\AppBundle\Form\User;

use AppBundle\Form\User\ForgotPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Form;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Tests\Fixtures\FakeMetadataFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;

class ForgotPasswordTypeTest extends TypeTestCase
{
    /**
     * Test with valid data.
     */
    public function testSubmitValidData()
    {
        $formData = [
            'emailRecovery' => 'zohac@test.fr',
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ForgotPasswordType::class);

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isValid());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    /**
     * Test with invalid data.
     */
    public function testSubmitInvalidData()
    {
        $formData = [
            'emailRecovery' => 'anInvalidEmail',
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(ForgotPasswordType::class);

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertFalse($form->isValid());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }

    public function getExtensions()
    {
        $extensions = parent::getExtensions();
        $metadataFactory = new FakeMetadataFactory();
        $metadataFactory->addMetadata(new ClassMetadata(Form::class));
        $validator = $this->createValidator($metadataFactory);

        $extensions[] = new CoreExtension();
        $extensions[] = new ValidatorExtension($validator);

        return $extensions;
    }

    protected function createValidator(MetadataFactoryInterface $metadataFactory, array $objectInitializers = array())
    {
        $translator = new IdentityTranslator();
        $translator->setLocale('en');
        $contextFactory = new ExecutionContextFactory($translator);
        $validatorFactory = new ConstraintValidatorFactory();

        return new RecursiveValidator($contextFactory, $metadataFactory, $validatorFactory, $objectInitializers);
    }
}
