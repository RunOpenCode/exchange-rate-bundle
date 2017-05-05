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
    public function itListsAll()
    {
        $controller = new ListController();
        $controller->setContainer($this->getContainer([
            'grant_access' => true
        ]));

        $response = $controller->indexAction(Request::createFromGlobals());

        $this->assertContains('empty', $response->getContent());
    }
}

