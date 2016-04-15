<?php

namespace Webburza\Sylius\ArticleBundle\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Webburza\Sylius\ArticleBundle\Doctrine\ORM\ArticleRepository;

class ArticleController extends ResourceController
{
    /**
     * Show publicly visible articles for the current locale.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexFrontAction(Request $request)
    {
        // Get current locale
        $locale = $this->get('sylius.context.locale')->getCurrentLocale();

        // Get request configuration
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var ArticleRepository $repository */
        $repository = $this->repository;

        // Get a paginator for publicly visible articles for locale
        $articlesPaginator = $repository->getPublicPaginatorForLocale($locale);
        $articlesPaginator->setCurrentPage($this->get('request')->get('page', 1), true, true);
        $articlesPaginator->setMaxPerPage($configuration->getPaginationMaxPerPage());

        // Create the view
        $view = View::create();

        // Set template and data
        $view->setTemplate('WebburzaSyliusArticleBundle:Frontend:index.html.twig');
        $view->setData(array(
            'articles' => $articlesPaginator
        ));

        // Handle view
        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * Get a publicly visible article by slug, for the current locale.
     *
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFrontAction(Request $request, $slug)
    {
        // Get current locale
        $locale = $this->get('sylius.context.locale')->getCurrentLocale();

        // Get request configuration
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /** @var ArticleRepository $repository */
        $repository = $this->repository;

        // Get a publicly visible article by translated slug
        $article = $repository->findPublicBySlug($slug, $locale);

        if (!$article) {
            throw $this->createNotFoundException();
        }

        // Create the view
        $view = View::create();

        // Set template and data
        $view->setTemplate('WebburzaSyliusArticleBundle:Frontend:show.html.twig');
        $view->setData(array(
            'article' => $article
        ));

        // Handle view
        return $this->viewHandler->handle($configuration, $view);
    }
}
