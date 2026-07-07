<?php

declare(strict_types=1);

namespace App\Http\Controller\Identity;

use App\Http\Controller\Controller;
use App\Http\Request\Request;
use App\Http\Response\Response;
use App\Presentation\Templating\Renderer;
use App\Repository\IdentityMeRepository;
use App\Security\AuthorizationService;
use InvalidArgumentException;
use JsonException;

final class IdentityMeController extends Controller
{
    public function __construct(
        Renderer $renderer,
        private readonly AuthorizationService $authorization,
        private readonly IdentityMeRepository $identityMe
    ) {
        parent::__construct($renderer);
    }

    public function index(Request $request): Response
    {
        if (!$this->authorization->isLoggedIn()) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'Bitte zuerst anmelden.',
            ], 401);
        }

        $userId = $this->authorization->currentUserId();
        if ($userId === null) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'Bitte zuerst anmelden.',
            ], 401);
        }

        $system = trim((string) ($request->query['system'] ?? ''));
        $system = $system === '' ? null : $system;

        try {
            $data = $this->identityMe->meForUserId($userId, $system);
        } catch (InvalidArgumentException $exception) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 404);
        }

        if ($data === []) {
            return $this->jsonResponse([
                'status' => 'error',
                'message' => 'Aktive Identität nicht gefunden.',
            ], 401);
        }

        return $this->jsonResponse($data, 200, (int) ($data['cache_ttl_seconds'] ?? 300));
    }

    /**
     * @param array<string,mixed> $payload
     */
    private function jsonResponse(array $payload, int $status = 200, int $cacheTtlSeconds = 0): Response
    {
        $headers = [
            'Content-Type' => 'application/json; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
        ];

        if ($status >= 200 && $status < 300 && $cacheTtlSeconds > 0) {
            $headers['Cache-Control'] = 'private, max-age=' . $cacheTtlSeconds;
        } else {
            $headers['Cache-Control'] = 'no-store';
        }

        try {
            $body = json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            $status = 500;
            $headers['Cache-Control'] = 'no-store';
            $body = '{"status":"error","message":"JSON response could not be encoded."}';
        }

        return new Response($status, $headers, $body . "\n");
    }
}
