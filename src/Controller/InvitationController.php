<?php

namespace App\Controller;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class InvitationController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }
    #[Route('api/invitation/create', name: 'create_invitation', methods: 'POST')]
    public function create(Request $request)
    {
        $invitation = $this->serializer->deserialize($request->getContent(), Invitation::class, 'json');
        $errors = $this->validator->validate($invitation);
        if (count($errors) > 0) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, "Les données saisies ne sont pas correcte.");
        }

        $this->em->persist($invitation);
        $this->em->flush();

        return new JsonResponse("Invitation créee avec succès!", Response::HTTP_CREATED);
    }

    #[Route('api/invitation/{invitation}', name: 'get_invitation', methods: 'GET')]
    public function getInvitation(Invitation $invitation)
    {
        if($invitation === null) {
            throw new NotFoundHttpException("Invitaion not found!");
        }

        $invitation = $this->serializer->serialize($invitation, 'json');
        return new JsonResponse($invitation, Response::HTTP_OK);
    }
    
    #[Route('api/invitation/update/{invitation}', name: 'update_invitation', methods: 'PUT')]
    public function update(Request $request, invitation $invitation) {

        if($invitation === null) {
            throw new NotFoundHttpException("Invitaion not found!");
        }

        $invitation = $this->serializer->deserialize($request->getContent(),Invitation::class,'json',[
            AbstractNormalizer::OBJECT_TO_POPULATE=>$invitation
        ]);
        
        $errors = $this->validator->validate($invitation);
        if (count($errors) > 0) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, "Les données saisies ne sont pas correcte.");
        }
        $this->em->flush(); 

        return new JsonResponse("Invitation modifiée avec succès!", Response::HTTP_CREATED);
    }
    
    #[Route('api/invitation/list/user', name: 'get_invitations_by_user', methods: 'GET')]
    public function getInvitionByUserExp(Request $request) {
        $invitations = [];
        // faire en sorte que $data = ['user_exp'=> 1] ou ['user_dest'=> 1]
        $data = json_decode($request->getContent(),'json',true);
        if (isset($data['user_exp']) || isset($data['user_dest'])) {
            $invitations = $this->em->getRepository(Invitation::class)->findBy($data );
        }
       
        if (empty($invitations)) {
            throw new NotFoundHttpException("Invitaions not found!");
        }

        return new JsonResponse($invitations , Response::HTTP_OK, true);
    }
}
