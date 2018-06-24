<?php

namespace tests\AppBundle\Utils;

use AppBundle\Entity\User;
use Symfony\Component\Form\Form;
use AppBundle\Repository\UserRepository;
use AppBundle\Form\User\ForgotPasswordType;
use Symfony\Component\Form\Test\TypeTestCase;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Utils\User\ForgotPasswordTypeHandler;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Tests\Fixtures\FakeMetadataFactory;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class ForgotPasswordTypeHandlerTest extends TypeTestCase
{
    /**
     * @var ObjectManager
     */
    private $entityManager;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    protected function setUp()
    {
        parent::setUp();

        // Last, mock the EntityManager to return the mock of the repository
        $this->entityManager = $this->createMock(ObjectManager::class);

        $this->session = new Session(new MockArraySessionStorage());

        $this->eventDispatcher = $this
            ->getMockBuilder(EventDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHandleTrue()
    {
        // Now, mock the repository so it returns the mock of the user
        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneByEmail'])
            ->getMock();
        $userRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->willReturn(new User());

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($userRepository);

        $formData = [
            'emailRecovery' => 'email@test.com',
        ];

        $form = $this->factory->create(ForgotPasswordType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new ForgotPasswordTypeHandler(
            $this->entityManager,
            $this->session,
            $this->eventDispatcher
        );

        $this->assertTrue($handler->handle($form));
    }

    public function testHandleFashBecauseNoUser()
    {
        // Now, mock the repository so it returns the mock of the user
        $userRepository = $this
            ->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findOneByEmail'])
            ->getMock();
        $userRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->willReturn(null);

        $this->entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($userRepository);

        $formData = [
            'emailRecovery' => 'email@test.com',
        ];

        $form = $this->factory->create(ForgotPasswordType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new ForgotPasswordTypeHandler(
            $this->entityManager,
            $this->session,
            $this->eventDispatcher
        );

        $this->assertFalse($handler->handle($form));
    }

    public function testHandleFalse()
    {
        $formData = [
            'emailRecovery' => null,
        ];

        $form = $this->factory->create(ForgotPasswordType::class);

        // submit the data to the form directly
        $form->submit($formData);

        $handler = new ForgotPasswordTypeHandler(
            $this->entityManager,
            $this->session,
            $this->eventDispatcher
        );

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
