<?php
namespace OpenDataWorld;

/**
 * Список api методов 115 (OpenApi)
 * @see https://disp.it-minsk.by/app/eds/open-api-catalog/
 * @see https://disp.it-minsk.by/app/eds/open-api-catalog/portal/request/
 */


/**
 *
 */
class Cabinet115 {

    private $base_url = "https://disp.it-minsk.by";

    private $login = '';
    private $pass  = '';
    private $token = '';

    /**
     * @var \GuzzleHttp\Client
     */
    private \GuzzleHttp\Client $client;


    /**
     * @param string $login
     * @param string $pass
     * @param string $token
     */
    public function __construct(string $login, string $pass, string $token) {

        $this->login = $login;
        $this->pass  = $pass;
        $this->token = $token;
    }


    /**
     * Получение списка заявок пользователя
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getOrders(): array {

        $uri   = "/app/eds/portal/request/get?token={$this->token}&username={$this->login}&pass={$this->pass}";
        $items = $this->getItems($uri);

        return $items;
    }


    /**
     * Получение комментариев по заявке пользователя
     * @param int $request_id
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getOrderComments(int $request_id): array {

        $uri   = "/app/eds/portal/request/get_ral?token={$this->token}&username={$this->login}&pass={$this->pass}&id={$request_id}";
        $items = $this->getItems($uri);

        return $items;
    }


    /**
     * Получение уведомлений по заявкам пользователя
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getNotifications(): array {

        $uri   = "/app/eds/portal/ntf/get?token={$this->token}&username={$this->login}&pass={$this->pass}";
        $items = $this->getItems($uri);

        return $items;
    }


    /**
     * Получение уведомлений по заявкам пользователя
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function getPlaces(): array {

        $uri   = "/app/eds/portal/places/get?token={$this->token}&username={$this->login}&pass={$this->pass}";
        $items = $this->getItems($uri);

        return $items;
    }


    /**
     * @param string $problem_text
     * @param string $lat
     * @param string $lng
     * @param array  $files
     * @return bool
     */
    public function createOrder(string $problem_text, string $lat, string $lng, array $files): bool {

        // TODO Добавить отправку
        return true;
    }


    /**
     * @param int $image_id
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getImageContent(int $image_id): string {

        $uri = "/app/eds/portal/i/download?token=IMGWEB&pid={$image_id}";
        $response = $this->request('get', $uri);

        return $response->getBody()->getContents();
    }


    /**
     * @param string $uri
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function getItems(string $uri): array {

        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            $response->getHeader('Content-Type')[0] != 'application/json'
        ) {
            throw new \Exception("Некорректный ответ от сервера. Content-Type не application/json: $uri");
        }

        $content       = $response->getBody()->getContents();
        $content_array = @json_decode($content, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("Некорректный ответ от сервера. Содержимое ответа не является json: $uri");
        }

        $requests = $content_array['items'] ?? [];

        return is_array($requests) ? $requests : [];
    }


    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function request(string $method, string $uri, array $options = []): \Psr\Http\Message\ResponseInterface {

        if (empty($this->client)) {
            $headers = [
                'Accept-Language' => "ru-RU,ru;q=0.9",
                'Accept-Encoding' => "gzip, deflate",
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.183 Safari/537.36',
                'Connection'      => "keep-alive",
                'Cache-Control'   => "no-cache",
            ];


            $this->client = new \GuzzleHttp\Client([
                'base_uri'           => $this->base_url,
                'timeout'            => 10,
                'connection_timeout' => 10,
                'allow_redirects'    => true,
                'headers'            => $headers,
            ]);
        }


        $response = $this->client->request($method, $uri, $options);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Некорректный ответ от сервера. Http код не 200: $uri");
        }

        return $response;
    }
}