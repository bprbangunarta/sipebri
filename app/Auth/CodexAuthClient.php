<?php

namespace App\Auth;

use App\Models\Office;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $office = $data['data']['office'] ?? null;

        if (! $this->mirrorable($user, $office)) {
            throw new CodexUnavailable('Codex accepted the credentials but sent a user that cannot be mirrored.');
        }

        return ['user' => $user, 'office' => $office];
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
        if (app()->isProduction() && ! filter_var(config('services.codex.verify'), FILTER_VALIDATE_BOOL)) {
            Log::warning('CODEX_VERIFY is off: the connection to Codex is not verified, so the sign-in could be read or altered on the way.');
        }

        try {
            $response = Http::withToken((string) config('services.codex.token'))
                ->acceptJson()
                ->asJson()
                ->withoutRedirecting()
                ->connectTimeout((float) config('services.codex.connect_timeout'))
                ->timeout((float) config('services.codex.timeout'))
                ->withOptions(['verify' => filter_var(config('services.codex.verify'), FILTER_VALIDATE_BOOL)])
                ->post(rtrim((string) config('services.codex.endpoint'), '/').$path, $payload);
        } catch (ConnectionException $exception) {
            throw new CodexUnavailable('Codex could not be reached.', previous: $exception);
        } catch (Throwable $exception) {
            throw new CodexUnavailable('Codex request failed.', previous: $exception);
        }

        if ($response->serverError() || $response->redirect()) {
            throw new CodexUnavailable("Codex answered {$response->status()}.");
        }

        return $response;
    }

    /**
     * Whether the answer can be mirrored without guessing: the local user is keyed by the Codex id and username, and every
     * field the mirror reads as text is text (or absent). A number sent as digits in a string is accepted.
     *
     * @param  array<mixed>  $user
     */
    private function mirrorable(array $user, mixed $office): bool
    {
        $id = filter_var($user['id'] ?? null, FILTER_VALIDATE_INT);

        if (! is_int($id) || $id < 1 || ! is_string($user['username'] ?? null) || trim($user['username']) === '') {
            return false;
        }

        foreach (['name', 'email', 'role', 'office'] as $field) {
            if (! is_string($user[$field] ?? '')) {
                return false;
            }
        }

        if ($office === null) {
            return true;
        }

        if (! is_array($office)) {
            return false;
        }

        foreach (['code', 'alias', 'name'] as $field) {
            if (! is_string($office[$field] ?? '')) {
                return false;
            }
        }

        // A value longer than its column would fail halfway through the sign-in on a strict database.
        return mb_strlen($office['code'] ?? '') <= Office::CODE_MAX
            && mb_strlen($office['alias'] ?? '') <= Office::ALIAS_MAX
            && mb_strlen($office['name'] ?? '') <= Office::NAME_MAX
            && mb_strlen($user['office'] ?? '') <= Office::NAME_MAX;
    }
}
