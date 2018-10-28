<?php

namespace RMST\DeviceDetector;

use Slim\Http\Response;

class DeviceDetectorTest extends SlimBaseTest
{
    /**
     * @dataProvider popularUserAgentsDataProvider
     * @param null|string $userAgent
     * @param $expectedResponseCode
     * @param null $expectedResponseData
     */
    public function testGetDeviceInfo(?string $userAgent, $expectedResponseCode, $expectedResponseData = null)
    {
        $requestData = [];
        if (!is_null($userAgent)) {
            $requestData['userAgent'] = $userAgent;
        }

        $response = $this->_getResponse($requestData);
        $this->assertEquals($expectedResponseCode, $response->getStatusCode(), 'response code is correct');
        if (!is_null($expectedResponseData)) {
            $this->assertSame($expectedResponseData, json_decode((string)$response->getBody(), true), 'response data is correct');
        }
    }

    private function _getResponse(array $requestData): Response
    {
        $routeName = 'getDeviceInfo';
        $router = $this->_app->getContainer()->get('router');
        $uri = $router->pathFor($routeName, $requestData);
        return $this->_runApp('GET', $uri, $requestData);
    }

    public function popularUserAgentsDataProvider()
    {
        $expectedBotResult = ['is_mobile' => false, 'is_tablet' => false, 'is_bot' => true, 'os_name' => false, 'os_version' => null];
        return [
            [
                null,
                400
            ],
            [
                '',
                400
            ],
            //bots
            [
                'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)',
                200,
                $expectedBotResult
            ],
            [
                'Mozilla/5.0 (compatible; YandexDirect/3.0; +http://yandex.com/bots)',
                200,
                $expectedBotResult
            ],
            [
                'Mozilla/5.0 (compatible; Linux x86_64; Mail.RU_Bot/2.0; +http://go.mail.ru/help/robots)',
                200,
                $expectedBotResult
            ],
            [
                'Mozilla/5.0 (compatible; YandexMetrika/3.0; +http://yandex.com/bots)',
                200,
                $expectedBotResult
            ],
            [
                'mfibot/1.1 (http://www.mfisoft.ru/analyst/; <admin@mfisoft.ru>; en-RU)',
                200,
                $expectedBotResult
            ],

            //mobile bots
            [
                'Mozilla/5.0 (iPhone; CPU iPhone OS 7_0 like Mac OS X) AppleWebKit/537.51.1 '
                    . '(KHTML, like Gecko) Version/7.0 Mobile/11A465 Safari/9537.53 '
                    . '(compatible; bingbot/2.0; http://www.bing.com/bingbot.htm)',
                200,
                ['is_mobile' => true, 'is_tablet' => false, 'is_bot' => true, 'os_name' => false, 'os_version' => null]
            ],

            //mobile
            [
                'userAgent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_2_1 like Mac OS X) AppleWebKit/601.1.46 '
                    . '(KHTML, like Gecko) Version/9.0 Mobile/13D15 Safari/601.1',
                200,
                ['is_mobile' => true, 'is_tablet' => false, 'is_bot' => false, 'os_name' => 'iOS', 'os_version' => '9.2']
            ],

            //pc
            [
                'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) '
                    . 'Chrome/47.0.2526.111 YaBrowser/16.2.0.3539 Safari/537.36	',
                200,
                ['is_mobile' => false, 'is_tablet' => false, 'is_bot' => false, 'os_name' => 'Windows', 'os_version' => '7']
            ],
            // andriod tablet
            [
                'Mozilla/5.0 (Linux; U; Android 4.2.2; ru-ru; GT-P5100 Build/JDQ39) AppleWebKit/534.30 '
                    . '(KHTML, like Gecko) Version/4.0 Safari/534.30',
                200,
                ['is_mobile' => true, 'is_tablet' => true, 'is_bot' => false, 'os_name' => 'Android', 'os_version' => '4.2']
            ],
            // ipad
            [
                'Mozilla/5.0 (iPad; CPU OS 9_3_2 like Mac OS X) AppleWebKit/601.1.46 '
                    . '(KHTML, like Gecko) Version/9.0 Mobile/13F69 Safari/601.1',
                200,
                ['is_mobile' => true, 'is_tablet' => true, 'is_bot' => false, 'os_name' => 'iOS', 'os_version' => '9.3']
            ],

            [
                'Mozilla/5.0 (Linux; Android 7.1.1; SM-N950F Build/NMF26X) AppleWebKit/537.36 '
                    . '(KHTML, like Gecko) Chrome/62.0.3202.84 Mobile Safari/537.36',
                200,
                ['is_mobile' => true, 'is_tablet' => false, 'is_bot' => false, 'os_name' => 'Android', 'os_version' => '7.1']
            ],

            [
                'Mozilla/5.0 (Linux; Android 7.1.1; SAMSUNG SM-N950F Build/NMF26X) AppleWebKit/537.36 '
                    . '(KHTML, like Gecko) SamsungBrowser/6.2 Chrome/56.0.2924.87 Mobile Safari/537.36',
                200,
                ['is_mobile' => true, 'is_tablet' => false, 'is_bot' => false, 'os_name' => 'Android', 'os_version' => '7.1']
            ],
        ];
    }
}
