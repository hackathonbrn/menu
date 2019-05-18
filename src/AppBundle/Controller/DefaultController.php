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

        //характеристики, которые надо отображать в фильтре для исключения
        $params=$this->getRepository('Parameter')->createQueryBuilder('p')
            ->select('p.id', 'p.visiblecaption as name')
            ->addSelect('pv.value as vvalue')
            ->addSelect('pv.id as vvid')
            ->leftJoin('p.values','pv' )
            ->where('p.active = true')
            ->andWhere('p.visible = true')
            ->andWhere('p.negative = 1')
            ->groupBy('p, vvalue')
            ->orderBy('p.priority', 'DESC')
            ->addOrderBy('p.visiblecaption')
            ->addOrderBy('vvalue')
            ->getQuery()->getResult();


        //характеристики, которые надо отображать в фильтре для включения
        $chars=$this->getRepository('Parameter')->createQueryBuilder('p')
            ->select('p.id', 'p.visiblecaption as name')
            ->addSelect('pv.value as vvalue')
            ->addSelect('pv.id as vvid')
            ->leftJoin('p.values','pv' )
            ->where('p.active = true')
            ->andWhere('p.negative = 0')
            ->andWhere('p.visible = true')
            ->groupBy('p, vvalue')
            ->orderBy('p.priority', 'DESC')
            ->addOrderBy('p.visiblecaption')
            ->addOrderBy('vvalue')
            ->getQuery()->getResult();


        //запрашиваем приемы пищи для вывода на форме
        $timeeats=$this->getRepository('Timeeat')->createQueryBuilder('t')
            ->where('t.active = true')
            ->orderBy('t.caption')
            ->getQuery()->getResult();
        

        //запрашиваем блюда по нужным видам пищи
        $filter_timeeats=$this->request->get('timeeat');
        $filter_days=$this->request->get('day');
        $selecteddays=array();
        if (!(empty($filter_days))) {
            foreach ($filter_days as $keyday =>$filter_day ) {
                array_push($selecteddays,$keyday);
            }
        }
        $dishes=array();
        $selecttimeeats=array();
        $filter_attrs=null;
        $filter_chars=null;
        
        if (!(empty($filter_timeeats))) {
            foreach ($filter_timeeats as $keytime =>$filter_timeaet ) {
                $timeeat=$this->getRepository('Timeeat')->findOneBy(array('id'=>$keytime));
                array_push($selecttimeeats,$timeeat);

                $qb  = $this->getRepository('Dish')->createQueryBuilder('dd')
                    ->select('dd')
                    ->leftJoin('dd.timeeats','te')
                    ->where('te.id = '.$keytime);

                //характеристики, по которым нужно фильтровать
                $filter_chars=$this->request->get('chars');
                $charsids=array();
                if (!(empty($filter_chars))) {
                    foreach ($filter_chars as $key =>$filter_char) {
                        foreach ($filter_char as $key2 =>$attr) {
                            array_push($charsids,$key2 );
                        }
                    }
                    $qb->leftJoin('dd.parametervalues', 'dpv')->andWhere($qb->expr()->in('dpv.id',$charsids));
                }


                //характеристики, которые надо исключить
                $filter_attrs=$this->request->get('attrs');
                if (!(empty($filter_attrs))) {
                    $qb2 = $this->getRepository('Dish')->createQueryBuilder('d');
                    $qb2->select('DISTINCT(d.id)')
                        ->leftJoin('d.parametervalues', 'pv');
                    foreach ($filter_attrs as $key =>$filter_attr) {
                        foreach ($filter_attr as $key2 =>$attr) {
                            $qb2->orWhere('pv.id = '.$key2);
                        }
                    }
                    $qb->andWhere($qb->expr()->notIn('dd.id', $qb2->getDQL()));
                }

                $dishesquery  = $qb->getQuery();
                $dishes[$keytime]=$dishesquery->getResult();
            }
        }




        //выбранные приемы пищи для формирования БД
        $this->view['selecttimeeats']=$selecttimeeats;
        $this->view['selecteddays']=$selecteddays;
        $this->view['timeeats']=$timeeats;
        $this->view['dishes']=$dishes;
        //рендерим шаблон
        $this->view['params']=$params;
        $this->view['chars']=$chars;
        return $this->render('AppBundle:General:createmenu.html.twig');
    }





}
