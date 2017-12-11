<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Listener\UserListener;
use Mockery\Mock;
use StarterKit\StartBundle\Event\UserEvent;
use App\Tests\BaseTestCase;

class UserListenerTest extends BaseTestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Swift_Mailer|Mock
     */
    protected $mailer;

    /**
     * @var UserListener
     */
    protected $userListener;

    public function setUp()
    {
        parent::setUp();
        $this->twig = $this->getContainer()->get('twig');
        $this->mailer = \Mockery::mock(\Swift_Mailer::class);
        $this->userListener = new UserListener($this->twig, $this->mailer, 'email@gmail.com');
    }

    /**
     * Tests that we can send out a forgot password email.  Tests the twig compiles
     */
    public function testForgetPasswordEmail()
    {
        $user = new User();
        $user->setForgetPasswordToken('token')
                ->setForgetPasswordExpired(new \DateTime());

        $this->mailer->shouldReceive('send')->with(\Mockery::type(\Swift_Message::class))->once();
        $this->userListener->onForgetPassword(new UserEvent($user));
    }

    /**
     * Tests we can send out a register email and that the twig compiles
     */
    public function testRegisterEmail()
    {
        $user = new User();
        $user->setEmail('blah@gmail.com');

        $this->mailer->shouldReceive('send')->with(\Mockery::type(\Swift_Message::class))->once();
        $this->userListener->onRegister(new UserEvent($user));
    }
}