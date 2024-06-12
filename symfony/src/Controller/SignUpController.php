<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignUpController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    private function luhnValidation(string $number): bool
    {
        $digits = array_map('intval', str_split($number));

        for ($i = count($digits) - 2; $i >= 0; $i -= 2) {
            $digits[$i] *= 2;
            if ($digits[$i] > 9) {
                $digits[$i] -= 9;
            }
        }

        $total = array_sum($digits);
        return $total % 10 === 0;
    }

    public function __invoke(Request $request): Response
    {
        $requestContent = json_decode($request->getContent(), true);

        $requiredFields = ['email', 'password', 'name', 'firstname', 'address', 'phone', 'card', 'crypto', 'expiry'];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $requestContent) || empty($requestContent[$field])) {
                return new Response('Un problème technique est survenu, veuillez réessayer ultérieurement.', 400);
            }
        }

        $userEmail = $requestContent['email'];
        $userPassword = $requestContent['password'];
        $userName = $requestContent['name'];
        $userFirstname = $requestContent['firstname'];
        $userAddress = $requestContent['address'];
        $userPhone = $requestContent['phone'];
        $userCard = $requestContent['card'];
        $userCrypto = $requestContent['crypto'];
        $userExpiry = $requestContent['expiry'];

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            return new Response('Veuillez entrer une adresse e-mail valide.', 400);
        }

        if (strlen($userName) < 2) {
            return new Response('Le nom doit contenir au moins 2 caractères.', 400);
        }
        if (strlen($userFirstname) < 2) {
            return new Response('Le prénom doit contenir au moins 2 caractères.', 400);
        }

        if (strlen($userAddress) < 5) {
            return new Response('L\'adresse doit contenir au moins 5 caractères.', 400);
        }

        if (!preg_match('/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/', $userPhone)) {
            return new Response('Veuillez entrer un numéro de téléphone français valide (ex: 0601020304).', 400);
        }

        if (strlen($userCard) !== 16 || !ctype_digit($userCard) || !$this->luhnValidation($userCard)) {
            return new Response('Le numéro de carte doit être composé de 16 chiffres et respecter la formule de Luhn.', 400);
        }

        if (strlen($userCrypto) !== 3 || !ctype_digit($userCrypto)) {
            return new Response('Le cryptogramme visuel doit être un entier composé de trois chiffres.', 400);
        }

        if (!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $userExpiry)) {
            return new Response('Veuillez entrer une date d\'expiration valide (ex: 12/26).', 400);
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        $registeredUser = $userRepository->findOneBy(['email' => $userEmail]);

        if ($registeredUser) {
            return new Response('Adresse email déjà enregistrée', 409);
        }

        $newUser = new User();
        $newUser->setEmail($userEmail);
        $newUser->setName($userName);
        $newUser->setFirstname($userFirstname);
        $newUser->setAddress($userAddress);
        $newUser->setPhone($userPhone);
        $newUser->setCard($userCard);
        $newUser->setCrypto($userCrypto);
        $newUser->setExpiry($userExpiry);
        $newUser->setRoles(["ROLE_USER"]);
        $newUser->setPassword($this->passwordHasher->hashPassword($newUser, $userPassword));

        $this->entityManager->persist($newUser);
        $this->entityManager->flush();

        return new Response('OK', 200);
    }
}
