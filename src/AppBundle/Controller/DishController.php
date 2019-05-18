<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Dish;
use AppBundle\Entity\ImageUpload;

use AppBundle\Entity\ParameterValue;

use AppBundle\Entity\Review;

use AppBundle\Form\Type\Dish1FormType;
use AppBundle\Form\Type\Dish2FormType;
use AppBundle\Form\Type\Dish3FormType;
use AppBundle\Form\Type\DishFilterFormType;
use AppBundle\Form\Type\ImageUploadFormType;
use AppBundle\Form\Type\ParameterValueAddFormType;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class DishController extends InitializableController
{
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/index/{pagenum}", name = "admin_dishes_index", defaults={ "pagenum": "1"})
     */
    public function indexAction($pagenum=1)
    {
        $form=$this->createForm(new DishFilterFormType());
        $caption = null;
        $form->handleRequest($this->request);

        $dishquery = $this->getRepository('Dish')->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->addOrderBy('d.caption', 'DESC');

        $dishescountquery = $this->getRepository('Dish')->createQueryBuilder('d')
            ->select('COUNT(DISTINCT d.id)');


        if ($form->isSubmitted() && $form->isValid()) {
            $caption = $form->get('caption')->getData();
        }

        if (!empty($caption)) {
            $dishquery->andWhere('LOWER(d.caption) LIKE LOWER(:caption) ')->setParameter('caption', '%' . trim($caption) . '%');
            $dishescountquery->andWhere('LOWER(d.caption) LIKE LOWER(:caption) ')->setParameter('caption', '%' . trim($caption) . '%');
        }
        


               

        $count=$dishescountquery->getQuery()->getSingleScalarResult();

        $pages = floor($count / 20) + ($count % 20 > 0 ? 1 : 0);
        if ($pages < 1) $pages = 1;
        if ($pagenum > $pages) $pagenum = $pages;
        $dishes = $dishquery->setFirstResult(($pagenum - 1) * 20)
            ->setMaxResults(20)
            ->getQuery()->getResult();

        $this->view['page']=$pagenum;
        $this->view['pages']=$pages;
        $this->view['dishes'] = $dishes;
        $this->forms['filter']=$form->createView();
        $this->navigation = array('active' => 'dishes');
        return $this->render('AppBundle:Dishes:index.html.twig');
    }

    /**
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/add", name = "admin_dishes_add")
     */
    public function addAction()
    {
        $dish = new Dish();
        $form = $this->createForm(new Dish1FormType(), $dish);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($dish);
            $this->manager->flush();

            $this->addNotice('success',
                'dishes.html.twig',
                array('notice' => 'added', 'caption' => $dish->getCaption())
            );

            return $this->redirectToRoute('admin_dishes_edit', array('dish'=>$dish->getId(), 'step'=>'1'));
        }

        $this->view['dish'] = null;
        $this->forms['dish'] = $form->createView();
        $this->navigation = array('active' => 'dishes');
        return $this->render('AppBundle:Dishes:dish1.html.twig');
    }

    /**
     * @param Dish $dish
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/{dish}/edit/{step}", name = "admin_dishes_edit", defaults={ "pagenum": "1"})
     * @Config\ParamConverter("dish", options = {"mapping": {"dish": "id"}})
     */
    public function editAction(Dish $dish, $step=1)
    {
        if($step>5) {$step=1;}


        switch ($step) {
            //общие данные
            case 1:
                $form = $this->createForm(new Dish1FormType(), $dish);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->manager->persist($dish);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'dishes.html.twig',
                        array('notice' => 'changed', 'caption' => $dish->getCaption())
                    );
                    return $this->redirectToRoute('admin_dishes_edit', array('dish'=>$dish->getId(), 'step'=>1));
                }
                $this->forms['dish']=$form->createView();
                break;
            //фотки
            case 2:
                $originalphotos = array();
                foreach ($dish->getPhotos() as $photo) {
                    $originalphotos[] = $photo;
                }

                $form = $this->createForm(new Dish2FormType(), $dish);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    foreach ($dish->getPhotos() as $photo) {
                        $photo->setDish($dish);
                        $photo->upload();
                    }

                    foreach ($dish->getPhotos() as $photo) {
                        foreach ($originalphotos as $key => $toDel) {
                            if ($toDel->getId() === $photo->getId()) {
                                unset($originalphotos[$key]);
                            }
                        }
                    }

                    foreach ($originalphotos as $photo) {
                        $dish->getPhotos()->removeElement($photo);
                        $photo->setProduct(null);
                        $this->manager->remove($photo);
                    }


                    $this->manager->persist($dish);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'dishes.html.twig',
                        array('notice' => 'changed', 'caption' => $dish->getCaption())
                    );
                    return $this->redirectToRoute('admin_dishes_edit', array('dish'=>$dish->getId(), 'step'=>2));
                }
                $this->forms['dish']=$form->createView();
                break;


            //хар-ки
            case 3:

                if ($this->request->isXmlHttpRequest() && $this->request->isMethod('GET')) {
                    return $this->handleValuesAjaxRequest();
                }
                
                $values=$this->getRepository('ParameterValue')->createQueryBuilder('pv')
                    ->leftJoin('pv.dishes','d' )
                    ->leftJoin('pv.parameter', 'p')
                    ->where('d.id = :dish')
                    ->setParameters(array('dish'=>$dish->getId()))
                    ->orderBy('p.caption')
                    ->addOrderBy('pv.value')
                    ->getQuery()->getResult();
                
                $form = $this->createForm(new ParameterValueAddFormType(), $dish);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    /** @var ArrayCollection|ParameterValue[] $formvalues */
                    $formvalues=$form->get('value')->getData();
                    foreach ($formvalues as $formvalue) {
                        //удаляем такое же на всякий случайб если вдруг был
                        $dish->getParametervalues()->removeElement($formvalue);
                        $dish->getParametervalues()->add($formvalue);
                    }

                    $this->manager->persist($dish);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'dishes.html.twig',
                        array('notice' => 'changed', 'caption' => $dish->getCaption())
                    );
                    return $this->redirectToRoute('admin_dishes_edit', array('dish'=>$dish->getId(), 'step'=>3));
                }
                $this->forms['dish']=$form->createView();
                $this->view['values']=$values;
                break;
            //Отзывы
            case 4:

                break;

            //ингридиенты 
            case 5:
                $originalreciepts = array();
                foreach ($dish->getReciepts() as $reciept) {
                    $originalreciepts[] = $reciept;
                }

                $form = $this->createForm(new Dish3FormType(), $dish);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    foreach ($dish->getReciepts() as $reciept) {
                        $reciept->setDish($dish);
                        if ($reciept->isTaste()) { //если по вкусу - зануляем
                            $reciept->setQuantity(0);
                        }
                    }

                    foreach ($dish->getReciepts() as $reciept) {
                        foreach ($originalreciepts as $key => $toDel) {
                            if ($toDel->getId() === $reciept->getId()) {
                                unset($originalreciepts[$key]);
                            }
                        }
                    }

                    foreach ($originalreciepts as $reciept) {
                        $dish->getReciepts()->removeElement($reciept);
                        $reciept->setDish(null);
                        $this->manager->remove($reciept);
                    }


                    $this->manager->persist($dish);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'dishes.html.twig',
                        array('notice' => 'changed', 'caption' => $dish->getCaption())
                    );
                    return $this->redirectToRoute('admin_dishes_edit', array('dish'=>$dish->getId(), 'step'=>5));
                }
                $this->forms['dish']=$form->createView();
                break;
            
        }

        $this->view['dish'] = $dish;
        $this->navigation = array('active' => 'dishes');
        return $this->render('AppBundle:Dishes:dish'.$step.'.html.twig');
    }

    protected function handleValuesAjaxRequest()
    {
        $parameter = $this->request->get('parameter', null);
        if (is_null($parameter)) return new JsonResponse();
        $values=$this->getRepository('ParameterValue')->createQueryBuilder('v')
            ->select('v.id as id')
            ->where('v.parameter = :parameter')
            ->setParameters(array('parameter'=> $parameter))
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return new JsonResponse($values);
    }

    /**
     * @param Dish $dish
     * @param ParameterValue $value
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/{dish}/values/{value}/delete", name = "admin_dishvalue_delete")
     * @Config\ParamConverter("dish", options = {"mapping": {"dish": "id"}})
     * @Config\ParamConverter("value", options = {"mapping": {"value": "id"}})
     */
    public function removeattrAction(Dish $dish, ParameterValue $value)
    {
        $dish->getParametervalues()->removeElement($value);
        $this->manager->persist($dish);
        $this->manager->flush();

        return $this->redirectToRoute(
            'admin_dishes_edit',
            array('dish' => $dish->getId(), 'step'=>3));
    }

    /**
     * @param Dish $dish
     * @param ImageUpload $photo
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/{product}/photos/{photo}/delete", name = "admin_dishes_deletephoto")
     * @Config\ParamConverter("dish", options = {"mapping": {"dish": "id"}})
     * @Config\ParamConverter("photo", options = {"mapping": {"photo": "id"}})
     */
    public function photodeleteAction(Dish $dish, ImageUpload $photo)
    {
        $dish->getPhotos()->removeElement($photo);
        $this->manager->persist($dish);
        $this->manager->remove($photo);
        $this->manager->flush();

        return $this->redirectToRoute(
            'admin_dishes_edit',
            array('dish' => $dish->getId()));
    }

    /**
     * @param Dish $dish
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/{dish}/unpublish", name = "admin_dishes_unpublsh")
     * @Config\ParamConverter("dish", options = {"mapping": {"dish": "id"}})
     */
    public function unpublishAction(Dish $dish)
    {

        $dish->setActive(false);
        $this->manager->persist($dish);
        $this->manager->flush();

        $this->addNotice('info',
            'dishes.html.twig',
            array('notice' => 'unpublished', 'caption' => $dish->getCaption())
        );

        return $this->redirectToRoute('admin_dishes_index');

    }

    /**
     * @param Dish $dish
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/{product}/publish", name = "admin_dishes_publsh")
     * @Config\ParamConverter("dish", options = {"mapping": {"dish": "id"}})
     */
    public function publishAction(Dish $dish)
    {

        $dish->setActive(true);
        
        $this->manager->persist($dish);
        $this->manager->flush();

        $this->addNotice('success',
            'dishes.html.twig',
            array('notice' => 'published', 'caption' => $dish->getCaption())
        );

        return $this->redirectToRoute('admin_dishes_index');

    }

    /**
     * @return RedirectResponse|Response
     * @Config\Route("/productcomments/index/{pagenum}", name = "admin_dishcomments_index", defaults={ "pagenum": "1"})
     */
    public function productcommentsAction($pagenum=1)
    {
        $productcommentsquery = $this->getRepository('Review')->createQueryBuilder('r')
            ->orderBy('r.apply')
            ->addOrderBy('r.active', 'DESC')
            ->addOrderBy('r.createdAt', 'DESC');


        $countquery = $this->getRepository('Review')->createQueryBuilder('r')
            ->select('COUNT(DISTINCT r.id)');

        $count=$countquery->getQuery()->getSingleScalarResult();

        $pages = floor($count / 20) + ($count % 20 > 0 ? 1 : 0);
        if ($pages < 1) $pages = 1;
        if ($pagenum > $pages) $pagenum = $pages;
        $productcomments = $productcommentsquery->setFirstResult(($pagenum - 1) * 20)
            ->setMaxResults(20)
            ->getQuery()->getResult();

        $this->view['page']=$pagenum;
        $this->view['pages']=$pages;
        $this->view['reviews'] = $productcomments;
        $this->navigation = array('active' => 'productcomments');
        return $this->render('AppBundle:Dishes:dish4.html.twig');
    }


    /**
     * @param Review $review
     * @return RedirectResponse|Response
     * @Config\Route("productreviews/{review}/delete", name = "admin_reviews_delete")
     * @Config\ParamConverter("review", options = {"mapping": {"review": "id"}})
     */
    public function reviewdelAction(Review $review)
    {
        $dish=$review->getDish();
        $dish->getReviews()->removeElement($review);
        
        $this->manager->persist($dish);
        $this->manager->remove($review);
        $this->manager->flush();

        //$dish->refreshBall();
        $this->manager->persist($dish);
        $this->manager->flush();
        
        return $this->redirectToRoute('admin_dishcomments_index');
    }





}
