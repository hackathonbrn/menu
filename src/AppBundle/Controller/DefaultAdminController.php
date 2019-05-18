<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultAdminController extends InitializableController
{
    //главная страница админки
    /**
     * @Route("/", name="admin_homepage")
     */
    public function indexAction(Request $request)
    {
        //если не авторизован. то переадресовываем на форму авторизации
        if (!($this->authChecker->isGranted(Role::USER))) {
            return $this->redirectToRoute('site_security_login');
        }

        return $this->redirectToRoute('admin_dishes_index');
    }

    //запросы консльтаций
    /**
     * @Route("/requests/{pagenum}", name="admin_requests_index")
     */
    public function requestsAction($pagenum=1)
    {
        $requestsquery=$this->getRepository('RequestConsultation')->createQueryBuilder('r');
        $count = $this->getRepository('RequestConsultation')->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.id)')->getQuery()->getSingleScalarResult();

        $pages = floor($count / 20) + ($count % 20 > 0 ? 1 : 0);
        if ($pages < 1) $pages = 1;
        if ($pagenum > $pages) $pagenum = $pages;
        $requests = $requestsquery->setFirstResult(($pagenum - 1) * 20)
            ->setMaxResults(20)
            ->getQuery()->getResult();

        $this->view['page']=$pagenum;
        $this->view['pages']=$pages;
        $this->view['count']=$count;

        $this->view['requests']=$requests;
        return $this->render('AppBundle:GeneralAdmin:requests.html.twig');

    }

    /**
     * @Route("/stats", name="stats" )
     */
    public function statsAction()
    {

        $allfiles=$this->getRepository('Product')->createQueryBuilder('p')
        ->select('COUNT(p.id)')
        ->where('p.active = 1')
        ->getQuery()->getSingleScalarResult();

        $views=$this->getRepository('Product')->createQueryBuilder('p')
            ->select('SUM(p.viewed)')
            ->getQuery()->getSingleScalarResult();

        $downloads=$this->getRepository('Product')->createQueryBuilder('p')
            ->select('SUM(p.downloaded)')
            ->getQuery()->getSingleScalarResult();

        $sizefiles=$this->getRepository('Product')->createQueryBuilder('p')
            ->select('SUM(p.filesize)')
            ->where('p.active = 1')
            ->getQuery()->getSingleScalarResult();

        $waitfiles=$this->getRepository('Product')->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.active = 0')
            ->andWhere('p.status = 1')
            ->getQuery()->getSingleScalarResult();

        $univercities=$this->getRepository('Univercity')->createQueryBuilder('u')
            ->select('COUNT(DISTINCT u.id)')
            ->innerJoin('u.products','p')
            ->where('u.active = 1')
            ->andWhere('p.active = 1')
            ->getQuery()->getSingleScalarResult();

        $categories=$this->getRepository('Category')->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.id)')
            ->innerJoin('c.products','p')
            ->where('c.active = 1')
            ->andWhere('p.active = 1')
            ->getQuery()->getSingleScalarResult();

        $disciplines=$this->getRepository('Discipline')->createQueryBuilder('d')
            ->select('COUNT(DISTINCT d.id)')
            ->innerJoin('d.products','p')
            ->where('d.active = 1')
            ->andWhere('p.active = 1')
            ->getQuery()->getSingleScalarResult();

        $users=$this->getRepository('User')->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->getQuery()->getSingleScalarResult();;

        $this->view['allfiles']=$allfiles;
        $this->view['sizefiles']=$sizefiles;
        $this->view['waitfiles']=$waitfiles;
        $this->view['univercities']=$univercities;
        $this->view['categories']=$categories;
        $this->view['disciplines']=$disciplines;
        $this->view['users']=$users;
        $this->view['views']=$views;
        $this->view['downloads']=$downloads;


        return $this->render('AppBundle:GeneralAdmin:statistics.html.twig');
    }

}
