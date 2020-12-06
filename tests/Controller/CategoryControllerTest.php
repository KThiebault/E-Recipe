<?php

namespace App\Tests\Controller;

use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends WebTestCase
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
        yield [Request::METHOD_GET, "/category/create"];
    }

    /**
     * @dataProvider goodDataProvider
     */
    public function testCreateSuccess(array $formData): void
    {
        $client = static::createClient();
        $user = static::$container->get(UserRepository::class)->findOneByEmail("example@example.com");
        $client->loginUser($user);

        $client->request(Request::METHOD_GET, "/category/create");
        $client->submitForm("Create", $formData);
        $client->followRedirect();

        $category = self::$container->get(CategoryRepository::class)
            ->findOneBy(["name" => $formData["category[name]"]]);

        $this->assertNotNull($category);
        $this->assertSame($category->getName(), $formData["category[name]"]);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function goodDataProvider(): \Generator
    {
        yield [
            [
                "category[name]" => "example",
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

        $client->request(Request::METHOD_GET, "/category/create");
        $client->submitForm("Create", $formData);


        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextSame("span.form-error-message", $errorMessage);
    }

    public function badDataProvider(): \Generator
    {
        yield [
            [
                "category[name]" => "",
            ],
            "This value should not be blank."
        ];
        yield [
            [
                "category[name]" => "exa",
            ],
            "This value is too short. It should have 4 characters or more."
        ];
    }
}
