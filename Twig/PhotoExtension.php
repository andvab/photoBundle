<?php

namespace PhotoBundle\Twig;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class PhotoExtension
 * @package PhotoBundle\Twig
 */
class PhotoExtension extends \Twig_Extension
{
    /**
     * @var Container
     */
    private $container;

    /**
     * PhotoExtension constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('pathPhoto', array($this, 'getPathPhoto')),
        );
    }

    /**
     * @param int $photoId
     * @return string
     * @throws \Throwable
     */
    public function getPathPhoto($photoId)
    {
        $managerPhoto = $this->container->get('photo.manager.photo_manager');

        if ($photo = $managerPhoto->getPhoto($photoId)) {
            return $photo->getPath();
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('instanceOf', array($this, 'isInstanceOf')),
        );
    }

    /**
     * @param object $object
     * @param object $class
     * @return bool
     */
    public function isInstanceOf($object, $class)
    {
        $reflectionClass = new \ReflectionClass($class);

        return $reflectionClass->isInstance($object);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'twig_photo_extension';
    }
}