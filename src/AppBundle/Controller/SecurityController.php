<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\LoginFormType;
use AppBundle\Form\Type\ProfileFormType;
use AppBundle\Controller\InitializableController;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\Type\RegistrationFormType;
use AppBundle\Form\Type\RemindPasswordFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Security;
require('autoload.php');

class SecurityController extends InitializableController
{
    //форма авторизации
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/admin/login", name = "site_security_login")
     */
    public function loginAction()
    {
        //если уже авторизован - то перекидываем на домашнюю страницу
        if ($this->authChecker->isGranted(Role::USER)) return $this->redirectToRoute('homepage');

        $error = null;
//проверяем на ошибки
        if ($this->request->attributes->has(Security::AUTHENTICATION_ERROR))
            $error = $this->request->attributes->get(Security::AUTHENTICATION_ERROR);
        else {
            $error = $this->session->get(Security::AUTHENTICATION_ERROR, null);
            $this->session->remove(Security::AUTHENTICATION_ERROR);
        }

        $form = $this->createForm(new LoginFormType(), new User());
        if (!is_null($error)) {
            $this->addNotice('error', 'security_login.html.twig', array('notice' => 'auth_error'));
        }
        $this->navigation = array('active' => 'login');
        $this->forms = array(
            'login' => $form->createView(),
            'last_username' => $this->session->get(Security::LAST_USERNAME, null)
        );


        return $this->render('AppBundle:Security:login.html.twig');
    }
//проверка логина и пароля (автоматом провеяет сама Symfony)
    /**
     * @throws NotFoundHttpException
     * @Config\Route("/admin/login-check", name = "site_security_login_check")
     */
    public function loginCheckAction()
    {
        throw $this->createNotFoundException();
    }
//выход из сайта (аналогично - все автоматически)
    /**
     * @throws NotFoundHttpException
     * @Config\Route("/admin/logout", name = "site_security_logout")
     */
    public function logoutAction()
    {
        throw $this->createNotFoundException();
    }
//управление профилем пользователя
    /**
     * @return RedirectResponse|Response
     * @Config\Route("/admin/profile", name = "site_security_profile")
     */
    public function profileAction()
    {

        $userpassword = $this->user->getPassword();
        $form = $this->createForm(new ProfileFormType(), $this->user);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            //если пароль не менялся
            if (!is_null($form->get('password')->getData())) {
                /** @var UserPasswordEncoder $encoder */
                $encoder = $this->get('security.password_encoder');
                $this->user->setSalt(User::generateSalt())
                    ->setPassword($encoder->encodePassword($this->user, $this->user->getPassword()));
            }
            //если пароль менялся
            else {
                /** @var UserPasswordEncoder $encoder */
                $this->user->setPassword($userpassword);
            }
            $this->user->upload();
            $this->manager->persist($this->user);
            $this->manager->flush();

            $this->addNotice('success',
                'security_profile.html.twig',
                array('notice' => 'user_changed')
            );


            return $this->redirectToRoute('site_security_profile');
        }

        $this->forms['profile'] = $form->createView();
        $this->navigation = array('active' => 'homepage');
        return $this->render('AppBundle:Security:profile.html.twig');
    }

