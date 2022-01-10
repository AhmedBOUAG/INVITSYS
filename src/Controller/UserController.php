<?php

namespace App\Controller;

use App\Entity\User;
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


class UserController extends AbstractController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }
    #[Route('api/users/create', name: 'create_user', methods: 'POST')]
    public function create(Request $request)
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, "Les données saisies ne sont pas correcte.");
        }

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse("Utilisateur crée avec succès!", Response::HTTP_CREATED);
    }

    #[Route('api/users/list', name: 'get_all_users', methods: 'GET')]
    public function getUsers()
    {
        $allUsers = $this->em->getRepository(User::class)->findAll();
        $users = $this->serializer->serialize($allUsers, 'json');

        return new JsonResponse($users);
    }

    #[Route('api/users/edit/{user}', name: 'edit_user_by_id', methods: ['PUT'])]
    public function editUserById(User $user, Request $request)
    {
        if ($user === null) {
            throw new NotFoundHttpException("Pas d'utilisateur correspondant");
        }
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);
        $this->em->flush();
        return new JsonResponse('OK', Response::HTTP_CREATED);
    }

    #[Route('api/users/get/{user}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(User $user)
    {
        if ($user === null) {
            throw new NotFoundHttpException("Pas d'utilisateur correspondant");
        }
        $users = $this->serializer->serialize($user, 'json');

        return new JsonResponse($users);
    }

    #[Route('api/users/delete/{user}', name: 'delete_user_by_id', methods: ['DELETE'])]
    public function deleteUserById(User $user)
    {
        if ($user === null) {
            throw new NotFoundHttpException("Pas d'utilisateur correspondant");
        }
        //$users = $this->serializer->serialize($user, 'json');
        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse('Utilisateur supprimé', Response::HTTP_CREATED);
    }
}
