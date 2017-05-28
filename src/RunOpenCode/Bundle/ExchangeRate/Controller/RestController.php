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

use RunOpenCode\ExchangeRate\Contract\ManagerInterface;
use RunOpenCode\ExchangeRate\Contract\RateInterface;
use RunOpenCode\ExchangeRate\Enum\RateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RestController
 *
 * @package RunOpenCode\Bundle\ExchangeRate\Controller
 */
class RestController extends Controller
{
    public function hasAction(Request $request)
    {
        list($sourceName, $currencyCode, $date, $rateType) = array_values($this->extractParameters($request));

        try {
            return new JsonResponse([
                'error' => false,
                'result' => $this->getManager()->has($sourceName, $currencyCode, $date, $rateType)
            ]);
        } catch (\Exception $e) {
            return $this->exceptionToResponse($e);
        }
    }

    public function getAction(Request $request)
    {
        list($sourceName, $currencyCode, $date, $rateType) = array_values($this->extractParameters($request));

        try {
            return new JsonResponse([
                'error' => false,
                'result' => $this->rateToArray($this->getManager()->get($sourceName, $currencyCode, $date, $rateType))
            ]);
        } catch (\Exception $e) {
            return $this->exceptionToResponse($e);
        }
    }

    public function latestAction(Request $request)
    {
        list($sourceName, $currencyCode, , $rateType) = array_values($this->extractParameters($request));

        try {
            return new JsonResponse([
                'error' => false,
                'result' => $this->rateToArray($this->getManager()->latest($sourceName, $currencyCode, $rateType))
            ]);
        } catch (\Exception $e) {
            return $this->exceptionToResponse($e);
        }
    }

    public function todayAction(Request $request)
    {
        list($sourceName, $currencyCode, , $rateType) = array_values($this->extractParameters($request));

        try {
            return new JsonResponse([
                'error' => false,
                'result' => $this->rateToArray($this->getManager()->today($sourceName, $currencyCode, $rateType))
            ]);
        } catch (\Exception $e) {
            return $this->exceptionToResponse($e);
        }
    }

    public function historicalAction(Request $request)
    {
        list($sourceName, $currencyCode, $date, $rateType) = array_values($this->extractParameters($request));

        try {
            return new JsonResponse([
                'error' => false,
                'result' => $this->rateToArray($this->getManager()->historical($sourceName, $currencyCode, $date, $rateType))
            ]);
        } catch (\Exception $e) {
            return $this->exceptionToResponse($e);
        }
    }

    /**
     * Extract params from request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function extractParameters(Request $request)
    {
        $params = [
            'sourceName' => $request->get('source'),
            'currencyCode' => $request->get('currency_code'),
            'date' => $request->get('date'),
            'rateType' => $request->get('rate_type')
        ];

        if (!empty($params['date'])) {
            $params['date'] = \DateTime::createFromFormat('Y-m-d', $params['date']);
        }

        if (empty($params['rateType'])) {
            $params['rateType'] = RateType::MEDIAN;
        }

        return $params;
    }

    /**
     * Build exception response.
     *
     * @param \Exception $e
     *
     * @return JsonResponse
     */
    private function exceptionToResponse(\Exception $e)
    {
        return new JsonResponse([
            'error' => true,
            'message' => $e->getMessage(),
            'class' => (new \ReflectionClass($e))->getShortName()
        ], 500);
    }

    /**
     * Serialize rate to array
     *
     * @param RateInterface $rate
     *
     * @return array
     */
    private function rateToArray(RateInterface $rate)
    {
        return [
            'source_name' => $rate->getSourceName(),
            'rate_type' => $rate->getRateType(),
            'base_currency_code' => $rate->getBaseCurrencyCode(),
            'date' => $rate->getDate()->format('Y-m-d'),
            'get_value' => $rate->getValue(),
            'currency_code' => $rate->getCurrencyCode(),
        ];
    }

    /**
     * Get manager.
     *
     * @return ManagerInterface
     */
    private function getManager()
    {
        return $this->get('runopencode.exchange_rate');
    }
}
