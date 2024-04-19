<?php 

declare(strict_types= 1);

namespace App\Domain\DomainException;

use App\Domain\DomainException\DomainRecordNotFoundException;

class CustomDomainException extends DomainRecordNotFoundException {
}