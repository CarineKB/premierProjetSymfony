<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\CreateVoitureFormType;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Orm\ManagerRegistryAwareEntityManagerProvider;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoitureController extends AbstractController
{
    #[Route('/enregistre_voiture', name: 'app_voiture')]
    public function record(ManagerRegistry $doctrine, Request $request): Response
    {
        $voiture = new Voiture();
        $form=$this->createForm(CreateVoitureFormType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $voiture -> setDateEnregistrement(new DateTime("now"));
            $manager = $doctrine->getManager();
            $manager->persist($voiture);
            $manager->flush();

            return $this->redirectToRoute('app_voiture');
        }
        return $this->render('formVoiture.html.twig', ['formVoiture'=>$form-> createView()]);
    }


    #[Route('/allVoitures', name: 'voiture_affiche')]
    public function allVoitures(ManagerRegistry $doctrine): Response
    {
        $voitures=$doctrine->getRepository(Voiture::class)->findAll();
        return $this->render('allVoitures.html.twig', ['voitures'=> $voitures]);

    }
    #[Route('/uneVoiture/{id<\d+>}', name: 'une_voiture')]
    public function uneVoiture($id, ManagerRegistry $doctrine): Response
     {
        $voiture=$doctrine->getRepository(Voiture::class)->find($id);
        return $this->render('uneVoiture.html.twig', ['voiture'=>$voiture]); 

    }
    #[Route('/update_voiture/{id<\d+>}', name: 'maj_voiture')]
    public function update(ManagerRegistry $doctrine, $id,Request $request){
        $voiture = $doctrine->getRepository(Voiture::class)->find($id);
        $form= $this->createForm(CreateVoitureFormType::class, $voiture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
        $manager= $doctrine->getManager();
        $manager->persist($voiture);
        $manager->flush();


            return $this->redirectToRoute("app_voiture");

        }
       
        return $this->render('formVoiture.html.twig', ['formVoiture'=>$form->createView()]);
        
    }

    #[Route('/delete_voiture', name: 'supprim_voiture')]
    public function delete($id, ManagerRegistry $doctrine){
    $voiture = $doctrine->getRepository(Voiture::class)->find($id);
    $manager= $doctrine->getManager();
    $manager->remove($voiture);
    $manager->flush();

    return $this->redirectToRoute('app_voiture');
    }

}
