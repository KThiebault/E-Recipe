<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testGoodResponse(string $method, string $url): void
    {
        $client = self::createClient();
        $client->request($method, $url);

        $this->assertResponseIsSuccessful();
    }

    public function urlProvider(): \Generator
    {
        yield [Request::METHOD_GET, "/register"];
        yield [Request::METHOD_POST, "/register"];
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testRegistrationSuccess(array $formData): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, "/register");
        $client->submitForm("Register", $formData);

        $user = self::$container->get(UserRepository::class)->findOneBy(["email" => $formData["registration[email]"]]);

        $this->assertNotNull($user);
        $this->assertSame($user->getEmail(), $formData["registration[email]"]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function goodDataProvider(): \Generator
    {
        yield [
            [
                "registration[email]" => "test@test.com",
                "registration[plainPassword][first]" => "example",
                "registration[plainPassword][second]" => "example"
            ]
        ];
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testRegistrationFail(array $formData, string $errorMessage): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, "/register");
        $client->submitForm("Register", $formData);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextSame("div > ul > li", $errorMessage);
    }

    public function badDataProvider(): \Generator
    {
        yield [
            [
                "registration[email]" => "",
                "registration[plainPassword][first]" => "example",
                "registration[plainPassword][second]" => "example"
            ],
            "This value should not be blank."
        ];
        yield [
            [
                "registration[email]" => "example",
                "registration[plainPassword][first]" => "example",
                "registration[plainPassword][second]" => "example"
            ],
            "This value is not a valid email address."
        ];
        yield [
            [
                "registration[email]" => "john@example.com",
                "registration[plainPassword][first]" => "",
                "registration[plainPassword][second]" => ""
            ],
            "This value should not be blank."
        ];
        yield [
            [
                "registration[email]" => "john@example.com",
                "registration[plainPassword][first]" => "",
                "registration[plainPassword][second]" => "example"
            ],
            "This value is not valid."
        ];
        yield [
            [
                "registration[email]" => "john@example.com",
                "registration[plainPassword][first]" => "example",
                "registration[plainPassword][second]" => ""
            ],
            "This value is not valid."
        ];
        yield [
            [
                "registration[email]" => "john@example.com",
                "registration[plainPassword][first]" => "examp",
                "registration[plainPassword][second]" => "examp"
            ],
            "This value is too short. It should have 6 characters or more."
        ];
    }
}
