<?php

namespace PhotoBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;
use PhotoBundle\Entity\Photo;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class PhotoToIdTransformer
 * @package PhotoBundle\Form\DataTransformer
 */
class PhotoToIdTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * TripRoutePhotoTransformer constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an photo to a string (number).
     *
     * @param mixed $photo
     * @return string
     */
    public function transform($photo)
    {
        if (null === $photo) {
            return '';
        }

        return $photo->getId();
    }

    /**
     * Transforms a string (number) to an object (photo).
     *
     * @param  string $photoNumber
     * @return Photo|null
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($photoNumber)
    {
        if (!$photoNumber || is_array($photoNumber)) {
            return;
        }

        $issue = $this->em
            ->getRepository('PhotoBundle:Photo')
            ->find($photoNumber)
        ;

        if (null === $issue) {
            throw new TransformationFailedException(sprintf(
                'An photo with number "%s" does not exist!',
                $photoNumber
            ));
        }

        return $issue;
    }
}
