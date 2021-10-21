<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Intervention\Image\ImageManagerStatic as Image;
use App\Model\User;
use App\Model\Profile;

final class ProfileController extends BaseController {

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function index(Request $request, Response $response, array $args): ?Response {

        $this->view->render($response, 'admin/pages/profile.twig', [
            'title' => 'Meu Perfil',
            'user_auth' => $_SESSION['user_auth'],
            'profile' => Profile::getOne($_SESSION['user_auth']['id'])
        ]);
        //

        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if ($request->isPost()) {
            $profile = $request->getParsedBody()['profile'];
            // Salva...
            User::updateName($profile['user_id'], $this->getShortName($profile['fullname']));

            $profile['bornat'] = $profile['bornat'] ? date('Y-m-d', strtotime(str_replace('/', '-', $profile['bornat']))) : null;

            Profile::save($profile);
        }

        return $response->withRedirect('../profile');
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function changePhoto(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/profile/photo.twig', [
                'user' => $_SESSION['user_auth']
            ]);
        } else {
            $userId = $request->getParsedBody()['userId'];
            // Upload da Imagem...
            $newPhoto = $this->uploadProfilePhoto($request, (string) User::getPhoto($userId));
            $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getBasePath();
            $_SESSION['user_auth']['photo'] = $baseUrl . $newPhoto;
            // Guarda nome da imagem na tabela user
            User::updatePhoto($userId, $newPhoto);

            $response = $response->withRedirect('../profile');
        }
        //
        return $response;
    }

    /**
     * 
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response|null
     */
    public function changePass(Request $request, Response $response, array $args): ?Response {
        if ($request->isPost()) {
            User::save($request->getParsedBody());
        }
        //
        return $response->withRedirect('../profile');
    }

    /**
     * 
     * @param Request $request
     * @param string $oldPhoto
     * @return string|null
     */
    private function uploadProfilePhoto(Request $request, string $oldPhoto): ?string {
        $UploadedFile = $request->getUploadedFiles()['photo'];

        $return = null;
        if ($UploadedFile->getError() === UPLOAD_ERR_OK) {
            $ext = pathinfo($UploadedFile->getClientFilename(), PATHINFO_EXTENSION);

            if (!is_dir($this->upload_dir)) {
                mkdir($this->upload_dir, 0777, true);
                chmod($this->upload_dir, 0777);
            }

            if (!is_dir($this->upload_dir . "/users")) {
                mkdir($this->upload_dir . "/users", 0777, true);
                chmod($this->upload_dir . "/users", 0777);
            }

            if (!is_dir($this->upload_dir . "/users/{$request->getParsedBody()['userId']}")) {
                mkdir($this->upload_dir . "/users/{$request->getParsedBody()['userId']}", 0777, true);
                chmod($this->upload_dir . "/users/{$request->getParsedBody()['userId']}", 0777);
            }

            // Apaga foto antiga...
            unlink($this->upload_dir . str_replace('/uploads', '', $oldPhoto));

            $newPhoto = "/users/{$request->getParsedBody()['userId']}/" . uniqid() . ".jpg";
            Image::configure(array('driver' => 'imagick'));
            $img = Image::make($UploadedFile->getStream()->getMetadata('uri'))
                    ->encode('jpg', 80)
                    ->fit(300, 300)
                    ->save($this->upload_dir . $newPhoto);
            //
            $return = "/uploads{$newPhoto}";
        }
        return $return;
    }

    /**
     * 
     * @param string $fullname
     * @return string|null
     */
    private function getShortName(string $fullname): ?string {
        $parts = explode(' ', $fullname);
        return array_shift($parts) . ' ' . array_pop($parts);
    }

}
