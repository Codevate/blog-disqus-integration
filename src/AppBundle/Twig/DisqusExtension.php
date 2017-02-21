<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DisqusExtension extends \Twig_Extension
{
  private $twig;
  private $router;
  private $tokenStorage;
  private $config;

  /**
   * @param \Twig_Environment $twig
   * @param RouterInterface $router
   * @param TokenStorageInterface $tokenStorage
   * @param array $config
   */
  public function __construct(\Twig_Environment $twig, RouterInterface $router, TokenStorageInterface $tokenStorage, array $config)
  {
    $this->twig = $twig;
    $this->router = $router;
    $this->tokenStorage = $tokenStorage;
    $this->config = $config;
  }

  public function getFunctions()
  {
    return array(
      new \Twig_SimpleFunction('disqus_embed', array($this, 'renderEmbed'), array('is_safe' => array('html'))),
    );
  }

  /**
   * @param Post $post
   * @return string
   */
  public function renderEmbed(Post $post)
  {
    return $this->twig->render(':disqus:_embed.html.twig', array(
      'hmac_signature' => $this->getHmacSignature(),
      'api_key' => $this->config['api_key'],
      'shortname' => $this->config['shortname'],
      'identifier' => sprintf('post_%d', $post->getId()),
      'title' => $post->getTitle(),
      'url' => $this->router->generate('blog_post_redirect', array('id' => $post->getId()), UrlGeneratorInterface::ABSOLUTE_URL),
    ));
  }

  /**
   * @return string
   */
  private function getHmacSignature()
  {
    $data = array();

    if (($token = $this->tokenStorage->getToken()) && ($user = $token->getUser()) && $user instanceof User) {
      $data['id'] = $user->getId();
      $data['username'] = $user->getFullName();
      $data['email'] = $user->getEmailCanonical();
    }

    $message = base64_encode(json_encode($data));
    $timestamp = time();
    $blocksize = 64;
    $secretKey = $this->config['api_secret'];

    if (strlen($secretKey) > $blocksize) {
      $secretKey = pack('H*', sha1($secretKey));
    }

    $secretKey = str_pad($secretKey, $blocksize, chr(0x00));
    $ipad = str_repeat(chr(0x36), $blocksize);
    $opad = str_repeat(chr(0x5c), $blocksize);
    $hmac = pack(
      'H*', sha1(
        ($secretKey ^ $opad) . pack(
          'H*', sha1(
            ($secretKey ^ $ipad) . sprintf('%s %s', $message, $timestamp)
          )
        )
      )
    );

    return sprintf('%s %s %s', $message, bin2hex($hmac), $timestamp);
  }
}
