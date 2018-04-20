<?php
namespace Drupal\icgeer\Plugin\Preprocess;

use Drupal\bootstrap\Plugin\Preprocess\Breadcrumb as BootstrapBreadcrumb;
use Drupal\bootstrap\Utility\Variables;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;

/**
 * Pre-processes variables for the "breadcrumb" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @BootstrapPreprocess("breadcrumb")
 */
class Breadcrumb extends BootstrapBreadcrumb {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    $breadcrumb = $variables['breadcrumb'];
    $breadcrumb = [$breadcrumb[0]];
    $node = \Drupal::request()->get('node');

    $request = \Drupal::request();
    $current_path = $request->getPathInfo();
    $path_args = explode('/', $current_path);

    if (!empty($path_args[2]) && $path_args[2] == 'village' && !empty($path_args[3])) {
      $town_page = taxonomy_term_load_multiple_by_name($path_args[3], 'towns');
      $town_page = reset($town_page);
    }

    if (!empty($path_args[2]) && $path_args[2] == 'categorie' && !empty($path_args[3])) {
      $tag_page = taxonomy_term_load_multiple_by_name($path_args[3], 'tags');
      $tag_page = reset($tag_page);
    }

    $types = ['article', 'candidate', 'page'];

    if (isset($node) && $node->getType() == 'article') {
      $term = Term::load($node->get('field_tags')->target_id);

      $breadcrumb[] = [
          'text' => t('News'),
          'url' => Url::fromRoute('view.icgeer_news_list.page_articles'),
      ];

      $breadcrumb[] = [
        'text' => $term->getName(),
        'url' => $term->url(),
      ];
    }

    if (isset($node) && $node->getType() == 'candidate') {
      $breadcrumb[] = [
          'text' => t('Candidats'),
          'url' => Url::fromRoute('view.candidates_list.page_candidats_list'),
      ];

      $term_page = Term::load($node->get('field_candidate_town')->target_id);

      $breadcrumb[] = [
          'text' => $term->getName(),
          'url' => $term->url(),
      ];
    }

    if (isset($node) && in_array($node->getType(), $types)) {
      $breadcrumb[] = [
        'text' => $node->getTitle(),
        'attributes' => new Attribute(['class' => ['active']]),
      ];
    }

    if (isset($town_page)) {
      $breadcrumb[] = [
          'text' => t('Candidats'),
          'url' => Url::fromRoute('view.candidates_list.page_candidats_list'),
      ];
      $breadcrumb[] = [
          'text' => $town_page->getName(),
          'attributes' => new Attribute(['class' => ['active']]),
      ];
    }

    if (isset($tag_page)) {
      $breadcrumb[] = [
          'text' => t('News'),
          'url' => Url::fromRoute('view.candidates_list.page_candidats_list'),
      ];
      $breadcrumb[] = [
          'text' => $tag_page->getName(),
          'attributes' => new Attribute(['class' => ['active']]),
      ];
    }

    if ($path_args[1] == 'articles') {
      if ($filter = \Drupal::request()->query->get('field_tags_target_id')) {
        $term_article = Term::load($filter);
        $breadcrumb[] = [
            'text' => t('News'),
            'url' => Url::fromRoute('view.icgeer_news_list.page_articles'),
        ];
        $breadcrumb[] = [
            'text' => $term_article->getName(),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
      elseif (empty($path_args[3]) || $filter == 'All') {
        $breadcrumb[] = [
            'text' => t('News'),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
    }

    if ($path_args[1] == 'candidats') {
      if ($filter = \Drupal::request()->query->get('field_candidate_town_target_id')) {
        $term_article = Term::load($filter);
        $breadcrumb[] = [
            'text' => t('Candidats'),
            'url' => Url::fromRoute('view.candidates_list.page_candidats_list'),
        ];
        $breadcrumb[] = [
            'text' => $term_article->getName(),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
      elseif (empty($path_args[3]) || $filter == 'All') {
        $breadcrumb[] = [
            'text' => t('Candidats'),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
    }

    if (count($breadcrumb) == 1) {
      $request = \Drupal::request();
      $route_match = \Drupal::routeMatch();
      $page_title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
      $breadcrumb[] = [
          'text' => $page_title,
          'attributes' => new Attribute(['class' => ['active']]),
      ];
    }

    $variables['breadcrumb'] = $breadcrumb;
    $variables['#cache']['contexts'][] = 'url';
  }

}