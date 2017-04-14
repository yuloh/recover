<?php

declare(strict_types=1);

namespace Yuloh\Recover\Renderer\Http\ResponseBuilder;

use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Yuloh\Recover\Concerns\GetsHeadersFromThrowables;
use Yuloh\Recover\Concerns\GetsStatusCodesFromThrowables;
use Yuloh\Recover\Renderer\Http\ResponseBuilderInterface;
use Yuloh\Recover\ThrowableSerializer;

final class HtmlResponseBuilder implements ResponseBuilderInterface
{
    use GetsStatusCodesFromThrowables,
        GetsHeadersFromThrowables;

    private const DEFAULT_HEADERS =  ['Content-Type' => 'text/html; charset=UTF-8'];

    private const TPL = '
<!doctype html>
<html>
<head>
    <meta charset="{{charset}}"/>
    <meta name="robots" content="noindex,nofollow"/>
    <style>
        body { color: #666;
            text-align: center;
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            margin: auto;
            font-size: 14px;
        }

        h1 {
            font-size: 56px;
            line-height: 100px;
            font-weight: normal;
            color: #345;
        }

        h3 {
            font-size: 20px;
            line-height: 28px;
            font-weight: normal;
            color: #345;
        }
        
        h4 {
            font-size: 18px;
            line-height: 24px;
            font-weight: normal;
            color: #345;
        }

        hr {
            max-width: 800px;
            margin: 18px auto;
            border-top: 1px solid #EEE;
            border-bottom: 1px solid white;
        }

        main {
            margin: auto 20px;
        }

        .details {
            height: 450px;
            max-width: 800px;
            text-align: left;
            margin: 0 auto;
            overflow: scroll;
        }

    </style>
</head>
<body>
<main>
    <h1>{{code}}</h1>
    <h3>{{title}}</h3>
    <hr/>
    {{#debug}}
        <h4><strong>{{class}}: </strong>{{message}}</h4>
        <div class="details">
            <pre><code>{{trace}}</code></pre>
        </div>
    {{/debug}}
    </div>
</main>
</body>
</html>
';

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var string
     */
    private $charset;
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        bool $debug = false,
        string $charset = 'UTF-8'
    )
    {
        $this->responseFactory = $responseFactory;
        $this->debug           = $debug;
        $this->charset         = $charset;
    }

    public function build(\Throwable $throwable): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($this->getStatusCode($throwable));

        foreach (array_merge(self::DEFAULT_HEADERS, $this->getHeaders($throwable)) as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        $response->getBody()->write($this->getContent($throwable));

        return $response;
    }

    private function getContent(\Throwable $throwable): string
    {
        if (($statusCode = $this->getStatusCode($throwable)) === 404) {
            $title   = 'The page you were looking for could not be found.';
        } else {
            $title = 'Whoops, something went wrong';
        }

        $data = [
            'charset' => $this->charset,
            'title'   => $title,
            'code'    => $statusCode,
        ];

        if ($this->debug) {
            $data = array_merge($data, [
                'class'   => get_class($throwable),
                'message' => $throwable->getMessage(),
                'trace'   => $this->getTrace($throwable),
            ]);
        }

        return $this->render($data);
    }

    private function getTrace(\Throwable $throwable): string
    {
        return json_encode(
            (new ThrowableSerializer())->serialize($throwable),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    private function render(array $data): string
    {
        foreach ($data as $k => $v) {
            $data['{{' . $k . '}}'] = $this->escape((string) $v);
            unset($data[$k]);
        }

        $tpl = strtr(self::TPL, $data);

        if ($this->debug) {
            $tpl = str_replace(['{{#debug}}', '{{/debug}}'], '', $tpl);
        } else {
            $tpl = substr_replace($tpl, '', strpos($tpl, '{{#debug}}',  -strrpos($tpl, '{{/debug}}')));
        }

        return $tpl;
    }

    private function escape(string $str): string
    {
        return htmlspecialchars($str, ENT_COMPAT | ENT_SUBSTITUTE, $this->charset);
    }
}
