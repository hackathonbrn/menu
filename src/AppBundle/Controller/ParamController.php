<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Parameter;

use AppBundle\Entity\ParameterValue;
use AppBundle\Form\Type\ParameterFormType;
use AppBundle\Form\Type\ParameterValueFormType;
use AppBundle\Form\Type\ParameterValuesFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class ParamController extends InitializableController
{


    /**
     * @return RedirectResponse|Response
     * @Config\Route("/parameters/index", name = "admin_parameters_index")
     */
    public function indexAction()
    {
        $parameters = $this->getRepository('Parameter')->createQueryBuilder('p')
            ->orderBy('p.caption', 'DESC')
            ->getQuery()->getResult();
        $this->view['parameters'] = $parameters;
        $this->navigation = array('active' => 'parameters');
        return $this->render('AppBundle:Parameter:index.html.twig');
    }

    /**
     * @return RedirectResponse|Response
     * @Config\Route("/parameters/add", name = "admin_parameters_add")
     */
    public function addAction()
    {
        $parameter = new Parameter();
        $form = $this->createForm(new ParameterFormType(), $parameter);
        $form->handleRequest($this->request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($parameter);
            $this->manager->flush();

            $this->addNotice('success',
                'parameters.html.twig',
                array('notice' => 'added', 'caption' => $parameter->getCaption())
            );
            return $this->redirectToRoute('admin_parameters_edit', array('parameter' => $parameter->getId(), 'step' => 1));
        }
        $this->view['parameter']=null;
        $this->forms['parameter'] = $form->createView();
        $this->navigation = array('active' => 'parameters');
        return $this->render('AppBundle:Parameter:parameter1.html.twig');
    }

    /**
     * @param Parameter $parameter
     * @return RedirectResponse|Response
     * @Config\Route("/parameters/{parameter}/edit/{step}", name = "admin_parameters_edit")
     * @Config\ParamConverter("parameter", options = {"mapping": {"parameter": "id"}})
     */
    public function editAction(Parameter $parameter, $step=1)
    {
        if ($step>2) {$step=1;}
        switch ($step) {
            //общие данные
            case 1:
                $form = $this->createForm(new ParameterFormType(), $parameter);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {

                    $this->manager->persist($parameter);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'parameters.html.twig',
                        array('notice' => 'changed', 'caption' => $parameter->getCaption())
                    );
                    return $this->redirectToRoute('admin_parameters_edit', array('parameter' => $parameter->getId(), 'step' => 1));
                }
                $this->forms['parameter'] = $form->createView();
                break;
            //параметры
            case 2:
                $originalvalues = array();
                foreach ($parameter->getValues() as $value) {
                    $originalvalues[] = $value;
                }

                $form = $this->createForm(new ParameterValuesFormType(), $parameter);
                $form->handleRequest($this->request);
                if ($form->isSubmitted() && $form->isValid()) {
                    foreach ($parameter->getValues() as $value) {
                        $value->setParameter($parameter);
                    }

                    foreach ($parameter->getValues() as $value) {
                        foreach ($originalvalues as $key => $toDel) {
                            if ($toDel->getId() === $value->getId()) {
                                unset($originalvalues[$key]);
                            }
                        }
                    }

                    foreach ($originalvalues as $value) {
                        $parameter->getValues()->removeElement($value);
                        $value->setParameter(null);
                        $this->manager->remove($value);
                    }


                    $this->manager->persist($parameter);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'parameters.html.twig',
                        array('notice' => 'changed', 'caption' => $parameter->getCaption())
                    );
                    return $this->redirectToRoute('admin_parameters_edit', array('parameter'=>$parameter->getId(), 'step'=>2));
                }
                $this->forms['parameter']=$form->createView();
                break;

        }

        $this->view['parameter'] = $parameter;
        $this->navigation = array('active' => 'parameters');
        return $this->render('AppBundle:Parameter:parameter'.$step.'.html.twig');
    }

    /**
     * @param ParameterValue $value
     * @return RedirectResponse|Response
     * @Config\Route("/parameters/{value}/remove", name = "admin_value_delete")
     * @Config\ParamConverter("value", options = {"mapping": {"value": "id"}})
     */
    public function removevalueAction(ParameterValue $value)
    {
        $parameter_id= $value->getParameter()->getId();
        $this->manager->remove($value);
        $this->manager->flush();

        return $this->redirectToRoute('admin_parameters_edit', array('parameter' =>$parameter_id, 'step' => 2));

    }

    

}
