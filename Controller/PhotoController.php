<?php

namespace PhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PhotoController
 * @package PhotoBundle\Controller
 */
class PhotoController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadDropzoneAction(Request $request)
    {
        $file          = $request->files->get('file');
        $folder        = $request->get('type');
        $subtype       = $request->get('subtype');
        $orientation   = $this->getParameter("photo.$folder.$subtype.orientation");
        $minWidth      = $this->getParameter("photo.$folder.$subtype.min-width");
        $minHeight     = $this->getParameter("photo.$folder.$subtype.min-height");
        $serviceUpload = $this->get('photo.service.upload_service');
        $success       = false;
        $data          = null;

        $errors = $serviceUpload->validationFile($file, $orientation, $minWidth, $minHeight);

        if (!count($errors)) {
            $photo = $serviceUpload->uploadFile($file, $folder);

            if ($photo) {
                $imagineCacheManager = $this->get('liip_imagine.cache.manager');

                $success = true;
                $data    = array(
                    'id'   => $photo->getId(),
                    'path' => $imagineCacheManager->getBrowserPath($photo->getPath(), $folder . '_' . $subtype . '_preview'),
                );
            } else {
                $errors[] = 'Failed to upload file';
            }
        }

        return new JsonResponse(array(
            'success' => $success,
            'errors'  => $errors,
            'data'    => $data,
        ));
    }
}
