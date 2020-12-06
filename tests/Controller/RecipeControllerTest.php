<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class RecipeControllerTest extends WebTestCase
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
        yield [Request::METHOD_GET, "/recipe/create"];
    }
}
