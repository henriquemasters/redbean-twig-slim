<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Intervention\Image\ImageManagerStatic as Image;
use App\Model\User;
use App\Model\Profile;

final class ProfileController extends BaseController {

    /**
     * Exibe a pagina de perfil do usuario autenticado.
     */
    public function index(Request $request, Response $response, array $args): ?Response {
        $this->view->render($response, 'admin/pages/profile.twig', [
            'title' => 'Meu Perfil',
            'user_auth' => $_SESSION['user_auth'],
            'profile' => Profile::getOne($_SESSION['user_auth']['id'])
        ]);

        return $response;
    }

    /**
     * Salva os dados enviados pelo formulario de perfil.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if ($request->isPost()) {
            $profile = $request->getParsedBody()['profile'] ?? [];
            $requiredFields = ['user_id', 'fullname', 'bornat', 'gender', 'maritalstatus', 'phone1', 'district', 'address', 'city'];

            foreach ($requiredFields as $field) {
                if (trim((string) ($profile[$field] ?? '')) === '') {
                    return $response->withRedirect($this->router->pathFor('profile'));
                }
            }

            User::updateName((int) $profile['user_id'], $this->getShortName($profile['fullname']));
            $profile['bornat'] = $profile['bornat'] ? date('Y-m-d', strtotime(str_replace('/', '-', $profile['bornat']))) : null;

            Profile::save($profile);
        }

        return $response->withRedirect($this->router->pathFor('profile'));
    }

    /**
     * Exibe o modal de foto ou grava um novo upload de foto do perfil.
     */
    public function changePhoto(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/profile/photo.twig', [
                'user' => $_SESSION['user_auth']
            ]);

            return $response;
        }

        $userId = (int) ($request->getParsedBody()['userId'] ?? 0);
        if ($userId <= 0 || !isset($request->getUploadedFiles()['photo'])) {
            return $response->withRedirect($this->router->pathFor('profile'));
        }

        $newPhoto = $this->uploadProfilePhoto($request, (string) User::getPhoto($userId));

        if ($newPhoto !== null) {
            $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getBasePath();
            $_SESSION['user_auth']['photo'] = $baseUrl . $newPhoto;
            User::updatePhoto($userId, $newPhoto);
        }

        return $response->withRedirect($this->router->pathFor('profile'));
    }

    /**
     * Atualiza a senha do usuario atual.
     */
    public function changePass(Request $request, Response $response, array $args): ?Response {
        if ($request->isPost()) {
            $data = $request->getParsedBody();
            $password = (string) ($data['pass'] ?? '');
            $confirmation = (string) ($data['confirmpassword'] ?? '');

            if ($password !== '' && $password === $confirmation) {
                User::save($data);
            }
        }

        return $response->withRedirect($this->router->pathFor('profile'));
    }

    /**
     * Armazena o avatar enviado como JPG normalizado em 300x300.
     */
    private function uploadProfilePhoto(Request $request, string $oldPhoto): ?string {
        $uploadedFile = $request->getUploadedFiles()['photo'];
        $data = $request->getParsedBody();
        $userId = $data['userId'];

        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return null;
        }

        foreach ([$this->upload_dir, $this->upload_dir . '/users', $this->upload_dir . "/users/{$userId}"] as $directory) {
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
                chmod($directory, 0777);
            }
        }

        $oldPhotoPath = $this->upload_dir . str_replace('/uploads', '', $oldPhoto);
        if ($oldPhoto !== '' && is_file($oldPhotoPath)) {
            unlink($oldPhotoPath);
        }

        $newPhoto = "/users/{$userId}/" . uniqid('', true) . '.jpg';
        Image::configure(['driver' => 'imagick']);
        Image::make($uploadedFile->getStream()->getMetadata('uri'))
                ->encode('jpg', 80)
                ->fit(300, 300)
                ->save($this->upload_dir . $newPhoto);

        return "/uploads{$newPhoto}";
    }

    /**
     * Converte um nome completo em um rotulo compacto com primeiro e ultimo nome.
     */
    private function getShortName(string $fullname): ?string {
        $parts = explode(' ', trim($fullname));

        if (count($parts) === 1) {
            return $parts[0];
        }

        return array_shift($parts) . ' ' . array_pop($parts);
    }

}
