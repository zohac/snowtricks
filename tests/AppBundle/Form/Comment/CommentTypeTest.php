<?php

namespace tests\AppBundle\Form\Comment;

use AppBundle\Entity\Comment;
use AppBundle\Form\Comment\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $commentToTest = new Comment();

        $formData = [
            'content' => 'A great test!',
        ];

        // $objectToCompare will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(CommentType::class, $commentToTest);

        $comment = new Comment();
        $comment->setContent('A great test!');

        // submit the data to the form directly
        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());

        // check that $objectToCompare was modified as expected when the form was submitted
        $this->assertEquals($comment, $commentToTest);

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
