<?php

namespace tests\AppBundle\Utils\Trick;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Picture;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;
use AppBundle\Utils\ThumbnailGenerator;
use AppBundle\Utils\Trick\TrickTypeHandler;
//use tests\AppBundle\Utils\Trick\FormTypeTestCase;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Tests\Fixtures\FakeMetadataFactory;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TrickTypeHandlerTest extends TypeTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;

    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    private $thumbnailGenerator;

    private $form;

    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->getFormFactory();

        // Last, mock the EntityManager to return the mock of the repository
        $this->entityManager = $this
            ->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->token = $this
            ->getMockBuilder(TokenInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getUser'])
            ->getMockForAbstractClass();

        $this->tokenStorage = $this
            ->getMockBuilder(TokenStorageInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getToken'])
            ->getMockForAbstractClass();
        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->willReturn($this->token);

        $this->thumbnailGenerator = $this
            ->getMockBuilder(ThumbnailGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->form = $this
            ->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testHandleTrue()
    {
        $trick = new Trick();
        $trick->addPicture(new Picture());
        
        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(new User());

        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(true);
        $this->form
            ->expects($this->once())
            ->method('isValid')
            ->willReturn(true);
        $this->form
            ->expects($this->once())
            ->method('getData')
            ->willReturn($trick);

        $this->thumbnailGenerator
            ->expects($this->once())
            ->method('makeThumb');

        $handler = new TrickTypeHandler($this->entityManager, $this->tokenStorage, $this->thumbnailGenerator);

        $this->assertTrue($handler->handle($this->form));
    }

    public function testHandleFalse()
    {
        $this->form
            ->expects($this->once())
            ->method('isSubmitted')
            ->willReturn(false);
        $this->form
            ->expects($this->any())
            ->method('isValid')
            ->willReturn(false);

        $handler = new TrickTypeHandler($this->entityManager, $this->tokenStorage, $this->thumbnailGenerator);

        $this->assertFalse($handler->handle($this->form));
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

    /**
     * {@inheritdoc}
     */
    protected function getEntities()
    {
        // Register entities used in 'entity' fields here
        return array_merge(parent::getEntities(), array(
            'AppBundle\Entity\Category',
            'AppBundle\Entity\Picture',
            'AppBundle\Entity\Video',
        ));
    }

    protected function tearDown()
    {
        parent::tearDown();
        // avoid memory leaks
        $this->entityManager = null;
    }
}