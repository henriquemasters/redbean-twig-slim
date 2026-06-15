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
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada.
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
     *
     * O nome exibido do usuario e derivado do nome completo do perfil para manter
     * menu/cabecalho sincronizados com o registro mais completo de perfil.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta de redirecionamento.
     */
    public function create(Request $request, Response $response, array $args): ?Response {
        if ($request->isPost()) {
            $profile = $request->getParsedBody()['profile'];

            User::updateName($profile['user_id'], $this->getShortName($profile['fullname']));
            $profile['bornat'] = $profile['bornat'] ? date('Y-m-d', strtotime(str_replace('/', '-', $profile['bornat']))) : null;

            Profile::save($profile);
        }

        return $response->withRedirect('../profile');
    }

    /**
     * Exibe o modal de foto ou grava um novo upload de foto do perfil.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta renderizada ou redirecionamento.
     */
    public function changePhoto(Request $request, Response $response, array $args): ?Response {
        if (!$request->isPost()) {
            $this->view->render($response, 'admin/ui/modals/profile/photo.twig', [
                'user' => $_SESSION['user_auth']
            ]);
        } else {
            $userId = $request->getParsedBody()['userId'];
            $newPhoto = $this->uploadProfilePhoto($request, (string) User::getPhoto($userId));

            if ($newPhoto !== null) {
                $baseUrl = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() . $request->getUri()->getBasePath();
                $_SESSION['user_auth']['photo'] = $baseUrl . $newPhoto;
                User::updatePhoto($userId, $newPhoto);
            }

            $response = $response->withRedirect('../profile');
        }

        return $response;
    }

    /**
     * Atualiza a senha do usuario atual.
     *
     * @param Request $request Requisicao PSR-7 atual.
     * @param Response $response Resposta PSR-7 atual.
     * @param array $args Argumentos da rota fornecidos pelo Slim.
     * @return Response|null Resposta de redirecionamento.
     */
    public function changePass(Request $request, Response $response, array $args): ?Response {
        if ($request->isPost()) {
            User::save($request->getParsedBody());
        }

        return $response->withRedirect('../profile');
    }

    /**
     * Armazena o avatar enviado como JPG normalizado em 300x300.
     *
     * O banco guarda um caminho web (/uploads/...), enquanto este metodo escreve
     * no caminho fisico configurado no container. Essa separacao e importante em
     * deploys com virtual host ou proxy reverso.
     *
     * @param Request $request Requisicao PSR-7 atual contendo o arquivo enviado.
     * @param string $oldPhoto Caminho web anterior armazenado na tabela user.
     * @return string|null Novo caminho web ou null quando nao ha upload valido.
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
     *
     * @param string $fullname Nome completo do perfil.
     * @return string|null Nome compacto para exibicao.
     */
    private function getShortName(string $fullname): ?string {
        $parts = explode(' ', trim($fullname));

        if (count($parts) === 1) {
            return $parts[0];
        }

        return array_shift($parts) . ' ' . array_pop($parts);
    }

}
