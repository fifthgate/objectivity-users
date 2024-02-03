<?php

declare(strict_types=1);

namespace Fifthgate\Objectivity\Users\Http\Middleware;

trait GeneratesForbiddenResponseTrait
{
    protected function generateForbiddenResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(["message" => "access_denied"], 403);
    }
}
