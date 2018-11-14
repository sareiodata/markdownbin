<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response, array $args) {
    $response = $this->view->render($response, 'index.phtml', []);

    return $response;
});

$app->post('/', function (Request $request, Response $response) {
    $markdown = preg_replace('#<script(.*?)>(.*?)</script>#is', '', (string)$_POST['markdown']);
    $Parsedown = new Parsedown();
    $html = $Parsedown->text($markdown);

    $output = $this->view->fetch('markdown-html.phtml', ['html' => $html]);

    $dir = date('Ymd');
    if (!file_exists('bin/' . $dir)) {
        mkdir('bin/' . $dir, 0777, true);
    }

    $file_slug = 'bin/' . $dir . "/" . bin2hex(random_bytes(10));

    $file_name = $file_slug . '.html';
    file_put_contents($file_name, $output);

    $cryptkey = $this->settings['salt'];

    $secret = \MarkdownBin\hexConverter::strToHex(openssl_encrypt( $file_slug, 'AES-128-CBC', $cryptkey));

    $file_name_mk = 'bin/' . $dir . "/" . $secret . '.txt';
    file_put_contents($file_name_mk, $markdown);

    return $response->withRedirect('/edit/' . $secret);
});

$app->get('/edit/{secret}', function (Request $request, Response $response, array $args){
    $secret = \MarkdownBin\hexConverter::hexToStr($args['secret']);

    $cryptkey = $this->settings['salt'];
    $file_slug = openssl_decrypt( $secret, 'AES-128-CBC', $cryptkey);

    $url = 'http://' . $_SERVER['HTTP_HOST']. '/' . $file_slug . '.html';

    $explode = explode('/', $file_slug);
    $dir = $explode[1];
    $markdown = file_get_contents('bin/' . $dir . '/' . \MarkdownBin\hexConverter::strToHex( $secret ). '.txt');

    $response = $this->view->render($response, 'edit.phtml', ['markdown' => $markdown, 'url' => $url]);
    return $response;
});

$app->post('/edit/{secret}', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    $markdown = preg_replace('#<script(.*?)>(.*?)</script>#is', '', (string)$data['markdown']);
    $Parsedown = new Parsedown();
    $html = $Parsedown->text($markdown);
    $output = $this->view->fetch('markdown-html.phtml', ['html' => $html]);

    $secret = \MarkdownBin\hexConverter::hexToStr($args['secret']);
    $cryptkey = $this->settings['salt'];
    $file_slug = openssl_decrypt( $secret, 'AES-128-CBC', $cryptkey);

    $url = 'http://' . $_SERVER['HTTP_HOST']. '/' . $file_slug . '.html';

    $explode = explode('/', $file_slug);
    $dir = $explode[1];

    file_put_contents($file_slug . '.html', $output);
    file_put_contents('bin/' . $dir . '/' . \MarkdownBin\hexConverter::strToHex( $secret ). '.txt', $markdown);

    $selectionStart = (int)$data['markdown-selectionStart'];
    $selectionEnd = (int)$data['markdown-selectionEnd'];
    $scrollTop = (int)$data['markdown-scrollTop'];

    $response = $this->view->render($response, 'edit.phtml', ['scrollTop' => $scrollTop, 'selectionStart' => $selectionStart, 'selectionEnd' => $selectionEnd, 'markdown' => $markdown, 'url' => $url, 'saved' => true]);
    return $response;

});