<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Role;

use AppBundle\Form\Type\ArticleFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;

class ArticleController extends InitializableController
{
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/articles", name = "admin_articles_index")
     */
    public function indexAction()
    {

        $articles = $this->getRepository('Article')->createQueryBuilder('a')
            ->orderBy('a.active', 'DESC')
            ->getQuery()->getResult();

        $this->view['articles'] = $articles;

        $this->navigation = array('active' => 'articles');
        return $this->render('AppBundle:Article:index.html.twig');
    }

    /**
     * @return RedirectResponse|Response
     * @Config\Route("/articles/add", name = "admin_articles_add")
     */
    public function addAction()
    {
        $article = new Article();
        $form = $this->createForm(new ArticleFormType(), $article);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->persist($article);
                $this->manager->flush();

                $this->addNotice('success',
                    'articles.html.twig',
                    array('notice' => 'added', 'caption' => $article->getCaption())
                );

                return $this->redirectToRoute('admin_articles_index');
        }


        $this->forms['article'] = $form->createView();
        $this->navigation = array('active' => 'articles');
        return $this->render('AppBundle:Article:add.html.twig');
    }

    /**
     * @param Article $article
     * @return RedirectResponse|Response
     * @Config\Route("/articles/{article}/edit", name = "admin_articles_edit")
     * @Config\ParamConverter("article", options = {"mapping": {"article": "id"}})
     */
    public function editAction(Article $article)
    {
        $form = $this->createForm(new ArticleFormType(), $article);
        
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($article);
            $this->manager->flush();

            $this->addNotice('success',
                'articles.html.twig',
                array('notice' => 'changed', 'caption' => $article->getCaption())
            );

            return $this->redirectToRoute('admin_articles_index');
        }


        $this->forms['article'] = $form->createView();
        $this->view['article'] = $article;
        $this->navigation = array('active' => 'articles');
        return $this->render('AppBundle:Article:edit.html.twig');
    }

   
}
