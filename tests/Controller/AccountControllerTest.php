<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testGoodResponse(string $method, string $url): void
    {
        $client = self::createClient();
        $user = static::$container->get(UserRepository::class)->findOneByEmail("example@example.com");
        $client->loginUser($user);

        $client->request($method, $url);
        $this->assertResponseIsSuccessful();
    }

    public function urlProvider(): \Generator
    {
        yield [Request::METHOD_GET, "/profile"];
    }

    /**
     * @dataProvider updateGoodDataProvider
     */
    public function testEditPasswordSuccess(array $formData): void
    {
        $client = self::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail("example@example.com");
        $passwordEncoder = static::$container->get(UserPasswordEncoderInterface::class);
        $client->loginUser($user);

        $client->request("GET", "/profile");
        $client->submitForm("Update", $formData);

        $this->assertTrue(
            $passwordEncoder->isPasswordValid(
                $userRepository->findOneByEmail("example@example.com"),
                $formData["update_password[plainPassword][first]"]
            )
        );
        $this->assertResponseRedirects("/profile", Response::HTTP_FOUND);
    }

    public function updateGoodDataProvider(): \Generator
    {
        yield [
            [
                "update_password[password]" => "example",
                "update_password[plainPassword][first]" => "example2",
                "update_password[plainPassword][second]" => "example2"
            ]
        ];
    }

    /**
     * @dataProvider updateBadDataProvider
     */
    public function testEditPasswordFail(array $formData, string $errorMessage): void
    {
        $client = self::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $passwordEncoder = static::$container->get(UserPasswordEncoderInterface::class);
        $user = $userRepository->findOneByEmail("example@example.com");

        $client->loginUser($user);

        $client->request("GET", "/profile");
        $client->submitForm("Update", $formData);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextSame("span.form-error-message", $errorMessage);
        $this->assertFalse(
            $passwordEncoder->isPasswordValid(
                $userRepository->findOneByEmail("example@example.com"),
                $formData["update_password[plainPassword][first]"]
            )
        );
    }

    public function updateBadDataProvider(): \Generator
    {
        yield [
            [
                "update_password[password]" => "",
                "update_password[plainPassword][first]" => "example2",
                "update_password[plainPassword][second]" => "example2"
            ],
            "This value should not be blank."
        ];
        yield [
            [
                "update_password[password]" => "test",
                "update_password[plainPassword][first]" => "example2",
                "update_password[plainPassword][second]" => "example2"
            ],
            "The current password is not the same."
        ];
        yield [
            [
                "update_password[password]" => "example",
                "update_password[plainPassword][first]" => "",
                "update_password[plainPassword][second]" => "example2"
            ],
            "This value is not valid."
        ];
        yield [
            [
                "update_password[password]" => "example",
                "update_password[plainPassword][first]" => "example2",
                "update_password[plainPassword][second]" => ""
            ],
            "This value is not valid."
        ];
        yield [
            [
                "update_password[password]" => "example",
                "update_password[plainPassword][first]" => "example1",
                "update_password[plainPassword][second]" => "example2"
            ],
            "This value is not valid."
        ];
        yield [
            [
                "update_password[password]" => "example",
                "update_password[plainPassword][first]" => "examp",
                "update_password[plainPassword][second]" => "examp"
            ],
            "This value is too short. It should have 6 characters or more."
        ];
    }
}
