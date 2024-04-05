<?php

/**
 * SmartCook Client
 * 
 * Prepared for the simple work of communicating 
 * with the SmartCook API in a PHP application
 * 
 * Example:
 * 
 * try {
 *     (new SmartCookClient)
 *         ->setRequestData(["mess" => "Hello there"])
 *         ->sendRequest("echo")
 *         ->printResponse();
 * } catch (Exception $e) {
 *     echo $e->getMessage();
 * }
 * 
 */
class SmartCookClient
{
    /**
     * URL to API
     * https://www.smartcook-project.eu/api/
     */
    private static $URL = "https://www.smartcook-project.eu/api/";

    /**
     * The user who sends requests to the API
     */
    private static $SENDER = [
        "id" => 10,
        "name" => "Král Pavel",
        "secret" => "udmp9v"
    ];

    /**
     * The user who sends responses from the API
     */
    private static $SMARTCOOK = [
        "id" => 0,
        "name" => "SmartCook",
        "secret" => "smrtck"
    ];

    private $request_data = [];
    private $response_data = [];

    public function __construct(array $request_data = [])
    {
        $this->setRequestData($request_data);
    }

    public function setRequestData(array $request_data): self
    {
        $this->request_data = $request_data;
        return $this;
    }

    public function getResponseData(): array
    {
        return $this->response_data;
    }

    public function prepareRequestDataToSend(): string
    {
        if (!empty($this->request_data)) {
            $data = $this->request_data;
            $data["user"] = self::$SENDER["id"];
            $data["time"] = time();
            $data['sign'] = self::createSignature($data, self::$SENDER["secret"]);
            return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        return '';
    }

    public function sendRequest(string $endpoint): self
    {
        $cUrl = curl_init(self::$URL . $endpoint);
        curl_setopt($cUrl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cUrl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($cUrl, CURLOPT_POSTFIELDS, $this->prepareRequestDataToSend());
        curl_setopt($cUrl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $cUrl,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Accept: application/json;charset=UTF-8'
            ]
        );
        $result = curl_exec($cUrl);
        if (curl_errno($cUrl)) {
            throw new Exception("cUrl exec failed: " . htmlspecialchars(curl_error($cUrl)));
        }
        if (curl_getinfo($cUrl, CURLINFO_HTTP_CODE) != 200) {
            throw new Exception("Bad http response: " . htmlspecialchars(curl_getinfo($cUrl, CURLINFO_HTTP_CODE)));
        }
        curl_close($cUrl);
        $this->response_data = json_decode($result, true);
        return $this;
    }

    public function validateResponseData(): bool
    {
        $signature = $this->response_data["sign"] ?? '';
        unset($this->response_data["sign"]);
        return self::validateData($this->response_data, $signature, self::$SMARTCOOK["secret"]);
    }

    public function printResponse(): self
    {
        echo "Response:\n" . json_encode(
            $this->getResponseData(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) . "\nResponse verified: " . ($this->validateResponseData() ? "yes" : "no");
        return $this;
    }

    public static function createSignature(array $data, string $secret): string
    {
        return hash_hmac("SHA256", json_encode($data), $secret);
    }

    public static function validateData(array $data, string $signature, string $secret): bool
    {
        return hash_hmac("SHA256", json_encode($data), $secret) === $signature;
    }

}
