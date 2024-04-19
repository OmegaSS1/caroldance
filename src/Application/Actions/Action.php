<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Traits\Helper;
use App\Database\DatabaseInterface;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    use Helper;
    protected LoggerInterface $logger;
    protected Request $request;
    protected Response $response;
    protected array $args;
    protected DatabaseInterface $database;
    protected $USER;
    protected array $logInfo;
    
    public function __construct(LoggerInterface $logger, DatabaseInterface $database)
    {
        $this->logger = $logger;
        $this->database = $database;
    }

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request  = $request;
        $this->response = $response;
        $this->args     = $args;
        $this->USER     = $this->request->getAttribute("USER") ?? (object) ["data" => (object) ["id" => "Usuário não logado"]];
        $this->logInfo  = [
            "User"     => $this->USER->data->id, 
            "Ip"       => IP, 
            "Method"   => $this->request->getMethod(), 
            "Route"    => $this->request->getUri()->getPath(),
        ];

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            $this->logInfo["Response"] = $e->getMessage();
            $this->logger->error(json_encode($this->logInfo, JSON_UNESCAPED_UNICODE), $this->request->getParsedBody() ?? $this->args);
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     */
    protected function getFormData()
    {
        return $this->request->getParsedBody();
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        if($this->request->getMethod() !== 'GET'){
            $this->logInfo['Response'] = $statusCode;
            $this->logger->info(json_encode($this->logInfo, JSON_UNESCAPED_UNICODE), $this->request->getParsedBody() ?? $this->args);
        }
        
        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus($payload->getStatusCode());
    }
}
