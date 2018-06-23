<?php

namespace tests\AppBundle\Utils;

use Symfony\Component\Form\Form;
use AppBundle\Form\User\UserType;
use AppBundle\Utils\User\UserTypeHandler;
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

class UserTypeHandlerTest extends TypeTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;

    /**
     * @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    protected function setUp()
    {
        parent::setUp();

        // Last, mock the EntityManager to return the mock of the repository
        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordEncoder = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHandleTrue()
    {
        $formData = [
            'username' => 'zohac',
            'email' => 'zohac@test.fr',
            'plainPassword' => [
                'first' => 'aGreatPassword',
                'second' => 'aGreatPassword',
            ],
        ];

        $form = $this->factory->create(UserType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new UserTypeHandler($this->entityManager, $this->passwordEncoder);

        $this->assertTrue($handler->handle($form));
    }

    public function testHandleFalse()
    {
        $formData = [
            'username' => null,
            'email' => 'zohac@test.fr',
            'plainPassword' => [
                'first' => 'aGreatPassword',
                'second' => 'aGreatPassword',
            ],
        ];

        $form = $this->factory->create(UserType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new UserTypeHandler($this->entityManager, $this->passwordEncoder);

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
