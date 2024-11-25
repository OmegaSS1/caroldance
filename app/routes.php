<?php

declare(strict_types=1);

use App\Application\Actions\ActivityStudent\{ActivityStudentListAction, ActivityStudentRegisterAction};
use App\Application\Actions\ClientTicket\{
    ClientTicketCancelPurchaseAction, 
    ClientTicketConfirmPurchaseAction, 
    ClientTicketPurchaseAction, 
    ClientTicketListAction, 
    ClientTicketListSeatsAction, 
    ClientTicketValidateTicketAction,
    ClientTicketPurchaseParkingAction,
    ClientTicketConfirmPurchaseParkingAction,
    ClientTicketListParkingAction,
    Teste};

use App\Application\Actions\ClientTicket\ClientTicketCancelPurchaseParkingAction;
use App\Application\Actions\ClientTicket\ClientTicketEnableValidateTicketAction;
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

    $app->group('/teste', function (Group $teste) {
        $teste->get('', Teste::class);

        $teste->post('/csrf', function (Request $request, Response $response) {
            $response->getBody()->write('Deu certo');
            return $response;
        });
        $teste->get('/jwt', function (Request $request, Response $response) {
            $response->getBody()->write('Deu certo');
            return $response;
        })->add(AuthenticateUserMiddleware::class);
        $teste->post('/csrfjwt', function (Request $request, Response $response) {
            $response->getBody()->write('Deu certo');
            return $response;
        })->add(AuthenticateUserMiddleware::class);
    });

    $app->group('/signin', function (Group $group) {
        $group->post('', SigninLoginAction::class);
        $group->post('/register', UserLoginRegisterAction::class);
        $group->post('/linkForgotPassword', SigninLinkForgotPasswordAction::class);
        $group->post('/changePassword', SigninChangePasswordAction::class);
    });

    $app->group('/user', function (Group $user) {
        $user->post('/{id}', UserViewAction::class);
    })->add(AuthenticateUserMiddleware::class);

    $app->group('/clientTicket', function(Group $clientTicket) {
        $clientTicket->group('/ticket', function(Group $ticket) {
            $ticket->post('/buy', ClientTicketPurchaseAction::class);
            $ticket->put('/cancel', ClientTicketCancelPurchaseAction::class);
            $ticket->put('/confirm', ClientTicketConfirmPurchaseAction::class);
            $ticket->get('', ClientTicketListAction::class);
            $ticket->post('/validateTicket', ClientTicketValidateTicketAction::class);
            $ticket->post('/enableValidateTicket', ClientTicketEnableValidateTicketAction::class);
        });
        $clientTicket->group('/seat', function(Group $seat) {
            $seat->get('', ClientTicketListSeatsAction::class);
        });
        $clientTicket->group('/parking', function(Group $ticket) {
            //$ticket->post('/buy', ClientTicketPurchaseParkingAction::class);
            //$ticket->put('/confirm', ClientTicketConfirmPurchaseParkingAction::class);
            $ticket->put('/cancel', ClientTicketCancelPurchaseParkingAction::class);
            $ticket->get('', ClientTicketListParkingAction::class);
        });
        
    });

    $app->group('/admin', function (Group $group) {
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

    $app->group('/token', function (Group $token) {
        $token->get('/csrf', function(Request $request, Response $response){
            $token = (new GenerateTokenCSRFMiddleware($this->get('database')))->getToken();
            $response->getBody()->write(json_encode(["statusCode" => 200], JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withHeader('Set-Cookie', "X-Csrf-Token=$token; Path=/; HttpOnly; Secure; SameSite=None");
        });
        $token->get('/recaptcha/{token}', RecaptchaMiddleware::class);
        $token->get('/forgotPassword/{token}', ValidateTokenJWTMiddleware::class);
        $token->get('/validateTokenJWT/{token}', ValidateTokenJWTMiddleware::class);
    });

    $app->group('/services', function (Group $services) {
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
};
