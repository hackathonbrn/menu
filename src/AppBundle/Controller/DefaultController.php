<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Page;
use AppBundle\Entity\Product;
use AppBundle\Entity\RequestConsultation;
use AppBundle\Entity\Role;
use AppBundle\Form\Type\ProductFileFormType;
use AppBundle\Form\Type\ProductFormType;
use AppBundle\Form\Type\ProductIndexFilterFormType;
use AppBundle\Form\Type\RequestConsultationFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

require('autoload.php');

class DefaultController extends InitializableController
{
    //главная страница
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        
        //рендерим шаблон
        return $this->render('AppBundle:General:index.html.twig');
    }

    /**
     * @param Product $product
     * @return RedirectResponse|Response
     * @Config\Route("/download/{product}", name = "products_getfile")
     * @Config\ParamConverter("product", options = {"mapping": {"product": "id"}})*
     */
    public function getfileAction(Product $product)
    {
        $product->setDownloaded($product->getDownloaded()+1);
        $this->manager->persist($product);
        $this->manager->flush();

        $file=new File($product->getUploadRootDir().'/'.$product->getFilename());
        $response = new Response(file_get_contents($file->getPathname()), 200, array(
            'Content-Type' => $file->getMimeType(),
            'Content-Disposition' => 'attachment; filename="' . $product->getViewcaption().'"',
        ));
        return $response;
    }

    //Список ВУЗов
    /**
     * @Route("/univercities", name="univercities")
     */
    public function univercitiesAction()
    {
        $univercities=$this->getRepository('Univercity')->createQueryBuilder('u')
            ->innerJoin('u.products','p')
            ->where('u.active = true')
            ->andWhere('p.active = true')
            ->orderBy('u.caption')
            ->getQuery()->getResult();
        
        $this->view['univercities']=$univercities;

        //рендерим шаблон
        return $this->render('AppBundle:General:univercities.html.twig');
    }

    //Список категорий
    /**
     * @Route("/categories", name="categories")
     */
    public function categoriesAction()
    {
        $categories=$this->getRepository('Category')->createQueryBuilder('c')
            ->innerJoin('c.products','p')
            ->where('c.active = true')
            ->andWhere('p.active = true')
            ->orderBy('c.caption')
            ->getQuery()->getResult();

        $this->view['categories']=$categories;

        //рендерим шаблон
        return $this->render('AppBundle:General:categories.html.twig');
    }

    //Список Дисциплин
    /**
     * @Route("/disciplines", name="disciplines")
     */
    public function disciplinesAction()
    {
        $disciplines=$this->getRepository('Discipline')->createQueryBuilder('d')
            ->innerJoin('d.products','p')
            ->where('d.active = true')
            ->andWhere('p.active = true')
            ->orderBy('d.caption')
            ->getQuery()->getResult();

        $this->view['disciplines']=$disciplines;

        //рендерим шаблон
        return $this->render('AppBundle:General:disciplines.html.twig');
    }


    //страница файла
    
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/preview/{product_id}/{pagenum}", name = "previewpagefile")
     */
    public function onepagefileAction($product_id, $pagenum=1)
    {
        /** @var Product $product */
        $product = $this->getRepository('Product')
            ->findOneBy(array('id' => $product_id, 'active' => true));

       if (is_null($product)) throw $this->createNotFoundException();

        /** @var Page $page */
       $page=$this->getRepository('Page')
           ->findOneBy(array('product' => $product_id, 'page_number' => $pagenum));;

        if (is_null($page)) throw $this->createNotFoundException();

        $product->setViewed($product->getViewed()+1);
        $this->manager->persist($product);
        $this->manager->flush();
        if ($this->authChecker->isGranted(Role::ADMIN)) {
            
            $form = $this->createForm(new ProductFormType(), $product);

            if ($this->request->isMethod('POST')) {
                $form->handleRequest($this->request);
                if ($form->isSubmitted()) {

                    $this->manager->persist($product);
                    $this->manager->flush();

                    return $this->redirectToRoute(
                        'previewpagefile',
                        array('product_id' => $product->getId(), 'pagenum'=>$pagenum)
                    );
                }
            }
            $this->forms['product']=$form->createView();
        }
       
        $this->view['product']=$product;
        $this->view['page']=$page;
        //рендерим шаблон
        return $this->render('AppBundle:General:productpage.html.twig');
    }
    
    //запрос через форму обратной связи

    /**
     * @Route("/send-question", name="requestconsultation" )
     */
    public function requestconsultationAction()
    {

        $requestconsultation = new RequestConsultation();
        $form = $this->createForm(new RequestConsultationFormType(), $requestconsultation);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recaptcha = new \ReCaptcha\ReCaptcha("6Lcu1nUUAAAAAEWNuRU8ezHmf36ux-ugpO_N-6Cc");
            $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if ($resp->isSuccess()) {
                $this->manager->persist($requestconsultation);
                $this->manager->flush();

                $body = "<html><body > 
<div style='background-color: #f5f5f5; width:100%;margin:0;padding-bottom:10px;min-height:350px;padding-top:20px;'><br />
<table style='background-color: #fff; color:#333;border-radius:10px;max-width:600px;min-width:400px;margin:auto;padding-left:20px;padding-right:20px;font-size:18px;padding-top:20px;box-shadow: 0 0 10px rgba(0,0,0,0.5);'>
	<tr><td style='text-align:center;font-size:28px;'><b>Файловый архив студентов</b><br /><br /> </td>	</tr>
	<tr><td style='text-align:left;'>Здравствуйте! <br /><br /></td></tr>
	<tr><td style='text-align:left;'><br />Новое сообщение через форму обратной связи.<br /></td></tr>
	<tr><td style='text-align:left;'><br />
		При заполнении формы были указаны следующие данные: <br />
		Имя пользователя: %author%<br />
		email: %email%<br />
		Вопрос: %question%<br /></td>
	</tr>
	<tr><td style='text-align:left;color:#222;font-size:18px;'><br /><hr></td></tr>
</table>
</div></body></html>";

                $body = preg_replace('/%author%/',$requestconsultation->getAuthor(), $body); //заменяем выражения
                $body = preg_replace('/%email%/',$requestconsultation->getEmail(), $body); //заменяем выражения
                $body = preg_replace('/%question%/',$requestconsultation->getQuestion(), $body); //заменяем выражения
                $emails = 'mshashin@student-it.ru';
                /** @var \Swift_Mailer $mailer */
                $mailer = $this->get('mailer');
                $message = $mailer->createMessage()
                    ->setSubject('Новый вопрос')
                    ->setFrom(array('noreply@files.ru' => 'Файловый архив'))
                    ->setTo($emails)
                    ->setBody($body, 'text/html');
                $mailer->send($message);
                return $this->redirectToRoute('thx');
            }
            else {
                $form->addError(new FormError('Поставьте галочку, что Вы не робот!'));
            }

        }

        $this->forms['requestconsultation'] = $form->createView();
        return $this->render('AppBundle:General:requestconsultation.html.twig');
    }

    /**
     * @Route("/thx", name="thx" )
     */
    public function txhAction()
    {
        $this->navigation = array('active' => 'homepage');
        return $this->render('AppBundle:General:thx.html.twig');
    }


    //индексовая карта сайта
    /**
     * @Route("/sitemap.{_format}", name="index_sitemap", Requirements={"_format" = "xml"})
     * @Template("AppBundle:Sitemaps:sitemap_index.xml.twig")
     */
    public function indexsitemapAction()
    {
        $urls = array();
        //сколько файлов в 1 xml
        $pagination=250;
        //считаем сколько раз у нас будет по $pagination файлов
        $count=$this->getRepository('Product')->createQueryBuilder('p')
            ->select('COUNT (p.id)')
            ->where('p.active = 1')
            ->getQuery()->getSingleScalarResult();


        $count=(floor($count/$pagination) + ($count % $pagination > 0 ? 1 : 0));

        //для каждой кучки файлов генерим свою xml
        for ($i=0; $i<$count; $i++) {
            $urls[] = array('loc' => $this->get('router')->generate('dinamic_sitemap',
                array('i' => $i,'_format'=>'xml'), UrlGeneratorInterface::ABSOLUTE_URL));
        }

        return array('urls' => $urls);
    }

    //карта постоянных ссылок для поисковика
    /**
     * @Route("/sitemap_main.{_format}", name="main_sitemap", Requirements={"_format" = "xml"})
     * @Template("AppBundle:Sitemaps:sitemap.xml.twig")
     */
    public function mainsitemapAction()
    {
        $urls = array();
        $hostname = $this->getRequest()->getHost();
        // домашняя страница
        $urls[] = array('loc' => $this->get('router')->generate('homepage',array(),UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'hourly', 'priority' => '1.0');

        //сколько файлов на 1 странице
        $pagination=20;
        //считаем сколько раз у нас будет по 20 файлов
        $count=$this->getRepository('Product')->createQueryBuilder('p')
            ->select('COUNT (p.id)')
            ->where('p.active = 1')
            ->getQuery()->getSingleScalarResult();
        $count=(floor($count/$pagination) + ($count % $pagination > 0 ? 1 : 0));

        //для каждой кучки файлов генерим свою ссылку (начиная со второй)
        for ($i=2; $i<$count; $i++) {
            $urls[] = array('loc' => $this->get('router')->generate('homepage',
                array('pagenum' => $i), UrlGeneratorInterface::ABSOLUTE_URL),'changefreq' => 'hourly', 'priority' => '1.0');
        }


        //список вузов
        $urls[] = array('loc' => $this->get('router')->generate('univercities',array(),UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '1.0');
        //категории
        $urls[] = array('loc' => $this->get('router')->generate('categories',array(),UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '1.0');
        //дисциплины
        $urls[] = array('loc' => $this->get('router')->generate('disciplines',array(),UrlGeneratorInterface::ABSOLUTE_URL), 'changefreq' => 'weekly', 'priority' => '1.0');

        return array('urls' => $urls, 'hostname' => $hostname);
    }
    


    //карта сайта для поисковика
    /**
     * @Route("/sitemap{i}.{_format}", name="dinamic_sitemap", Requirements={"_format" = "xml"})

     */
    public function sitemapAction($i)
    {
        $urls = array();
        $hostname = $this->getRequest()->getHost();
        $pagination=250;
        //для каждой страницы каждого файла

        $products = $this->getRepository('Product')->createQueryBuilder('p')
            ->select('p.id')
            ->orderBy('p.createdAt', 'ASC')->where('p.active = true')
            ->setFirstResult($i*$pagination)->setMaxResults($pagination)
            ->getQuery()->getArrayResult();
        if ($products) {
            foreach ($products as $product) {

                $pages = $this->getRepository('Page')->createQueryBuilder('pg')
                    ->select('pg.page_number, pg.modifiedAt')
                    ->where('pg.product = :product')
                    ->setParameters(array('product'=>$product['id']))
                    ->orderBy('pg.page_number', 'ASC')
                    ->getQuery()->getArrayResult();

                foreach ($pages as $page) {
                    $urls[] = array('loc' => $this->get('router')->generate('previewpagefile',
                        array('product_id' => $product['id'], 'pagenum'=>$page['page_number']), UrlGeneratorInterface::ABSOLUTE_URL),
                        'priority' => '0.5',
                        'lastmod'=>date_format($page['modifiedAt'],'Y-m-d' ),
                        'changefreq'=>'monthly');
                }

            }
            return $this->render('@App/Sitemaps/sitemap.xml.twig',array('urls' => $urls, 'hostname' => $hostname));
            //return array('urls' => $urls, 'hostname' => $hostname);

        }
        else {
            throw $this->createNotFoundException();
        }

    }


    //статья
    /**
     * @Route("/{article}", name="onearticle", requirements = {"article": "(\w|-)+"})
     */
    public function onearticleAction($article)
    {

        /** @var Article $article */
        $article = $this->getRepository('Article')
            ->findOneBy(array('alias' => $article, 'active' => true));

        if (is_null($article)) throw $this->createNotFoundException();

        if ($article->isHelp()) {
            $articles=$this->getRepository('Article')->createQueryBuilder('a')
                ->where('a.help = true')
                ->andWhere('a.active = true')
                ->getQuery()->getResult();
            $this->view['articles']=$articles;
        }

        $this->view['article']=$article;
        //рендерим шаблон
        return $this->render('AppBundle:General:article.html.twig');
    }
   
}
