<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Google\Client as GoogleClient;
use Google\Service\PeopleService;

class GoogleAuthenticator extends OAuth2Authenticator
{
    private $clientRegistry;
    private $entityManager;
    private $router;
    private $passwordHasher;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $client->getOAuth2Provider()->setHttpClient(
            new \GuzzleHttp\Client(['verify' => false])
        );
        $accessToken = $this->fetchAccessToken($client, [
            'scope' => [
                'email',
                'profile',
                'https://www.googleapis.com/auth/user.birthday.read',
                'https://www.googleapis.com/auth/user.gender.read',
                'https://www.googleapis.com/auth/user.addresses.read'
            ]
        ]);

        //dd($accessToken);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var \League\OAuth2\Client\Provider\GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                //dd($googleUser);

                $googleClient = new GoogleClient();
                $googleClient->setAccessToken($accessToken->getToken());

                $guzzleClient = new \GuzzleHttp\Client(['verify' => '/usr/local/etc/cacert.pem']);
                $googleClient->setHttpClient($guzzleClient);

                $peopleService = new PeopleService($googleClient);

                // Faire une requête à l'API People pour obtenir les informations supplémentaires
                $profile = $peopleService->people->get('people/me', [
                    'personFields' => 'birthdays,genders,addresses'
                ]);

                // Extraire les informations supplémentaires
                $birthday = '';
                $gender = '';
                $address = '';

                //dd($profile);

                if (!empty($profile->getBirthdays())) {
                    $birthdayDate = $profile->getBirthdays()[0]->getDate();
                    $birthday = sprintf('%02d-%02d', $birthdayDate->getDay(), $birthdayDate->getMonth());
                }

                if (!empty($profile->getGenders())) {
                    $gender = $profile->getGenders()[0]->getValue();
                }

                if (!empty($profile->getAddresses())) {
                    $address = $profile->getAddresses()[0]->getFormattedValue();
                }

                $firstName = $googleUser->getFirstName();
                $lastName = $googleUser->getLastName();
                $email = $googleUser->getEmail();

                // 1) have they logged in with Google before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($existingUser) {
                    $this->updateUserInfo($existingUser, $googleUser, $birthday, $address, $gender);
                    return $existingUser;
                }

                // 2) do we have a matching user by email?
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                // 3) Maybe you just want to "register" them by creating a User object
                $user = new User();
                $user->setEmail($email);
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setGender($gender);
                $user->setBirthdate($birthday);
                $user->setAddress($address);
                // Générer un mot de passe aléatoire pour les utilisateurs Google
                $randomPassword = bin2hex(random_bytes(16));
                $hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
                $user->setPassword($hashedPassword);

                $user->setRoles(['ROLE_USER']);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    private function updateUserInfo(User $user, $googleUser, $birthday, $address, $gender): void
    {
        $user->setFirstName($googleUser->getFirstName());
        $user->setLastName($googleUser->getLastName());
        $user->setGender($gender);
        $user->setBirthdate($birthday);
        $user->setAddress($address);

        // Vous pouvez également récupérer l'image de profil si vous le souhaitez
        // $user->setProfilePicture($googleUser->getAvatar());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // change "app_homepage" to some route in your app
        $targetUrl = $this->router->generate('app_home');

        return new RedirectResponse($targetUrl);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
}