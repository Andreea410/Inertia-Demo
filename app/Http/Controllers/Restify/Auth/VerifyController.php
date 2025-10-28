<?php

namespace App\Http\Controllers\Restify\Auth;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VerifyController extends Controller
{
    public function __invoke(Request $request, int $id, string $hash)
    {
        $frontendUrl = config('restify.auth.user_verify_url');
        
        if (! $request->hasValidSignature()) {
            if ($frontendUrl) {
                $redirectUrl = str_replace(
                    ['{id}', '{emailHash}'],
                    [$id, $hash],
                    $frontendUrl
                );
                
                return redirect($redirectUrl . '?success=false&message=' . urlencode('Invalid or expired verification link.'));
            }
            
            throw new AuthorizationException('Invalid or expired verification link.');
        }

        /**
         * @var Authenticatable $user
         */
        $user = config('restify.auth.user_model')::query()->findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            if ($frontendUrl) {
                $redirectUrl = str_replace(
                    ['{id}', '{emailHash}'],
                    [$user->getKey(), $hash],
                    $frontendUrl
                );
                
                return redirect($redirectUrl . '?success=false&message=' . urlencode('Invalid hash'));
            }
            
            throw new AuthorizationException('Invalid hash');
        }

        if ($user instanceof MustVerifyEmail && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($frontendUrl) {
            $redirectUrl = str_replace(
                ['{id}', '{emailHash}'],
                [$user->getKey(), $hash],
                $frontendUrl
            );

            return redirect($redirectUrl . '?success=true&message=' . urlencode('Email verified successfully.'));
        }

        return rest($user)->indexMeta([
            'message' => 'Email verified successfully.',
        ]);
    }
}
