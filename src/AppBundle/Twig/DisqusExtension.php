<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Post;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class DisqusExtension extends \Twig_Extension
{
  private $twig;
  private $router;
  private $config;

  /**
   * @param \Twig_Environment $twig
   * @param array $config
   */
  public function __construct(\Twig_Environment $twig, RouterInterface $router, array $config)
  {
    $this->twig = $twig;
    $this->router = $router;
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
      'shortname' => $this->config['shortname'],
      'identifier' => sprintf('post_%d', $post->getId()),
      'title' => $post->getTitle(),
      'url' => $this->router->generate('blog_post_redirect', array('id' => $post->getId()), UrlGeneratorInterface::ABSOLUTE_URL),
    ));
  }
}
