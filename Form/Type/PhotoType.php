<?php

namespace PhotoBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use PhotoBundle\Form\DataTransformer\PhotoToIdTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Class PhotoType
 * @package PhotoBundle\Form\Type
 */
class PhotoType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * PhotoType constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new PhotoToIdTransformer($this->em));
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}
