<?php

namespace Drupal\product_portal\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\File\FileSystemInterface;

/**
 * Provides a 'ProductScanner' Block.
 *
 * @Block(
 *   id = "productscanner_block",
 *   admin_label = @Translation("ProductScanner block"),
 * )
 */
class ProductScannerBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() 
  {
    return ['label_display' => FALSE];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $build = [];
    $path = '';
    $directory = "public://Images/QrCodes/";
    \Drupal::service('file_system')->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY);
    $qrName = 'myQrcode';
    $uri = $directory . 'QR'. '.png'; // Generates a png image.
    $path =  \Drupal::service('file_system')->realpath($uri);
    $relative_file_url =  \Drupal::service('file_url_generator')
      ->generateAbsoluteString($uri);  
    $qr_image = "<img src='{$relative_file_url}'/>";
    //Get current node object
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof \Drupal\node\NodeInterface) {
      $product_link = $node->field_product_app_link->getValue()[0]['uri'];
      // calculate $qr_image with $node
      $build = [
        '#markup' => $qr_image,
        '#cache' => ['tags' => $node->getCacheTags()],
      ];
    }
    //Set url for QRcode
    \PHPQRCode\QRcode::png($product_link, $path, 'L', 4, 2);
    
    $build['#cache']['contexts'] = ['route'];
    return $build;

  }

}