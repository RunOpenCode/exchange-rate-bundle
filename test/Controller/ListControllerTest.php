<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Tests\Controller;

use RunOpenCode\Bundle\ExchangeRate\Controller\ListController;
use RunOpenCode\ExchangeRate\Model\Rate;
use Symfony\Component\HttpFoundation\Request;

class ListControllerTest extends AbstractControllerTest
{
    /**
     * @test
     *
     * @expectedException \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function itDeniesAccess()
    {
        $controller = new ListController();
        $controller->setContainer($this->getContainer());
        $controller->indexAction(Request::createFromGlobals());
    }

    /**
     * @test
     */
    public function itRendersList()
    {
        $controller = new ListController();
        $container = $this->getContainer([
            'grant_access' => true
        ]);

        $controller->setContainer($container);

        // Empty list
        $response = $controller->indexAction(Request::createFromGlobals());
        $this->assertContains('table.empty', $response->getContent());

        // Add a rate to repository
        $container->get('runopencode.exchange_rate.repository')->save([
            new Rate('source', 10, 'EUR', 'median', new \DateTime('now'), 'RSD')
        ]);

        // List is not empty
        $response = $controller->indexAction(Request::createFromGlobals());
        $this->assertNotContains('table.empty', $response->getContent());
    }
}

