<?php

namespace App\Auth;

use Illuminate\Http\Client\ConnectionException;
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
        try {
            $response = Http::withToken((string) config('services.codex.token'))
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.codex.timeout'))
                ->withOptions(['verify' => filter_var(config('services.codex.verify'), FILTER_VALIDATE_BOOL)])
                ->post(rtrim((string) config('services.codex.endpoint'), '/').'/api/web-auth', [
                    'username' => $username,
                    'password' => $password,
                ]);
        } catch (ConnectionException $exception) {
            throw new CodexUnavailable('Codex could not be reached.', previous: $exception);
        } catch (Throwable $exception) {
            throw new CodexUnavailable('Codex request failed.', previous: $exception);
        }

        if ($response->serverError()) {
            throw new CodexUnavailable("Codex answered {$response->status()}.");
        }

        $data = $response->json();

        if (! $response->successful() || ($data['success'] ?? false) !== true || ! is_array($data['data']['user'] ?? null)) {
            return null;
        }

        return ['user' => $data['data']['user'], 'office' => $data['data']['office'] ?? null];
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
        try {
            $response = Http::withToken((string) config('services.codex.token'))
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('services.codex.timeout'))
                ->withOptions(['verify' => filter_var(config('services.codex.verify'), FILTER_VALIDATE_BOOL)])
                ->post(rtrim((string) config('services.codex.endpoint'), '/').'/api/web-auth/password', [
                    'username' => $username,
                    'current_password' => $current,
                    'password' => $password,
                    'confirmed_password' => $confirmation,
                ]);
        } catch (Throwable $exception) {
            throw new CodexUnavailable('Codex could not be reached.', previous: $exception);
        }

        if ($response->serverError()) {
            throw new CodexUnavailable("Codex answered {$response->status()}.");
        }

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
}
