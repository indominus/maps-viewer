<?php

namespace App\Tests;

use App\Bootstrap;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Stdlib\Parameters;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{

    /**
     * @var Bootstrap
     */
    private $application;

    public function setUp(): void
    {
        $this->application = new Bootstrap();
    }

    private function callApi($method, $uri, array $params = []): Response
    {
        $request = (new Request())->setMethod($method)->setUri($uri);
        if (!empty($params)) {
            $request->setPost(new Parameters($params));
        }
        return $this->application->run($request);
    }

    public function testGetProviders()
    {
        $response = $this->callApi('GET', '/providers');
        $this->assertNotEmpty($response->getContent(), 'Response is empty');
    }

    public function testSearchGeo()
    {
        $response = $this->callApi('POST', '/search', ['query' => 'Sofia']);
        $this->assertNotEmpty($response->getContent(), 'Response is empty');
    }
}
