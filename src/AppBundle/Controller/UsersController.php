<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\UserFormType;
use AppBundle\Form\Type\UserFilterFormType;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;

class UsersController extends InitializableController
{
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/users/index/{pagenum}", name = "site_users_index", defaults={ "pagenum": "1"})
     */
    public function indexAction($pagenum=1)
    {
        if (!($this->authChecker->isGranted(Role::ADMIN))) {
            return $this->redirectToRoute('homepage');
        }
        else {
            $form=$this->createForm(new UserFilterFormType());
            $username = null;
            $email=null;
            $phone=null;
            $role=null;
            $form->handleRequest($this->request);

            $usersquery = $this->getRepository('User')->createQueryBuilder('u')
                ->leftJoin('u.roles', 'r')
                ->where('u.deleted <> 1')
                ->orderBy('u.id', 'DESC');

            $countquery = $this->getRepository('User')->createQueryBuilder('u')
                ->select('COUNT(DISTINCT u.id)')
                ->leftJoin('u.roles', 'r')
                ->where('u.deleted <> 1');


            if ($form->isSubmitted() && $form->isValid()) {
                $username = $form->get('username')->getData();
                $email = $form->get('email')->getData();
                $phone = $form->get('phone')->getData();
                $role = $form->get('role')->getData();
                $userfio=$form->get('userfio')->getData();
            }

            if (!empty($username)) {
                $usersquery->andWhere('LOWER(u.username) LIKE LOWER(:username) ')->setParameter('username', '%' . trim($username) . '%');
                $countquery->andWhere('LOWER(u.username) LIKE LOWER(:username) ')->setParameter('username', '%' . trim($username) . '%');
            }

            if (!empty($email)) {
                $usersquery->andWhere('LOWER(u.email) LIKE LOWER(:email) ')->setParameter('email', '%' . trim($email) . '%');
                $countquery->andWhere('LOWER(u.email) LIKE LOWER(:email) ')->setParameter('email', '%' . trim($email) . '%');
            }

            if (!empty($phone)) {
                $usersquery->andWhere('LOWER(u.phone) LIKE LOWER(:phone) ')->setParameter('phone', '%' . trim($phone) . '%');
                $countquery->andWhere('LOWER(u.phone) LIKE LOWER(:phone) ')->setParameter('phone', '%' . trim($phone) . '%');
            }

            if (!empty($userfio)) {
                $usersquery->andWhere('LOWER(u.userfio) LIKE LOWER(:userfio) ')->setParameter('userfio', '%' . trim($userfio) . '%');
                $countquery->andWhere('LOWER(u.userfio) LIKE LOWER(:userfio) ')->setParameter('userfio', '%' . trim($userfio) . '%');
            }

            if (!empty($role)) {
                $usersquery->andWhere('r.id = :role')->setParameter('role', $role);
                $countquery->andWhere('r.id = :role')->setParameter('role', $role);
            }


            $count=$countquery->getQuery()->getSingleScalarResult();

            $pages = floor($count / 20) + ($count % 20 > 0 ? 1 : 0);
            if ($pages < 1) $pages = 1;
            if ($pagenum > $pages) $pagenum = $pages;
            $users = $usersquery->setFirstResult(($pagenum - 1) * 20)
                ->setMaxResults(20)
                ->getQuery()->getResult();

            $this->view['page']=$pagenum;
            $this->view['pages']=$pages;

            $this->view['users'] = $users;
            $this->view['form'] = $form->createView();

            $this->navigation = array('active' => 'users');
            return $this->render('AppBundle:Users:index.html.twig');
        }

    }

    /**
     * @return RedirectResponse|Response
     * @Config\Route("/users/add", name = "site_users_add")
     */
    public function addAction()
    {
        if (!($this->authChecker->isGranted(Role::ADMIN))) {
            return $this->redirectToRoute('homepage');
        }
        else {
            $user = new User();
            $user->getRolesCollection()->add($this->getRepository('Role')->findOneByRole(Role::USER));
            $form = $this->createForm(new UserFormType(), $user);
            $form->handleRequest($this->request);

            if ($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                $sames = $this->getRepository('User')->createQueryBuilder('u')
                    ->select('COUNT(u.id) AS id')
                    ->where('u.username = :username')
                    ->setParameters(array('username' => $user->getUsername()))
                    ->getQuery()->getSingleScalarResult();

                if ($sames > 0) {
                    $form->get('username')->addError(new FormError('Пользователь с таким логином уже существует.'));
                    $valid = false;
                }

                if (is_null($form->get('password')->getData())) {
                    $form->get('password')->addError(new FormError('Пожалуйста, укажите пароль пользователя.'));
                    $valid = false;
                }



                if ($valid) {
                    /** @var UserPasswordEncoder $encoder */
                    $encoder = $this->get('security.password_encoder');
                    $user->setSalt(User::generateSalt())
                        ->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    $this->manager->persist($user);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'users.html.twig',
                        array('notice' => 'user_added', 'username' => $user->getUsername())
                    );

                    return $this->redirectToRoute('site_users_index');
                }
            }

            $this->forms['user'] = $form->createView();
            $this->navigation = array('active' => 'users');
            return $this->render('AppBundle:Users:add.html.twig');
        }

    }

    /**
     * @param User $user
     * @return RedirectResponse|Response
     * @Config\Route("/users/{user}/edit", name = "site_users_edit")
     * @Config\ParamConverter("user", options = {"mapping": {"user": "id"}})
     */
    public function editAction(User $user)
    {
        if (!($this->authChecker->isGranted(Role::ADMIN))) {
            return $this->redirectToRoute('homepage');
        }
        else {
            $password = $user->getPassword();
            $form = $this->createForm(new UserFormType(), $user);
            $form->handleRequest($this->request);
            if ($form->isSubmitted() && $form->isValid()) {
                $valid = true;
                $sames = $this->getRepository('User')->createQueryBuilder('u')
                    ->select('COUNT(u.id) AS id')
                    ->where('u.username = :username')
                    ->andWhere('u.id <> :id')
                    ->setParameters(array('username' => $user->getUsername(), 'id' => $user->getId()))
                    ->getQuery()->getSingleScalarResult();

                if ($sames > 0) {
                    $form->get('username')->addError(new FormError('Пользователь с таким логином уже существует.'));
                    $valid = false;
                }

                if ($valid) {
                    if (!is_null($form->get('password')->getData())) {
                        /** @var UserPasswordEncoder $encoder */
                        $encoder = $this->get('security.password_encoder');
                        $user->setSalt(User::generateSalt())
                            ->setPassword($encoder->encodePassword($user, $user->getPassword()));
                    } else {
                        $user->setPassword($password);
                    }


                    $this->manager->persist($user);
                    $this->manager->flush();

                    $this->addNotice('success',
                        'users.html.twig',
                        array('notice' => 'user_changed', 'username' => $user->getUsername())
                    );

                    return $this->redirectToRoute('site_users_index');
                }
            }

            $this->forms['user'] = $form->createView();
            $this->view['user'] = $user;
            $this->navigation = array('active' => 'users');
            return $this->render('AppBundle:Users:edit.html.twig');
        }

    }

    /**
     * @param User $user
     * @return Response
     * @Config\Route("/users/{user}/remove", name = "site_users_remove")
     * @Config\ParamConverter("user", options = {"mapping": {"user": "id"}})
     */
    public function removeAction(User $user)
    {
        $user->setDeleted(true);
        $this->manager->persist($user);
        $this->manager->flush();

        return $this->redirectToRoute('site_users_index');

    }


    
}
