<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Dish;
use AppBundle\Entity\ImageUpload;

use AppBundle\Entity\Ingridient;
use AppBundle\Entity\ParameterValue;

use AppBundle\Entity\Review;

use AppBundle\Form\Type\Dish1FormType;
use AppBundle\Form\Type\Dish2FormType;
use AppBundle\Form\Type\DishFilterFormType;
use AppBundle\Form\Type\ImageUploadFormType;
use AppBundle\Form\Type\Ingridient1FormType;
use AppBundle\Form\Type\ParameterValueAddFormType;

use AppBundle\Form\Type\ParameterValueAddIngridientFormType;
use AppBundle\Form\Type\ParameterValueIngridientAddFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;


class IngridientController extends InitializableController
{
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/ingridients/index/{pagenum}", name = "admin_ingridients_index", defaults={ "pagenum": "1"})
     */
    public function indexAction($pagenum=1)
    {
        $form=$this->createForm(new DishFilterFormType());
        $caption = null;
        $form->handleRequest($this->request);

        $ingquery = $this->getRepository('Ingridient')->createQueryBuilder('i')
            ->orderBy('i.createdAt', 'DESC')
            ->addOrderBy('i.caption', 'DESC');

        $ingcountquery = $this->getRepository('Ingridient')->createQueryBuilder('i')
            ->select('COUNT(DISTINCT i.id)');


        if ($form->isSubmitted() && $form->isValid()) {
            $caption = $form->get('caption')->getData();
        }

        if (!empty($caption)) {
            $ingquery->andWhere('LOWER(i.caption) LIKE LOWER(:caption) ')->setParameter('caption', '%' . trim($caption) . '%');
            $ingcountquery->andWhere('LOWER(i.caption) LIKE LOWER(:caption) ')->setParameter('caption', '%' . trim($caption) . '%');
        }


        $count=$ingcountquery->getQuery()->getSingleScalarResult();

        $pages = floor($count / 20) + ($count % 20 > 0 ? 1 : 0);
        if ($pages < 1) $pages = 1;
        if ($pagenum > $pages) $pagenum = $pages;
        $ingridients = $ingquery->setFirstResult(($pagenum - 1) * 20)
            ->setMaxResults(20)
            ->getQuery()->getResult();

        $this->view['page']=$pagenum;
        $this->view['pages']=$pages;
        $this->view['ingridients'] = $ingridients;
        $this->forms['filter']=$form->createView();
        $this->navigation = array('active' => 'ingridients');
        return $this->render('AppBundle:Ingridients:index.html.twig');
    }

    /**
     * @return RedirectResponse|Response
     * @Config\Route("/ingridients/add", name = "admin_ingridients_add")
     */
    public function addAction()
    {
        $ingridient = new Ingridient();
        $form = $this->createForm(new Ingridient1FormType(), $ingridient);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($ingridient);
            $this->manager->flush();

            $this->addNotice('success',
                'ingridients.html.twig',
                array('notice' => 'added', 'caption' => $ingridient->getCaption())
            );

            return $this->redirectToRoute('admin_ingridients_edit', array('ingridient'=>$ingridient->getId(), 'step'=>'1'));
        }

