<?php


require '../vendor/autoload.php';

$client = new GuzzleHttp\Client();
$cache = new Doctrine\Common\Cache\FilesystemCache(__DIR__ . '/../storage/cache/doctrine');

$twig = new Twig_Environment(
    new Twig_Loader_Filesystem(__DIR__ . '/../templates'),
    [
        'cache' => __DIR__ . '/../storage/cache/twig',
    ]);

if ($cache->contains('posts')) {
    $recentPosts = $cache->fetch('posts');
} else {
    $response = $client->get('http://www.onemorething.nl/api/get_recent_posts');
    $body = json_decode($response->getBody());
    $recentPosts = $body->posts;
    foreach ($recentPosts as &$article) {
        $article->title = html_entity_decode($article->title);
    }
    $cache->save('posts', $recentPosts);
}

echo $twig->render('home.twig', ['articles' => $recentPosts]);
