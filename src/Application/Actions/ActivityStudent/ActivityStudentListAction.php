<?php

declare(strict_types= 1);

namespace App\Application\Actions\ActivityStudent;

use Psr\Http\Message\ResponseInterface as Response;

class ActivityStudentListAction extends ActivityStudentAction {

    protected function action(): Response{
        return $this->respondWithData($this->activityStudentRepository->findAll());
    }
}