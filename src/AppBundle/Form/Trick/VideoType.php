<?php

// src/AppBundle/Form/Trick/VideoType.php

namespace AppBundle\Form\Trick;

use AppBundle\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Sub form for addType.
 */
class VideoType extends AbstractType
{
    /**
     * Construct the form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $option
     */
    public function buildForm(FormBuilderInterface $builder, array $option)
    {
        // Using $option to avoid Codacy error "Unused Code"
        $option = null;

        // The entity fields are added to our form.
        $builder
            ->add('url', UrlType::class, [
                'label' => 'Url de la video',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'error_bubbling' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '#^(http|https)://(www.youtube.com|www.dailymotion.com|vimeo.com)/#',
                        'message' => 'L\'url n\'est pas valide. Sont seulement supportés les plateformes 
                        youtube, dailymotion et vimeo.',
                    ]),
                ],
            ]);
    }

    /**
     * The options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Video::class,
        ));
    }
}
