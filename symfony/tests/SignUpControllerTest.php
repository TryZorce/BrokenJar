<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SignUpControllerTest extends WebTestCase
{
    private function request(array $data)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/ld+json'],
            json_encode($data)
        );
        return $client;
    }

    public function testSignUpSuccess(): void
    {
        $data = [
            'email' => 'test@example.com',
            'roles' => ['ROLE_USER'],
            'password' => 'password123',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '08/26',
            'phone' => '0601020304',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
        ];

        $client = $this->request($data);
        dump($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testInvalidEmail(): void
    {
        $data = [
            'email' => 'invalid-email',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Veuillez entrer une adresse e-mail valide.', $client->getResponse()->getContent());
    }

    public function testShortName(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'D',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Le nom doit contenir au moins 2 caractères.', $client->getResponse()->getContent());
    }

    public function testShortFirstname(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'J',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Le prénom doit contenir au moins 2 caractères.', $client->getResponse()->getContent());
    }

    public function testShortAddress(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('L\'adresse doit contenir au moins 5 caractères.', $client->getResponse()->getContent());
    }

    public function testInvalidPhone(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '123456789',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Veuillez entrer un numéro de téléphone français valide (ex: 0601020304).', $client->getResponse()->getContent());
    }

    public function testInvalidCardNumber(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '123456789',
            'crypto' => '123',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Le numéro de carte doit être composé de 16 chiffres et respecter la formule de Luhn.', $client->getResponse()->getContent());
    }

    public function testInvalidCrypto(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '12',
            'expiry' => '12/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Le cryptogramme visuel doit être un entier composé de trois chiffres.', $client->getResponse()->getContent());
    }

    public function testInvalidExpiry(): void
    {
        $data = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '13/04',
        ];

        $client = $this->request($data);

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Veuillez entrer une date dexpiration valide (ex: 12/04).', $client->getResponse()->getContent());
    }
    public function testEmailAlreadyRegistered(): void
    {
        $data = [
            'email' => 'existing@example.com',
            'password' => 'password123',
            'name' => 'Doe',
            'firstname' => 'John',
            'address' => '123 Rue de Test',
            'phone' => '0601020304',
            'card' => '2600098893254452',
            'crypto' => '123',
            'expiry' => '12/04',];

        $client = $this->request($data);

        $this->assertEquals(409, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Adresse email déjà enregistrée', $client->getResponse()->getContent());
    }
}
