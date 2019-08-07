<?php 
namespace App\Controller;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use App\Entity\Prestataire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

    /**
     * @Route("/api", name="register")
     */
class UserController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $data=$request->request->all();
        $file=$request->files->all()['imageFile'];
        $form->submit($data);
        
        //if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setImageFile($file);
           $repository = $this->getDoctrine()->getRepository(Prestataire::class);
            $parte= $repository->findAll();
    //$user->setPrestataire($parte[]);
            $user->setStatut("actif");
            $profil= $data["profil"];
            $user->setProfil($profil);
            $roles=[];
            if($profil=="admin"){
                $user->setRoles(['ROLE_Admin_Wari']);
            }
            elseif ($profil=="user") {
                $user->setRoles(['ROLE_User']);
            }
            elseif ($profil=="caissier"){
                $user->setRoles(['ROLE_Caissier_Systéme']);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // // do anything else you need here, like send an email
            // return $this->handleView($this->View(['stataus'=> 'ok'],Response::HTTP_CREATED));  
            $data = [
                'status' => 201,
                'message' => 'Le user est  bien ajouté'
            ];
            return new JsonResponse($data, 201); 
        //}

        $data = [
            'status' => 500,
            'message' => 'Vous devez renseigner les champs'
        ];
        return new JsonResponse($data, 500);
    }
    
}

