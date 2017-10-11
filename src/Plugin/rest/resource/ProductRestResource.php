<?php

namespace Drupal\socialrest\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\Entity\Node;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "product_rest_resource",
 *   label = @Translation("Product rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/product/{id}",
 *     "https://www.drupal.org/link-relations/create" = "/api/product"
 *   }
 * )
 */
class ProductRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Current request.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * Constructs a new ProductRestResource object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user,
    Request $request) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;

    $this->request = $request;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('socialrest'),
      $container->get('current_user'),
      \Drupal::request()
    );
  }

  /**
   * Responds to GET requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get($id) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $node = Node::load($id);

    $response = [
      'id' => $node->id(),
      'title' => $node->getTitle(),
      'description' => $node->get('field_description')->value,
      'price' => $node->get('field_price')->value,

    ];

    return new ResourceResponse($response);
  }

  /**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function post($data) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $data = json_decode($this->request->getContent(), TRUE);

    $node = Node::create(['type' => 'product', 'uid' => 1]);

    if (isset($data['title'])) {
      $node->setTitle($data['title']);
    }
    if (isset($data['description'])) {
      $node->set('field_description', $data['description']);
    }
    if (isset($data['price'])) {
      $node->set('field_price', $data['price']);
    }

    $node->save();

    $response = [
      'id' => $node->id(),
      'title' => $node->getTitle(),
      'description' => $node->get('field_description')->value,
      'price' => $node->get('field_price')->value,

    ];

    return new ResourceResponse($response);
  }

  /**
   * Responds to DELETE requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function delete($id) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $node = Node::load($id);
    $node->delete();

    return new ResourceResponse(NULL, 204);
  }

  /**
   * Responds to PATCH requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function patch($id) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    $data = json_decode($this->request->getContent(), TRUE);

    $node = Node::load($id);

    if (isset($data['title'])) {
      $node->setTitle($data['title']);
    }
    if (isset($data['description'])) {
      $node->set('field_description', $data['description']);
    }
    if (isset($data['price'])) {
      $node->set('field_price', $data['price']);
    }

    $node->save();

    $response = [
      'id' => $node->id(),
      'title' => $node->getTitle(),
      'description' => $node->get('field_description')->value,
      'price' => $node->get('field_price')->value,

    ];

    return new ResourceResponse($response);
  }

}