        $this->view['ingridient'] = null;
        $this->forms['ingridient'] = $form->createView();
        $this->navigation = array('active' => 'ingridients');
        return $this->render('AppBundle:Ingridients:ingridient1.html.twig');
    }

    /**
     * @param Ingridient $ingridient
     * @return RedirectResponse|Response
     * @Config\Route("/ingridients/{ingridient}/edit/{step}", name = "admin_ingridients_edit", defaults={ "pagenum": "1"})
     * @Config\ParamConverter("ingridient", options = {"mapping": {"ingridient": "id"}})
     */
    public function editAction(Ingridient $ingridient, $step=1)
    {
        if($step>2) {$step=1;}


        switch ($step) {
            //общие данные
            case 1:
                $form = $this->createForm(new Ingridient1FormType(), $ingridient);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $this->manager->persist($ingridient);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'ingridients.html.twig',
                        array('notice' => 'changed', 'caption' => $ingridient->getCaption())
                    );
                    return $this->redirectToRoute('admin_ingridients_edit', array('ingridient'=>$ingridient->getId(), 'step'=>1));
                }
                $this->forms['ingridient']=$form->createView();
                break;
            
            //атрибуты
            case 2:

                if ($this->request->isXmlHttpRequest() && $this->request->isMethod('GET')) {
                    return $this->handleValuesAjaxRequest();
                }
                
                $values=$this->getRepository('ParameterValue')->createQueryBuilder('pv')
                    ->leftJoin('pv.ingridients','i' )
                    ->leftJoin('pv.parameter', 'p')
                    ->where('i.id = :ingridient')
                    ->setParameters(array('ingridient'=>$ingridient->getId()))
                    ->orderBy('p.caption')
                    ->addOrderBy('pv.caption')
                    ->getQuery()->getResult();
                
                $form = $this->createForm(new ParameterValueAddIngridientFormType(), $ingridient);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    /** @var ArrayCollection|ParameterValue[] $formvalues */
                    $formvalues=$form->get('value')->getData();
                    foreach ($formvalues as $formvalue) {
                        //удаляем такое же на всякий случайб если вдруг был
                        $ingridient->getParametervalues()->removeElement($formvalue);
                        $ingridient->getParametervalues()->add($formvalue);
                    }

                    $this->manager->persist($ingridient);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'ingridients.html.twig',
                        array('notice' => 'changed', 'caption' => $ingridient->getCaption())
                    );
                    return $this->redirectToRoute('admin_ingridients_edit', array('ingridient'=>$ingridient->getId(), 'step'=>2));
                }
                $this->forms['ingridient']=$form->createView();
                $this->view['values']=$values;
                break;
            //Отзывы
            case 4:

                break;
        }

        $this->view['ingridient'] = $ingridient;
        $this->navigation = array('active' => 'ingridients');
        return $this->render('AppBundle:Ingridients:ingridient'.$step.'.html.twig');
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
     * @param Ingridient $ingridient
     * @param ParameterValue $value
     * @return RedirectResponse|Response
     * @Config\Route("/ingridients/{ingridient}/values/{value}/delete", name = "admin_ingridientvalue_delete")
     * @Config\ParamConverter("ingridient", options = {"mapping": {"ingridient": "id"}})
     * @Config\ParamConverter("value", options = {"mapping": {"value": "id"}})
     */
    public function removeattrAction(Ingridient $ingridient, ParameterValue $value)
    {
        $ingridient->getParametervalues()->removeElement($value);
        $this->manager->persist($ingridient);
        $this->manager->flush();

        return $this->redirectToRoute(
            'admin_ingridients_edit',
            array('ingridient' => $ingridient->getId(), 'step'=>3));
    }

    
    /**
     * @param Ingridient $ingridient
     * @return RedirectResponse|Response
     * @Config\Route("/products/{ingridient}/unpublish", name = "admin_ingridients_unpublsh")
     * @Config\ParamConverter("ingridient", options = {"mapping": {"ingridient": "id"}})
     */
    public function unpublishAction(Ingridient $ingridient)
    {

        $ingridient->setActive(false);
        $this->manager->persist($ingridient);
        $this->manager->flush();

        $this->addNotice('info',
            'ingridients.html.twig',
            array('notice' => 'unpublished', 'caption' => $ingridient->getCaption())
        );

        return $this->redirectToRoute('admin_ingridients_index');

    }

    /**
     * @param Ingridient $ingridient
     * @return RedirectResponse|Response
     * @Config\Route("/dishes/{ingridient}/publish", name = "admin_ingridients_publsh")
     * @Config\ParamConverter("ingridient", options = {"mapping": {"ingridient": "id"}})
     */
    public function publishAction(Ingridient $ingridient)
    {

        $ingridient->setActive(true);
        
        $this->manager->persist($ingridient);
        $this->manager->flush();

        $this->addNotice('success',
            'ingridients.html.twig',
            array('notice' => 'published', 'caption' => $ingridient->getCaption())
        );

        return $this->redirectToRoute('admin_ingridients_index');

    }

   


}
