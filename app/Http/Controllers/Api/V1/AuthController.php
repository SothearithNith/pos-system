<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const ACCESS_MINUTES = 60;   // short-lived: used on every API call
    private const REFRESH_DAYS   = 30;   // long-lived: only used on POST /refresh

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::with('client')->where('username', $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid username or password.'], 401);
        }

        if ($error = $this->accountError($user)) {
            return $error;
        }

        return response()->json([
            'message' => 'Login successful.',
            ...$this->issueTokens($user),
            ...$this->payload($user),
        ]);
    }

    /**
     * Exchange a refresh token for a NEW access + refresh token (rotation).
     * The old pair is deleted, so a refresh token can be used only once.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->loadMissing('client');

        $this->revokePair($user, $request->user()->currentAccessToken()->name);

        // re-check on every refresh: deactivated users / expired clients stop here
        if ($error = $this->accountError($user)) {
            return $error;
        }

        return response()->json([
            'message' => 'Token refreshed.',
            ...$this->issueTokens($user),
            ...$this->payload($user),
        ]);
    }

    /** Current user + roles + permissions + menus. */
    public function me(Request $request): JsonResponse
    {
        return response()->json($this->payload($request->user()));
    }

    /** Logs out this device only (deletes its access + refresh token). */
    public function logout(Request $request): JsonResponse
    {
        $this->revokePair($request->user(), $request->user()->currentAccessToken()->name);

        return response()->json(['message' => 'Logout successful.']);
    }

    // ------------------------------------------------------------------

    /** Returns an error response if this user may not sign in, otherwise null. */
    private function accountError(User $user): ?JsonResponse
    {
        if (! $user->is_active) {
            return response()->json(['message' => 'Your account is inactive.'], 403);
        }

        // platform admin has no client; everyone else needs an active, non-expired client
        if ($user->client_id) {
            $client = $user->client;

            if (! $client || ! $client->is_active) {
                return response()->json(['message' => 'Your shop account is inactive.'], 403);
            }

            if ($client->expired_at && $client->expired_at->isPast()) {
                return response()->json(['message' => 'Your subscription has expired.'], 403);
            }
        }

        return null;
    }

    /** Creates an access token and a refresh token that belong together (same device). */
    private function issueTokens(User $user): array
    {
        $prefix         = 'pos:' . Str::uuid();
        $accessExpires  = now()->addMinutes(self::ACCESS_MINUTES);
        $refreshExpires = now()->addDays(self::REFRESH_DAYS);

        $access  = $user->createToken($prefix . ':access', ['access'], $accessExpires);
        $refresh = $user->createToken($prefix . ':refresh', ['refresh'], $refreshExpires);

        return [
            'token_type'         => 'Bearer',
            'access_token'       => $access->plainTextToken,
            'access_expires_at'  => $accessExpires->toIso8601String(),
            'refresh_token'      => $refresh->plainTextToken,
            'refresh_expires_at' => $refreshExpires->toIso8601String(),
        ];
    }

    /** Deletes the access + refresh token of one device. */
    private function revokePair(User $user, string $tokenName): void
    {
        $prefix = Str::beforeLast($tokenName, ':');

        $user->tokens()->where('name', 'like', $prefix . ':%')->delete();
    }

    private function payload(User $user): array
    {
        $user->loadMissing('client', 'roles', 'defaultLocation');

        $permissions = $user->getAllPermissions()->pluck('name')->sort()->values()->all();

        return [
            'user'        => new UserResource($user),
            'roles'       => $user->roles->map(fn($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'name_kh' => $r->name_kh,
            ])->values(),
            'permissions' => $permissions,
            'menus'       => $this->menusFor($permissions),
        ];
    }

    /** Sidebar menu tree, containing only what this user may see. */
    private function menusFor(array $permissions): array
    {
        $menus = Menu::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn($m) => ! $m->permission_name || in_array($m->permission_name, $permissions, true));

        $children = $menus->whereNotNull('parent_id')->groupBy('parent_id');

        $item = fn($m) => [
            'slug'       => $m->slug,
            'name_en'    => $m->name_en,
            'name_kh'    => $m->name_kh,
            'icon'       => $m->icon,
            'route_name' => $m->route_name,
        ];

        return $menus->whereNull('parent_id')
            ->map(fn($m) => $item($m) + [
                'children' => ($children[$m->id] ?? collect())->map($item)->values()->all(),
            ])
            ->filter(fn($m) => $m['route_name'] || count($m['children']))
            ->values()
            ->all();
    }
}
