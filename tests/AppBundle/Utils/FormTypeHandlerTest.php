<?php

namespace tests\AppBundle\Utils;

use Symfony\Component\Form\Form;
use AppBundle\Utils\FormTypeHandler;
use AppBundle\Form\Comment\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Tests\Fixtures\FakeMetadataFactory;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;

class FormTypeHandlerTest extends TypeTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;

    protected function setUp()
    {
        parent::setUp();

        // Last, mock the EntityManager to return the mock of the repository
        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHandleTrue()
    {
        $formData = [
            'content' => 'A great test!',
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CommentType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new FormTypeHandler($this->entityManager);

        $this->assertTrue($handler->handle($form));
    }

    public function testHandleFalse()
    {
        $formData = [
            'content' => null,
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CommentType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new FormTypeHandler($this->entityManager);

        $this->assertFalse($handler->handle($form));
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

    protected function tearDown()
    {
        parent::tearDown();

        // avoid memory leaks
        $this->entityManager = null;
    }
}
