<?php

// Non-functional test

namespace tests\AppBundle\Service\Comment;

use AppBundle\Entity\User;
use AppBundle\Entity\Trick;
use AppBundle\Entity\Comment;
use AppBundle\Service\Comment\AddComment;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Extension\Core\CoreExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Tests\Fixtures\FakeMetadataFactory;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AddCommentTest extends TypeTestCase
{
    private $tokenStorageInterface;
    private $objectManager;
    private $token;
    private $formFactory;

    public function setUp()
    {
        $user = new User();

        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->tokenStorageInterface = $this->createMock(TokenStorageInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new HttpFoundationExtension())
            ->getFormFactory();

        $this->token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $this->tokenStorageInterface
            ->expects($this->once())
            ->method('getToken')
            ->willReturn($this->token);
    }

    public function testCreateComment()
    {
        $request = new Request();
        $trick = new Trick();
        $comment = 'A great comment!';

        $addComment = new AddComment($this->objectManager, $this->tokenStorageInterface, $this->formFactory);
        $form = $addComment->add($request, $trick);
        $this->assertInstanceOf(FormView::class, $form);
    }

    public function tearDown()
    {
        $this->tokenStorageInterface = null;
        $this->token = null;
        $this->objectManager = null;
        $this->formFactory = null;
    }

    public function getExtensions()
    {
        $extensions = parent::getExtensions();
        $metadataFactory = new FakeMetadataFactory();
        $metadataFactory->addMetadata(new ClassMetadata(  Form::class));
        $validator = $this->createValidator($metadataFactory);

        $extensions[] = new CoreExtension();
        $extensions[] = new ValidatorExtension($validator);
        $extensions[] = new ValidatorExtension(Validation::createValidator());

        return $extensions;
        //return [new ValidatorExtension(Validation::createValidator())];
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
