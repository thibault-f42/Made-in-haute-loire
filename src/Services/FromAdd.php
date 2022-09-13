<?php

namespace App\Services;
use App\Entity\Entreprise;
use App\Entity\Fichier;
use Symfony\Component\HttpFoundation\Request;

class FromAdd
{
    public function getLastPage(Request $request){
        $lastUrl = $request->getSession()->get('lastUrl');
        $thisUrl = $request->getSession()->get('thiUrl');
        if ($lastUrl == null || $thisUrl != $request->getPathInfo()){
            $lastUrl = $request->headers->get('referer');
            $request->getSession()->set('lastUrl',$lastUrl);
            $request->getSession()->set('thiUrl',$request->getPathInfo());
        }
        return $lastUrl;
    }
    public function savePicture($images , Entreprise $entreprise , $nomRepertorier, $type ){
        //On boucle pour récupérer toutes les images
        foreach ($images as $image) {
            // On génère un nom unique
            $nomFichier=md5(uniqid()).'.'.$image->guessExtension();
            // On copie le fichier dans le dossier upload
            $image->move($nomRepertorier, $nomFichier);
            //On stocke le chemin d'accès en base de données
            $fichier = new Fichier();
            $fichier->setUrlFichier($nomFichier);
            $fichier->setTypeFichier($type);
            //on ajoute le fichier a notre entreprise
            $entreprise->addFichier($fichier);
        }
    }

}