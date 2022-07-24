<?php
namespace OpenDataWorld;
use JetBrains\PhpStorm\ArrayShape;


/**
 *
 */
class Cabinet115bel {

    private $base_url = "https://115.xn--90ais";

    private $login = '';
    private $pass  = '';


    private $instance5        = '';
    private $loginDialogCs    = '';
    private $pageSubmissionId = '';
    private $protected6       = '';
    private $salt7            = '';
    private $plugin8          = '';
    private $plugin2          = '';
    private $p6_phone         = '';
    private $p6_phone_ck      = '';
    private $p6_prop_email    = '';
    private $p6_prop_email_ck = '';
    private $p6_email         = '';
    private $p6_email_ck      = '';
    private $p_dialog_cs      = '';

    /**
     * @var \GuzzleHttp\Client
     */
    private \GuzzleHttp\Client $client;


    /**
     * @param string $login
     * @param string $pass
     */
    public function __construct(string $login, string $pass) {

        $this->login = $login;
        $this->pass  = $pass;
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function start(): void {

        $uri      = '/portal/f?p=10901:1';
        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html: $uri");
        }

        $content = $response->getBody()->getContents();

        preg_match('/p=10901:REQUESTS_MAP:(.*?)::NO:::">/', $content, $match);
        $this->instance5 = $match[1] ?? null;

        preg_match("/p_dialog_cs=(.*?)',/", $content, $match);
        $this->loginDialogCs = $match[1] ?? null;

        preg_match('/<input type="hidden" id="pPageItemsProtected" value="(.*?)" \/>/', $content, $match);
        $this->protected6 = $match[1] ?? null;

        preg_match('/<input type="hidden" value="(.*?)"\s*?id="pSalt" \/>/', $content, $match);
        $this->salt7 = $match[1] ?? null;

        preg_match('/p_request: "PLUGIN=(.*?)"/', $content, $match);
        $this->plugin8 = $match[1] ?? null;
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function login(): void {

        $this->loginForm();
        $this->login1();
        $this->login2();
        $this->login3();
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function logout(): void {

       $this->accountPage();
       $this->logout1();
       $this->logout2();
    }


    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    #[ArrayShape(['reports' => "array", 'pages' => "array"])]
    public function getReportsMe(): array {

        $uri      = "/portal/f?p=10901:REQUESTS:{$this->instance5}::NO:8::";
        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html: $uri");
        }


        $content = $response->getBody()->getContents();

        preg_match('/init\("R46446660640596495","(.*?)",/', $content, $match);
        $this->plugin8 = $match[1] ?? null;



        $pages = [];

        preg_match('/<td nowrap="nowrap" class="pagination">([\s\S]*?)<\/td>/', $content, $match); // g
        $pages_html = $match[1] ?? null;

        if ($pages_html) {
            preg_match_all('/[;<][b"]>(.*?)-(.*?)&nbsp;<\/[ba]>/', $pages_html, $matches);

            foreach ($matches as $key => $match) {
                if ($key == 0) {
                    continue;
                }

                $pages[] = [
                    'start'  => $match[0],
                    'end'    => $match[1],
                    'plugin' => $match,
                ];
            }
        }



        $reports = [];

        preg_match('/<ul class="t-SearchResults-list">([\s\S]*?)<\/ul>/', $content, $match);
        $reports_html = $match[1] ?? null;

        if ($reports_html) {
            preg_match_all('/<div class="problem_main_box">\s*<section>([\s\S]*?)<\/section>/', $reports_html, $matches); // g

            if ( ! empty($matches[0])) {
                foreach ($matches[0] as $match_report) {
                    preg_match('/[\s\S]*?P10_ID_REQUEST:(.*?)"/', $match_report, $match);
                    $report_id = $match[1] ?? null;

                    preg_match('/Заявка № (.*?)[\r\n]/', $match_report, $match);
                    $code = $match[1] ?? null;

                    preg_match('/problem_title_text">\s*(.*?)[\r\n]/', $match_report, $match);
                    $title = $match[1] ?? null;

                    preg_match('/problem_status">\s*<span>\s*(.*?)[\r\n]/', $match_report, $match);
                    $status = $match[1] ?? null;

                    preg_match('/problem_status_date">\s*(.*?)[\r\n]/', $match_report, $match);
                    $status_date = $match[1] ?? null;

                    preg_match('/problem_address">\s*(.*?)[\r\n]/', $match_report, $match);
                    $address = $match[1] ?? null;

                    preg_match('/problem_pic"><img src="(.*?)"/', $match_report, $match);
                    $thumbnail_url = $match[1] ?? null;

                    preg_match('/problem_description">\s*(.*?)[\r\n]/', $match_report[0], $match);
                    $user_comment = $match[1] ?? null;

                    preg_match('/Принят:[\s\S]*?problem_date">\s*(.*?)[\r\n]/', $match_report[0], $match);
                    $register_date = $match[1] ?? null;

                    $reports[] = [
                        'report_id'     => $report_id,
                        'code'          => $code,
                        'title'         => $title,
                        'status'        => $status,
                        'status_date'   => $status_date,
                        'address'       => $address,
                        'thumbnail_url' => $thumbnail_url,
                        'user_comment'  => $user_comment,
                        'register_date' => $register_date,
                    ];
                }
            }
        }

        return [
            'reports' => $reports,
            'pages'   => $pages,
        ];
    }


    /**
     * @param string $bounds1_lat
     * @param string $bounds1_lng
     * @param string $bounds2_lat
     * @param string $bounds2_lng
     * @param array  $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getReportsMap(string $bounds1_lat, string $bounds1_lng, string $bounds2_lat, string $bounds2_lng, array $options = []): array {

        $this->mapPage();

        $options['period'] = $options['period'] ?? "Воскресенье, 01 Ноябрь, 2020";
        $options['zoom']   = $options['zoom'] ?? '16';

        return $this->getMapMarkers($bounds1_lat, $bounds1_lng, $bounds2_lat, $bounds2_lng, $options);
    }


    /**
     * @param string $report_url
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getReportMap(string $report_url): array {

        $uri      = "/portal/f?p=10901:35:{$this->instance5}::::P35_ID_REQUEST:{$report_url}";
        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html: $uri");
        }

        $content = $response->getBody()->getContents();

        preg_match('/name="P35_ID_REQUEST" value="(.*?)"/', $content, $match);
        $report['report_id'] = $match[1] ?? null;

        preg_match('name="P35_FAKE_ID" value="Заявка №(.*?)"/', $content, $match);
        $report['code'] = $match[1] ?? null;

        preg_match('/name="P35_SUBJECT" value="(.*?)"/', $content, $match);
        $report['title'] = $match[1] ?? null;

        preg_match('/name="P35_STATUS" value="(.*?)"/', $content, $match);
        $report['status'] = $match[1] ?? null;

        preg_match('/name="P35_HOURS_LEFT" value="(.*?)"/', $content, $match);
        $report['status_date'] = $match[1] ?? null;

        preg_match('/name="P35_ADDRESS" value="(.*?)"/', $content, $match);
        $report['address'] = $match[1] ?? null;

        preg_match('name="P35_DESC" value="(.*?)"/', $content, $match);
        $report['user_comment'] = $match[1] ?? null;

        preg_match('name="P35_ORG_COMMENT" value="(.*?)"/', $content, $match);
        $report['org_comment'] = $match[1] ?? null;

        preg_match('name="P35_CREATE_DATE" value="(.*?)"/', $content, $match);
        $report['date_register'] = $match[1] ?? null;

        preg_match('name="P35_MODIFY_DATE" value="(.*?)"/', $content, $match);
        $report['date_last_modified'] = $match[1] ?? null;


        $user_images = [];

        preg_match('/Изображения пользователя([\s\S]*?)<\/ul>/', $content, $match);
        $images_html = $match[1] ?? null;

        if ($images_html) {
            preg_match_all('/background-image: url\((.*?)\);"><a href="(.*?)" class/', $images_html, $matches);

            foreach ($matches as $key => $match) {
                if ($key == 0) {
                    continue;
                }

                $user_images[] = [
                    'thumbnail' => $match[0],
                    'url'       => $match[1],
                ];
            }
        }


        $org_images = [];

        preg_match('/Изображения организации([\s\S]*?)<\/ul>/', $content, $match);
        $images_html = $match[1] ?? null;

        if ($images_html) {
            preg_match_all('/background-image: url\((.*?)\);"><a href="(.*?)" class/', $images_html, $matches);


            foreach ($matches as $key => $match) {
                if ($key == 0) {
                    continue;
                }
                $user_images[] = [
                    'thumbnail' => $match[0],
                    'url'       => $match[1],
                ];
            }
        }

        $report['user_images'] = $user_images;
        $report['org_images']  = $org_images;


        return $report;
    }


    /**
     * @param string $problem_text
     * @param array  $images
     * @param string $lat
     * @param string $lng
     * @param array  $options
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function createReport(string $problem_text, array $images, string $lat, string $lng, array $options = []): void {

        $options['region_id'] = $options['region_id'] ?? '21';

        $this->createRequest1($options);
        $this->createRequest2($options);
        $this->createRequestForm();
        $this->setReportCoordinates($lat, $lng);

        foreach ($images as $image) {
            $this->setReportImages($image);
        }

        $this->acceptedReport($problem_text, $lat, $lng, $options);
    }


    /**
     * @param array $options
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function createRequest1(array $options = []): void {

        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'headers' => [
                "X-Requested-With" => 'XMLHttpRequest',
                "Accept"           => 'application/json, text/javascript, */*; q=0.01',
            ],
            'form_params' => [
                'p_flow_id'      => '10901',
                'p_flow_step_id' => 1,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "PLUGIN={$this->plugin8}",
                'p_json'         => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P1_REGION", 'v' => (int)($options['region_id'] ?? ''),],
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ({$content_type} createRequest1): $uri");
        }
    }


    /**
     * @param array $options
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function createRequest2(array $options = []): void {

        $uri      = "/portal/wwv_flow.accept";
        $response = $this->request('post', $uri, [
            'headers' => [
                "X-Requested-With" => 'XMLHttpRequest',
                "Accept"           => 'application/json, text/javascript, */*; q=0.01',
            ],
            'form_params' => [
                'p_flow_id'            => '10901',
                'p_flow_step_id'       => 1,
                'p_instance'           => $this->instance5,
                'p_debug'              => '',
                'p_request'            => "CreateReq",
                'p_reload_on_submit'   => "S",
                'p_page_submission_id' => $this->pageSubmissionId,
                'p_json'               => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P1_REGION", 'v' => (int)($options['region_id'] ?? ''),],
                            ['n' => "P1_ADDRESS", 'v' => $options['address'] ?? '',],
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);


        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ({$content_type} createRequest2): $uri");
        }


        $content = $response->getBody()->getContents();

        preg_match('/p_dialog_cs=(.*?)\\\u0027/', $content, $match);
        $this->p_dialog_cs = $match[1] ?? null;
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function createRequestForm(): void {

        $uri      = "/portal/f?p=10901:9:{$this->instance5}::NO:RP,9::&p_dialog_cs={$this->p_dialog_cs}";
        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html ($content_type): $uri");
        }

        $content = $response->getBody()->getContents();

        preg_match('/<input type="hidden" id="pPageItemsProtected" value="(.*?)" \/>/', $content, $match);
        $this->protected6 = $match[1] ?? null;

        preg_match('/<input type="hidden" value="(.*?)"\s*?id="pSalt" \/>/', $content, $match);
        $this->salt7 = $match[1] ?? null;

        preg_match('/<input type="hidden" name="p_page_submission_id" value="(.*?)" id="pPageSubmissionId" \/>/', $content, $match);
        $this->pageSubmissionId = $match[1] ?? null;

        preg_match('/createPluginMap\("map","(.*?)","eds/', $content, $match);
        $this->plugin8 = $match[1] ?? null;

        preg_match('/R67032236963703517",{"ajaxIdentifier":"(.*?)"/', $content, $match);
        $this->plugin2 = $match[1] ?? null;
    }


    /**
     * @param string $lat
     * @param string $lng
     * @return void
     * @throws \Exception
     */
    private function setReportCoordinates(string $lat, string $lng): void {

        $lat = str_replace('.', '', $lat);
        $lng = str_replace('.', '', $lng);

        $lat = str_pad($lat, 17, '0');
        $lng = str_pad($lng, 17, '0');


        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'headers' => [
                "X-Requested-With" => 'XMLHttpRequest',
                "Accept"           => 'application/json, text/javascript, */*; q=0.01',
            ],
            'form_params' => [
                'p_flow_id'      => 10901,
                'p_flow_step_id' => 9,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "PLUGIN={$this->plugin8}",
                'x01'            => "{$lat}:{$lat}:{$lat}:{$lat}",
                'x02'            => "{$lng}:{$lng}:{$lng}:{$lng}",
                'x03'            => 4326,
                'x04'            => 4326,
                'x10'            => "TRANSFORM",
                'p_json'         => json_encode([
                    'p_debug' => '1',
                    'salt'    => $this->salt7,
                ]),
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ({$content_type} setReportCoordinates): $uri");
        }
    }


    /**
     * @param string $image
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function setReportImages(string $image): void {

        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'headers' => [
                "Accept" => 'application/json, text/javascript, */*; q=0.01',
            ],
            'multipart' => [
                [ 'name' => 'p_flow_id',      'contents' => 10901 ],
                [ 'name' => 'p_flow_step_id', 'contents' => 9 ],
                [ 'name' => 'p_instance',     'contents' => $this->instance5 ],
                [ 'name' => 'p_debug',        'contents' => '' ],
                [ 'name' => 'p_request',      'contents' => "PLUGIN={$this->plugin2}" ],
                [ 'name' => 'X01',            'contents' => 'UPLOAD' ],
                [ 'name' => 'X02',            'contents' => basename($image) ],
                [ 'name' => 'X03',            'contents' => mime_content_type($image) ],
                [
                    'name'     => 'F01',
                    'contents' => file_get_contents($image),
                    'filename' => basename($image)
                ],
            ],
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html ({$content_type} setReportImages): $uri");
        }
    }


    /**
     * @param string $problem_text
     * @param string $lat
     * @param string $lng
     * @param array  $options
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function acceptedReport(string $problem_text, string $lat, string $lng, array $options = []): void {

        $lat = str_replace('.', ',', $lat);
        $lng = str_replace('.', ',', $lng);


        $uri      = "/portal/wwv_flow.accept";
        $response = $this->request('post', $uri, [
            'headers' => [
                "X-Requested-With" => 'XMLHttpRequest',
                "Accept"           => 'application/json, text/javascript, */*; q=0.01',
            ],
            'form_params' => [
                'p_flow_id'            => '10901',
                'p_flow_step_id'       => 9,
                'p_instance'           => $this->instance5,
                'p_debug'              => '',
                'p_request'            => "CreateRequest",
                'p_reload_on_submit'   => "S",
                'p_page_submission_id' => $this->pageSubmissionId,
                'p_json'               => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P9_REQUEST_TYPE", 'v' => '2'],
                            ['n' => "P9_REGION", 'v' => $options['region_id'] ?? '',],
                            ['n' => "P9_ADDRESS", 'v' => $options['address'] ?? '',],
                            ['n' => "P9_PROBLEM", 'v' => $problem_text,],
                            ['n' => "P9_LNG", 'v' => $lng],
                            ['n' => "P9_LAT", 'v' => $lat],
                            ['n' => "P9_TESTFU", 'v' => ''],
                            //['n' => "P9_PROVIDE_CONTACTS", 'v' => ''],
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);


        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ({$content_type} acceptedReport): $uri");
        }
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function mapPage(): void {

        $uri      = "/portal/f?p=10901:REQUESTS_MAP:{$this->instance5}::NO:::";
        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html: $uri");
        }

        $content = $response->getBody()->getContents();

        preg_match('/<input\s*?type="hidden" name="p_instance" value="(.*?)" id="pInstance" \/>/', $content, $match);
        $this->instance5 = $match[1] ?? null;

        preg_match('/<input type="hidden" id="pPageItemsProtected" value="(.*?)" \/>/', $content, $match);
        $this->protected6 = $match[1] ?? null;

        preg_match('/<input type="hidden" value="(.*?)"\s*?id="pSalt" \/>/', $content, $match);
        $this->salt7 = $match[1] ?? null;

        preg_match('/createPluginMap\("R230481754811300191","(.*?)","eds/', $content, $match);
        $this->plugin8 = $match[1] ?? null;
    }


    /**
     * @param string $bounds1_lat
     * @param string $bounds1_lng
     * @param string $bounds2_lat
     * @param string $bounds2_lng
     * @param array  $options
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function getMapMarkers(string $bounds1_lat, string $bounds1_lng, string $bounds2_lat, string $bounds2_lng, array $options = []): array {

        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'form_params' => [
                'p_flow_id'      => '10901',
                'p_flow_step_id' => 6,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "PLUGIN={$this->plugin8}",
                'p_json'         => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P19_PERIOD", 'v' => $options['period'],],
                            ['n' => "P19_SUBJECT", 'v' => '',],
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
                'x01' => '4326',
                'x02' => preg_replace('.', '', $bounds1_lat),
                'x03' => preg_replace('.', '', $bounds1_lng),
                'x04' => preg_replace('.', '', $bounds2_lat),
                'x05' => preg_replace('.', '', $bounds2_lng),
                'x06' => 'Y',
                'x07' => $options['zoom'],
                'x10' => 'FOIDATA',
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json (getMapMarkers): $uri");
        }

        $content       = $response->getBody()->getContents();
        $content_array = @json_decode($content, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("Некорректный ответ от сервера. Содержимое ответа не является json (getMapMarkers): $uri");
        }

        return $content_array['rows'] ?? [];
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function accountPage(): void {

        $uri      = "/portal/f?p=10901:3:{$this->instance5}::NO:3::&p_dialog_cs={$this->loginDialogCs}";
        $response = $this->request('get', $uri);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html: $uri");
        }

        $content = $response->getBody()->getContents();

        preg_match('/<input\s*?type="hidden" name="p_instance" value="(.*?)" id="pInstance" \/>/', $content, $match);
        $this->instance5 = $match[1] ?? null;

        preg_match('/<input type="hidden" id="pPageItemsProtected" value="(.*?)" \/>/', $content, $match);
        $this->protected6 = $match[1] ?? null;

        preg_match('/<input type="hidden" value="(.*?)"\s*?id="pSalt" \/>/', $content, $match);
        $this->salt7 = $match[1] ?? null;

        preg_match('/<input type="hidden" name="p_page_submission_id" value="(.*?)" id="pPageSubmissionId" \/>/', $content, $match);
        $this->pageSubmissionId = $match[1] ?? null;

        preg_match('/ajaxIdentifier":"(.*?)","attribute01":"PLSQL_EXPRESSION/', $content, $match);
        $this->plugin8 = $match[1] ?? null;

        preg_match('/id="P6_PHONE" value="(.*?)">/', $content, $match);
        $this->p6_phone = $match[1] ?? null;

        preg_match('/data-for="P6_PHONE" value="(.*?)">/', $content, $match);
        $this->p6_phone_ck = $match[1] ?? null;

        preg_match('/id="P6_PROP_EMAIL" value="(.*?)">/', $content, $match);
        $this->p6_prop_email = $match[1] ?? null;

        preg_match('/data-for="P6_PROP_EMAIL" value="(.*?)">/', $content, $match);
        $this->p6_prop_email_ck = $match[1] ?? null;

        preg_match('/id="P6_EMAIL" value="(.*?)">/', $content, $match);
        $this->p6_email = $match[1] ?? null;

        preg_match('/data-for="P6_EMAIL" value="(.*?)">/', $content, $match);
        $this->p6_email_ck = $match[1] ?? null;
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function logout1(): void {

        $uri      = "/portal/wwv_flow.accept";
        $response = $this->request('post', $uri, [
            'form_params' => [
                'p_flow_id'      => '10901',
                'p_flow_step_id' => 6,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "EXIT_ACTION",
                'p_reload_on_submit'      => "S",
                'p_page_submission_id'      => $this->pageSubmissionId,
                'p_json'         => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P6_LAST_NAME", 'v' => '',],
                            ['n' => "P6_FIRST_NAME", 'v' => '',],
                            ['n' => "P6_CITY", 'v' => '21',],
                            ['n' => "P6_NOTIFICATIONS", 'v' => ["1", "2", "3", "4"],],
                            ['n' => "P6_PHONE", 'v' => $this->p6_phone, 'ck' => $this->p6_phone_ck],
                            ['n' => "P6_PROP_EMAIL", 'v' => $this->p6_prop_email, 'ck' => $this->p6_prop_email_ck],
                            ['n' => "P6_EMAIL", 'v' => $this->p6_email, 'ck' => $this->p6_email_ck],
                            ['n' => "P6_ROWCNT", 'v' => '3',],
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ({$content_type} logout1): $uri");
        }
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function logout2(): void {

        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'form_params' => [
                'p_flow_id'      => '10901',
                'p_flow_step_id' => 6,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "PLUGIN={$this->plugin8}",
                'p_json'         => json_encode([
                    'pageItems' => null,
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html ({$content_type} logout2): $uri");
        }
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function loginForm(): void {

        $uri      = "/portal/f?p=10901:3:{$this->instance5}::NO:3::&p_dialog_cs={$this->loginDialogCs}";
        $response = $this->request('get', $uri);


        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html: $uri");
        }

        $content = $response->getBody()->getContents();

        preg_match('/<input\s*?type="hidden" name="p_instance" value="(.*?)" id="pInstance" \/>/', $content, $match);
        $this->instance5 = $match[1] ?? null;

        preg_match('/<input type="hidden" id="pPageItemsProtected" value="(.*?)" \/>/', $content, $match);
        $this->protected6 = $match[1] ?? null;

        preg_match('/<input type="hidden" value="(.*?)"\s*?id="pSalt" \/>/', $content, $match);
        $this->salt7 = $match[1] ?? null;

        preg_match('/<input type="hidden" name="p_page_submission_id" value="(.*?)" id="pPageSubmissionId" \/>/', $content, $match);
        $this->pageSubmissionId = $match[1] ?? null;

        preg_match('/ajaxIdentifier":"(.*?)","attribute01":"PLSQL_EXPRESSION/', $content, $match);
        $this->plugin8 = $match[1] ?? null;

        preg_match('/ajaxIdentifier":"(.*?)","attribute01":"#P3_USERNAME,#P3_PASSWORD,#P3_REMEMBER/', $content, $match);
        $this->plugin2 = $match[1] ?? null;
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function login1(): void {

        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'form_params' => [
                'p_flow_id'      => '10901',
                'p_flow_step_id' => 3,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "PLUGIN={$this->plugin8}",
                'p_json'         => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P3_USERNAME", 'v' => $this->login,],
                            ['n' => "P3_PASSWORD", 'v' => $this->pass,]
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);


        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ({$content_type} login1): $uri");
        }
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function login2(): void {

        $uri      = "/portal/wwv_flow.ajax";
        $response = $this->request('post', $uri, [
            'form_params' => [
                'p_flow_id'      => '10901',
                'p_flow_step_id' => 3,
                'p_instance'     => $this->instance5,
                'p_debug'        => '',
                'p_request'      => "PLUGIN={$this->plugin2}",
                'p_json'         => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P3_USERNAME", 'v' => $this->login,],
                            ['n' => "P3_PASSWORD", 'v' => $this->pass,],
                            ['n' => "P3_REMEMBER", 'v' => ['1'],]
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'text/html') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не text/html ({$content_type} login2): $uri");
        }
    }


    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    private function login3(): void {

        $uri      = "/portal/wwv_flow.accept";
        $response = $this->request('post', $uri, [
            'form_params' => [
                'p_flow_id'            => '10901',
                'p_flow_step_id'       => 3,
                'p_instance'           => $this->instance5,
                'p_debug'              => '',
                'p_request'            => "RESOK",
                'p_reload_on_submit'   => "S",
                'p_page_submission_id' => $this->pageSubmissionId,
                'p_json'               => json_encode([
                    'pageItems' => [
                        'itemsToSubmit' => [
                            ['n' => "P3_USERNAME", 'v' => $this->login, ],
                            ['n' => "P3_PASSWORD", 'v' => $this->pass, ],
                            ['n' => "P3_REMEMBER", 'v' => ['1'], ],
                            ['n' => "P3_LOGIN_ERROR", 'v' => "Неизвестная ошибка. Пожалуйста, обратитесь в техподдержку (support115@it-minsk.by)", ],
                            ['n' => "P3_LOGIN_RESULT", 'v' => "RESULT_OK", ]
                        ],
                        'protected'     => $this->protected6,
                        'rowVersion'    => '',
                    ],
                    'salt'      => $this->salt7,
                ]),
            ]
        ]);

        if ( ! $response->hasHeader('Content-Type') ||
            strpos($response->getHeader('Content-Type')[0], 'application/json') !== 0
        ) {
            $content_type = $response->getHeader('Content-Type')[0];
            throw new \Exception("Некорректный ответ от сервера - Content-Type не application/json ($content_type login3): $uri");
        }
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
                'Accept'          => "*/*",
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.183 Safari/537.36',
                'Connection'      => "keep-alive",
                'Cache-Control'   => "no-cache",
            ];


            $this->client = new \GuzzleHttp\Client([
                'base_uri'           => $this->base_url,
                'timeout'            => 10,
                'connection_timeout' => 10,
                'allow_redirects'    => true,
                'cookies'            => true,
                'headers'            => $headers,
                'curl'               => [
                    CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1' // На сервере в 115 слабый ключ шифрования
                ],
            ]);
        }


        $response = $this->client->request($method, $uri, $options);


        if ($response->getStatusCode() >= 400) {
            $code = $response->getStatusCode();

            throw new \Exception("Некорректный ответ от сервера. Http код {$code}: {$uri}");
        }

        return $response;
    }
}