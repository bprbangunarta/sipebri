<?php

namespace App\Auth;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Asks the Codex API who a username/password belongs to. It only reports facts; deciding whether
 * to let the person in, and the session that follows, is up to the caller.
 */
class CodexAuthClient
{
    /**
     * @return array{user: array<string, mixed>, office: array<string, mixed>|null}|null null when the credentials are not accepted
     *
     * @throws CodexUnavailable when the API cannot be reached or answers with a server error
     */
    public function authenticate(string $username, string $password): ?array
    {
        $response = $this->post('/api/web-auth', ['username' => $username, 'password' => $password]);
        $data = $response->json();

        if (! $response->successful() || ($data['success'] ?? false) !== true || ! is_array($data['data']['user'] ?? null)) {
            return null;
        }

        $user = $data['data']['user'];

        // The local user is keyed by the Codex id and username; an answer without them cannot be mirrored safely.
        if (! is_int($user['id'] ?? null) || $user['id'] < 1 || ! is_string($user['username'] ?? null) || trim($user['username']) === '') {
            throw new CodexUnavailable('Codex accepted the credentials but sent no usable user id or username.');
        }

        return ['user' => $user, 'office' => is_array($data['data']['office'] ?? null) ? $data['data']['office'] : null];
    }

    /**
     * Change a person's password in Codex, where it lives. Codex checks the current password itself.
     * The password fields are never logged or stored here.
     *
     * @return array{ok: bool, message: string, errors: array<string, string>}
     *
     * @throws CodexUnavailable when the API cannot be reached or answers with a server error
     */
    public function changePassword(string $username, string $current, string $password, string $confirmation): array
    {
        $response = $this->post('/api/web-auth/password', [
            'username' => $username,
            'current_password' => $current,
            'password' => $password,
            'confirmed_password' => $confirmation,
        ]);
        $data = $response->json() ?? [];
        $errors = [];

        foreach ((array) ($data['errors'] ?? []) as $field => $messages) {
            $errors[$field === 'confirmed_password' ? 'password_confirmation' : (string) $field] = (string) (is_array($messages) ? ($messages[0] ?? '') : $messages);
        }

        return [
            'ok' => $response->successful() && ($data['success'] ?? false) === true,
            'message' => (string) ($data['message'] ?? ''),
            'errors' => $errors,
        ];
    }

    /**
     * One call to Codex. Not retried: both calls carry a password and change state on the other side (attempt counters,
     * the password itself).
     *
     * @param  array<string, string>  $payload
     *
     * @throws CodexUnavailable when the API cannot be reached or answers with a server error
     */
    private function post(string $path, array $payload): Response
    {
        try {
            $response = Http::withToken((string) config('services.codex.token'))
                ->acceptJson()
                ->asJson()
                ->connectTimeout((float) config('services.codex.connect_timeout'))
                ->timeout((float) config('services.codex.timeout'))
                ->withOptions(['verify' => filter_var(config('services.codex.verify'), FILTER_VALIDATE_BOOL)])
                ->post(rtrim((string) config('services.codex.endpoint'), '/').$path, $payload);
        } catch (ConnectionException $exception) {
            throw new CodexUnavailable('Codex could not be reached.', previous: $exception);
        } catch (Throwable $exception) {
            throw new CodexUnavailable('Codex request failed.', previous: $exception);
        }

        if ($response->serverError()) {
            throw new CodexUnavailable("Codex answered {$response->status()}.");
        }

        return $response;
    }
}
