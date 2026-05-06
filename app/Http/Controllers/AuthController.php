<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use App\Mail\WelcomeMail;
use App\Mail\VerifyEmailMail;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        Mail::to($user->email)->send(new WelcomeMail($user));

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
        Mail::to($user->email)->send(new VerifyEmailMail($verificationUrl, $user->name));

        $token = $user->createToken('spa')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'errors' => ['email' => ['Les identifiants ne correspondent pas.']],
            ], 422);
        }

        $user = Auth::user();
        $token = $user->createToken('spa')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $user->email_verified = $user->hasVerifiedEmail();
        $user->is_admin = $user->isAdmin();
        $user->avatar_url = $user->avatar ? Storage::disk('s3')->url($user->avatar) : null;

        return response()->json($user);
    }

    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email déjà vérifié.']);
        }

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id'   => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );

        Mail::to($user->email)->send(new VerifyEmailMail($verificationUrl, $user->name));

        return response()->json(['message' => 'Lien de vérification renvoyé !']);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load('annonces.images');

        $annonces = $user->annonces->map(function ($a) {
            $firstImage = $a->images->first();
            return [
                'id'            => $a->id,
                'titre'         => $a->titre,
                'ville'         => $a->ville,
                'prix_par_nuit' => $a->prix_par_nuit,
                'image_url'     => $firstImage ? Storage::disk('s3')->url($firstImage->path) : null,
            ];
        });

        return response()->json([
            'user' => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->role,
                'phone'          => $user->phone,
                'bio'            => $user->bio,
                'avatar_url'     => $user->avatar ? Storage::disk('s3')->url($user->avatar) : null,
                'email_verified' => $user->hasVerifiedEmail(),
                'created_at'     => $user->created_at,
            ],
            'stats' => [
                'annonces'     => $user->annonces->count(),
                'reservations' => Reservation::where('user_id', $user->id)->count(),
            ],
            'annonces' => $annonces,
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $user->name  = $request->name;
        $user->phone = $request->input('phone');
        $user->bio   = $request->input('bio');

        if ($request->email !== $user->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('s3')->delete($user->avatar);
            $user->avatar = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('s3')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 's3');
        }

        $user->save();

        return response()->json(['message' => 'Profil mis à jour avec succès !']);
    }
}
