<?php

namespace App\Tests\Controller;

use App\Repository\IngredientRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientControllerTest extends WebTestCase
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
        yield [Request::METHOD_GET, "/ingredient"];
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testCreateSuccess(array $formData): void
    {
        $client = static::createClient();
        $user = static::$container->get(UserRepository::class)->findOneByEmail("example@example.com");
        $client->loginUser($user);

        $client->request(Request::METHOD_GET, "/ingredient/create");
        $client->submitForm("Create", $formData);
        $client->followRedirect();

        $ingredient = self::$container->get(IngredientRepository::class)
            ->findOneBy(["name" => $formData["ingredient[name]"]]);

        $this->assertNotNull($ingredient);
        $this->assertSame($ingredient->getName(), $formData["ingredient[name]"]);
        $this->assertSame($ingredient->getUser()->getEmail(), $user->getEmail());
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testUpdateSuccess(array $formData): void
    {
        $client = static::createClient();
        $user = static::$container->get(UserRepository::class)->findOneByEmail("example@example.com");
        $client->loginUser($user);

        $client->request(Request::METHOD_GET, "/ingredient/update/1");
        $client->submitForm("Update", $formData);
        $client->followRedirect();

        $ingredient = self::$container->get(IngredientRepository::class)
            ->findOneBy(["name" => $formData["ingredient[name]"]]);

        $this->assertNotNull($ingredient);
        $this->assertSame($ingredient->getName(), $formData["ingredient[name]"]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function goodDataProvider(): \Generator
    {
        yield [
            [
                "ingredient[name]" => "flour",
                "ingredient[state]" => 1,
            ]
        ];
        yield [
            [
                "ingredient[name]" => "water",
                "ingredient[state]" => 0,
            ]
        ];
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testCreateFail(array $formData, string $errorMessage): void
    {
        $client = static::createClient();
        $user = static::$container->get(UserRepository::class)->findOneByEmail("example@example.com");
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, "/ingredient/create");
        $form = $crawler->selectButton('Create')->form()->disableValidation();
        $client->submit($form, $formData);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextSame("span.form-error-message", $errorMessage);
    }

    /**
     * @dataProvider badDataProvider
     */
    public function testUpdateFail(array $formData, string $errorMessage): void
    {
        $client = static::createClient();
        $user = static::$container->get(UserRepository::class)->findOneByEmail("example@example.com");
        $client->loginUser($user);
        $crawler = $client->request(Request::METHOD_GET, "/ingredient/update/1");
        $form = $crawler->selectButton('Update')->form()->disableValidation();
        $client->submit($form, $formData);


        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextSame("span.form-error-message", $errorMessage);
    }

    public function badDataProvider(): \Generator
    {
        yield [
            [
                "ingredient[name]" => "",
                "ingredient[state]" => 1,
            ],
            "This value should not be blank."
        ];
        yield [
            [
                "ingredient[name]" => "flour",
                "ingredient[state]" => "",
            ],
            "This value should not be blank."
        ];
        yield [
            [
                "ingredient[name]" => "example",
                "ingredient[state]" => 3,
            ],
            "This value is not valid."
        ];
    }
}
