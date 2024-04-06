<?php

declare(strict_types=1);

use App\Application\Actions\Signin\{SigninChangePasswordAction, SigninLinkForgotPasswordAction, SigninLoginAction};
use App\Application\Actions\User\{UserListAction, UserViewAction, UserLoginRegisterAction, UserAdminRegisterAction};
use App\Application\Actions\Student\{StudentRegisterAction};

use App\Application\Services\Google\GoogleOAuth;
use App\Application\Services\Facebook\FacebookOAuth;
use App\Application\Services\MercadoPago\{MercadoPagoCreatePreference, MercadoPagoCallback, MercadoPagoNotification, MercadoPagoPagamento};

use App\Application\Middleware\{AuthenticateUserMiddleware, GenerateTokenCSRFMiddleware, RecaptchaMiddleware, ValidateTokenJWTMiddleware};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('/view', function (Group $group) {
        $group->get('/login', function (Request $request, Response $response) {
            $header = $this->get("html")->render('view.header');
            $body = $this->get("html")->render('view.login');
            $page = $header . $body;
            $response->getBody()->write($page);
            return $response;
        });
    });
    $app->group('/caroldance', function (Group $group) {

        $group->group('/signin', function (Group $group) {
            $group->post('', SigninLoginAction::class);
            $group->post('/register', UserLoginRegisterAction::class);
            $group->post('/linkForgotPassword', SigninLinkForgotPasswordAction::class);
            $group->post('/changePassword', SigninChangePasswordAction::class);
        });

        $group->group('/user', function (Group $group) {
            $group->post('', UserListAction::class);
            $group->post('/{id}', UserViewAction::class);
        })->add(AuthenticateUserMiddleware::class);

        $group->group('/admin', function (Group $group) {
            $group->post('/register/student', StudentRegisterAction::class);
            $group->post('/register/user', UserAdminRegisterAction::class);
            // $group->post('/{id}', ViewUserAction::class);
        });
        // ->add(AuthenticateUserMiddleware::class);

        $group->group('/token', function (Group $group) {
            $group->get('/csrf', GenerateTokenCSRFMiddleware::class);
            $group->get('/recaptcha/{token}', RecaptchaMiddleware::class);
            $group->get('/forgotPassword/{token}', ValidateTokenJWTMiddleware::class);
            $group->get('/validateTokenJWT/{token}', ValidateTokenJWTMiddleware::class);
        });

        $group->group('/services', function (Group $services) {
            $services->group('/facebook', function (Group $facebook) {
                $facebook->get('/oAuth/{token}', FacebookOAuth::class);
            });
            $services->group('/google', function (Group $google) {
                $google->get('/oAuth/{token}', GoogleOAuth::class);
            });
            $services->group('/mercadopago', function (Group $mercadopago) {
                $mercadopago->get('/createPreference', MercadoPagoCreatePreference::class);
                $mercadopago->get('/callback', MercadoPagoCallback::class);
                $mercadopago->get('/notification', MercadoPagoNotification::class);
                $mercadopago->get('/pagamento', MercadoPagoPagamento::class);
            });
        });
    });
};
