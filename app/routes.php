<?php

declare(strict_types=1);

use App\Application\Actions\Signin\{ChangePassword, LinkForgotPassword, Signin, Register};
use App\Application\Actions\User\{ListUsersAction, ViewUserAction};

use App\Application\Services\Google\GoogleOAuth;
use App\Application\Services\Facebook\FacebookOAuth;
use App\Application\Services\MercadoPago\{MercadoPagoCreatePreference, MercadoPagoCallback, MercadoPagoNotification, MercadoPagoPagamento};

use App\Application\Middleware\{AuthenticateUserMiddleware, ClearTokenCSRFMiddleware, GenerateTokenCSRFMiddleware, RecaptchaMiddleware, ValidateTokenJWTMiddleware};

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->group('/view',function (Group $group) {
        $group->get('/login', function (Request $request, Response $response) {
            $header = $this->get("html")->render('view.header');
            $body = $this->get("html")->render('view.login');
            $page = $header . $body;
            $response->getBody()->write($page);
            return $response;
        });
    });

    $app->group('/token', function (Group $group) {
        $group->get('/csrf', GenerateTokenCSRFMiddleware::class);
        $group->get('/recaptcha/{token}', RecaptchaMiddleware::class);
        $group->get('/forgotPassword/{token}', ValidateTokenJWTMiddleware::class);
        $group->get('/validateTokenJWT/{token}', ValidateTokenJWTMiddleware::class);
    });

    $app->group('/users', function (Group $group) {
        $group->post('', ListUsersAction::class);
        $group->post('/{id}', ViewUserAction::class);
    })->add(AuthenticateUserMiddleware::class);

    $app->group('/services', function (Group $services){
        $services->group('/facebook', function (Group $facebook){
            $facebook->get('/oAuth/{token}', FacebookOAuth::class);
        });
        $services->group('/google', function (Group $google){
            $google->get('/oAuth/{token}', GoogleOAuth::class);
        });
        $services->group('/mercadopago', function (Group $mercadopago){
            $mercadopago->get('/createPreference', MercadoPagoCreatePreference::class);
            $mercadopago->get('/callback', MercadoPagoCallback::class);
            $mercadopago->get('/notification', MercadoPagoNotification::class);
            $mercadopago->get('/pagamento', MercadoPagoPagamento::class);
        });
    });

    $app->group('/signin', function (Group $group) {
        $group->post('', Signin::class);
        $group->post('/register', Register::class)->add(ClearTokenCSRFMiddleware::class);
        $group->post('/linkForgotPassword', LinkForgotPassword::class)->add(ClearTokenCSRFMiddleware::class);
        $group->post('/changePassword/{token}', ChangePassword::class)->add(ClearTokenCSRFMiddleware::class);
    });
};
