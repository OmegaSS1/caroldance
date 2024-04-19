<?php

declare(strict_types=1);

use App\Application\Actions\ActivityStudent\{ActivityStudentListAction, ActivityStudentRegisterAction};
use App\Application\Actions\MonthlyPayment\{MonthlyPaymentListAction};
use App\Application\Actions\Signin\{SigninChangePasswordAction, SigninLinkForgotPasswordAction, SigninLoginAction};
use App\Application\Actions\User\{UserListAction, UserViewAction, UserLoginRegisterAction, UserAdminRegisterAction, UserExportAction};
use App\Application\Actions\Student\{StudentRegisterAction, StudentListAction, StudentExportAction};

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

        $group->group('/teste', function (Group $group) {
            $group->post('/csrf', function (Request $request, Response $response) {
                $response->getBody()->write('Deu certo');
                return $response;
            });
            $group->get('/jwt', function (Request $request, Response $response) {
                $response->getBody()->write('Deu certo');
                return $response;
            })->add(AuthenticateUserMiddleware::class);
            $group->post('/csrfjwt', function (Request $request, Response $response) {
                $response->getBody()->write('Deu certo');
                return $response;
            })->add(AuthenticateUserMiddleware::class);
        });

        $group->group('/signin', function (Group $group) {
            $group->post('', SigninLoginAction::class);
            $group->post('/register', UserLoginRegisterAction::class);
            $group->post('/linkForgotPassword', SigninLinkForgotPasswordAction::class);
            $group->post('/changePassword', SigninChangePasswordAction::class);
        });

        $group->group('/user', function (Group $group) {
            $group->post('/{id}', UserViewAction::class);
        })->add(AuthenticateUserMiddleware::class);

        $group->group('/admin', function (Group $group) {
            $group->group('/student', function (Group $student) {
                $student->post('/register', StudentRegisterAction::class);
                $student->get('/list', StudentListAction::class);
                $student->get('/export/{extension}', StudentExportAction::class);
            });
            $group->group('/user', function (Group $user) {
                $user->post('/register', UserAdminRegisterAction::class);
                $user->get('/list', UserListAction::class);
                $user->get('/export/{extension}', UserExportAction::class);

            });
            $group->group('/activityStudent', function (Group $activityStudent) {
                $activityStudent->post('/register', ActivityStudentRegisterAction::class);
                $activityStudent->get('/list', ActivityStudentListAction::class);
            });
            $group->group('/monthlyPayment', function (Group $monthlyPayment) {
                // $monthlyPayment->post('/register', ActivityStudentRegisterAction::class);
                $monthlyPayment->get('/list', MonthlyPaymentListAction::class);
            });
        });
        //->add(AuthenticateUserMiddleware::class);

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
