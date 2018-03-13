<?php

namespace App\Controller;

use App\Entity\User;
use StarterKit\StartBundle\Form\ChangePasswordType;
use StarterKit\StartBundle\Form\UpdateUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use StarterKit\StartBundle\Service\FileUploadInterface;
use StarterKit\StartBundle\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AccountSettingController
 * @package AppBundle\Controller
 */
class AccountSettingController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var FileUploadInterface
     */
    private $fileUploadInterface;

    public function __construct(UserServiceInterface $userService, FileUploadInterface $fileUploadInterface)
    {
        $this->userService = $userService;
        $this->fileUploadInterface = $fileUploadInterface;
    }

    /**
     * @Route("/account-settings/information", name="update_user")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     *
     * @return Response
     */
    public function updateUserAction(Request $request)
    {
        $form = $this->createForm(UpdateUserType::class, $this->getUser());
        $form->handleRequest($request);
        $showSuccess = false;

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $form->getData();
            if (!empty($user->getImage())) {
                $fileUploadModel = $this->fileUploadInterface->uploadFileWithFolderAndName($user->getImage(), 'profile_pics', md5($user->getId() . '_profile_id'));
                $user->setImageUrl($fileUploadModel->getUrl())
                    ->setImageId($fileUploadModel->getFileId())
                    ->setImageVendor($fileUploadModel->getVendor());
            }

            $this->userService->save($user);
            $showSuccess = true;
        }

        return $this->render('account-settings/update-user.html.twig', [
            'updateUserForm' => $form->createView(),
            'showSuccess' => $showSuccess,
            'message' => 'You have successfully updated your profile.'
        ]);
    }

    /**
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     * @Route("/account-settings/change-password", name="change_password")
     * @return Response
     */
    public function changePasswordAction(Request $request)
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);
        $showSuccess = false;

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var User $user */
            $user = $this->getUser();
            $user->setPlainPassword($form->get('newPassword')->getData());
            $this->userService->saveUserWithPlainPassword($user);
            $showSuccess = true;
        }

        return $this->render('account-settings/change-password.html.twig', [
            'changePasswordForm' => $form->createView(),
            'showSuccess' => $showSuccess,
            'message' => 'You have successfully updated your password.'
        ]);
    }
}