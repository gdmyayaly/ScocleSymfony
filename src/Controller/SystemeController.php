<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produits;
use App\Entity\SousCategories;
use App\Repository\CategorieRepository;
use App\Repository\ProduitsRepository;
use App\Repository\SousCategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Mime\Message;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SystemeController extends AbstractController
{
    /**
     * @Route("/infos", name="systeme")
     */
    public function infos(){
        $a=$this->getUser();

        return new JsonResponse($a->getPrenom());
    }
    /**
     * @Route("/addcategorie")
     */
    public function addcategorie(Request $request,EntityManagerInterface $entityManagerInterface){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $categorie= new Categorie();
        $categorie->setNom($data['nom']);
        $entityManagerInterface->persist($categorie);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'status'=>201,
            'message'=>'Bravo'
        ]);
        //return new Response('Ajout Réussit.', Response::HTTP_CREATED);
    }
        /**
     * @Route("/addsouscategorie")
     */
    public function addsouscategorie(Request $request,EntityManagerInterface $entityManagerInterface,CategorieRepository $categorieRepository){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $souscategorie= new SousCategories();
        $souscategorie->setNom($data['nom']);
        $cat=$categorieRepository->find($data['id']);
        $souscategorie->setCategorie($cat);
        $entityManagerInterface->persist($souscategorie);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'status'=>201,
            'message'=>'Bravo'
        ]);
      //  return new Response('Ajout Réussit.', Response::HTTP_CREATED);
    }
    /**
     * @Route("/allcategorie")
     */
    public function allcategorie(CategorieRepository $categorieRepository,SerializerInterface $serializer){
        $cat = $categorieRepository->findAll();
        $data = $serializer->serialize($cat, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/onesouscategorie")
     */
    public function onesouscategorie(Request $request,SousCategoriesRepository $sousCategoriesRepository,SerializerInterface $serializer){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $souscat = $sousCategoriesRepository->findBy(['categorie'=>$data['id']]);
        $data = $serializer->serialize($souscat, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/addproduis")
     */
    public function addproduis(Request $request,EntityManagerInterface $entityManagerInterface,CategorieRepository $categorieRepository,SousCategoriesRepository $sousCategoriesRepository){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $produits= new Produits();
        $produits->setNom($data['nom']);
        $produits->setDescription($data['description']);
        $produits->setPrix($data['prix']);
        
        if ($categorieRepository->find($data['categorie'])) {
            $produits->setCategorie($categorieRepository->find($data['categorie']));
        }
        if ($sousCategoriesRepository->find($data['souscategorie'])) {
            $produits->setSouscategorie($sousCategoriesRepository->find($data['souscategorie']));
        }

        if ($requestFile=$request->files->all()) {
            $file = $requestFile['image'];
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('chemin'), $fileName);
            $produits->setImage($fileName);
        }
        
        $entityManagerInterface->persist($produits);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'status'=>201,
            'message'=>'Bravo'
        ]);
    }
    /**
     * @Route("/allproduitsweb")
     */
    public function allproduitsweb(Request $request,ProduitsRepository $produitsRepository,SerializerInterface $serializer,CategorieRepository $categorieRepository,SousCategoriesRepository $sousCategoriesRepository){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        if ($data['categorie'] && $data['souscategorie'] ) {
            $cat=$categorieRepository->findOneBy(['nom'=>$data['categorie']]);
            $souscat=$sousCategoriesRepository->findOneBy(['id'=>$data['souscategorie']]);
            $produits = $produitsRepository->findBy(['categorie'=>$cat->getId(),'souscategorie'=>$souscat->getId()]);
        }
        elseif($data['categorie']){
            $cat=$categorieRepository->findOneBy(['nom'=>$data['categorie']]);
            $produits = $produitsRepository->findBy(['categorie'=>$cat->getId()]);
        }
        elseif ($data['souscategorie']) {
            $souscat=$sousCategoriesRepository->findOneBy(['id'=>$data['souscategorie']]);
            $produits = $produitsRepository->findBy(['souscategorie'=>$souscat->getId()]);
        }

        // $produits = $produitsRepository->findAll();
        $data = $serializer->serialize($produits, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/allproduits")
     */
    public function allproduits(Request $request,ProduitsRepository $produitsRepository,SerializerInterface $serializer){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $produits = $produitsRepository->findAll();
        $data = $serializer->serialize($produits, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/fiveproduits")
     */
    public function fiveproduits(ProduitsRepository $produitsRepository,SerializerInterface $serializer){
        $produits = $produitsRepository->allproduispar(5);
        $data = $serializer->serialize($produits, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
        /**
     * @Route("/tenproduits")
     */
    public function tenproduits(ProduitsRepository $produitsRepository,SerializerInterface $serializer){
        $produits = $produitsRepository->allproduispar(10);
        $data = $serializer->serialize($produits, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/oneproduit")
     */
    public function oneproduit(Request $request,ProduitsRepository $produitsRepository,SerializerInterface $serializer){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $produits = $produitsRepository->findOneBy(['id'=>$data['id']]);
        $data = $serializer->serialize($produits, 'json', [
            'groups' => ['list']
        ]);
        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }
    /**
     * @Route("/deleteproduits")
     */
    public function deleteproduits(Request $request,ProduitsRepository $produitsRepository,EntityManagerInterface $entityManagerInterface){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $prod=$produitsRepository->find($data['id']);
        $entityManagerInterface->remove($prod);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'status'=>201,
            'message'=>'Bravo'
        ]);
    }
        /**
     * @Route("/deletecat")
     */
    public function deletecat(Request $request,CategorieRepository $categorieRepository,EntityManagerInterface $entityManagerInterface){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $prod=$categorieRepository->find($data['id']);
        $entityManagerInterface->remove($prod);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'status'=>201,
            'message'=>'Bravo'
        ]);
    }
            /**
     * @Route("/deletesouscat")
     */
    public function deletesouscat(Request $request,SousCategoriesRepository $sousCategoriesRepository,EntityManagerInterface $entityManagerInterface){
        $data = json_decode($request->getContent(),true);
        if(!$data){
            $data=$request->request->all();
        }
        $prod=$sousCategoriesRepository->find($data['id']);
        $entityManagerInterface->remove($prod);
        $entityManagerInterface->flush();
        return new JsonResponse([
            'status'=>201,
            'message'=>'Bravo'
        ]);
    }
}
