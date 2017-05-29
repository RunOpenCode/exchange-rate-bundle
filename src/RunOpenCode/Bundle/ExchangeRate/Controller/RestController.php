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
use RunOpenCode\ExchangeRate\Model\Rate;
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
    /**
     * Check if repository has rate
     *
     * @see \RunOpenCode\ExchangeRate\Contract\ManagerInterface::has()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function hasAction(Request $request)
    {
        /**
         * @var $sourceName
         * @var $currencyCode
         * @var $date
         * @var $rateType
         */
        extract($this->extractParametersFromRequest($request), EXTR_OVERWRITE);

        try {
            return $this->createSuccessResponse($this->getManager()->has($sourceName, $currencyCode, $date, $rateType));
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Get rate.
     *
     * @see \RunOpenCode\ExchangeRate\Contract\ManagerInterface::get()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getAction(Request $request)
    {
        /**
         * @var $sourceName
         * @var $currencyCode
         * @var $date
         * @var $rateType
         */
        extract($this->extractParametersFromRequest($request), EXTR_OVERWRITE);

        try {
            return $this->createSuccessResponse($this->getManager()->get($sourceName, $currencyCode, $date, $rateType));
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Get latest applicable rate.
     *
     * @see \RunOpenCode\ExchangeRate\Contract\ManagerInterface::latest()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function latestAction(Request $request)
    {
        /**
         * @var $sourceName
         * @var $currencyCode
         * @var $date
         * @var $rateType
         */
        extract($this->extractParametersFromRequest($request), EXTR_OVERWRITE);

        try {
            return $this->createSuccessResponse($this->getManager()->latest($sourceName, $currencyCode, $rateType));
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Get rate applicable for today.
     *
     * @see \RunOpenCode\ExchangeRate\Contract\ManagerInterface::today()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function todayAction(Request $request)
    {
        /**
         * @var $sourceName
         * @var $currencyCode
         * @var $date
         * @var $rateType
         */
        extract($this->extractParametersFromRequest($request), EXTR_OVERWRITE);

        try {
            return $this->createSuccessResponse($this->getManager()->today($sourceName, $currencyCode, $rateType));
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Get applicable rate for given date.
     *
     * @see \RunOpenCode\ExchangeRate\Contract\ManagerInterface::historical()
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function historicalAction(Request $request)
    {
        /**
         * @var $sourceName
         * @var $currencyCode
         * @var $date
         * @var $rateType
         */
        extract($this->extractParametersFromRequest($request), EXTR_OVERWRITE);

        try {
            return $this->createSuccessResponse($this->getManager()->historical($sourceName, $currencyCode, $date, $rateType));
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e);
        }
    }

    /**
     * Extract params from request.
     *
     * @param Request $request
     *
     * @return array
     */
    private function extractParametersFromRequest(Request $request)
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

        return $params;
    }

    /**
     * Build exception response.
     *
     * @param \Exception $exception
     *
     * @return JsonResponse
     */
    private function createExceptionResponse(\Exception $exception)
    {
        return new JsonResponse([
            'error' => true,
            'message' => $exception->getMessage(),
            'class' => (new \ReflectionClass($exception))->getShortName()
        ], 500);
    }

    /**
     * Build success response
     *
     * @param mixed $result
     * @return JsonResponse
     */
    private function createSuccessResponse($result)
    {
        $rateToArray = function (Rate $rate) {

            return [
                'source_name' => $rate->getSourceName(),
                'rate_type' => $rate->getRateType(),
                'base_currency_code' => $rate->getBaseCurrencyCode(),
                'date' => $rate->getDate()->format('Y-m-d'),
                'value' => $rate->getValue(),
                'currency_code' => $rate->getCurrencyCode(),
            ];
        };

        return new JsonResponse([
            'error' => false,
            'result' => ($result instanceof Rate) ? $rateToArray($result) : $result
        ]);
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
