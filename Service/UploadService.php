<?php

namespace PhotoBundle\Service;

use PhotoBundle\Entity\Photo;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadService
 * @package PhotoBundle\Service
 */
class UploadService
{
    /**
     * @var Container
     */
    private $container;

    /**
     * UploadService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    private function getUploadRootPath()
    {
        return $this->container->getParameter('kernel.root_dir') . '/../web/uploads/';
    }

    /**
     * @param string $folder
     * @return string
     */
    private function getUploadPath($folder)
    {
        return $this->getUploadRootPath() . $folder . '/';
    }

    /**
     * @return string
     */
    private function getWebRootPath()
    {
        return '/uploads/';
    }

    public function getWebPath($folder)
    {
        return $this->getWebRootPath() . $folder . '/';
    }

    /**
     * @param string $extension
     * @return string
     */
    private function generateFileName($extension)
    {
        $bytes = random_bytes(5);
        return bin2hex($bytes) . '.' . $extension;
    }

    /**
     * @param UploadedFile $file
     * @param string       $folder
     * @return null|Photo
     * @throws \Throwable
     */
    public function uploadFile(UploadedFile $file, $folder)
    {
        if (is_dir($this->getUploadPath($folder))) {
            $managerPhoto = $this->container->get('photo.manager.photo_manager');
            $newFilename  = $this->generateFileName($file->guessExtension());

            try {
                if (move_uploaded_file($file->getPathname(), $this->getUploadPath($folder) . $newFilename)) {
                    $photo = new Photo();
                    $photo->setPath($this->getWebPath($folder) . $newFilename);

                    return $managerPhoto->createPhoto($photo);
                }
            } catch (\Exception $e) { }
        }

        return null;
    }

    /**
     * @param UploadedFile $file
     * @param string       $orientation
     * @param string       $minWidth
     * @param string       $minHeight
     * @return array
     */
    public function validationFile(UploadedFile $file, $orientation, $minWidth, $minHeight)
    {
        list($width, $height) = getimagesize($file->getPathname());

        $translator = $this->container->get('translator');

        $errors = array();

        if ($orientation == 'landscape') {
            if ($width < $height) {
                $errors[] = $translator->trans('errors.orientation', ['%orientation%' => 'landscape'], 'photo');
            }
        } else {
            if ($width >= $height) {
                $errors[] = $translator->trans('errors.orientation', ['%orientation%' => 'portrait'], 'photo');
            }
        }

        if ($minWidth > $width) {
            $errors[] = $translator->trans('errors.min_width', ['%width%' => $minWidth], 'photo');
        }

        if ($minHeight > $height) {
            $errors[] = $translator->trans('errors.min_height', ['%height%' => $minHeight], 'photo');
        }

        return $errors;
    }
}
