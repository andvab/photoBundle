<?php

namespace PhotoBundle\Manager;

use Doctrine\ORM\EntityManager;
use PhotoBundle\Entity\Photo;

/**
 * Class PhotoManager
 * @package PhotoBundle\Manager
 */
class PhotoManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;

    /**
     * PhotoManager constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em   = $em;
        $this->repo = $em->getRepository('PhotoBundle:Photo');
    }

    /**
     * @param int $photoId
     * @return Photo
     */
    public function getPhoto($photoId)
    {
        return $this->repo->find($photoId);
    }

    /**
     * @param Photo $photo
     * @return Photo
     */
    public function createPhoto(Photo $photo)
    {
        $this->em->persist($photo);
        $this->em->flush();

        return $photo;
    }
}
