<?php
/*
 * This file is part of the Exchange Rate Bundle, an RunOpenCode project.
 *
 * (c) 2017 RunOpenCode
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RunOpenCode\Bundle\ExchangeRate\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class DeleteController extends Controller
{
    public function indexAction(Request $request)
    {
        if (!$this->isCsrfTokenValid($request->getRequestUri(), $request->get('_csrf_token'))) {
            throw new InvalidCsrfTokenException();
        }
    }
}
