<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\PostType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends AbstractController
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function index()
    {
        return $this->render('base.html.twig');
    }

    public function addPost(Request $request): Response
    {
        // try {
        //     $posts = $this->em->getRepository(Post::class)->findAll();

        //     if (!$posts) {
        //         throw new NotFoundHttpException('Post not found.'); // Throw a 404 exception
        //     }

        //     // ... your code to handle the existing post

        // } catch (NotFoundHttpException $e) {
        //     // Handle the case where the post is not found
        //     return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        // } catch (\Exception $e) {
        //     // Handle other exceptions here
        //     return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        // }
        // $posts = $this->em->getRepository(Post::class)->findAll();
        // $post = new Post();


        // $form = $this->createForm(PostType::class, $post);


        // $form->handleRequest($request);

        if ($request->isMethod('POST')
            //  && $form->isSubmitted() && $form->isValid()
        ) {

            try {
                //If your using the symfony form
                // $postValue = $request->get('post')['post'];
                //If your using the vue js form
                $postValue = json_decode($request->getContent(), true);
                // echo json_encode($postData);
                // die;
                $post = new Post();
                $post->setPost($postValue['data']);

                $post->setCreatedAt(new \DateTime());
                $this->em->persist($post);
                $this->em->flush();

                $lastAddedPost = $this->em->getRepository(Post::class)->findOneBy([], ['createdAt' => 'DESC'])->getId();

                $responseData = [
                    'message' => 'Post added successfully',
                    'lastAddedPostId' => $lastAddedPost,
                ];

                return new JsonResponse($responseData, 200);
            } catch (\Exception $e) {
                // Log the exception or handle it appropriately
                echo new Response($e->getMessage(), 500);
            }

            //You redirect in the case that you use a symfony form alone without vue js
            // return $this->redirectToRoute('addpost');
        }
        return $this->render('addPost.html.twig', [
            // 'form' => $form->createView(),
            // 'posts' => $posts,
        ]);
    }

    public function deletePost($id)
    {
        try {
            $postToDelete = $this->em->getRepository(Post::class)->find($id);
            !$postToDelete ? throw $this->createNotFoundException('Post not found.') : '';
            $this->em->remove($postToDelete);
            $this->em->flush();
            return new Response('Post deleted successfully', 200);
        } catch (\Exception $e) {
            // Log the exception or handle it appropriately
            echo new Response($e->getMessage(), 500);
        }
    }

    public function editPost(Request $request, $id): Response
    {
        $post = $this->em->getRepository(Post::class)->find($id);
        $editForm = $this->createForm(PostType::class, $post);
        $editForm->handleRequest($request);

        if ($request->isMethod('POST') && $editForm->isSubmitted() && $editForm->isValid()) {

            $editedPost = $request->get('post')['post'];
            $post->setPost($editedPost);
            $post->setCreatedAt(new \DateTime());

            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('addpost');
        }
        return $this->render('editPost.html.twig', [
            'editForm' => $editForm->createView()
        ]);
    }

    public function getAllPosts()
    {
        // $encoders = [new XmlEncoder(), new JsonEncoder()];
        // $normalizers = [new ObjectNormalizer()];
        // $serializer = new Serializer($normalizers, $encoders);
        try {
            $posts = $this->em->getRepository(Post::class)->findAll();

            if (!$posts) {
                throw new NotFoundHttpException('Posts not found.'); // Throw a 404 exception
            }

            // ... your code to handle the existing post

        } catch (NotFoundHttpException $e) {
            // Handle the case where the post is not found
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            // Handle other exceptions here
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!empty($posts)) {
            foreach ($posts as $post) {
                // Convert each post to an array of data
                $data[] = [
                    'id' => $post->getId(),
                    'post' => $post->getPost(),
                    'CreatedAt' => $post->getCreatedAt(),
                ];
            }
        } else {
            $data = [];
        }

        return new JsonResponse($data);
    }
}