//регистрация на сайте
    /**
     * @return Response
     * @Config\Route("/admin/registration", name = "site_general_registration")
     */
    public function registerAction()
    {
        throw $this->createNotFoundException();
        $user = new User();
        $user->getRolesCollection()->add($this->getRepository('Role')->findOneByRole(Role::USER));
        $form = $this->createForm(new RegistrationFormType(), $user);
        $form->handleRequest($this->request);
//обработка отправки формы
        if ($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            $samesemail = $this->getRepository('User')->createQueryBuilder('u')
                ->select('COUNT(u.id) AS id')
                ->where('u.email = :email')
                ->setParameters(array('email' => $user->getEmail()))
                ->getQuery()->getSingleScalarResult();

            if ($samesemail > 0) {
                $form->get('email')->addError(new FormError('Пользователь с таким email уже существует.'));
                $valid = false;
            }

            $sameslogin = $this->getRepository('User')->createQueryBuilder('u')
                ->select('COUNT(u.id) AS id')
                ->where('u.username = :username')
                ->setParameters(array('username' => $user->getUsername()))
                ->getQuery()->getSingleScalarResult();

            if ($sameslogin > 0) {
                $form->get('username')->addError(new FormError('Пользователь с таким логином уже существует.'));
                $valid = false;
            }
            $recaptcha = new \ReCaptcha\ReCaptcha("6LeQfCgUAAAAAD1feXHUi0I9QXUTHPVLiFJ4t5Do");
            $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if (!($resp->isSuccess())) {
                $valid = false;
                $form->addError(new FormError('Поставьте галочку, что Вы не робот!'));
            }
            if ($valid) {
                /** @var UserPasswordEncoder $encoder */
                $encoder = $this->get('security.password_encoder');
                $userpassword = $user->getPassword();
                $user->setSalt(User::generateSalt())
                    ->setPassword($encoder->encodePassword($user, $user->getPassword()));
                $this->manager->persist($user);
                $this->manager->flush();


                $emails = $user->getEmail();
                /** @var \Swift_Mailer $mailer */
                $mailer = $this->get('mailer');
                $message = $mailer->createMessage()
                    ->setSubject('Регистрация на сервисе')
                    ->setFrom(array('noreply@'.$_SERVER['SERVER_NAME'] => 'Student-it.ru'))
                    ->setTo($emails)
                    ->setBody($this->renderView('AppBundle:Mail:registration.html.twig',
                        array(
                            'login'=>$user->getUsername(),
                            'password'=>$userpassword,
                            'userfio'=>$user->getUserfio()
                        )), 'text/html');
                $mailer->send($message);

                $emails = 'info@student-it.ru';
                /** @var \Swift_Mailer $mailer */
                $mailer = $this->get('mailer');
                $message = $mailer->createMessage()
                    ->setSubject('Регистрация на сервисе')
                    ->setFrom(array('noreply@'.$_SERVER['SERVER_NAME'] => 'Student-it.ru'))
                    ->setTo($emails)
                    ->setBody($this->renderView('AppBundle:Mail:newuser.html.twig',
                        array(
                            'login'=>$user->getUserfio(),
                            'email'=>$user->getEmail(),
                        )), 'text/html');
                $mailer->send($message);

                $token = new UsernamePasswordToken($user, null, 'user_provider', $user->getRoles());
                $this->get('security.context')->setToken($token);
                return $this->redirectToRoute('admin_homepage');
            }
        }
        $this->navigation = array('active' => 'homepage');
        $this->forms['user'] = $form->createView();

        return $this->render('AppBundle:Security:registration.html.twig');
    }
//напоминание пароля
    /**
     * @return Response
     * @Config\Route("/admin/remindpassword", name = "site_general_remindpassword")
     */
    public function remindpasswordAction()
    {
        throw $this->createNotFoundException();
        $user = new User();
        $form = $this->createForm(new RemindPasswordFormType(), $user);
        $form->handleRequest($this->request);
//обработка формы
        if ($form->isSubmitted() && $form->isValid()) {
            $valid = true;
            /** @var User $user */
            $user = $this->getRepository('User')->findOneBy(array('email'=>$form->get('email')->getData()));

            if (is_null($user)) {
                $form->get('email')->addError(new FormError('Пользователь с таким email не зарегистрирован.'));
                $valid = false;
            }
            $recaptcha = new \ReCaptcha\ReCaptcha("6LeQfCgUAAAAAD1feXHUi0I9QXUTHPVLiFJ4t5Do");
            $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
            if (!($resp->isSuccess())) {
                $valid = false;
                $form->addError(new FormError('Поставьте галочку, что Вы не робот!'));
            }

            if ($valid) {
                /** @var UserPasswordEncoder $encoder */
                $encoder = $this->get('security.password_encoder');
                $userpassword = substr (md5(uniqid(rand(),true)),rand(1,4),rand(6,8));
                $user->setSalt(User::generateSalt())
                    ->setPassword($encoder->encodePassword($user, $userpassword));
                $this->manager->persist($user);
                $this->manager->flush();
                
                $emails = $user->getEmail();
                /** @var \Swift_Mailer $mailer */
                $mailer = $this->get('mailer');
                $message = $mailer->createMessage()
                    ->setSubject('Восстановление пароля')
                    ->setFrom(array('noreply@'.$_SERVER['SERVER_NAME'] => 'Сайт МФЦ'))
                    ->setTo($emails)
                    ->setBody($this->renderView('AppBundle:Mail:remindpassword.html.twig',
                        array(
                            'login'=>$user->getUsername(),
                            'password'=>$userpassword,
                            'userfio'=>$user->getUserfio()
                        )), 'text/html');;
                $mailer->send($message);

                return $this->redirectToRoute('site_general_remindpasswordsuccess');
            }
        }
        $this->navigation = array('active' => 'homepage');
        $this->forms['profile'] = $form->createView();

        return $this->render('AppBundle:Security:remindpassword.html.twig');
    }
//страница с инстукцией по проверке почты, на которую был выслан новый пароль
    /**
     * @return Response
     * @Config\Route("/admin/remindpasswordok", name = "site_general_remindpasswordsuccess")
     */
    public function remindpasswordsuccessAction()
    {
        throw $this->createNotFoundException();

        $this->navigation = array('active' => 'homepage');

        return $this->render('AppBundle:Security:remindpasswordok.html.twig');
    }
    
}
