<?php
declare(strict_types=1);

namespace V4U\ZipChecker\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use V4U\ZipChecker\Helper\Data as DataHelper;

class ShiprocketApi
{
    private const TOKEN_CACHE_KEY = 'v4u_zipchecker_shiprocket_token';
    private const TOKEN_EXPIRY_CACHE_KEY = 'v4u_zipchecker_shiprocket_token_expiry';
    private const TOKEN_TTL_BUFFER = 120;

    /**
     * @var CurlFactory
     */
    private $curlFactory;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var DataHelper
     */
    private $dataHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CurlFactory $curlFactory,
        Json $jsonSerializer,
        CacheInterface $cache,
        DataHelper $dataHelper,
        LoggerInterface $logger
    ) {
        $this->curlFactory = $curlFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->cache = $cache;
        $this->dataHelper = $dataHelper;
        $this->logger = $logger;
    }

    /**
     * @param string $deliveryPostcode
     * @return bool
     * @throws LocalizedException
     */
    public function isPincodeServiceable(string $deliveryPostcode, ?float $weightKg = null): bool
    {
        $serviceabilityData = $this->getServiceabilityData($deliveryPostcode, $weightKg);
        return (bool) ($serviceabilityData['is_serviceable'] ?? false);
    }

    /**
     * @param string $deliveryPostcode
     * @param float|null $weightKg Product weight in kg; if null or <= 0, falls back to admin static weight.
     * @return array
     * @throws LocalizedException
     */
    public function getServiceabilityData(string $deliveryPostcode, ?float $weightKg = null): array
    {
        $apiResponse = $this->callServiceabilityApi($deliveryPostcode, false, $weightKg);
        if (isset($apiResponse['status_code']) && (int) $apiResponse['status_code'] === 401) {
            $apiResponse = $this->callServiceabilityApi($deliveryPostcode, true, $weightKg);
        }
        
        if (!isset($apiResponse['status']) || (int) $apiResponse['status'] !== 200) {
            return [
                'is_serviceable' => false,
                'estimated_delivery_days' => null,
                'etd' => null
            ];
        }

        $companies = $apiResponse['data']['available_courier_companies'] ?? [];
        if (!is_array($companies) || empty($companies)) {
            return [
                'is_serviceable' => false,
                'estimated_delivery_days' => null,
                'etd' => null
            ];
        }

        $selectedCourier = $this->getFastestCourier($companies);

        return [
            'is_serviceable' => true,
            'estimated_delivery_days' => $selectedCourier['estimated_delivery_days'] ?? null,
            'etd' => $selectedCourier['etd'] ?? null
        ];
    }

    /**
     * @param string $deliveryPostcode
     * @param bool $forceRefreshToken
     * @return array
     * @throws LocalizedException
     */
    private function callServiceabilityApi(string $deliveryPostcode, bool $forceRefreshToken, ?float $weightKg = null): array
    {
        $serviceabilityApiUrl = $this->dataHelper->getServiceabilityApiUrl();
        $pickupPostcode = $this->dataHelper->getPickupPostcode();
        $weight = ($weightKg !== null && $weightKg > 0) ? $weightKg : $this->dataHelper->getWeight();

        if ($serviceabilityApiUrl === '' || $pickupPostcode === '' || $weight <= 0) {
            throw new LocalizedException(__('Shiprocket API configuration is incomplete.'));
        }

        $query = http_build_query([
            'pickup_postcode' => $pickupPostcode,
            'delivery_postcode' => $deliveryPostcode,
            'cod' => $this->dataHelper->getCod() ? 1 : 0,
            'weight' => $weight
        ]);

        $curl = $this->curlFactory->create();
        $curl->setTimeout(20);
        $curl->addHeader('Authorization', 'Bearer ' . $this->getAuthToken($forceRefreshToken));
        $curl->addHeader('Content-Type', 'application/json');
        $curl->get(rtrim($serviceabilityApiUrl, '?') . '?' . $query);

        return $this->decodeResponse($curl->getBody(), 'serviceability');
    }

    /**
     * @param bool $forceRefresh
     * @return string
     * @throws LocalizedException
     */
    private function getAuthToken(bool $forceRefresh = false): string
    {
        if (!$forceRefresh) {
            $cachedToken = (string) $this->cache->load(self::TOKEN_CACHE_KEY);
            $cachedExpiry = (int) $this->cache->load(self::TOKEN_EXPIRY_CACHE_KEY);
            if ($cachedToken !== '' && $cachedExpiry > time()) {
                return $cachedToken;
            }
        }

        $authUrl = $this->dataHelper->getAuthApiUrl();
        $email = $this->dataHelper->getApiEmail();
        $password = $this->dataHelper->getApiPassword();

        if ($authUrl === '' || $email === '' || $password === '') {
            throw new LocalizedException(__('Shiprocket auth configuration is incomplete.'));
        }

        $payload = $this->jsonSerializer->serialize([
            'email' => $email,
            'password' => $password
        ]);

        $curl = $this->curlFactory->create();
        $curl->setTimeout(20);
        $curl->addHeader('Content-Type', 'application/json');
        $curl->post($authUrl, $payload);

        $response = $this->decodeResponse($curl->getBody(), 'auth');
        $token = $response['token'] ?? '';
        if ($token === '') {
            $this->logger->error('Shiprocket auth token missing in API response.', ['response' => $response]);
            throw new LocalizedException(__('Unable to authenticate with Shiprocket API.'));
        }

        $tokenExpiry = (int) ($response['token_expiration'] ?? 0);
        $ttl = 3000;
        if ($tokenExpiry > 0) {
            $ttl = max(60, $tokenExpiry - self::TOKEN_TTL_BUFFER);
        }
        $cacheExpiry = time() + $ttl;

        $this->cache->save($token, self::TOKEN_CACHE_KEY, [], $ttl);
        $this->cache->save((string) $cacheExpiry, self::TOKEN_EXPIRY_CACHE_KEY, [], $ttl);

        return $token;
    }

    /**
     * @param string $body
     * @param string $apiType
     * @return array
     * @throws LocalizedException
     */
    private function decodeResponse(string $body, string $apiType): array
    {
        try {
            $decoded = $this->jsonSerializer->unserialize($body);
            return is_array($decoded) ? $decoded : [];
        } catch (\InvalidArgumentException $exception) {
            $this->logger->error(
                sprintf('Shiprocket %s API invalid JSON response.', $apiType),
                ['body' => $body]
            );
            throw new LocalizedException(__('Received invalid response from Shiprocket API.'));
        }
    }

    /**
     * @param array $apiResponse
     * @param array $companies
     * @return array
     */
    private function getFastestCourier(array $companies): array
    {
        $fastestCourier = $companies[0];
        $fastestDays = $this->toComparableDays($fastestCourier['estimated_delivery_days'] ?? null);
        $fastestHours = $this->toComparableHours($fastestCourier['etd_hours'] ?? null);

        foreach ($companies as $company) {
            $currentDays = $this->toComparableDays($company['estimated_delivery_days'] ?? null);
            $currentHours = $this->toComparableHours($company['etd_hours'] ?? null);

            if ($currentDays < $fastestDays) {
                $fastestCourier = $company;
                $fastestDays = $currentDays;
                $fastestHours = $currentHours;
                continue;
            }

            if ($currentDays === $fastestDays && $currentHours < $fastestHours) {
                $fastestCourier = $company;
                $fastestHours = $currentHours;
            }
        }

        return $fastestCourier;
    }

    /**
     * @param mixed $days
     * @return int
     */
    private function toComparableDays($days): int
    {
        if ($days === null || $days === '') {
            return PHP_INT_MAX;
        }

        return (int) $days;
    }

    /**
     * @param mixed $hours
     * @return int
     */
    private function toComparableHours($hours): int
    {
        if ($hours === null || $hours === '') {
            return PHP_INT_MAX;
        }

        return (int) $hours;
    }
}
