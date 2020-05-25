<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private ?KernelBrowser $client = null;

    public function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * @param string $username
     * @param string $password
     * @return KernelBrowser
     */
    protected function createAuthenticatedClient($username = 'admin@example.com', $password = 'admin123')
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                '_username' => $username,
                '_password' => $password,
            ))
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testAdd()
    {
        $this->client->request(
            'POST',
            '/api/user',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                'name' => "lorem",
                'surname' => "ipsum",
                'email' => 'admin@example.com',
                'password' => 'admin123'
            ))
        );

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseData['user_id']);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testLogin()
    {
        $this->client->request(
            'POST',
            '/api/login_check',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            json_encode(array(
                '_username' => 'admin@example.com',
                '_password' => 'admin123'
            ))
        );

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertNotEmpty($responseData['token']);
        $this->assertNotEmpty($responseData['refresh_token']);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
