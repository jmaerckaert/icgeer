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
      $path = \Drupal::service('path.alias_manager')->getPathByAlias($current_path);
      if(preg_match('/term\/(\d+)/', $path, $matches)) {
        $town_page = Term::load($matches[1]);
      }
      //$town_page = taxonomy_term_load_multiple_by_name($path_args[3], 'towns');
      //$town_page = reset($town_page);
    }

    if (!empty($path_args[2]) && $path_args[2] == 'categorie' && !empty($path_args[3])) {
      $path = \Drupal::service('path.alias_manager')->getPathByAlias($current_path);
      if(preg_match('/term\/(\d+)/', $path, $matches)) {

        $tag_page = Term::load($matches[1]);
      }
      //$tag_page = taxonomy_term_load_multiple_by_name($path_args[3], 'tags');
      //$tag_page = reset($tag_page);
    }

    if (!empty($path_args[2]) && $path_args[2] == 'theme' && !empty($path_args[3])) {
      $path = \Drupal::service('path.alias_manager')->getPathByAlias($current_path);
      if(preg_match('/term\/(\d+)/', $path, $matches)) {

        $theme_program = Term::load($matches[1]);
      }
      //$tag_page = taxonomy_term_load_multiple_by_name($path_args[3], 'tags');
      //$tag_page = reset($tag_page);
    }

    $types = ['article', 'candidate', 'page', 'program', 'project'];

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
          'text' => $term_page->getName(),
          'url' => $term_page->url(),
      ];
    }

    if (isset($node) && $node->getType() == 'program') {
      $breadcrumb[] = [
          'text' => t('Notre programme'),
          'url' => Url::fromRoute('view.programme.page_program'),
      ];

      $term_page = Term::load($node->get('field_program_theme')->target_id);

      $breadcrumb[] = [
          'text' => $term_page->getName(),
          'url' => $term_page->url(),
      ];
    }

    if (isset($node) && $node->getType() == 'project') {
      $breadcrumb[] = [
          'text' => t('Nos projets'),
          'url' => Url::fromRoute('view.projects.page_projects'),
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
          'text' => t('Articles'),
          'url' => Url::fromRoute('view.candidates_list.page_articles'),
      ];
      $breadcrumb[] = [
          'text' => $tag_page->getName(),
          'attributes' => new Attribute(['class' => ['active']]),
      ];
    }

    if (isset($theme_program)) {
      $breadcrumb[] = [
          'text' => t('Notre programme'),
          'url' => Url::fromRoute('view.programme.page_program'),
      ];
      $breadcrumb[] = [
          'text' => $theme_program->getName(),
          'attributes' => new Attribute(['class' => ['active']]),
      ];
    }

    if ($path_args[1] == 'articles' && empty($path_args[2])) {
      $filter = \Drupal::request()->query->get('field_tags_target_id');
      if ($filter && $filter != 'All') {
        $term_article = Term::load($filter);
        $breadcrumb[] = [
            'text' => t('Articles'),
            'url' => Url::fromRoute('view.icgeer_news_list.page_articles'),
        ];
        $breadcrumb[] = [
            'text' => $term_article->getName(),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
      elseif (empty($path_args[3])) {
        $breadcrumb[] = [
            'text' => t('Articles'),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
    }

    if ($path_args[1] == 'candidats' && empty($path_args[2])) {
      $filter = \Drupal::request()->query->get('field_candidate_town_target_id');
      if ($filter && $filter != 'All') {
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
      elseif (empty($path_args[3])) {
        $breadcrumb[] = [
            'text' => t('Candidats'),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
    }

    if ($path_args[1] == 'programme' && empty($path_args[2])) {
      $filter = \Drupal::request()->query->get('field_program_theme_target_id');
      if ($filter && $filter != 'All') {
        $theme_program = Term::load($filter);
        $breadcrumb[] = [
            'text' => t('Notre programme'),
            'url' => Url::fromRoute('view.programme.page_program'),
        ];
        $breadcrumb[] = [
            'text' => $theme_program->getName(),
            'attributes' => new Attribute(['class' => ['active']]),
        ];
      }
      elseif (empty($path_args[3])) {
        $breadcrumb[] = [
            'text' => t('Notre programme'),
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