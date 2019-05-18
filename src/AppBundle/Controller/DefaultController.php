<?php

namespace AppBundle\Controller;


use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;



class DefaultController extends InitializableController
{
    //главная страница
    /**
     * @Config\Route("/", name="homepage")
     */
    public function indexAction()
    {
        
        //рендерим шаблон
        return $this->render('AppBundle:General:index.html.twig');
    }


    
    /**
     * @Config\Route("/createmenu", name="createmenu")
     */
    public function createmenuAction()
    {

        //характеристики, которые надо отображать в фильтре
        $params=$this->getRepository('Parameter')->createQueryBuilder('p')
            ->select('p.id', 'p.caption as name')
            ->addSelect('pv.value as vvalue')
            ->leftJoin('p.values','pv' )
            ->where('p.active = true')
            ->groupBy('p, vvalue')
            ->orderBy('p.caption')
            ->addOrderBy('vvalue')
            ->getQuery()->getResult();

        //рендерим шаблон
        $this->view['params']=$params;
        return $this->render('AppBundle:General:createmenu.html.twig');
    }





}
